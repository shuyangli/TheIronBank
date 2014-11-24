<?php

include("../partials/connect.php");

// Read genre data
$actor_data_file = file("actor_data_sample", FILE_SKIP_EMPTY_LINES);

$person_fetch_stmt = $link->prepare("SELECT Person_ID FROM FM_Person WHERE Person_Name = ?");
$insert_actor_stmt = $link->prepare("INSERT INTO FM_Person (Person_Name, Num_Awards) VALUES (?, 0)");

foreach ($actor_data_file as $idx => $val_str) {
	$pair = explode('|', $val_str);
	$actor_name = trim($pair[1]);
	$imdb_id = trim($pair[0]);

	// Check if the actor exists
	echo "Checking $actor_name\n";
	$person_fetch_stmt->bind_param("s", $actor_name);
	$person_fetch_stmt->execute();

	$person_id = 0;
	$person_fetch_stmt->bind_result($person_id);
	$person_fetch_stmt->fetch();

	// The actor doesn't exist, we need to insert him/her first
	if ($person_id == 0) {
		echo "$actor_name doesn't exist\n";

		if ($insert_actor_stmt) {
			echo "Inserting\n";
			$insert_actor_stmt->bind_param("s", $actor_name);
			$insert_actor_stmt->execute();
		}

		// Then after insert, get the insert id to be used
		$person_id = $link->insert_id;
	}

	// Then insert the relationship into FM_Acted_In
	$link->query("INSERT INTO FM_Acted_In (Person_ID, IMDB_ID) values ($person_id, $imdb_id)");
	echo $link->error;
}

?>
