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
	
	trait DatabaseConnection
	{
		// Open connection
		public static function connect()
		{
			// Database config
			$sqlconfig = array();
			$sqlconfig['username']	= 'root';
			$sqlconfig['password']	= 'root';
			$sqlconfig['host']		= 'localhost';
			$sqlconfig['dbname']	= 'multilinear';
			$sqlconfig['port']		= 3306;
			
			$link = mysqli_init();
			$success = true;
			
			// Connect with MySQL database
			if(is_null($sqlconfig['port']))
			{
				$success = mysqli_real_connect(
											   $link,
											   $sqlconfig['host'],
											   $sqlconfig['username'],
											   $sqlconfig['password'],
											   $sqlconfig['dbname'],
											   $sqlconfig['port']
				);
			}
			else
			{
				$success = mysqli_real_connect(
											   $link,
											   $sqlconfig['host'],
											   $sqlconfig['username'],
											   $sqlconfig['password'],
											   $sqlconfig['dbname']
				);
			}
			
			unset($sqlconfig);
	
			// Check connection
			if(!$success) {
				printf("Error: Unable to connect to MySQL.".PHP_EOL);
				printf("Debugging errno: ".mysqli_connect_errno().PHP_EOL);
				printf("Debugging error: ".mysqli_connect_error().PHP_EOL);
				exit();
			}
			
			// Set encoding
			if(!mysqli_set_charset($link, "utf8")) {
				printf("MYSQL: Error loading character set utf8: %s\n", mysqli_error($link));
				exit();
			}
			
			return $link;
		}
		
		// Close specific connection
		public static function disconnect($link)
		{
			mysqli_close($link);
		}
	}
	
?>
