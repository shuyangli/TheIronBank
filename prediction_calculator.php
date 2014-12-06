<?php

include('partials/connect.php');

//get films acted by person
$db_director = $_GET['director'];
$db_writer = $_GET['writer'];
$db_distributor = $_GET['distributor'];
$db_rating = $_GET['rating'];
$db_genre = $_GET['genre'];
$db_releaseYear = $_GET['year'];
$db_actorsArray = explode(',',$_GET['actorList']);

print "Actors: <br>";
foreach($i = 0; $i < count($db_actorsArray); ++$i){
    $db_actorsArray[$i] = trim($db_actorsArray[$i]);
    print $db_actorsArray[$i] . "<br>";
}

print "Director is " . $db_director . "<br>";
print "Writer is " . $db_writer . "<br>";
print "Distributor is " . $db_distributor . "<br>";
print "Rating is " . $db_rating . "<br>";
print "Genre is " . $db_genre . "<br>";
print "Release Year is " . $db_releaseYear . "<br>";

?>