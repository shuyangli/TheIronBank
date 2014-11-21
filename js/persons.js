$(document).ready(function() {

	$(".delete-button").click(function(event) {
		$.ajax({
			url: 'delete_person.php',

			data: {
				IMDB_ID: $(this).attr("data-person_id")
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

	$(".edit-button").click(function(event) {
		$(this).siblings().show();
		$(this).hide();

		//turn rows into text boxes
		//button > td > siblings
		$(this).parent().siblings("td").each(function() {
		// $("td[data-imdb_id='" + $(this).attr("data-imdb_id") + "']").each(function () {
			//ignore primary key
			if ($(this).attr("data-column_name") != "Person_ID") {
				var html = $(this).html();
		        var input = $('<input type="text" name="' + $(this).attr("data-column_name") + '"/>');
		        input.val(html);
		        $(this).attr("data-original", html);
		        $(this).html(input);
			};
	    });
	});

	$(".cancel-button").click(function(event) {
		$(this).siblings().hide();
		$(this).hide();
		$(this).siblings(".edit-button").show();

		//turn text boxes back into rows
		$(this).parent().siblings("td").each(function() {
	        var original = $(this).attr("data-original");
	        $(this).html(original);
	    });
	    
	});

	$(".save-button").click(function(event) {
		//button > column > row
		var updateData = {};
		$(this).parent().siblings("td").find("input").each(function() {
			console.log($(this).parent().attr("data-column_name"));
			console.log($(this).val());
			updateData[$(this).parent().attr("data-column_name")] = $(this).val();
	    });
		//add IMDB_ID
		updateData["Person_ID"] = $(this).parent().siblings("td").html();

	    console.log(updateData);
	    $.ajax({
			url: 'update_person.php',

			data: updateData,

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