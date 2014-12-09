<?php include('partials/connect.php') ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Search</title>

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
    <script>
        $("#search_submit").on('click', function (e) {
            // Prevent default
            e.preventDefault();

            // AJAX call
            $.ajax({
                url: 'search_actor.php',
                type: 'GET',
                dataType: 'json',
                data: { name: $("#name_input").text() },
                success: function(result) {

                    $("#result-container").empty();

                    // Populate result container
                    resArray = JSON.parse(result);

                    for (var i = 0; i < resArray.length; i += 1) {
                        $("#result-container").append("ID: " + resArray[i][0] + ", Name: " + resArray[i][1] + "<br />");
                    }
                }
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
                        <h1>Search</h1>
                        <form method="get">
                        Name: <input type="textbox" name="name" id="name_input" /><br/>
                        <input type="submit" id="search_submit"/>
                    </div>
                </div>
                <div class="row">
                    <div id="result-container" class="col-lg-12"></div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

</body>

</html>
