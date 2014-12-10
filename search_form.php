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
    <script type="text/javascript">
    $(document).ready(function() {

        // Hide everything initially
        {
            $("#actor-row").hide();
            $("#film-row").hide();
        }

        // Search actors
        $("#search_form").on('submit', function (e) {

            // Prevent form from actually submitting
            e.preventDefault();

            if (!$("#name_input").val()) {
                // If input is empty
                return false;
            }

            // AJAX call to get actors
            $.ajax({
                url: 'search_actor.php',
                type: 'GET',
                data: $("#search_form").serialize(),
                success: function(result) {
                    $("#actor-result-container").empty();

                    // Populate result container
                    resArray = JSON.parse(result);
                    if (resArray.length === 0) {
                        $("#actor-row").hide();
                    } else {
                        $("#actor-row").show();

                        for (var i = 0; i < resArray.length; i += 1) {
                            $("#actor-result-container").append("<tr><td>" + resArray[i][0] + "</td><td>" + resArray[i][1] + "</td></tr>");
                        }
                    }
                }
            });

            // AJAX call to get films
            $.ajax({
                url: 'search_film.php',
                type: 'GET',
                data: $("#search_form").serialize(),
                success: function(result) {
                    $("#film-result-container").empty();

                    // Populate result container
                    resArray = JSON.parse(result);
                    if (resArray.length === 0) {
                        $("#film-row").hide();
                    } else {
                        $("#film-row").show();

                        for (var i = 0; i < resArray.length; i += 1) {
                            $("#film-result-container").append("<tr><td>" + resArray[i][0] + "</td><td>" + resArray[i][1] + "</td><td>" + resArray[i][2] + "</td></tr>");
                        }
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
                        <h1>Search</h1>
                        <form id="search_form" method="get">
                            Keyword: <input type="textbox" name="name" id="name_input" /><br/>
                            <input type="submit"/>
                        </form>
                    </div>
                </div>
                <div class="row" id="actor-row">
                    <div class="col-lg-12">
                        <h2>Persons</h2>
                        <table class="table table-striped">
                            <thead><tr><th>ID</th><th>Name</th></tr></thead>
                            <tbody id="actor-result-container">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row" id="film-row">
                    <div class="col-lg-12">
                        <h2>Films</h2>
                        <table class="table table-striped">
                            <thead><tr><th>IMDB ID</th><th>Title</th><th>Release Year</th></tr></thead>
                            <tbody id="film-result-container">
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
