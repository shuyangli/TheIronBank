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

	$(".edit-button").click(function() {
		$(this).siblings().show();
		$(this).hide();

		//turn rows into text boxes
		//button > td > siblings
		$(this).parent().siblings("td").each(function() {
		// $("td[data-imdb_id='" + $(this).attr("data-imdb_id") + "']").each(function () {
	        var html = $(this).html();
	        var input = $('<input type="text" name="' + $(this).attr("data-column_name") + '"/>');
	        input.val(html);
	        $(this).attr("data-original", html);
	        $(this).html(input);
	    });
	});

	$(".cancel-button").click(function() {
		$(this).siblings().hide();
		$(this).hide();
		$(this).siblings(".edit-button").show();

		//turn text boxes back into rows
		$(this).parent().siblings("td").each(function() {
	        var original = $(this).attr("data-original");
	        $(this).html(original);
	    });
	    
	});

	$(".save-button").click(function() {
		//button > td > form
		console.log($(this).parent().parent().serialize);
	}




});