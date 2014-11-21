<?php
include("partials/connect.php");

$query = $link->prepare("UPDATE FM_Film SET Description = ?, Distributor = ?, Gross = ?, MPAA_Rating = ?, Num_Awards = ?, Release_Year = ?, Runtime_Min = ?, Title = ? WHERE IMDB_ID = ? ");

$query->bind_param("ssisiiiss", $_POST['Description'], $_POST['Distributor'], $_POST['Gross'], $_POST['MPAA_Rating'], $_POST['Num_Awards'], $_POST['Release_Year'], $_POST['Runtime_Min'], $_POST['Title'], $_POST['IMDB_ID']);

// $query = $link->prepare("UPDATE FM_Film SET MPAA_Rating = ? WHERE IMDB_ID = ? ");

// $query->bind_param("si", $_POST['MPAA_Rating'], $_POST['IMDB_ID']);

$query->execute();


?>