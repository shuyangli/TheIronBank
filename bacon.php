
<?php include('partials/connect.php') ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Bacon Number Calculator</title>

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

    <!-- Bacon JS -->
    <script src="js/bacon.js"></script>


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
                        <h1>Bacon Number Calculator</h1>
                        <form id="baconForm">
                            First Person: <input type="textbox" name="firstPersonName"/><br/>
                            Second Person: <input type="textbox" name="secondPersonName"/><br/>
                            <input type="submit"/>
                        </form>

                    </div>
                </div>
                <div id="resultsRows">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2 id="namesHeader"></h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 id="pathHeader"></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

</body>

</html>
