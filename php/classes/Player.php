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
	
	final class Player
	{
		// Properties
		private $id;				// ID of the player
		private $history;			// Array / Displayed history, the player went through
		private $memory;			// Associative Array / Bool hash table of actions, the character remembers
		private $experience;		// Associative Array / Bool hash table of actions, the player has done
		private $finished;			// Finished game? / Ready to start game?
		private $points;			// Counting points...
		private $avatar;			// Associative Array of images which are used for the avatar picture
		public $name;				// Name of the player
		
		// Class constructor
		public function __construct($id)
		{
			$this->id = $id;
		}
		
		// Load player data
		public function loadData($link)
		{
			$dc = new DatabaseController($link);
			$dbArray = $dc->getRow('player', array('id' => $this->id));
			
			// Load player data
			if(!is_null($dbArray))
			{
				$this->history = json_decode($dbArray['history']);
				$this->memory = json_decode($dbArray['memory']);
				$this->experience = json_decode($dbArray['experience']);
				$this->points = (int)$dbArray['points'];
				$this->avatar = json_decode($dbArray['avatar']);
				
				if($dbArray['finished'] == 0)
				{
					$this->finished = false;
				}
				else
				{
					$this->finished = true;
				}
			}
			
			unset($dc);
		}
		
		// Save player data
		public function saveData($link)
		{
			// Database update Player
			$dc = new DatabaseController($link);
			$update = array('id' => $this->id,
							'name' => $this->name,
							'history' => json_encode($this->history),
							'memory' => json_encode($this->memory),
							'experience' => json_encode($this->experience),
							'points' => $this->points,
							'avatar' => json_encode($this->avatar),
							'finished' => $this->finished ? 1 : 0
							);
			$dc->updateRow('player', $update, array('id' => $this->id));
			
			unset($dc);
		}
		
		// Create new player data
		public function newData($link)
		{
			// Check if player is named
			if(!isset($this->name))
			{
				$this->name = 'noname';
			}
			
			// Predefine values
			$this->history = array(EMPTY_HISTORY);
			$this->memory = EMPTY_PROCESS;
			$this->experience = EMPTY_PROCESS;
			$this->finished = true;
			$this->points = 0;
			$this->avatar = 'noavatar';
			
			// Database create Player
			$dc = new DatabaseController($link);
			$insert = array('id' => $this->id,
							'name' => $this->name,
							'history' => json_encode($this->history),
							'memory' => json_encode($this->memory),
							'experience' => json_encode($this->experience),
							'points' => $this->points,
							'avatar' => $this->avatar,
							'finished' => $this->finished ? 1 : 0
							);
			$dc->insertRow('player', $insert);
			
			unset($dc);
		}
		
		// Set boolean of specific milestone
		// 'remember' defines the memory of the character:
		// - true		Character remembers this action
		// - false		Character cannot remember this action
		public function reachMilestone($milestone, $remember = false)
		{
			// Check milestone name
			if(!is_string($milestone))
			{
				trigger_error("'reachMilestone' expected argument 0 to be string.", E_USER_WARNING);
			}
			
			// Check for milestone state
			if($this->experience[$milestone] === false)
			{
				$this->experience[$milestone] = true;
				
				// Character remembers?
				if($remember)
				{
					$this->memory[$milestone] = true;
				}
			}
		}
		
		// Add a new history element
		public function addHistoryElement($layer, $position, $branches, $description)
		{
			// Check function arguments
			if(!is_int($layer))
			{
				trigger_error("'addHistoryElement' expected argument 0 to be integer.", E_USER_WARNING);
			}
			if(!is_int($position))
			{
				trigger_error("'addHistoryElement' expected argument 1 to be integer.", E_USER_WARNING);
			}
			if(!is_string($branches))
			{
				trigger_error("'addHistoryElement' expected argument 2 to be string.", E_USER_WARNING);
			}
			else
			{
				// Only accept PNG-images
				if(strpos($branches, '.png') === false)
				{
					trigger_error("'addHistoryElement' expected argument 2 to be a filename with the extension '.png'.", E_USER_WARNING);
				}
			}
			if(!is_string($description))
			{
				trigger_error("'addHistoryElement' expected argument 3 to be string.", E_USER_WARNING);
			}
			
			// Add history element
			array_push($this->history, array(
											 'layer' => $layer,
											 'position' => $position,
											 'branches' => $branches,
											 'description' => $description
											));
		}
	}
	
?>
