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

        //Hide return row
        $("#return-row").hide();
        $("#title-row").hide();

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
                    $("#res-container").empty();
                    $("#title-row").show();
                    $("#return-row").show();

                    // Populate result container
                    resArray = JSON.parse(result);
                    $("#title-container").text("Domestic Gross Estimate is $" + resArray[0]);
                    for (var i = 1; i < resArray.length; i += 1){
                        $("#res-container").append("<tr><td>" + resArray[i][0] + "</td><td>$" + resArray[i][1] + "</td></tr>");
                    }
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
                        Comma Separated Actors <input type="textbox" name="actorList"/><br/>
                        Comma Separated Directors: <input type="textbox" name="director"/><br/>
                        Comma Separated Writers: <input type="textbox" name="writer"/><br/>
                        Distributor: <input type="textbox" name="distributor"/><br/>
                        Rating: <input type="textbox" name="rating"/><br/>
                        Genre: <input type="textbox" name="genre"/><br/>
                        Release Year: <input type="textbox" name="year"/><br/>
                        <input type="submit"/>
                    </div>
                </div>
                <div class="row" id="title-row">
                    <div class="col-lg-12" id="title-container" style="font-size:1.5ems;">
                    </div>
                </div>
                <div class="row" id="return-row">
                    <div class="col-lg-12" style="font-size:1.5ems;">
                        Look Below for Recent Movies had Similar Domestic Gross
                    </div>
                    <div class="col-lg-12">
                        <table class="table table-striped">
                            <thead><tr><th>Movie Title</th><th>Domestic Gross</th></tr></thead>
                            <tbody id="res-container">
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--<div class="row">
                    <div class="col-lg-12" id="res-container">
                    </div>
                </div>-->
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

</body>

</html>