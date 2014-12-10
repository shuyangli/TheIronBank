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

    <!-- AJAX request to search for information -->
    <script type="text/javascript">
    $(document).ready(function() {

        // Search actors
        $("#prediction_form").on('submit', function (e) {

            // Prevent form from actually submitting
            e.preventDefault();

            // AJAX call to get films
            $.ajax({
                url: 'prediction_calculator.php',
                type: 'GET',
                data: $("#prediction_form").serialize(),
                success: function(result) {

                    // Populate result container
                    resArray = JSON.parse(result);
                    $("#res-container").append("<tr><td>Your Movie Estimate</td><td>" + resArray[0] + "</td></tr>");
                    for (int i = 1; i < resArray.length; i++){
                        $("#res-container").append("<tr><td>" + resArray[i][0] + "</td><td>" + resArray[i][1] + "</td></tr>");
                    }
                    //$("#res-container").text(JSON.stringify(resArray));
                }
            });
        });
    });
    </script>

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
                        <form action="prediction_calculator.php" id="prediction_form" method="get">
                        Comma Separated Actor List: <input type="textbox" name="actorList"/><br/>
                        Director: <input type="textbox" name="director"/><br/>
                        Writer: <input type="textbox" name="writer"/><br/>
                        Distributor: <input type="textbox" name="distributor"/><br/>
                        Rating: <input type="textbox" name="rating"/><br/>
                        Genre: <input type="textbox" name="genre"/><br/>
                        Release Year: <input type="textbox" name="year"/><br/>
                        <input type="submit"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead><tr><th>Movie Title</th><th>Domestic Gross</th></tr></thead>
                            <tbody id="res-container">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

</body>

</html>