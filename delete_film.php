<?php
include("partials/connect.php");

$query = $link->prepare("DELETE FROM FM_Film WHERE IMDB_ID = ? ");

$query->bind_param("s", $_POST['IMDB_ID']);

$query->execute();


?>