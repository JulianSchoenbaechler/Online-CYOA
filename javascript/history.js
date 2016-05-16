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
var elementStack = [];
// Element stack:
// Array filled with objects:
// Every objects holds three attributes:
// id, circle and description

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
			if(JSON.stringify(elementStack[i].circle.node) === JSON.stringify(elementPar))
			{
				return elementStack[i];
			}
			
		}
		
	}
	
	return false;
	
}

// Connect two elements with a linear or a bezier curve
function connect(element1, element2, curve) {
	
	// Get coordinates of the given elements
	var x1 = element1.getBBox().x + (element1.getBBox().width / 2);
	var y1 = element1.getBBox().y + (element1.getBBox().height / 2);
	var x2 = element2.getBBox().x + (element2.getBBox().width / 2);
	var y2 = element2.getBBox().y + (element2.getBBox().height / 2);
	
	// Calculate the distance (vector) between the two objects
	var diffX = Math.abs(x1 - x2);
	var diffY = Math.abs(y1 - y2);
	
	// Variables: controlpoint 1 + 2 and midpoints
	var c1, c2, midpointX, midpointY;
	
	// Set control points and midpoints
	if(x1 < x2) {
		c1 = x1 + (diffX / 2) * 0.8;
		c2 = x2 - (diffX / 2) * 0.8;
		midpointX = (diffX / 2) + x1;
	}
	else
	{
		c1 = x1 - (diffX / 2) * 0.8;
		c2 = x2 + (diffX / 2) * 0.8;
		midpointX = (diffX / 2) + x2;
	}
	
	if(y1 < y2) {
		midpointY = (diffY / 2) + y1;
	}
	else
	{
		midpointY = (diffY / 2) + y2;
	}
	
	// Create a string with the path parameters for Raphaël
	var pathString = "M " + x1.toString() + " " + y1.toString();
	pathString += " Q " + c1.toString() + " " + y1.toString();
	pathString += " " + midpointX.toString() + " " + midpointY.toString();
	pathString += " " + c2.toString() + " " + y2.toString();
	pathString += " " + x2.toString() + " " + y2.toString();
	
	// Create path
	var path = paper.path(pathString);
	path.toBack();
	path.attr({ "stroke": "#fff", "stroke-width": 3});
	
	// Return path
	return path;
}

// Get history element
// Function fires a callback when finished loading
function getElement(elementID, callback) {
	
	var useCallback = arguments.length == 2 ? true : false;
	
	// Get element from server
	if(useCallback) {
		
		$.post("php/game.php", { task: "history", id: elementID.toString() }, callback, "json");
		
	}
	
}

// Draw history element
function drawElement(elementID) {
	
	var connectedElements = [];
	
	// Get history element from database
	getElement(elementID, function(element) {
		
		// Does this element exist?
		if(element != "none") {
			
			// Element stack lookup
			var canvasObj = getElementFromStack(element.id.toString());
			
			if(canvasObj === false) {
				
				// Creates history element circle
				var cir = paper.circle(parseInt(element.x), parseInt(element.y), 6);
				cir.attr({
					"stroke": "#bce1ff",
					"stroke-width": 1.5,
					"fill": "r(0.5, 0.5)#0a46ca-#2b6bd2-#67b9e9",
				});
				
				// Creates history element description
				var box = paper.rect(parseInt(element.x), parseInt(element.y) + 14, 200, 34, 4);
				box.attr({
					"stroke": "#d0d0d0",
					"stroke-width": 2,
					"fill": "90-#0a46ca-#3980ff",
					});
				
				var text = paper.text(parseInt(element.x) + 10, parseInt(element.y) + 30, element.description);
				text.attr({"font": "Arial", "font-size": 14, "fill": "#fff", "text-anchor": "start"});
				
				// Group
				var desc = paper.set();
				desc.push(box, text);
				//desc.hide();
				
				// Add to element stack
				elementStack.push({ id: element.id, circle: cir, description: desc });
				
				// Create mouseover event
				cir.node.onmouseover = function() {
					getElementFromStack(this).description.show();
					$("#history").css('cursor', 'help');
				};
				cir.node.onmouseout = function() {
					getElementFromStack(this).description.hide();
					$("#history").css('cursor', 'auto');
				};
			
			} else {
				
				// Edit element from stack
				canvasObj.circle.attr({
					"stroke": "#bce1ff",
					"stroke-width": 1.5,
					"fill": "r(0.5, 0.5)#0a46ca-#2b6bd2-#67b9e9",
				});
				
				// Create mouseover event
				canvasObj.circle.node.onmouseover = function() {
					getElementFromStack(this).description.show();
					$("#history").css('cursor', 'help');
				};
				canvasObj.circle.node.onmouseout = function() {
					getElementFromStack(this).description.hide();
					$("#history").css('cursor', 'auto');
				};
				
			}
			
		}
		
	});
	
}

// Set up canvas
function initHistory() {
	
	// Check if canvas is already drawed
	if(typeof paper == 'undefined')
	{
		paper = new Raphael(document.getElementById('history'), 1024, 512);
		//drawElement("current");
	}
	drawElement("start");
	
}
