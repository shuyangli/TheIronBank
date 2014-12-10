$(document).ready(function() {
    $("#resultsRows").hide();

    $("#baconForm").on('submit', function (e) {

        // Prevent form from actually submitting
        e.preventDefault();

        // AJAX call to get actors
        $.ajax({
            url: 'calculate_bacon.php',
            type: 'GET',
            data: $("#baconForm").serialize(),
            success: function(result) {
                // Populate result container
                resArray = JSON.parse(result);

                $("#namesHeader").html("The Bacon Number for " + resArray["firstActorName"] + " and " + resArray["secondActorName"] + " is " + (resArray["path"].length - 1));
                $("#pathHeader").html("The path is " + resArray["path"].join(" -> "));
                $("#resultsRows").show();
            }
        });
    });
})