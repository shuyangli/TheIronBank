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

    return $actors;
}

//vertices is an array of all nodes that are either visited or unvisited
//unvisited is just unvisited
//first actor is the one all the others are adjacent to
function addToGraph(&$vertices, &$unvisited, &$neighbors, &$distances, &$previous, $firstActor, $actors) {

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

        } 
    }
}

//updates the respective vectors and returns the node next progressed according to Dijkstra
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

    //returns difference of &Q - &u
    //pulls u out of Q
    $unvisited = array_diff($unvisited, array($u));

    //add more to the arrays
    addToGraph($vertices, $unvisited, $neighbors, $distances, $previous, $u, getAdjacentActors($link, $u));

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

//returns the potential overlap between two actors' adjacent actors' vector
function checkForOverlap($link, $currentFirst, $currentSecond, $unvisitedSource, $unvisitedTarget, $previousSource, $previousTarget, $firstIsMoreRecent) {

    $overlap = array_intersect($unvisitedSource, $unvisitedTarget);

    if(count($overlap)) {
        
        //generate paths from previous vectors
        $pathFirst = array();
        $u = $currentFirst;
        while (isset($previousSource[$u])) {
            array_unshift($pathFirst, $u);
            $u = $previousSource[$u];
        }
        array_unshift($pathFirst, $u);

        $pathSecond = array();
        $u = $currentSecond;
        while (isset($previousTarget[$u])) {
            array_unshift($pathSecond, $u);
            $u = $previousTarget[$u];
        }
        array_unshift($pathSecond, $u);

        //final path
        reset($overlap);

        //find the subpath
        //first check if we need to recurse
        if(!is_null($previousSource[$currentFirst]) || !is_null($previousTarget[$currentSecond])) {
            if($firstIsMoreRecent) {
                //need to compute the subpath between the overlap and the less recent node
                $subpath = dijkstra($link, current($overlap), $pathSecond[0]);
                $finalPath = array_merge($pathFirst, $subpath);
            } else {
                //need to compute the subpath between the overlap and the less recent node
                $subpath = dijkstra($link, $pathFirst[0], current($overlap));
                $finalPath = array_merge($subpath, array_reverse($pathSecond));
            }
        } else {
            //if both intersect each other
            if(count(array_intersect(array($currentFirst), $unvisitedTarget)) && count(array_intersect(array($currentSecond), $unvisitedSource))){
                $finalPath = array($currentFirst, $currentSecond);
            } else {
                //must include the overlap if the two nodes don't intersect each other
                $finalPath = array($currentFirst, current($overlap), $currentSecond);
            }
        }
        return $finalPath;
    } else {
        return FALSE;
    }

}

//returns the path between the two nodes
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

    //initialize
    addToGraph($verticesSource, $unvisitedSource, $neighborsSource, $distancesSource, $previousSource, $source, getAdjacentActors($link, $source));
    addToGraph($verticesTarget, $unvisitedTarget, $neighborsTarget, $distancesTarget, $previousTarget, $target, getAdjacentActors($link, $target));

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

    while (1) {

        //must check for path each time an unvisited array is expanded
        $potentialPath = checkForOverlap($link, $currentFirst, $currentSecond, $unvisitedSource, $unvisitedTarget, $previousSource, $previousTarget, FALSE);
        if($potentialPath) {
            return $potentialPath;
        }

        $currentFirst = progressToNextNode($link, $verticesSource, $unvisitedSource, $neighborsSource, $distancesSource, $previousSource);

        $potentialPath = checkForOverlap($link, $currentFirst, $currentSecond, $unvisitedSource, $unvisitedTarget, $previousSource, $previousTarget, TRUE);
        if ($potentialPath) {
            return $potentialPath;
        }

        $currentSecond = progressToNextNode($link, $verticesTarget, $unvisitedTarget, $neighborsTarget, $distancesTarget, $previousTarget);
        
    }
}

//queries for the name for the ID
function getNameForID($link, $person_ID) {
    $actorsQuery = $link->prepare("SELECT Person_Name FROM FM_Person WHERE Person_ID = ? ") or die(printDebug(mysqli_error($link)));
    $actorsQuery->bind_param("i", $person_ID);
    $actorsQuery->execute();
    $actorsQuery->bind_result($actor);
    $actorsQuery->fetch();

    return $actor;
}

//queries for the mutual movie between two IDs
function getMutualMovie($link, $firstPersonID, $secondPersonID) {
    $filmQuery = $link->prepare("SELECT Title FROM FM_Film WHERE IMDB_ID IN (SELECT IMDB_ID FROM FM_Acted_In WHERE Person_ID = ? AND IMDB_ID IN (SELECT IMDB_ID FROM FM_Acted_In WHERE Person_ID = ? )) LIMIT 1") or die(printDebug(mysqli_error($link)));
    $filmQuery->bind_param("ii", $firstPersonID, $secondPersonID);
    $filmQuery->execute();
    $filmQuery->bind_result($film);
    $filmQuery->fetch();

    return $film;
}



//~~~~~~~~~~~~BEGIN~~~~~~~~~~~~~~

$success = TRUE;
$error = "";

//convert names to IDs
$firstNameQuery = $link->prepare("SELECT Person_ID FROM FM_Person WHERE UPPER(Person_Name) = UPPER(?) ") or die(printDebug(mysqli_error($link)));
$firstNameQuery->bind_param("s", $_GET['firstPersonName']);
$firstNameQuery->execute();
$firstNameQuery->bind_result($firstNameQueryResult);
$firstNameQuery->fetch();

//first ID
$firstNameID = $firstNameQueryResult;
if(!$firstNameID) {
    $error = "No First Actor Found";
    $success = false;
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
    $error = "No Second Actor Found";
    $success = false;
}
$secondNameQuery->close();

//do sanity check if they're the same
if($firstNameID === $secondNameID) {
    $actorNames = array(ucwords($_GET['firstPersonName']));
    $mutualMovies = array("N/A");
} else {
    if($success) {
        //compute path
        $path = dijkstra($link, $firstNameID, $secondNameID);
        //get names for each actor
        $actorNames = array();
        $mutualMovies = array();

        //generate name arrays
        for($i = 0; $i < count($path); ++$i) {

            $firstActor = current($path);
            $actorNames[] = getNameForID($link, $firstActor);
            $secondActor = next($path);
            //if next element, get mutual film
            if (!$secondActor === FALSE) {
                $mutualMovies[] = getMutualMovie($link, $firstActor, $secondActor);
            }
        }
    }   
}



//GOGO AJAX!
if ($success) { 
    $ajaxArray = array('success' => $success, 'firstActorName' => ucwords($_GET['firstPersonName']), 'secondActorName' => ucwords($_GET['secondPersonName']), 'actors' => $actorNames, 'movies' => $mutualMovies);
} else {
    $ajaxArray = array('success' => $success, 'error' => $error, 'firstActorName' => ucwords($_GET['firstPersonName']), 'secondActorName' => ucwords($_GET['secondPersonName']));
}
$jsonString = json_encode($ajaxArray);

echo $jsonString;

?>

