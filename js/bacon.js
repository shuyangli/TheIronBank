$(document).ready(function() {

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
                console.log(resArray);
            }
        });
    });
})