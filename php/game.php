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
	
	
	
	// Include library files
	require_once 'Includes.php';
	
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
						
						$row = $dc->getRow('story', array('id' => 'start'));
						$row['template'] = template('start');
						
						echo json_encode($row);
					}
					else
					{
						// Load last story fragment of player
						$row = $dc->getRow('story', array('id' => $player->fragment));
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
