$(document).ready(function() {
	$(".delete-button").click(function() {
		$.ajax({
			url: 'delete_film.php',

			data: {
				IMDB_ID: $(this).attr("data-imdb_id")
			},

			type: "POST",

			success: function() {
				location.reload(true);
			},

			error: function( xhr, status, errorThrown ) {
		        alert( "Sorry, there was a problem!" );
		        console.log( "Error: " + errorThrown );
		        console.log( "Status: " + status );
		        console.dir( xhr );
   			}
		});
	});
});