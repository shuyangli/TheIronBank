<?php

include("../partials/connect.php");

// Read genre data
$actor_data_file = file("writer_data", FILE_SKIP_EMPTY_LINES);

foreach ($actor_data_file as $idx => $val_str) {
	$pair = explode('|', $val_str);
	$writer_name = trim($pair[1]);
	$imdb_id = trim($pair[0]);

	// Check if the actor exists
	echo "Checking $writer_name\n";
	if ($person_fetch_stmt = $link->prepare("SELECT Person_ID FROM FM_Person WHERE Person_Name = ?")) {

		$person_fetch_stmt->bind_param("s", $writer_name);
		$person_fetch_stmt->execute();

		$person_id = 0;
		$person_fetch_stmt->bind_result($person_id);
		$person_fetch_stmt->fetch();
	} else {
		echo $link->error;
		exit();
	}

	// The actor doesn't exist, we need to insert him/her first
	if ($person_id == 0) {
		echo "$writer_name doesn't exist\n";

		if ($insert_actor_stmt = $link->prepare("INSERT INTO FM_Person (Person_Name, Num_Awards) VALUES (?, 0)")) {
			echo "Inserting\n";
			$insert_actor_stmt->bind_param("s", $writer_name);
			$insert_actor_stmt->execute();
			$insert_actor_stmt->close();
		} else {
			echo $link->error;
			exit();
		}

		// Then after insert, get the insert id to be used
		$person_id = $link->insert_id;
	}

	// Clean up
	$person_fetch_stmt->close();

	// Then insert the relationship into FM_Wrote
	$insert_result = $link->query("INSERT INTO FM_Wrote (`Person_ID`, `IMDB_ID`) values ('$person_id', '$imdb_id')");
}

?>
