<?php include('partials/connect.php') ?>

<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Films</title>

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

    <!-- Films JS-->
    <script src="js/films.js"></script>


</head>

<body>

	<h1>Films</h1>

	<?php

	$test_sql = "SELECT * FROM FM_Film;";

	$result = $link->query($test_sql) or die($link->error.__LINE__);

	echo '<table class="table table-striped">';
	echo '<thead><tr><th>IMDB_ID</th><th>Poster URL</th><th>Description</th><th>Runtime (in min)</th><th>MPAA Rating</th><th>Gross Revenue (in $)</th><th>Release Year</th><th>Award Score</th><th>Title</th><th>Distributor</th></tr></thead>';

	while($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
	        echo '<tr>';
	        foreach($tuple as $column => $value) {
	        		if ($column == "Poster_URL") {
	        			echo '<td><a src="'.$value.'"></td>';

	        		} else {
	        			echo '<td data-imdb_id="'.$tuple['IMDB_ID'].'">'.$value.'</td>';
	        		}

	        }
	        //buttons
	       	echo '<td><button class="button edit-button" data-imdb_id="'.$tuple['IMDB_ID'].'">Edit</button>';
	       	echo '<button style="display: none" class="button save-button" data-imdb_id="'.$tuple['IMDB_ID'].'">Save</button>';
	       	echo '<button style="display: none" class="button cancel-button" data-imdb_id="'.$tuple['IMDB_ID'].'">Cancel</button>';
	        echo '<button style="display: none" class="button delete-button" data-imdb_id="'.$tuple['IMDB_ID'].'">Delete</button></td>';
	        echo '</tr>';
	}
	echo '</table>';

	?>

</body>

</html>
