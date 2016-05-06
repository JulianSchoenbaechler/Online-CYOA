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
* Julian Schönbächler
* Marcel Arioli
* Michael von Ah
*
*/
	namespace CYOA_Engine;
	
	final class Player
	{
		// Properties
		private $id;				// ID of the player
		private $history;			// Displayed history, the player went through
		private $memory;			// Associative Array / Bool hash table of actions, the character remembers
		private $experience;		// Associative Array / Bool hash table of actions, the player has done
		private $finished;			// Finished game? / Ready to start game?
		private $points;			// Counting points...
		private $avatar;			// Associative Array of images which are used for the avatar picture
		public $name;				// Name of the player
		
		// History structure
		// Key: Story fragment ID
		// Value: Properties (Associative Array)
		//			- 
		
		// Class constructor
		public function __construct($id)
		{
			$this->id = $id;
		}
		
		// Load player data
		public function loadData($link)
		{
			$da = new DatabaseController($link);
			$dbArray = $da->getRow('player', array('id' => $this->id));
			
			// Load player data
			if(!is_null($dbArray))
			{
				$this->history = $dbArray['history'];
				$this->memory = $dbArray['memory'];
				$this->experience = $dbArray['experience'];
				$this->points = $dbArray['points'];
				$this->avatar = $dbArray['avatar'];
				
				if($dbArray['finished'] == 0)
				{
					$this->finished = false;
				}
				else
				{
					$this->finished = true;
				}
			}
		}
		
		// Save player data
		public function saveData($link)
		{
			// Database update Player
			$da = new DatabaseController($link);
			$update = array('id' => $this->id,
							'name' => $this->name,
							'history' => $this->history,
							'memory' => $this->memory,
							'experience' => $this->experience,
							'points' => $this->points,
							'avatar' => $this->avatar,
							'finished' => $this->finished ? 1 : 0
							);
			$da->updateRow('player', $update, array('id' => $this->id));
			
			unset($da);
		}
		
		// Create new player data
		public function newData($link)
		{
			// Check if player is named
			if(!isset($this->name))
			{
				$this->name = 'noname';
			}
			
			// Create first history element
			$he = new History(5, 0, 'branch0.png');
			
			// Predefine values
			$this->history = json_encode($he->element);
			$this->memory = json_encode(EMPTY_PROCESS);
			$this->experience = json_encode(EMPTY_PROCESS);
			$this->finished = true;
			$this->points = 0;
			$this->avatar = 'noavatar';
			
			// Database create Player
			$da = new DatabaseController($link);
			$insert = array('id' => $this->id,
							'name' => $this->name,
							'history' => $this->history,
							'memory' => $this->memory,
							'experience' => $this->experience,
							'points' => $this->points,
							'avatar' => $this->avatar,
							'finished' => $this->finished ? 1 : 0
							);
			$da->insertRow('player', $insert);
			
			unset($da);
		}
	}
	
?>
