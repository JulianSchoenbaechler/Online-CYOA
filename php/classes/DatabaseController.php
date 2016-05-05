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
		public function insertRow($table, $columns = array(), $values = array())
		{
			// Check table name
			if(!is_string($table))
			{
				trigger_error("'insertRow' expected argument 0 to be string.", E_USER_WARNING);
			}
			else
			{
				$table = mysqli_real_escape_string($this->link, $table);
			}
			
			// Check columns and values
			if(count($columns) != count($values))
			{
				trigger_error("'insertRow' expected argument 1 and 2 (array) of the same length.", E_USER_WARNING);
			}
			
			$sql = "INSERT INTO `$table`(";
			
			// Define columns to insert
			for($i = 0;$i < count($columns);$i++)
			{
				$element = mysqli_real_escape_string($this->link, $columns[$i]);
				
				$sql .= "`$element`";
				
				// Add comma?
				if(($i + 1) < count($columns))
				{
					$sql .= ", ";
				}
			}
			
			$sql .= ") VALUES (";
			
			// Values to be inserted
			for($i = 0;$i < count($values);$i++)
			{
				$element = $values[$i];
				
				// Is the value a string or an integer?
				if(is_string($element))
				{
					$element = mysqli_real_escape_string($this->link, $values[$i]);
					$sql .= "'$element'";
				}
				else
				{
					$sql .= (string)$element;
				}
				
				// Add comma?
				if(($i + 1) < count($values))
				{
					$sql .= ", ";
				}
			}
			
			$sql .= ")";
			
			// Insert row
			if(!mysqli_query($this->link, $sql))
			{
				printf("MYSQL: Error %s\n", mysqli_error($this->link));
			}
		}
	}
	
?>
