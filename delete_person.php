<?php
include("partials/connect.php");

$query = $link->prepare("DELETE FROM FM_Person WHERE Person_ID = ? ");

$query->bind_param("s", $_POST['Person_ID']);

$query->execute();


?>