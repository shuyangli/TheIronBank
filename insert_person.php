<?php 
//connecting
include("partials/connect.php");

//prepare statement
$stmt = $link->prepare("insert into FM_Person (Person_Name,Num_Awards) values (?,?);");

//bind parameters Person_Name, Num_Awards
$db_name = $_GET['name'];
$db_numawards = $_GET['numawards'];

$db_insertformat = "si";

$stmt->bind_param($db_insertformat, $db_name, $db_numawards);



if( !(is_int($db_numawards) ) or $db_numawards < 0 )
	{			}
elseif(strlen($db_name) > 80)
	{			}
else{	
	$stmt->execute();	
	}		//execute if it does not fail these tests, return to a success window (needs implementation)

?>
