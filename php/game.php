<?php
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
	namespace CYOA_Engine;
	
	// Include library files
	require_once 'Includes.php';
	
	
	// Function to generate book-passwords
	function getBook($bookID)
	{
		$books = array("Troban kehrt zur&uuml;ck", "Torbants Weltreise", "Tobi Drift", "Lexikon", "Arrrrno der Pirat", "Taborn der Schreckliche", "Feel the Beat");
		
		// Check argument
		if(!is_string($bookID))
		{
			trigger_error("[Runtime] 'getBook' expected argument 0 to be string.", E_USER_WARNING);
		}
		elseif(strpos($bookID, 'pw') !== 0)
		{
			trigger_error("[Runtime] 'getBook' expected argument 0 (string) starting with 'pw'.", E_USER_WARNING);
		}
		
		// Should generate pw?
		if(SessionController::getParameter('pw1') == 'none')
		{
			$passwords = array();
			
			// Generate random book titles
			for($i = 0;$i < 7;$i++)
			{
				$passwords[$i] = mt_rand(0, count($books) - 1);
			}
			
			// Check for doubles
			
			// pw1
			while($passwords[0] == $passwords[1])
			{
				$passwords[1] = mt_rand(0, count($books) - 1);
			}
			
			// pw2
			while(($passwords[2] == $passwords[3]) ||
				  (($passwords[0] == $passwords[2]) && ($passwords[1] == $passwords[3])) ||
				  (($passwords[0] == $passwords[3]) && ($passwords[1] == $passwords[2])))
			{
				$passwords[3] = mt_rand(0, count($books) - 1);
			}
			
			// pw3
			while(($passwords[4] == $passwords[5]) ||
				  (($passwords[0] == $passwords[4]) && ($passwords[1] == $passwords[5])) ||
				  (($passwords[0] == $passwords[5]) && ($passwords[1] == $passwords[4])) ||
				  (($passwords[2] == $passwords[4]) && ($passwords[3] == $passwords[5])) ||
				  (($passwords[2] == $passwords[5]) && ($passwords[3] == $passwords[4])))
			{
				$passwords[5] = mt_rand(0, count($books) - 1);
			}
			
			while(($passwords[5] == $passwords[6]) || ($passwords[4] == $passwords[6]) ||
				  (($passwords[0] == $passwords[5]) && ($passwords[1] == $passwords[6])) ||
				  (($passwords[0] == $passwords[6]) && ($passwords[1] == $passwords[5])) ||
				  (($passwords[2] == $passwords[5]) && ($passwords[3] == $passwords[6])) ||
				  (($passwords[2] == $passwords[6]) && ($passwords[3] == $passwords[5])))
			{
				$passwords[6] = mt_rand(0, count($books) - 1);
			}
			
			// Save into session
			for($i = 1;$i < 8;$i++)
			{
				SessionController::setParameter('pw'.(string)$i, $books[$passwords[$i - 1]]);
			}
			
		}
		
		// Decode book ID
		switch($bookID)
		{
			case 'pw1':
				return (string)(SessionController::getParameter('pw1').', '.SessionController::getParameter('pw2'));
				break;
			
			case 'pw2':
				return (string)(SessionController::getParameter('pw3').', '.SessionController::getParameter('pw4'));
				break;
			
			default:
				return (string)(SessionController::getParameter('pw5').', '.SessionController::getParameter('pw6').', '.SessionController::getParameter('pw7'));
				break;
		}
	}
	
	// Function to resolve template
	function template($fragment)
	{
		switch($fragment)
		{
			case 'test':
				return 'bookshelf';
				break;
			
			default:
				return 'standard';
				break;
		}
	}
	
	
	
	// POST arguments
	$task = trim($_POST['task']);
	
	// User logged in?
	if(SessionController::getSessionID() !== false)
	{
		// Open database
		$link = DatabaseController::connect();
		
		// Create Player instance and load its data
		$player = new Player(SessionController::getSessionID());
		
		// Player exists in database?
		if($player->loadData($link))
		{
			$dc = new DatabaseController($link);
			
			// Resolve task
			switch($task)
			{
				// Start / load game
				case 'reload':
					// New start of a game?
					if($player->finished)
					{
						$player->finished = false;
						$player->saveData($link);
						
						// Standard story fragment: 'prolog1'
						$row = $dc->getRow('story', array('id' => 'prolog1'));
						
						// Search text for book-passwords
						$row['text'] = str_replace('$=pw1=$', getBook('pw1'), $row['text']);
						$row['text'] = str_replace('$=pw2=$', getBook('pw2'), $row['text']);
						$row['text'] = str_replace('$=pw3=$', getBook('pw3'), $row['text']);
						
						// HTML template
						$row['template'] = template('start');
						
						echo json_encode($row);
					}
					else
					{
						// Load last story fragment of player
						$row = $dc->getRow('story', array('id' => $player->fragment));
						
						// Search text for book-passwords
						$row['text'] = str_replace('$=pw1=$', getBook('pw1'), $row['text']);
						$row['text'] = str_replace('$=pw2=$', getBook('pw2'), $row['text']);
						$row['text'] = str_replace('$=pw3=$', getBook('pw3'), $row['text']);
						
						// HTML template
						$row['template'] = template($player->fragment);
						
						echo json_encode($row);
					}
					break;
				
				// Player has chosen an answer /option
				case 'answer':
					// Received an id?
					if(isset($_POST['id']))
					{
						$id = trim($_POST['id']);
						
						// Change current fragment for player
						$player->fragment = $id;
						$player->saveData($link);
						
						// Load last story fragment of player
						$row = $dc->getRow('story', array('id' => $id));
						
						// Search text for book-passwords
						$row['text'] = str_replace('$=pw1=$', getBook('pw1'), $row['text']);
						$row['text'] = str_replace('$=pw2=$', getBook('pw2'), $row['text']);
						$row['text'] = str_replace('$=pw3=$', getBook('pw3'), $row['text']);
						
						// HTML template
						$row['template'] = template($id);
						
						// Load this story fragment
						echo json_encode($row);
					}
					break;
				
				// Client requests a history element from the player (saving)
				case 'history':
					// Received an id?
					if(isset($_POST['id']))
					{
						$id = trim($_POST['id']);
						
						// Client is looking for an element with the id of the
						// current story fragment
						if($id == 'current')
						{
							// Is there an element? Add it to player database if so...
							if($player->addHistoryElement($player->fragment, $link))
							{
								$player->saveData($link);
								echo json_encode($dc->getRow('history', array('id' => $player->fragment)));
							}
							else
							{
								// Return 'none'
								echo json_encode('none');
							}
						}
						else
						{
							// Is there an element? Add it to player database if so...
							if($player->addHistoryElement($id, $link))
							{
								$player->saveData($link);
								echo json_encode($dc->getRow('history', array('id' => $id)));
							}
							else
							{
								// Return 'none'
								echo json_encode('none');
							}
						}
					}
					break;
				
				// Client requests a history element from the player (without saving)
				case 'historyStaged':
					// Received an id?
					if(isset($_POST['id']))
					{
						$id = trim($_POST['id']);
						
						// Client is looking for an element with the id of the
						// current story fragment
						if($id == 'current')
						{
							// Get attributes from history elements from database
							
							// Add history element
							$row = $dc->getRow('history', array('id' => $player->fragment));
							
							if(!is_null($row))
							{
								$row['connections'] = json_decode($row['connections'], true);
								
								// Return element
								echo json_encode($row);
							}
							else
							{
								// Return 'none'
								echo json_encode('none');
							}
						}
						else
						{
							// Add history element
							$row = $dc->getRow('history', array('id' => $id));
							
							if(!is_null($row))
							{
								$row['connections'] = json_decode($row['connections'], true);
								
								// Return element
								echo json_encode($row);
							}
							else
							{
								// Return 'none'
								echo json_encode('none');
							}
						}
					}
					break;
				
				// Client requests the whole player history
				case 'historyPlayer':
					echo json_encode($player->getHistory());
					break;
				
				default:
					break;
			}
			
			unset($dc);
		}
		else
		{
			// Tell client to logout
			echo json_encode('logout');
			SessionController::destroySession();
		}
		
		// Close database
		DatabaseController::disconnect();
		unset($link);
	}
	else
	{
		// Tell client to logout
		echo json_encode('logout');
		SessionController::destroySession();
	}
	
	exit();
	
?>
