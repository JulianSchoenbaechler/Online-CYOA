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

// Layout / template management
function layout() {
	
	// Standard height
	var height = 40;
	
	// Calculate free space, divide it by 2 (top and bottom) and subtract 20px margin
	height = $(window).height() - $("#answers").outerHeight() - $("#textWrap").outerHeight();
	height /= 2;
	height = Math.round(height) - 20;
	
	if(height > 40) {
		
		$("#textWrap").css('margin-top', height.toString() + 'px');
		
	} else {
		
		$("#textWrap").css('margin-top', '40px');
		
	}
	
}

// When document ready
$(document).ready(function() {
	
	// Prevent resize event from firing multiple times
	// Anonymous function
	var resizeDelay = (function() {
		
		var timer = 0;
		
		// Function waites for a given time
		return function(callback, ms) {
			
			clearTimeout (timer);
			
			// Callback after delay
			timer = setTimeout(callback, ms);
			
		};
		
	})();
	
	// Window resize event
	$(window).resize(function() {
		
		// Delay event
		resizeDelay(function() {
		
			// Recalculate layout
			if($("#textWrap").length) {
				layout();
			}
			
		}, 200);
	});
	
});