<?php

include("../partials/connect.php");

// Read genre data
$genre_data_file = file("genre_data", FILE_SKIP_EMPTY_LINES);

foreach ($genre_data_file as $idx => $val_str) {

	// Split: 0 => IMDB_ID; 1 => Genre
	$pair = explode('|', $val_str);
	$genre_name = trim($pair[1]);
	$imdb_id = trim($pair[0]);

	$stmt = $link -> prepare("INSERT INTO FM_Genre (Genre_Name, IMDB_ID) values (?, ?);");
	$stmt -> bind_param("ss", $genre_name, $imdb_id);
	$stmt -> execute();
}

?>
