<?php

include('partials/connect.php');

//parse input data
$db_directors = $_GET['director'];
$db_writer = $_GET['writer'];
$db_distributor = $_GET['distributor'];
$db_rating = $_GET['rating'];
$db_genre = $_GET['genre'];
$db_releaseYear = $_GET['year'];
$db_relevantDecade = $db_releaseYear-10;
$db_actorsArray = explode(',',$_GET['actorList']);

print "Actors: <br>";
for ($i = 0; $i < count($db_actorsArray); ++$i){
    $db_actorsArray[$i] = trim($db_actorsArray[$i]);
    print $db_actorsArray[$i] . "<br>";
}

print "<br>Director is " . $db_directors . "<br>";
print "Writer is " . $db_writer . "<br>";
print "Distributor is " . $db_distributor . "<br>";
print "Rating is " . $db_rating . "<br>";
print "Genre is " . $db_genre . "<br>";
print "Release Year is " . $db_releaseYear . "<br>";

//Perform Linear Regression to make predictions
$estimates = array(); //Will hold estimates for gross based upon each input

//Distributor
$query = "select Gross from FM_Film where Distributor=? and Gross!='null' and Release_Year>=? order by Release_Year desc limit 25;";

if ($stmt = $link->prepare($query)){
    $grosses = array();
    $stmt->bind_param("si", $db_distributor, $db_relevantDecade);
    $stmt->execute();
    $stmt->store_result();
    $rowCount = $stmt->num_rows;
    $result = $stmt->bind_result($grosses);
    //$stmt->fetch();
    
    print $grosses;

    $sum = 0;
    $count = 0;

    //for ($i = 0; $i < count($grosses); ++$i){
    while($stmt->fetch()){ 
        print $gross . "<br>";
        if ($gross>0){
            $count = $count +1;
            $sum = $sum + $gross;
        }
    }

    //while ($tuple = $result->fetch_assoc()){
    //    print "<br>";
    //    foreach ($tuple as $key => $value) {
    //        if ($key=="Gross" && $value>0){
    //            $count = $count +1;
    //            $sum = $sum + $value;
    //        }
    //        print $key . "\t" . $value . "<br>";
    //    }
    //}
    if ($count>0){
        array_push($estimates, $sum/$count);
    }
    $stmt->free_result();
    $stmt->close();
    $link->close();
}



//Writers

//Rating

//Genre

//Actors

print_r($estimates);

?>