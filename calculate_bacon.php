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
        if ($distances[$vertex] <= $min) {
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

    return $u; //return new next node
}

function checkForOverlap($currentFirst, $currentSecond, $unvisitedSource, $unvisitedTarget, $previousSource, $previousTarget, $firstIsMoreRecent) {

    $overlap = array_intersect($unvisitedSource, $unvisitedTarget);

    if(count($overlap)) {
        var_dump("stopped with currentFirst at:");
        printDebug($currentFirst);
        var_dump("and currentSecond at:");
        printDebug($currentSecond);

        var_dump("Overlap:");
        printDebug($overlap);
        $pathFirst = array();
        $u = $currentFirst;
        while (isset($previousSource[$u])) {
            array_unshift($pathFirst, $u);
            $u = $previousSource[$u];
        }
        array_unshift($pathFirst, $u);

        var_dump("Path from Source:");
        printDebug($pathFirst);

        $pathSecond = array();
        $u = $currentSecond;
        while (isset($previousTarget[$u])) {
            array_unshift($pathSecond, $u);
            $u = $previousTarget[$u];
        }
        array_unshift($pathSecond, $u);

        var_dump("Path from Target:");
        printDebug($pathSecond);

        //final path
        reset($overlap);

        //find the subpath
        if($firstIsMoreRecent) {
            //need to recurse on the second half and the overlap
            if(!$previousSource[$currentFirst] && !previousTarget[$currentSecond]) {
                $subpath = dijkstra($link, current($overlap), $pathSecond[0]);
                $finalPath = array_merge($pathFirst, $subpath);
            } else {
                $finalPath = array($currentFirst, $currentSecond);
            }

        } else {
            if(!$previousSource[$currentFirst] && !previousTarget[$currentSecond]) {
                $subpath = dijkstra($link, $pathFirst[0], current($overlap));
                $finalPath = array_merge($subpath, array_reverse($pathSecond));
            } else {
                $finalPath = array($currentFirst, $currentSecond);
            }
        }

        var_dump("Final Path");
        printDebug($finalPath);

        return $finalPath;
    } else {
        printDebug("No overlap between ".$currentFirst." and ".$currentSecond);
        return FALSE;
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
    $distancesSource[$source] = 0;
    $distancesTarget[$target] = 0;

    $currentFirst = $source;
    $currentSecond = $target;

    //compute initial neighbors
    if (isset($neighborsSource[$source])) {
        foreach ($neighborsSource[$source] as $arr) {
            $alt = $distancesSource[$source] + $arr["cost"];
            if ($alt < $distancesSource[$arr["end"]]) {
                $distancesSource[$arr["end"]] = $alt;
                $previousSource[$arr["end"]] = $source;
            }
        }
    }
    if (isset($neighborsTarget[$target])) {
        foreach ($neighborsTarget[$target] as $arr) {
            $alt = $distancesTarget[$target] + $arr["cost"];
            if ($alt < $distancesTarget[$arr["end"]]) {
                $distancesTarget[$arr["end"]] = $alt;
                $previousTarget[$arr["end"]] = $target;
            }
        }
    }

    var_dump("currentFirst is:");
    printDebug($currentFirst);
    var_dump("and currentSecond is:");
    printDebug($currentSecond);

    while (1) {

        //must check for path each time an unvisited array is expanded

        $potentialPath = checkForOverlap($currentFirst, $currentSecond, $unvisitedSource, $unvisitedTarget, $previousSource, $previousTarget);
        if($potentialPath) {
            var_dump("previous source:");
            printDebug($previousSource);
            var_dump("previous target:");
            printDebug($previousTarget);
            return $potentialPath;
        }

        $currentFirst = progressToNextNode($link, $verticesSource, $unvisitedSource, $neighborsSource, $distancesSource, $previousSource);

        var_dump("currentFirst is:");
        printDebug($currentFirst);
        var_dump("and currentSecond is:");
        printDebug($currentSecond);



        $potentialPath = checkForOverlap($currentFirst, $currentSecond, $unvisitedSource, $unvisitedTarget, $previousSource, $previousTarget);
        if ($potentialPath) {
            var_dump("previous source:");
            printDebug($previousSource);    
            var_dump("previous target:");
            printDebug($previousTarget);
            return $potentialPath;
        }

        $currentSecond = progressToNextNode($link, $verticesTarget, $unvisitedTarget, $neighborsTarget, $distancesTarget, $previousTarget);
        
    }
    //pull path out of previouses
    // $path = array();
    // $u = $target;
    // while (isset($previous[$u])) {
    //     array_unshift($path, $u);
    //     $u = $previous[$u];
    // }
    // array_unshift($path, $u);
    // return $path;
}

function getNameForID($link, $person_ID) {
    $actorsQuery = $link->prepare("SELECT Person_Name FROM FM_Person WHERE Person_ID = ? ") or die(printDebug(mysqli_error($link)));
    $actorsQuery->bind_param("i", $person_ID);
    $actorsQuery->execute();
    $actorsQuery->bind_result($actor);
    $actorsQuery->fetch();

    return $actor;
}

function getMutualMovie($link, $firstPersonID, $secondPersonID) {
    $filmQuery = $link->prepare("SELECT Title FROM FM_Film WHERE IMDB_ID IN (SELECT IMDB_ID FROM FM_Acted_In WHERE Person_ID = ? AND IMDB_ID IN (SELECT IMDB_ID FROM FM_Acted_In WHERE Person_ID = ? )) LIMIT 1") or die(printDebug(mysqli_error($link)));
    $filmQuery->bind_param("ii", $firstPersonID, $secondPersonID);
    $filmQuery->execute();
    $filmQuery->bind_result($film);
    $filmQuery->fetch();

    return $film;
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
$mutualMovies = array();

for($i = 0; $i < count($path); ++$i) {

    $firstActor = current($path);
    $actorNames[] = getNameForID($link, $firstActor);
    $secondActor = next($path);
    //if next element, get mutual film
    if (!$secondActor === FALSE) {
        $mutualMovies[] = getMutualMovie($link, $firstActor, $secondActor);
    }
}

var_dump("Final Names:");
printDebug($actorNames);
var_dump("Mutual Films:");
printDebug($mutualMovies);

//GOGO AJAX!
$ajaxArray = array('firstActorName' => ucwords($_GET['firstPersonName']), 'secondActorName' => ucwords($_GET['secondPersonName']), 'path' => $actorNames);

$jsonString = json_encode($ajaxArray);

echo $jsonString;

?>

