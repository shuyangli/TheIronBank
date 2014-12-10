<?php
include("partials/connect.php");

// Queries
$stmt_film_query = $link->prepare("SELECT Title, Person_Name FROM (
	SELECT Title, Person_ID
	FROM (
		SELECT IMDB_ID, Title FROM FM_Film WHERE Title LIKE ?
	) correct_films
		INNER JOIN FM_Acted_In ON correct_films.IMDB_ID = FM_Acted_In.IMDB_ID
	) films_with_persons NATURAL JOIN FM_Person;");

$get_name_lower = strtolower( $_GET['movie_name'] );
$db_name_param = "%{$get_name_lower}%" ;

$stmt_film_query->bind_param("s", $db_name_param);
$stmt_film_query->execute();

$data_film_title = "";
$data_actor_name = "";
$data_arr = [];

$stmt_film_query->bind_result($data_film_title, $data_actor_name);
$stmt_film_query->store_result();
while ($stmt_film_query->fetch()) {
    // More actors
    array_push($data_arr, [$data_film_title, $data_actor_name]);
}

// Clean up
$stmt_film_query->close();

// Return data
echo json_encode($data_arr);

?>
