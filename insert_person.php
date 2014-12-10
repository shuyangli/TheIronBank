<?php 
//connecting
include("partials/connect.php");

//prepare statement
$stmt = $link->prepare("insert into FM_Person (Person_Name,Num_Awards) values (?,?);");

//bind parameters Person_Name, Num_Awards
$db_name = $_GET['name'];
$db_numawards = $_GET['numawards'];

$db_insertformat = "si";




if( $db_numawards < 0 )
	{		die("Invalid value for number of awards.\n Value must be an integer greater than 0.\n Could not complete request.");	}
elseif(strlen($db_name) > 80)
	{		die("Name cannot be longer than 80 characters.");	}
else{	
	$stmt->bind_param($db_insertformat, $db_name, $db_numawards);
	$stmt->execute();	
	}		//execute if it does not fail these tests, return to a success window (needs implementation)

?>
