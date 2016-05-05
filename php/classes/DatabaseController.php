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
		public function __construct($link)
		{
			$this->link = $link;
		}
		
		// Insert row into database table
		public function insertRow($table, $values = array())
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
			
			$sql1 = "INSERT INTO `$table`(";
			$sql2 = ") VALUES (";
			
			foreach($values as $column => $value)
			{
				// Define columns to insert
				$element = mysqli_real_escape_string($this->link, $column);
				$sql1 .= "`$element`, ";
				
				// Is the value a string or an integer?
				if(is_string($value))
				{
					$element = mysqli_real_escape_string($this->link, $value);
					$sql2 .= "'$element', ";
				}
				else
				{
					$sql2 .= (string)$value.", ";
				}
			}
			
			// Create query
			$sql1 = substr($sql1, 0, strlen($sql1) - 2);
			$sql2 = substr($sql2, 0, strlen($sql2) - 2);
			$sql = $sql1.$sql2.")";
			
			// Insert row
			if(!mysqli_query($this->link, $sql))
			{
				printf("MYSQL: Error %s\n", mysqli_error($this->link));
			}
		}
	}
	
?>
