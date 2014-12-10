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

    function search_by_movie() {
        // AJAX call to get actors from movie
        $.ajax({
            url: 'advanced_search_film.php',
            type: 'GET',
            data: $("#search_form").serialize(),
            success: function(result) {
                $("#film-result-container").empty();

                // Populate result container
                resArray = JSON.parse(result);
                for (var i = 0; i < resArray.length; i += 1) {
                    $("#film-result-container").append("<tr><td>" + resArray[i][0] + "</td><td>" + resArray[i][1] + "<tr><td>" + resArray[i][2] + "</td></tr>");
                }
            }
        });
    }

    function search_by_actor() {
        // AJAX call to get movies from actor
        $.ajax({
            url: 'advanced_search_actor.php',
            type: 'GET',
            data: $("#search_form").serialize(),
            success: function(result) {
                $("#result-container").empty();

                // Populate result container
                resArray = JSON.parse(result);
                for (var i = 0; i < resArray.length; i += 1) {
                    $("#result-container").append("<tr><td>" + resArray[i][0] + "</td><td>" + resArray[i][1] + "<tr><td>" + resArray[i][2] + "</td></tr>");
                }
            }
        });
    }

    $(document).ready(function() {

        // Search actors
        $("#search_form").on('submit', function (e) {

            // Prevent form from actually submitting
            e.preventDefault();

            if ( !($("#movie_name_input").val()) && !($("#actor_name_input").val()) ) {
                // If input is empty
                return false;
            } else if (!$("#movie_name_input").val()) {
                // If there's no movie name, we're querying by actors
                search_by_actor();
            } else {
                // Otherwise we're querying by movies
                // This way whenever there's a movie name, we use it
                search_by_movie();
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
                        <h1>Advanced Search</h1>
                        <form id="search_form" method="get">
                            All actors in a film: <input type="textbox" name="movie_name" id="movie_name_input" placeholder="Movie Name" /><br/>
                            All films an actor has acted in: <input type="textbox" name="actor_name" id="actor_name_input" placeholder="Actor Name" /><br/>
                            <input type="submit"/>
                        </form>
                    </div>
                </div>
                <div class="row" id="film-row">
                    <div class="col-lg-12" id="film-results">
                        <h2>Actors in a film</h2>
                        <table class="table table-striped">
                            <thead><tr><th>Person ID</th><th>Name</th><th>Film</th></tr></thead>
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
