<?php

include('partials/connect.php');

//parse input data
$db_director = $_GET['director'];
$db_writer = $_GET['writer'];
$db_distributor = $_GET['distributor'];
$db_rating = $_GET['rating'];
$db_genre = $_GET['genre'];
$db_releaseYear = $_GET['year'];
$db_actorsArray = explode(',',$_GET['actorList']);

print "Actors: <br>";
for ($i = 0; $i < count($db_actorsArray); ++$i){
    $db_actorsArray[$i] = trim($db_actorsArray[$i]);
    print $db_actorsArray[$i] . "<br>";
}

print "<br>Director is " . $db_director . "<br>";
print "Writer is " . $db_writer . "<br>";
print "Distributor is " . $db_distributor . "<br>";
print "Rating is " . $db_rating . "<br>";
print "Genre is " . $db_genre . "<br>";
print "Release Year is " . $db_releaseYear . "<br>";

//Perform Linear Regression to make predictions
$estimates = array(); //Will hold estimates for gross based upon each input

//Distributor
$stmt = "select * from FM_Film where Distributor='" . $db_distributor . "' and Gross!='null' limit 10;";
$result = $link->query($stmt) or die($link->error.__Line__);

while ($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)){
    print "<br>";
    foreach ($tuple as $key => $value) {
        print $key . "\t" . $value . "<br>";
    }
}

?>