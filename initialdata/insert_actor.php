<?php

include("../partials/connect.php");

// Read genre data
$genre_data_file = file("actor_data", FILE_SKIP_EMPTY_LINES);

foreach ($genre_data_file as $idx => $val_str) {
	$pair = explode('|', $val_str);
	$actor_name = trim($pair[1]);
	$imdb_id = trim($pair[0]);

	// Check if the actor exists
	$person_id = 0;
	$select_stmt = $link->prepare("SELECT Person_ID FROM FM_Person WHERE Person_Name = '?'");
	$select_stmt->bind_param("s", $actor_name);
	$select_stmt->bind_result($person_id);

	// If the actor exists, we use the stored person id
	if ($select_stmt->fetch()) {
		echo ("Actor: $actor_name ($person_id");
	} else {
		// The actor doesn't exist, we need to insert him/her first
		$insert_actor_stmt = $link->prepare("INSERT INTO FM_Person (Person_Name, Num_Awards) VALUES (?, 0);");
		$insert_actor_stmt->bind_param("s", $actor_name);
		$insert_actor_stmt->execute();

		// Then after insert, get the insert id to be used
		$person_id = $link->insert_id;
	}

	// Then insert the relationship into FM_Acted_In
	$stmt = $link -> prepare("INSERT INTO FM_Acted_In (Person_ID, IMDB_ID) values (?, ?);");
	$stmt -> bind_param("ss", $person_id, $imdb_id);
	$stmt -> execute();
}

?>
