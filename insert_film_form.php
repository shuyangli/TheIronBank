<?php include('partials/connect.php') ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Add A Film</title>

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

    <div id="wrapper">

        <!-- Sidebar -->
        <?php include('partials/sidebar.php') ?>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>Add A Film</h1>
                        <form action="calculate_bacon.php" method="get">
                        IMDB_id: <input type="textbox" name="id"/><br/>
						URL: <input type="textbox" name="url"/><br/>
						Description: <input type="textbox" name="description"/><br/>
						Runtime: <input type="textbox" name="runtime"/><br/>
						Rating: <input type="textbox" name="rating"/><br/>
						Gross: <input type="textbox" name="gross"/><br/>
						Year: <input type="textbox" name="year"/><br/>
						Number of Awards: <input type="textbox" name="numawards"/><br/>
						Title: <input type="textbox" name="title"/><br/>
						Distributor: <input type="textbox" name="distributor"/><br/>
                        <input type="submit"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

</body>

</html>

