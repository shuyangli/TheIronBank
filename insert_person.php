<?php 
//connecting
include("partials/connect.php");

//prepare statement
$stmt = $link->prepare("insert into FM_Person (Person_Name,Num_Awards) values (?,?);");

//bind parameters Person_Name, Num_Awards
$db_name = $_GET['name'];
$db_numawards = $_GET['numawards'];

$db_insertformat = "si";

$stmt->bind_param($db_insertformat, $db_name, $db_numawards);

$stmt->execute();

?>
