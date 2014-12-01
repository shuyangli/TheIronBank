<?php
include("partials/connect.php");

$query = $link->prepare("DELETE FROM FM_Genre WHERE Genre_name = ? ");

$query->bind_param("s", $_POST['Genre_name']);

$query->execute();


?>