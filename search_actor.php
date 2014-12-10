<?php
include("partials/connect.php");

// Queries
$stmt_actor_query = $link->prepare("SELECT Person_ID, Person_Name FROM FM_Person WHERE Person_Name like ? ORDER BY Person_ID");

$get_name_lower = strtolower( $_GET['name'] );
$db_name_param = "%{$get_name_lower}%" ;

$stmt_actor_query->bind_param("s", $db_name_param);
$stmt_actor_query->execute();

$data_actor_id = 0;
$data_actor_name = "";
$data_arr = [];

$stmt_actor_query->bind_result($data_actor_id, $data_actor_name);
$stmt_actor_query->store_result();
while ($stmt_actor_query->fetch()) {
    // More actors
    array_push($data_arr, [$data_actor_id, $data_actor_name]);
}

// Clean up
$stmt_actor_query->close();

// Return data
echo json_encode($data_arr);

?>
