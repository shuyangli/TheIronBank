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

                if(resArray['success']) {
                    $("#namesHeader").html("The Bacon Number for " + resArray["firstActorName"] + " and " + resArray["secondActorName"] + " is " + (resArray["actors"].length - 1));
                    $("#pathHeader").html("The path is " + resArray["actors"].join(" -> "));
                    $("#resultsRows").show();
                } else {
                    $("#namesHeader").html("Error: " + resArray['error']);
                    for (var i = resArray['actors'].length - 1; i > 0; i--) {
                        $("#namesRow").after('<div class="row"><div class="col-lg-12">' + resArray['actors'][i-1] + " and " + resArray['actors'][i] + " starred in " + resArray['mutualFilms'][i-1] + '</div></div>');
                    };
                    $("#pathHeader").html("");
                }

            }
        });
    });
})