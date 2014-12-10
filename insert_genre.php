<?php 
//connecting
include("partials/connect.php");

//prepare statement
$stmt = $link->prepare("insert into FM_Genre values (?,?);");

//bind parameters ID, URL, Description, Runtime, Rating, Gross, Year, Num_Awards, Title, Distributor
$name = $_GET['g_name'];
$db_id = $_GET['imdb_id'];


$db_insertformat = "ss";

$stmt->bind_param($db_insertformat, $name, $db_id);


if( !(is_int($db_id) ) or $db_id < 0 )
	{		die("Invalid value for IMDB ID.\n Value must be an integer greater than 0.\n Could not complete request.");		}	
			//if ID is an INT >=0, then we are ok, else do nothing, but should just load an error page
elseif(strlen($name) > 80)
	{			die("Name cannot be longer than 80 characters.");}
else{	
	$stmt->execute();	
	}		//execute if it does not fail these tests, return to a success window (needs implementation)

?>
