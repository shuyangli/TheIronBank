<?php
include("partials/connect.php");

$query = $link->prepare("UPDATE FM_Genre SET Genre_name = ?");

$query->bind_param("ss", $_POST['Genre_name']);

// $query = $link->prepare("UPDATE FM_Film SET MPAA_Rating = ? WHERE IMDB_ID = ? ");

// $query->bind_param("si", $_POST['MPAA_Rating'], $_POST['IMDB_ID']);

$query->execute();


?>