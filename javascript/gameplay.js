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
function evaluateFragment(fragment) {
	
	// Still logged in?
	if(fragment != "logout") {
		
		// Load HTML template
		$("body").load("template/" + fragment.template + ".html", function() {
			
			$("#title").html(/*fragment.title*/'<img src="https://upload.wikimedia.org/wikipedia/commons/c/c7/House_at_1329_Carroll_Ave.%2C_Los_Angeles_%28Charmed_House%29.JPG" />');
			$("#text").html(fragment.text);
			
			// Delete existing answer links
			$("#answers").html(' ');
			
			// Resolve new answers
			alert(fragment.answers);
			$.each($.parseJSON(fragment.answers), function(i, object) {
				
				$("#answers").append('<a href="#" onclick="goto(' + object.id + ')">' + object.answer + '</a><br />');
				
			});
			
			// Let page load...
			$("#container").waitForImages(function() {
				alert("test");
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
	
	$.post("php/game.php", { task: "reload" }, evaluateFragment, "json");
	
});
