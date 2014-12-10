<?php
include("partials/connect.php");

// Queries
$stmt_film_query = $link->prepare("SELECT IMDB_ID, Title, Release_Year FROM FM_Film WHERE Title like ? ORDER BY Release_Year");

$get_name_lower = strtolower( $_GET['name'] );
$db_name_param = "%{$get_name_lower}%" ;

$stmt_film_query->bind_param("s", $db_name_param);
$stmt_film_query->execute();

$data_film_id = 0;
$data_film_title = "";
$data_film_release = 0;
$data_arr = [];

$stmt_film_query->bind_result($data_film_id, $data_film_title, $data_film_release);
$stmt_film_query->store_result();
while ($stmt_film_query->fetch()) {
    // More actors
    array_push($data_arr, [$data_film_id, $data_film_title, $data_film_release]);
}

// Clean up
$stmt_film_query->close();

// Return data
echo json_encode($data_arr);

?>
