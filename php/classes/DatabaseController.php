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
			
			// For every value
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
		
		// Update row in database table
		public function updateRow($table, $values = array(), $conditions = array())
		{
			// Check table name
			if(!is_string($table))
			{
				trigger_error("'updateRow' expected argument 0 to be string.", E_USER_WARNING);
			}
			else
			{
				$table = mysqli_real_escape_string($this->link, $table);
			}
			
			$sql = "UPDATE `$table` SET ";
			
			// For every value
			foreach($values as $column => $value)
			{
				// Define columns to insert
				$element = mysqli_real_escape_string($this->link, $column);
				$sql .= "`$element`=";
				
				// Is the value a string or an integer?
				if(is_string($value))
				{
					$element = mysqli_real_escape_string($this->link, $value);
					$sql .= "'$element', ";
				}
				else
				{
					$sql .= (string)$value.", ";
				}
			}
			
			// First fragment of query
			$sql = substr($sql, 0, strlen($sql) - 2);
			
			// Are there conditions for updating?
			if(count($conditions) > 0)
			{
				$sql .= " WHERE ";
				
				// For every condition
				foreach($conditions as $column => $value)
				{
					// Define columns to insert
					$element = mysqli_real_escape_string($this->link, $column);
					$sql .= "`$element`=";
					
					// Is the value a string or an integer?
					if(is_string($value))
					{
						$element = mysqli_real_escape_string($this->link, $value);
						$sql .= "'$element' AND ";
					}
					else
					{
						$sql .= (string)$value." AND ";
					}
				}
				
				// Second fragment of query
				$sql = substr($sql, 0, strlen($sql) - 5);
			}
			else
			{
				$sql .= " WHERE 1";
			}
			
			// Update row
			if(!mysqli_query($this->link, $sql))
			{
				printf("MYSQL: Error %s\n", mysqli_error($this->link));
			}
		}
		
		// Get row content from database table
		public function getRow($table, $conditions = array())
		{
			// Check table name
			if(!is_string($table))
			{
				trigger_error("'getRow' expected argument 0 to be string.", E_USER_WARNING);
			}
			else
			{
				$table = mysqli_real_escape_string($this->link, $table);
			}
			
			$sql = "SELECT * FROM `$table` WHERE ";
			
			// For every condition
			foreach($conditions as $column => $value)
			{
				// Define columns to insert
				$element = mysqli_real_escape_string($this->link, $column);
				$sql .= "`$element`=";
				
				// Is the value a string or an integer?
				if(is_string($value))
				{
					$element = mysqli_real_escape_string($this->link, $value);
					$sql .= "'$element' AND ";
				}
				else
				{
					$sql .= (string)$value." AND ";
				}
			}
			
			// Strip 'AND' from query
			$sql = substr($sql, 0, strlen($sql) - 5);
			$sql .= " LIMIT 1";
			
			$result = mysqli_query($this->link, $sql);
			
			// Check result
			if(!$result)
			{
				printf("MYSQL: Error %s\n", mysqli_error($this->link));
			}
			
			// If row was found
			if(mysqli_num_rows($result) > 0)
			{
				$row = mysqli_fetch_array($result);
				
				mysqli_free_result($result);
				
				// Return database content
				return $row;
			}
			else
			{
				// Not found a matching row
				return null;
			}
		}
	}
	
?>
