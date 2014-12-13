<?php
include('partials/connect.php');

//Debugging function
function printDebug($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

//returns an array of all actors one degree away from the input
function getAdjacentActors($link, $person_ID) {

    $actorsQuery = $link->prepare("SELECT Person_ID FROM FM_Acted_In WHERE IMDB_ID IN (SELECT IMDB_ID FROM FM_Acted_In WHERE Person_ID = ? )") or die(printDebug(mysqli_error($link)));
    $actorsQuery->bind_param("i", $person_ID);
    $actorsQuery->execute();
    $actorsQuery->bind_result($actor);

    //save to array
    $actors = array();
    while ($actorsQuery->fetch()) {
        array_push($actors, $actor);
    }

    // echo "Adjacent Actors";
    // printDebug($uniqueActors);
    

    return $actors;
}

function getEvenMoreAdjacentActors($link, $personArray) {


    $sql = "SELECT Person_ID FROM FM_Acted_In WHERE IMDB_ID IN (SELECT IMDB_ID FROM FM_Acted_In WHERE Person_ID IN (".implode(", ", $personArray).") )";

    if(!$result = $link->query($sql)){
        die('There was an error running the query [' . $db->error . ']');
    }


    //save to array
    $actorsArrayOfArrays = $result->fetch_all();
    $actors = array();
    foreach ($actorsArrayOfArrays as $actorArray) {
        $actors[] = intval($actorArray[0]);
    }

    // printDebug($actors);

    // echo "Adjacent Actors";
    // printDebug($uniqueActors);

    return $actors;

}

//vertices is an array of all nodes that are either visited or unvisited
//unvisited is just unvisited
//first actor is the one all the others are adjacent to
function addToGraph(&$vertices, &$unvisited, &$neighbors, &$distances, &$previous, $firstActor, $actors) {

    // echo "First Actor in addToGraph:\n";
    // printDebug($firstActor);
    // echo "\n";
    //add first actor
    if(!in_array($firstActor, $vertices, true)) {
        array_push($vertices, $firstActor);
        array_push($unvisited, $firstActor);
        $distances[$firstActor] = INF;
        $previous[$firstActor] = NULL;

    }

    //add new vertices to relevant arrays
    foreach ($actors as $actor) {
        if(!in_array($actor, $vertices, true)) {
            array_push($vertices, $actor);
            array_push($unvisited, $actor);

            //add neighbors with edge cost 1
            $neighbors[$firstActor][] = array("end" => $actor, "cost" => 1);
            $neighbors[$actor][] = array("end" => $firstActor, "cost" => 1);

            $distances[$actor] = INF;
            $previous[$actor] = NULL;
            // echo $actor." was added to vertices\n";

        } else {
            // echo $actor." was found in vertices\n";
        }
    }
    // echo "Distances: \n";
    // printDebug($distances);
    // echo "\n";
}

//the plan:
//get adjacent actors of the first actor
//add them to the vertices, neighbors, and unvisited arrays
//compute closest (random if tie)
//get next closest's actors
//update distances
//etc i hope

function progressToNextNode($link, &$vertices, &$unvisited, &$neighbors, &$distances, &$previous) {
    //the source is the first node to be set as visited, which updates the distances
    $min = INF;
    foreach ($unvisited as $vertex){
        if ($distances[$vertex] < $min) {
            // echo "New min is ".$distances[$vertex]." on node ".$vertex."\n";
            $min = $distances[$vertex];
            $u = $vertex; //save closest node to u
        }
    }

    // echo "<p>";
    // echo "Next traversed node is ".$u." with distance ".$distances[$u]."\n";
    // echo " from node : ".$previous[$u];
    // echo "</p>";

    //returns difference of &Q - &u
    //pulls u out of Q
    $unvisited = array_diff($unvisited, array($u));

    
    // echo "Unvisited length after removing element: \n";
    // printDebug(count($unvisited));
    // echo "Vertices length: \n";
    // printDebug(count($vertices));
    // echo "\n";
    // if ($distances[$u] == INF or $u == $target) {
    //     // echo "Reached the end, or no nodes had noninfinite distance. \n";
    //     break;
    // }

    //add more to the arrays
    addToGraph($vertices, $unvisited, $neighbors, $distances, $previous, $u, getAdjacentActors($link, $u));
    // addToGraph($vertices, $unvisited, $neighbors, $distances, $previous, $source, getEvenMoreAdjacentActors($link, $vertices));

    //recompute distances from the new latest node
    if (isset($neighbors[$u])) {
        foreach ($neighbors[$u] as $arr) {
            $alt = $distances[$u] + $arr["cost"];
            if ($alt < $distances[$arr["end"]]) {
                $distances[$arr["end"]] = $alt;
                $previous[$arr["end"]] = $u;
            }
        }
    }
}

function dijkstra($link, $source, $target) {

    //initialize vertices and neighbors
    $verticesSource = array();
    $unvisitedSource = array();
    $neighborsSource = array();
    $distancesSource = array();
    $previousSource = array();

    //initialize vertices and neighbors
    $verticesTarget = array();
    $unvisitedTarget= array();
    $neighborsTarget = array();
    $distancesTarget = array();
    $previousTarget = array();

    addToGraph($verticesSource, $unvisitedSource, $neighborsSource, $distancesSource, $previousSource, $source, getAdjacentActors($link, $source));
    addToGraph($verticesTarget, $unvisitedTarget, $neighborsTarget, $distancesTarget, $previousTarget, $target, getAdjacentActors($link, $target));


    // echo "Unvisited length: \n";
    // printDebug(count($unvisited));
    // echo "\n";

    //first node has distance 0
    $distances[$source] = 0;
    $distances[$target] = 0;

    while (count($unvisitedSource) > 0) {
        progressToNextNode($link, $verticesSource, $unvisitedSource, $neighborsSource, $distancesSource, $previousSource);
        progressToNextNode($link, $verticesTarget, $unvisitedTarget, $neighborsTarget, $distancesTarget, $previousTarget);

        $overlap = array_diff($verticesSource, $verticesTarget);

        if(count($overlap)) {
            var_dump("Previous for Source");
            printDebug($previousSource);
            var_dump("Previous for Target");
            printDebug($previousTarget);
            var_dump("Overlap:")
            printDebug($overlap);
            break;
        }
        
    }
    //pull path out of previouses
    $path = array();
    $u = $target;
    while (isset($previous[$u])) {
        array_unshift($path, $u);
        $u = $previous[$u];
    }
    array_unshift($path, $u);
    return $path;
}

function getNameForID($link, $person_ID) {
    $actorsQuery = $link->prepare("SELECT Person_Name FROM FM_Person WHERE Person_ID = ? ") or die(printDebug(mysqli_error($link)));
    $actorsQuery->bind_param("i", $person_ID);
    $actorsQuery->execute();
    $actorsQuery->bind_result($actor);
    $actorsQuery->fetch();

    return $actor;
}



//~~~~~~~~~~~~BEGIN~~~~~~~~~~~~~~

//convert names to IDs

$firstNameQuery = $link->prepare("SELECT Person_ID FROM FM_Person WHERE UPPER(Person_Name) = UPPER(?) ") or die(printDebug(mysqli_error($link)));
$firstNameQuery->bind_param("s", $_GET['firstPersonName']);
$firstNameQuery->execute();
$firstNameQuery->bind_result($firstNameQueryResult);
$firstNameQuery->fetch();

//first ID
$firstNameID = $firstNameQueryResult;
if(!$firstNameID) {
    die("No First Actor Found");
}
$firstNameQuery->close();

// include('partials/connect.php');
$secondNameQuery = $link->prepare("SELECT Person_ID FROM FM_Person WHERE UPPER(Person_Name) = UPPER(?) ") or die(printDebug(mysqli_error($link)));
$secondNameQuery->bind_param("s", $_GET['secondPersonName']);
$secondNameQuery->execute();
$secondNameQuery->bind_result($secondNameQueryResult);
$secondNameQuery->fetch();

//second ID
$secondNameID = $secondNameQueryResult;
if(!$secondNameID) {
    die("No Second Actor Found");
}
$secondNameQuery->close();

$path = dijkstra($link, $firstNameID, $secondNameID);

//get names for each actor
$actorNames = array();
foreach ($path as $person) {
    $actorNames[] = getNameForID($link, $person);
}

//GOGO AJAX!
$ajaxArray = array('firstActorName' => ucwords($_GET['firstPersonName']), 'secondActorName' => ucwords($_GET['secondPersonName']), 'path' => $actorNames);

$jsonString = json_encode($ajaxArray);

echo $jsonString;

?>

