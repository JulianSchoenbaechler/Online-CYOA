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
var bookCombination = [];
var bookPasswords = [];

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
	
	// Load password books if in front of bookshelf
	if(currentTemplate == "bookshelf") {
		
		$.post("php/game.php", { task: "password" }, function(object) {
		
			// Set book-passwords
			bookPasswords = object;
		
		}, "json");
			
	}
	
	// History-tree click event
	$("#tree").click(function() {
		
		$("#historyWrap").show();
		
	});
	
	// History click event
	$("#history").click(function() {
		
		$("#historyWrap").hide();
		
	});
	
	// Library click event
	$("#character1.library").click(function(e) {
		
		// Clear books array
		bookCombination = [];
		
		// Goto bookshelf
		gotoFragment('bookshelf');
		
	});
	
	$("#character1.library img").click(function(e) {
		
		// Cancel onclick fire
		e.stopPropagation();
		
	});
	
	// Click event for books
	$(".book").click(function() {
		
		var id = $(this).attr('id');
		
		$(this).hide();
		
		// Extract book number
		id = id.substring(4);
		
		// Add to array
		bookCombination.push(parseInt(id) - 1);
		
		// Resolve chosen books
		switch(bookCombination.length) {
			
			// Two books
			case 2:
				
				// pw1
				if(($.inArray(bookPasswords[0], bookCombination) !== -1) &&
				   ($.inArray(bookPasswords[1], bookCombination) !== -1))
				{
					paper.clear();
					startID = "bookshelfZ1";
					gotoFragment("bookshelfZ1");
				}
				
				// pw2
				else if(($.inArray(bookPasswords[2], bookCombination) !== -1) &&
						($.inArray(bookPasswords[3], bookCombination) !== -1))
				{
					paper.clear();
					startID = "bookshelfZ2";
					gotoFragment("bookshelfZ2");
				}
				
				break;
			
			// Three books
			case 3:
				
				// pw3
				if(($.inArray(bookPasswords[4], bookCombination) !== -1) &&
				   ($.inArray(bookPasswords[5], bookCombination) !== -1) &&
				   ($.inArray(bookPasswords[6], bookCombination) !== -1))
				{
					paper.clear();
					startID = "bookshelfZ3";
					gotoFragment("bookshelfZ3");
				}
				
				break;
			
			default:
				break;
				
		}
		
	});
	
	// Book-name mousover position
	var booknamePos = function(event) {
		
		var x = event.pageX - 20;
		var y = event.pageY + 20;
		
		$("div.bookname").css( { top: y, left: x } );
	};

	var showBookname = function(event) {
		
		// Remove if still active
		$("div.bookname").remove();
		
		var id = $(this).attr('id');
		var bookname = $("#" + id + "name").html();
		
		$('<div class="bookname"><em>' + bookname + '</em></div>').appendTo("body");
		booknamePos(event);
		
	};

	var hideBookname = function() {
		
	   $("div.bookname").remove();
	   
	};

	$(".book").bind({
		mousemove : booknamePos,
		mouseenter : showBookname,
		mouseleave: hideBookname
	});
	
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
