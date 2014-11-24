<?php 
//connecting
include("partials/connect.php");

//prepare statement
$stmt = $link->prepare("insert into FM_Genre values (?,?);");

//bind parameters ID, URL, Description, Runtime, Rating, Gross, Year, Num_Awards, Title, Distributor
$name = $_GET['g_name'];
$db_id = $_GET['imdb_id'];


$db_insertformat = "ss";

$stmt->bind_param($db_insertformat, $name, $db_id);

$stmt->execute();

?>
