<?php
include("partials/connect.php");

$query = $link->prepare("UPDATE FM_Person SET Person_Name = ?, Num_Awards = ? WHERE Person_ID = ? ");

$query->bind_param("isi", $_POST['Person_Name'], $_POST['Num_Awards'] $_POST['Person_ID']);

// $query = $link->prepare("UPDATE FM_Film SET MPAA_Rating = ? WHERE IMDB_ID = ? ");

// $query->bind_param("si", $_POST['MPAA_Rating'], $_POST['IMDB_ID']);

$query->execute();


?>