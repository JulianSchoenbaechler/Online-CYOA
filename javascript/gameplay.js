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

// Globals
var debugString = "";
var debugTimer;
var datasets = 1;
var currentSet = "";
var currentTemplate = "";

// Evaluate received story fragment
// Change template, texts and answers
function evaluateFragment(fragment, callback) {
	
	var useCallback = arguments.length == 2 ? true : false;
	
	// Still logged in?
	if(fragment != "logout") {
		
		// Load HTML template
		$("#container").load("template/" + fragment.template + ".html #wrapper", function() {
			
			// Change background image
			$("#container").attr("class", "bg-" + fragment.template);
			
			// Text
			$("#title").html(fragment.title);
			$("#text").html(fragment.text);
			
			// Characters
			if(fragment.character1 != 'none')
				$("#character1").html('<img src="img/character_' + fragment.character1 + '.png" alt="character" />');
			
			if(fragment.character2 != 'none')
				$("#character2").html('<img src="img/character_' + fragment.character2 + '.png" alt="character" />');
			
			// Delete existing answer links
			$("#answers").html(' ');
			
			// Resolve new answers
			$.each($.parseJSON(fragment.answers), function(i, object) {
				
				$("#answers").append('<a href="#" onclick="gotoFragment(\'' + object.id + '\')"><div class="answer"><p>' + object.answer + '</p></div></a>');
				
			});
			
			// Let page load...
			$("#container").waitForImages(function() {
				
				// Setup history canvas
				initHistory();
				
				// Page has been loaded...
				if(useCallback) {
					
					callback();
					
				}
				
				// Fade wrapper in
				if((fragment.template != currentTemplate) && (fragment.template != 'bookshelf')) {
					
					$("#wrapper").delay(2000).fadeTo(2000, 1.0);
					
				} else {
					
					$("#wrapper").fadeTo(10, 1.0);
					
				}
				
				currentTemplate = fragment.template;
				
			});
			
		});
		
		// Update current set
		currentSet = fragment.id;
	
	}
	else
	{
		// Redirect to index page
		window.location.assign("index.html");
	}
	
}

// Player has clicked an answer / option
function gotoFragment(answerID) {
	
	$.post("php/game.php", { task: "answer", id: answerID.toString() }, function(fragment) {
	
		// Callback
		evaluateFragment(fragment, layout);
	
	}, "json");
	datasets++;
	
}

// When document ready
$(document).ready(function() {
	
	$.post("php/game.php", { task: "reload" }, function(fragment) {
		
		// Set up story and history canvas
		evaluateFragment(fragment, layout);
		
	}, "json");
	
	// Keypress event
	$(window).keypress(function(e)
	{
		var id = "";
		
		// If key is in range a-z
		if((e.charCode >= 97) && (e.charCode <= 122)) {
			
			var output = "[Debug]\nTimestamp: " + Date.now();
			output += "\nStarting point: " + startID;
			output += "\nDatasets loaded: " + datasets.toString();
			output += "\nCurrent dataset: " + currentSet;
			output += "\n\nProcess running...\n\n";
			output += "Jump to:";
			
			// Prevent auto-search
			e.preventDefault();
			
			// More than 5 letters for debugging keyword?
			if(debugString.length >= 5) {
				debugString = String.fromCharCode(e.charCode);
			} else {
				debugString += String.fromCharCode(e.charCode);
			}
			
			// Debug code
			if(debugString == "alpha") {
				
				id = prompt(output, "");
				
				// Jump to position
				if(id.length > 0) {
					gotoFragment(id);
				}
			}
			
			// Timer
			clearTimeout(debugTimer);
			debugTimer = setTimeout(function() { debugString = ""; }, 2000);
		
		}
	});
	
});
