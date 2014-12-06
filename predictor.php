<?php include('partials/connect.php') ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Film Success Predictor</title>

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
                        <h1>Film Success Predictor</h1>
                        <form action="prediction_calculator.php" method="get">
                        Comma Separated Actor List: <input type="textbox" name="actorList"/><br/>
                        Director: <input type="textbox" name="director"/><br/>
                        Writer: <input type="textbox" name="writer"/><br/>
                        Distributor: <input type="textbox" name="director"/><br/>
                        Rating: <input type="textbox" name="rating"/><br/>
                        Genre: <input type="textbox" name="genre"/><br/>
                        Release Year: <input type="textbox" name="year"/><br/>
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