<?php
include('partials/connect.php');

//get films acted by person

// $query = $link->prepare("SELECT IMDB_ID FROM FM_Acted_In WHERE Person_ID = ( SELECT Person_ID FROM FM_Person WHERE Person_Name = ? ) ") or die(var_dump(mysqli_error($link)));

$query = $link->prepare('SELECT Person_ID FROM FM_Person WHERE Person_Name = ? ');

$query->bind_param("s", $_POST['firstPersonName']);

$query->execute();
$queryResult = array();
$query->bind_result($queryResult);
$query->fetch();

var_dump($queryResult);
    
//get persons acted in films

//Code inspired from http://rosettacode.org/wiki/Dijkstra's_algorithm#PHP

function dijkstra($graph_array, $source, $target) {

    //initialize vertices and neighbors
    $vertices = array();
    $neighbours = array();
    foreach ($graph_array as $edge) {
        // add each vertex to array
        array_push($vertices, $edge[0], $edge[1]);
        //update neighbors
        $neighbours[$edge[0]][] = array("end" => $edge[1], "cost" => $edge[2]);
        $neighbours[$edge[1]][] = array("end" => $edge[0], "cost" => $edge[2]);
    }
    //remove duplicates
    $vertices = array_unique($vertices);
 
    //initialize distance and previous
    foreach ($vertices as $vertex) {
        $dist[$vertex] = INF;
        $previous[$vertex] = NULL;
    }
 
    //first node has distance 0
    $dist[$source] = 0;
    //mutable set of vertices
    $unvisited = $vertices; 
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
        if (isset($neighbours[$u])) {
            foreach ($neighbours[$u] as $arr) {
                $alt = $dist[$u] + $arr["cost"];
                if ($alt < $dist[$arr["end"]]) {
                    $dist[$arr["end"]] = $alt;
                    $previous[$arr["end"]] = $u;
                }
            }
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