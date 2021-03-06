<?php 
//connecting
include("partials/connect.php");

//prepare statement
$stmt = $link->prepare("insert into FM_Film values (?,?,?,?,?,?,?,?,?,?);");

//bind parameters ID, URL, Description, Runtime, Rating, Gross, Year, Num_Awards, Title, Distributor
$db_id = $_GET['id'];
$db_url = $_GET['url'];
$db_description = $_GET['description'];
$db_runtime = $_GET['runtime'];
$db_rating = $_GET['rating'];
$db_gross = $_GET['gross'];
$db_year = $_GET['year'];
$db_numawards = $_GET['numawards'];
$db_title = $_GET['title'];
$db_distributor = $_GET['distributor'];

$db_insertformat = "sssisiiiss";

$stmt->bind_param($db_insertformat, $db_id, $db_url, $db_description, $db_runtime, $db_rating, $db_gross, $db_year, $db_numawards, $db_title, $db_distributor);


if( strlen($db_id) > 80)
	{		die("IMDB ID cannot be longer than 80 characters.");		}	
elseif( $db_runtime  < 0 )	
	{		die("Invalid value for Runtime.\n Value must be an integer greater than 0.\n Could not complete request.");	}
elseif($db_rating != "G" and $db_rating != "PG-13" and $db_rating != "PG" and $db_rating != "X" and $db_rating != "R" )
	{		die("Invalid entry for Rating:\n Rating must be G, PG, PG-13, R, or X" );	}
elseif( $db_gross < 0 )
	{		die("Invalid value for Gross.\n Value must be an integer greater than 0.\n Could not complete request.");	}
elseif( $db_year < 0 )
	{		die("Invalid value for IMDB ID.\n Value must be a valid year.");	}
elseif(  $db_numawards < 0 )
	{		die("Invalid value for Number of Awards.\n Value must be an integer greater than 0.\n Could not complete request.");	}
elseif(strlen($db_title) > 80)
	{		die("Title cannot be longer than 80 characters.");	}
elseif( strlen($db_distributor) > 80)
	{			die("Distributor cannot be longer than 80 characters.");	}
else{	
	$stmt->execute();	
	}		//execute if it does not fail these tests, return to a success window (needs implementation)

?>