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


</head>

<body>

	<?php

	$test_sql = "SELECT * FROM FM_Film;";

	$result = $link->query($test_sql) or die($link->error.__LINE__);

	echo '<table class="table table-striped">';
	echo '<thead><tr><th>IMDB_ID</th><th>Poster URL</th><th>Description</th><th>Runtime (in min)</th><th>MPAA Rating</th><th>Gross Revenue</th><th>Release Year</th><th>Award Score</th><th>Title</th><th>Distributor</th></tr></thead>';

	while($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
	        echo '<tr>';
	        foreach($tuple as $colval) {
	                echo '<td>'.$colval.'</td>';
	        }
	        echo '</tr>';
	}
	echo '</table>';

	?>

</body>

</html>
