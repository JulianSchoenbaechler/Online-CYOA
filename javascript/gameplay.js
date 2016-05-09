/**
*
* This file is part of the 'Online Choose your own Adventure' MultiOil (working title).
*
* University of the Arts Zurich (Switzerland)
* Game Design 2016
* Task: Writing and producing an Online CYOA, including own illustrations.
*
* @copyright (c) University of the Arts Zurich
*
* Author(s):
* Arno Justus
* Julian Schoenbaechler
* Marcel Arioli
* Michael von Ah
*
*/

// When document ready
$(document).ready(function() {
	
	$.post("php/game.php", { task: "start" }, function(fragment) {
		
		$("#title").html(fragment.title);
		$("#text").html(fragment.text);
		
		$.each($.parseJSON(fragment.answers), function(i, object) {
			
			$("#answers").append('<a href="#" onclick="goto(' + object.id + ')">' + object.answer + '</a><br />');
			
		});
		
	}, "json");
	
});