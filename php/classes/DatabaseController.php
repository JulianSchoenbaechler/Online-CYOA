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
	
	// Includes
	require_once '../Database.php';
	
	class DatabaseController
	{
		use DatabaseConnection;
		
		// Properties
		private $link;
		
		// Constructor
		public function __construct()
		{
			$this->link = $this->connectDB();
		}
		
		// Deconstructor
		public function __destruct()
		{
			mysqli_close($link);
		}
		
		// Insert row into database table
		
	}
	
?>
