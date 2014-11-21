<?php include('partials/connect.php') ?>

<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Persons</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/simple-sidebar.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    
    <!-- Sidebar JS-->
    <script src="js/sidebar.js"></script>

    <script src="js/persons.js"></script>



</head>

<body>

	<h1>Persons</h1>

	<?php

	$test_sql = "SELECT * FROM FM_Person;";

	$result = $link->query($test_sql) or die($link->error.__LINE__);

	echo '<table class="table table-striped">';
	echo '<thead><tr><th>Person_ID</th><th>Name</th><th>Award Score</th></tr></thead>';

	while($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
	        echo '<tr>';
	        foreach($tuple as $colval) {
	                echo '<td>'.$colval.'</td>';
	        }
        //buttons
        echo '<td><button class="button edit-button" data-person_id="'.$tuple['Person_ID'].'">Edit</button>';
        echo '<button style="display: none" class="button save-button" data-person_id="'.$tuple['Person_ID'].'">Save</button>';
        echo '<button style="display: none" class="button cancel-button" data-person_id="'.$tuple['Person_ID'].'">Cancel</button>';
        echo '<button style="display: none" class="button delete-button" data-person_id="'.$tuple['Person_ID'].'">Delete</button></td>';
        echo '</tr>';
	}

	echo '</table>';

	?>

</body>

</html>
