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

// Evaluate received story fragment
// Change template, texts and answers
function evaluateFragment(fragment, callback) {
	
	var useCallback = arguments.length == 2 ? true : false;
	
	// Still logged in?
	if(fragment != "logout") {
		
		// Load HTML template
		$("#container").load("template/" + fragment.template + ".html #container", function() {
			
			$("#title").html(fragment.title);
			$("#text").html(fragment.text);
			
			// Delete existing answer links
			$("#answers").html(' ');
			
			// Resolve new answers
			$.each($.parseJSON(fragment.answers), function(i, object) {
				
				$("#answers").append('<a href="#" onclick="goto(' + object.id + ')">' + object.answer + '</a><br />');
				
			});
			
			// Let page load...
			$("#container").waitForImages(function() {
				
				// Setup history canvas
				initHistory();
				
				// Page has been loaded...
				if(useCallback) {
					
					callback();
					
				}
				
			});
			
		});
	
	}
	else
	{
		// Redirect to index page
		window.location.assign("index.html");
	}
	
}

// Player has clicked an answer / option
function goto(answerID) {
	
	$.post("php/game.php", { task: "answer", id: answerID.toString() }, evaluateFragment, "json");
	
}

// When document ready
$(document).ready(function() {
	
	$.post("php/game.php", { task: "reload" }, function(fragment) {
		
		// Set up story and history canvas
		evaluateFragment(fragment);
		
	}, "json");
	
});
