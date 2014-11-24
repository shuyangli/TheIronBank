<?php

include("../partials/connect.php");

// Read genre data
$actor_data_file = file("actor_data_sample", FILE_SKIP_EMPTY_LINES);

foreach ($actor_data_file as $idx => $val_str) {
	$pair = explode('|', $val_str);
	$actor_name = trim($pair[1]);
	$imdb_id = trim($pair[0]);

	// Check if the actor exists
	$person_id = 0;
	$person_fetch_result = $link->query("SELECT Person_ID FROM FM_Person WHERE Person_Name = $actor_name");

	if ($person_fetch_result) {
		// If the actor exists, we use the stored person id
		$arr = $person_fetch_result->fetch_assoc();
		$person_id = $arr["Person_ID"];
	} else {
		// The actor doesn't exist, we need to insert him/her first
		$insert_actor_stmt = $link->prepare("INSERT INTO FM_Person (Person_Name, Num_Awards) VALUES (?, 0)");
		$insert_actor_stmt->bind_param("s", $actor_name);
		$insert_actor_stmt->execute();

		// Then after insert, get the insert id to be used
		$person_id = $link->insert_id;
	}

	// Then insert the relationship into FM_Acted_In
	$insert_relationship_stmt = $link->prepare("INSERT INTO FM_Acted_In (Person_ID, IMDB_ID) values (?, ?)");
	if ($insert_relationship_stmt) {
		$insert_relationship_stmt->bind_param("ss", $person_id, $imdb_id);
		$insert_relationship_stmt->execute();
	} else {
		echo $link->error;
	}
}

?>
