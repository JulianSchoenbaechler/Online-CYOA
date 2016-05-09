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
	
	// Function to generate a new random player id
	function generateID()
	{
		$characters = 'abcdefghijklmnopqrstuvwxyz';
		$newID = 'id_';
	
		for($i = 0;$i < 6;$i++)
		{
			if(mt_rand(0, 1) == 1)
			{
				$newID .= $characters[mt_rand(0, 25)];
			}
			else
			{
				$newID .= (string)mt_rand(0, 9);
			}
		}
		
		return $newID;
	}
	
	
	
	// Include library files
	require_once 'Includes.php';
	
	// Open database
	$link = DatabaseController::connect();
	
	$player = new Player(generateID());
	$player->newData($link);
	
	// Close database
	DatabaseController::disconnect();
	unset($link);
	
?>
