<?php 
//connecting
include("partials/connect.php");

//prepare statement
$stmt = $link->prepare("insert into FM_Film values (?,?,?,?,?,?,?,?,?,?);");

//bind parameters ID, URL, Description, Runtime, Rating, Gross, Year, Num_Awards, Title, Distributor
$db_id = $_GET['id'];
$db_url = $_GET['url'];
$db_description = $_GET['description'];
$db_runtime = $_GET['runtime'];
$db_rating = $_GET['rating'];
$db_gross = $_GET['gross'];
$db_year = $_GET['year'];
$db_numawards = $_GET['numawards'];
$db_title = $_GET['title'];
$db_distributor = $_GET['distributor'];

$db_insertformat = "issisiiiss";

$stmt->bind_param($db_insertformat, $db_id, $db_url, $db_description, $db_runtime, $db_rating, $db_gross, $db_year, $db_numawards, $db_title, $db_distributor);

$stmt->execute();

?>
