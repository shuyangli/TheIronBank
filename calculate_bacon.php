<?php
include('partials/connect.php');

//returns an array of all actors one degree away from the input
function getAdjacentActors($link, $person_ID) {

    $actorsQuery = $link->prepare("SELECT Person_ID FROM FM_Acted_In WHERE IMDB_ID IN (SELECT IMDB_ID FROM FM_Acted_In WHERE Person_ID = ? )") or die(var_dump(mysqli_error($link)));
    $actorsQuery->bind_param("i", $person_ID);
    $actorsQuery->execute();
    $actorsQuery->bind_result($actor);

    $actors = array();
    while ($actorsQuery->fetch()) {
        array_push($actors, $actor);
    }

    //have to delete the original actor from the adjacent list
    $uniqueActors = array_unique($actors);
    $keys = array_keys($uniqueActors, $person_ID);
    foreach($keys as $k) {
        unset($uniqueActors[$k]);
    }

    // echo "Adjacent Actors";
    // var_dump($uniqueActors);

    return $actors;
}

//vertices is an array of all nodes that are either visited or unvisited
//unvisited is just unvisited
//first actor is the one all the others are adjacent to
function addToGraph(&$vertices, &$unvisited, &$neighbors, &$distance, &$previous, $firstActor, $actors) {

    echo "First Actor in addToGraph:\n"
    var_dump($firstActor);
    echo "\n";
    //add first actor
    if(!in_array($firstActor, $vertices, true)) {
        array_push($vertices, $firstActor);
        array_push($unvisited, $firstActor);
        $dist[$firstActor] = INF;
        $previous[$firstActor] = NULL;

    }

    foreach ($actors as $actor) {
        if(!in_array($actor, $vertices, true)) {
            array_push($vertices, $actor);
            array_push($unvisited, $actor);

            //add neighbors with edge cost 1
            $neighbors[$firstActor][] = array("end" => $actor, "cost" => 1);
            $neighbors[$actor][] = array("end" => $firstActor, "cost" => 1);

            $dist[$actor] = INF;
            $previous[$actor] = NULL;

        }
    }
}

//the plan:
//get adjacent actors of the first actor
//add them to the vertices, neighbors, and unvisited arrays
//compute closest (random if tie)
//get next closest's actors
//update distances
//etc i hope

function dijkstra($link, $source, $target) {

    //initialize vertices and neighbors
    $vertices = array();
    $unvisited = array();
    $neighbors = array();
    $dist = array();
    $previous = array();

    addToGraph($vertices, $unvisited, $neighbors, $source, $dist, $previous, getAdjacentActors($link, $source));

    echo "Unvisited:\n";
    var_dump($unvisited);
    echo "\n";

    //first node has distance 0
    $dist[$source] = 0;

    // foreach ($graph_array as $edge) {
    //     // add each vertex to array
    //     array_push($vertices, $edge[0], $edge[1]);
    //     //update neighbors
    //     $neighbors[$edge[0]][] = array("end" => $edge[1], "cost" => $edge[2]);
    //     $neighbors[$edge[1]][] = array("end" => $edge[0], "cost" => $edge[2]);
    // }
    // //remove duplicates
    // $vertices = array_unique($vertices);
 
    //initialize distance and previous
    // foreach ($vertices as $vertex) {
    //     $dist[$vertex] = INF;
    //     $previous[$vertex] = NULL;
    // }
 
    //mutable set of vertices
    // $unvisited = $vertices; 


    while (count($unvisited) > 0) {
 
        // TODO - Find faster way to get minimum
        //the source is the first node to be set as visited, which updates the distances
        $min = INF;
        foreach ($unvisited as $vertex){
            if ($dist[$vertex] < $min) {
                echo "New min is ".$dist[$vertex]." from ".$vertex."\n";
                $min = $dist[$vertex];
                $u = $vertex; //save closest node to u
            }
        }

        //returns difference of &Q - &u
        //pulls u out of Q
        $unvisited = array_diff($unvisited, array($u));
        if ($dist[$u] == INF or $u == $target) {
            break;
        }
 
        //recompute distances from the new latest node
        if (isset($neighbors[$u])) {
            foreach ($neighbors[$u] as $arr) {
                $alt = $dist[$u] + $arr["cost"];
                if ($alt < $dist[$arr["end"]]) {
                    $dist[$arr["end"]] = $alt;
                    $previous[$arr["end"]] = $u;
                }
            }
        }

        //check if we've reached our destination
        if($u == $target) {
            break;
        }

        //add more to the arrays
        addToGraph($vertices, $unvisited, $neighbors, $u, $dist, $previous, getAdjacentActors($link, $source));
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

//convert names to IDs

$firstNameQuery = $link->prepare("SELECT Person_ID FROM FM_Person WHERE Person_Name = ? ") or die(var_dump(mysqli_error($link)));
$firstNameQuery->bind_param("s", $_GET['firstPersonName']);
$firstNameQuery->execute();
$firstNameQuery->bind_result($firstNameQueryResult);
$firstNameQuery->fetch();

//first ID
$firstNameID = $firstNameQueryResult;
$firstNameQuery->close();

// include('partials/connect.php');
$secondNameQuery = $link->prepare("SELECT Person_ID FROM FM_Person WHERE Person_Name = ? ") or die(var_dump(mysqli_error($link)));
$secondNameQuery->bind_param("s", $_GET['secondPersonName']);
$secondNameQuery->execute();
$secondNameQuery->bind_result($secondNameQueryResult);
$secondNameQuery->fetch();

//second ID
$secondNameID = $secondNameQueryResult;
$secondNameQuery->close();

dijkstra($link, $firstNameID, $secondNameID);
    
//get persons acted in films

//Code inspired from http://rosettacode.org/wiki/Dijkstra's_algorithm#PHP



 
// $graph_array = array(
//                     array("a", "b", 7),
//                     array("a", "c", 9),
//                     array("a", "f", 14),
//                     array("b", "c", 10),
//                     array("b", "d", 15),
//                     array("c", "d", 11),
//                     array("c", "f", 2),
//                     array("d", "e", 6),
//                     array("e", "f", 9)
//                );
 
// $path = dijkstra($graph_array, "a", "e");
 
// echo "path is: ".implode(", ", $path)."\n";

?>