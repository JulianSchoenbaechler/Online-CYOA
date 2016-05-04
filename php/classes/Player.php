<?php
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
		
		// Class constructor
		public function __construct($id)
		{
			$this->id = $id;
		}
		
		// Load player data
		public function loadData()
		{
			
		}
		
		// Save player data
		public function saveData()
		{
			
		}
		
		// Create new player data
		public function newData()
		{
			
		}
	}
	
	// Player
	// ======
	// memory (bool hash-table) synchron
	// -- experienced (bool hash-table)
	// name
	// id
	// finished
	// points
	// 
	
?>