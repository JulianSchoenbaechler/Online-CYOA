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

// Global paper variable
var paper;
var startID = "1";
var elementStack = [];
// Element stack:
// Array filled with objects:
// Every objects holds three attributes:
// id, circle and description

// Drawing a line
function logConnection(x1, y1, x2, y2) {
	
	var diffX = Math.abs(x1 - x2);
	var diffY = Math.abs(y1 - y2);
	
	// Drawing points
	// The line is divided by four creating four segments.
	var pointsX;
	var pointsY;
	
	if(x1 < x2) {
		pointsX = [x1, x1 + (diffX / 4), x1 + (diffX / 2), x2 - (diffX / 4), x2];
	} else {
		pointsX = [x1, x1 - (diffX / 4), x2 + (diffX / 2), x2 + (diffX / 4), x2];
	}
	
	if(y1 < y2) {
		pointsY = [y1, y1 + (diffY / 4), y1 + (diffY / 2), y2 - (diffY / 4), y2];
	} else {
		pointsY = [y1, y1 - (diffY / 4), y2 + (diffY / 2), y2 + (diffY / 4), y2];
	}
	
	// Generate a set of 4 random numbers
	var randomNumbers = [0, (Math.random() * 2) + Math.random(), (Math.random() * 2) + Math.random(), (Math.random() * 2) + Math.random(), 0];
	
	// Randomize drawing-points
	if(Math.random() < 0.5) {
		
		for(var i = 0;i < 5;i++) {
			
			if(((x1 < x2) && (y1 < y2)) ||
			   ((x1 > x2) && (y1 > y2))) {
				
				pointsX[i] += randomNumbers[i];
				pointsY[i] -= randomNumbers[i];
				
			} else {
				
				pointsX[i] += randomNumbers[i];
				pointsY[i] += randomNumbers[i];
				
			}
		}
		
	} else {
		
		for(var i = 0;i < 5;i++) {
			
			if(((x1 < x2) && (y1 < y2)) ||
			   ((x1 > x2) && (y1 > y2))) {
				
				pointsX[i] -= randomNumbers[i];
				pointsY[i] += randomNumbers[i];
				
			} else {
				
				pointsX[i] -= randomNumbers[i];
				pointsY[i] -= randomNumbers[i];
				
			}
		}
		
	}
	
	// Draw line
	var vector = "M";
	
	for(var i = 0;i < 5;i++)
	{
		vector += pointsX[i].toString() + " " + pointsY[i].toString();
		
		// Specify a quadratic bezier curve on first iteration
		if(i == 0) {
			vector += "Q";
		} else {
			vector += " ";
		}
	}
	
	// Draw vector onto canvas
	return paper.path(vector).attr({"type": "path", "stroke-width": 1, "fill": "black" });
	
}

// Drawing a circle
function logCircle(x, y) {
	
	if(arguments.length == 2) {
		
		var radius = 9;															// Initial radius
		var step = 2 * Math.PI / 20;											// Resolution of circle
		var start = Math.random() * (2 * Math.PI);								// Start position
		var end = (0.1 * Math.random() + 0.95) * ((2 * Math.PI) + start);		// End position
		
		var vector = "M";														// Vector string
		
		var gotoX, gotoY;														// Coordinates of the one single point
		var r = radius;															// Variable / Randomized radius
		
		// Draw circle from start to end
		for(var angle = start; angle <= end; angle += step) {
			
			// Randomize radius
			if(Math.random() < 0.5) {
				r += 0.4 * Math.random();
			} else {
				r -= 0.4 * Math.random();
			}
			
			// Calculate next point to draw
			gotoX = x + r * Math.cos(angle);
			gotoY = y - r * 0.8 * Math.sin(angle);
			
			// Specify a quadratic bezier curve on first iteration
			if(angle == 0) {
				vector += gotoX.toString() + " " + gotoY.toString() + "Q";
			} else {
				vector += gotoX.toString() + " " + gotoY.toString() + " ";
			}
		}
		
		// Draw circle from end back to the start
		for(var angle = end; angle > start; angle -= step) {
			
			// Randomize radius
			if(Math.random() < 0.5) {
				r += 0.3 * Math.random();
			} else {
				r -= 0.3 * Math.random();
			}
			
			// Calculate next point to draw
			gotoX = x + r * Math.cos(angle);
			gotoY = y - r * 0.8 * Math.sin(angle);
			
			// Add point to vector string
			vector += gotoX.toString() + " " + gotoY.toString() + " ";
		}
		
		// Draw vector onto canvas
		return paper.path(vector + "Z").attr({ "type": "path", "stroke-width": 1, "fill": "black" });
		
	}
}

// Get a specific element from the stack
function getElementFromStack(elementPar) {
	
	// ID or circle object?
	if(typeof elementPar === "string" || elementPar instanceof String) {
		
		// Search element stack
		for(var i = 0;i < elementStack.length;i++) {
			
			// If correct element found, return it
			if(elementStack[i].id === elementPar)
			{
				return elementStack[i];
			}
			
		}
		
	} else {
		
		// Search element stack
		for(var i = 0;i < elementStack.length;i++) {
			
			// If correct element found, return it
			if(JSON.stringify(elementStack[i].collection[2].node) === JSON.stringify(elementPar))
			{
				return elementStack[i];
			}
			
		}
		
	}
	
	return false;
	
}

// Connect two elements with a linear or a bezier curve
function connect(element1, element2) {
	
	// Get coordinates of the given elements
	var x1 = element1[2].getBBox().x + (element1[2].getBBox().width / 2);
	var y1 = element1[2].getBBox().y + (element1[2].getBBox().height / 2);
	var x2 = element2[2].getBBox().x + (element2[2].getBBox().width / 2);
	var y2 = element2[2].getBBox().y + (element2[2].getBBox().height / 2);
	
	var v = [x1 - x2, y1 - y2]; 						// Vector
	var m = Math.sqrt(v[0] * v[0] + v[1] * v[1]);		// Magnitude
	var u = [v[0] / m, v[1] / m];						// Unit vector
	
	var offset = 18;									// Offset to middle point
	var length = m - offset * 2;						// Real drawing length
	
	var startX = x1 - u[0] * offset;
	var startY = y1 - u[1] * offset;
	
	var endX = startX - u[0] * length;
	var endY = startY - u[1] * length;
	
	// Return path
	return logConnection(startX, startY, endX, endY);
	
}

// Get history element
// Function fires a callback when finished loading
function getElement(elementID, main, callback) {
	
	// Standard disable argument
	if(typeof main === "undefined") {
        main = false;
    }
	
	var useCallback = arguments.length == 3 ? true : false;
	
	// Get element from server
	if(useCallback) {
		
		if(main) {
			$.post("php/game.php", { task: "history", id: elementID.toString() }, callback, "json");
		} else {
			$.post("php/game.php", { task: "historyStaged", id: elementID.toString() }, callback, "json");
		}
		
	}
	
}

// Draw the element (circle and description) onto the paper
function drawElement(element, disable) {
	
	// Standard disable argument
	if(typeof disable === "undefined") {
        disable = false;
    }
	
	// Element stack lookup
	var canvasObj = getElementFromStack(element.id.toString());
	
	// Element does not exist in stack?
	if(canvasObj === false) {
		
		var eCollection = [];
		
		// Creates history element circle
		var cir = logCircle(parseInt(element.x), parseInt(element.y));
		var cross1 = logConnection(parseInt(element.x) + 10, parseInt(element.y) + 10, parseInt(element.x) - 10, parseInt(element.y) - 10);
		var cross2 = logConnection(parseInt(element.x) + 10, parseInt(element.y) - 10, parseInt(element.x) - 10, parseInt(element.y) + 10);
		var cross = paper.set();
		cross.push(cross1, cross2);
		var cirMO = paper.circle(parseInt(element.x), parseInt(element.y), 20);
		
		// Make mouseover circle transparent
		cirMO.attr({ "stroke": "none", "fill": "#FFFFFF", "fill-opacity": 0.0 });
		
		// Hide cross if disabled element
		if(disable) {
			cross.hide();
		}
		
		// Creates history element description
		var text = paper.text(1100, 364, element.description);
		text.attr({"font-family": "Talking To The Moon", "font-size": 30, "text-anchor": "end"});
		text.hide();
		
		// Pack into collection
		eCollection[0] = cir;
		eCollection[1] = cross;
		eCollection[2] = cirMO;
		eCollection[3] = text;
		
		// Add to element stack
		elementStack.push({ id: element.id, collection: eCollection });
		
		// Create mouseover event
		if(!disable) {
			
			eCollection[2].node.onmouseover = function() {
				getElementFromStack(this).collection[3].show();
				$("#history").css('cursor', 'help');
			};
			eCollection[2].node.onmouseout = function() {
				getElementFromStack(this).collection[3].hide();
				$("#history").css('cursor', 'auto');
			};
			
		}
		
		// Return collection
		return eCollection;
	
	} else {
		
		// Edit element from stack
		
		// Reset all events
		canvasObj.collection[2].node.onmouseover = null;
		canvasObj.collection[2].node.onmouseout = null;
		
		// Unhide cross
		canvasObj.collection[1].show();
		
		// Create mouseover event
		canvasObj.collection[2].node.onmouseover = function() {
			getElementFromStack(this).collection[3].show();
			$("#history").css('cursor', 'help');
		};
		canvasObj.collection[2].node.onmouseout = function() {
			getElementFromStack(this).collection[3].hide();
			$("#history").css('cursor', 'auto');
		};
		
		// Return circle object
		return canvasObj.collection;
		
	}
	
}

// Add history element
function addElement(elementID) {
	
	// Temp element for connections
	var tempElement;
	
	// Get history element from database
	getElement(elementID, true, function(element) {
		
		// Does this element exist?
		if(element != "none") {
			
			// Draw main element
			tempElement = drawElement(element, false);
			
			$.each($.parseJSON(element.connections), function(i, object) {
				
				// Get each element by its id and draw it to the paper
				getElement(object, false, function(element) {
					
					// Does this element exist?
					if(element != "none") {
						
						// Draw it and connect it
						connect(tempElement, drawElement(element, true));
						
					}
					
				});
				
			});
			
		}
		
	});
	
}

// Add all history elements
function addAllElements() {
	
	// Temp element for connections
	var tempElement;
	
	$.post("php/game.php", { task: "historyPlayer" }, function(allElements) {
		
		// No history elements? (new game)
		if(allElements.length == 0) {
			
			addElement(startID);
			
		} else {
			
			for(var i = 0;i < allElements.length;i++) {
				
				// Draw element
				tempElement = drawElement(allElements[i], false);
				
				// Add temp element for connection into an anonymous function
				// to make it unique for each iteration
				(function(connected) {
					
					$.each(allElements[i].connections, function(j, object) {
						
						// Get each element by its id and draw it to the paper
						getElement(object, false, function(element) {
							
							// Does this element exist?
							if(element != "none") {
								
								// Draw it and connect it
								connect(connected, drawElement(element, true));
								
							}
							
						});
						
					});
					
				})(tempElement);
				
			}
			
		}
		
	}, "json");
	
}

// This function is fired everytime a new story fragment gets loaded
function initHistory() {
	
	// Check if canvas is already drawed
	if(typeof paper == 'undefined') {
		
		paper = new Raphael(document.getElementById('history'), 1200, 800);
		addAllElements();
		
	} else {
		
		addElement("current");
		
	}
	
	// Set current cross to red color
	for(var i = 0;i < elementStack.length;i++)
	{
		if(elementStack[i].id == currentSet) {
			
			elementStack[i].collection[1].attr({ "stroke": "#AA0000", "fill": "#AA0000" });
			
		} else {
		
			elementStack[i].collection[1].attr({ "stroke": "black", "fill": "black" });
			
		}
	}
}
