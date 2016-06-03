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
	
	
	
	// Global
	$books = array(
				   "Troban kehrt zur&uuml;ck",
				   "Tobi Drifter",
				   "Necronomicon ex mortis",
				   "Arrrrno der Pirat",
				   "D&uuml;p&uuml;ch der Schreckliche",
				   "Feel the Beat",
				   "Z&uuml;&uuml;&uuml;&uuml;g",
				   "Bananenk&ouml;nig Fridolin",
				   "Der Graf von Murcielago",
				   "GameDesign for Dummies",
				   "Marcelix und Trobelix",
				   "How to steal a palm tree",
				   "Wetterph&auml;nomene",
				   "Die Geschichte des Yoghurts",
				   "Tschuuls Verne",
				   "Derek auf der Suche nach dem mystischen Mangus",
				   "Johnny und Clyde",
				   "Max und Moritz",
				   "Magn&uuml;sse mag N&uuml;sse",
				   "Single Malte"
				   );
	
	// Function to generate book-passwords
	function getBook($bookID)
	{
		global $books;
		
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
			$used = array();
			
			// Generate random book titles
			for($i = 0;$i < count($books);$i++)
			{
				do
				{
					$passwords[$i] = mt_rand(0, count($books) - 1);
				
				// Check for doubles
				} while(in_array($passwords[$i], $used));
				
				array_push($used, $passwords[$i]);
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
						$row['text'] = str_replace('$=pw1=$', '<em>'.getBook('pw1').'</em>', $row['text']);
						$row['text'] = str_replace('$=pw2=$', '<em>'.getBook('pw2').'</em>', $row['text']);
						$row['text'] = str_replace('$=pw3=$', '<em>'.getBook('pw3').'</em>', $row['text']);
						
						echo json_encode($row);
					}
					else
					{
						// Load last story fragment of player
						$row = $dc->getRow('story', array('id' => $player->fragment));
						
						// Search text for book-passwords
						$row['text'] = str_replace('$=pw1=$', '<em>'.getBook('pw1').'</em>', $row['text']);
						$row['text'] = str_replace('$=pw2=$', '<em>'.getBook('pw2').'</em>', $row['text']);
						$row['text'] = str_replace('$=pw3=$', '<em>'.getBook('pw3').'</em>', $row['text']);
						
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
						$row['text'] = str_replace('$=pw1=$', '<em>'.getBook('pw1').'</em>', $row['text']);
						$row['text'] = str_replace('$=pw2=$', '<em>'.getBook('pw2').'</em>', $row['text']);
						$row['text'] = str_replace('$=pw3=$', '<em>'.getBook('pw3').'</em>', $row['text']);
						
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
				
				// Client requests the whole player history
				case 'password':
					$pass = array();
					getBook('pw1');
					array_push($pass, array_search(SessionController::getParameter('pw1'), $books));
					array_push($pass, array_search(SessionController::getParameter('pw2'), $books));
					array_push($pass, array_search(SessionController::getParameter('pw3'), $books));
					array_push($pass, array_search(SessionController::getParameter('pw4'), $books));
					array_push($pass, array_search(SessionController::getParameter('pw5'), $books));
					array_push($pass, array_search(SessionController::getParameter('pw6'), $books));
					array_push($pass, array_search(SessionController::getParameter('pw7'), $books));
					
					echo json_encode($pass);
					
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
