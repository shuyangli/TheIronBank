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
                    $("#namesRow").nextAll().remove();
                    $("#namesHeader").html("The Bacon Number for " + resArray["firstActorName"] + " and " + resArray["secondActorName"] + " is " + (resArray["actors"].length - 1));
                    for (var i = resArray['actors'].length - 1; i > 0; i--) {
                        $("#namesRow").after('<div class="row"><div class="col-lg-12"><b>' + resArray['actors'][i-1] + "</b> and <b>" + resArray['actors'][i] + "</b> starred in <i>" + resArray['movies'][i-1] + '</i></div></div>');
                    }
                    $("#resultsRows").show();
                } else {
                    $("#namesHeader").html("Error: " + resArray['error']);
                    $("#namesRow").nextAll().remove();
                }

            }
        });
    });
})