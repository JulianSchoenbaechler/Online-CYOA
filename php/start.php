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
	
	// Get arguments
	$game = trim($_GET['game']);
	$player = null;
	
	// Open database
	$link = DatabaseController::connect();
	
	// New game?
	if($game == 'new')
	{
		$newID = generateID();
		$player = new Player($newID);
		$player->newData($link);
		SessionController::setSessionID($newID);
		
		// Book passwords
		SessionController::setParameter('pw1', 'none');
		SessionController::setParameter('pw2', 'none');
		SessionController::setParameter('pw3', 'none');
		SessionController::setParameter('pw4', 'none');
		SessionController::setParameter('pw5', 'none');
		SessionController::setParameter('pw6', 'none');
		SessionController::setParameter('pw7', 'none');
	}
	else if($game == 'load')
	{
		// Check sessions
		if(SessionController::getSessionID() === false)
		{
			// No saved game for this client -> create new
			$newID = generateID();
			$player = new Player($newID);
			$player->newData($link);
			SessionController::setSessionID($newID);
			
			// Book passwords
			SessionController::setParameter('pw1', 'none');
			SessionController::setParameter('pw2', 'none');
			SessionController::setParameter('pw3', 'none');
			SessionController::setParameter('pw4', 'none');
			SessionController::setParameter('pw5', 'none');
			SessionController::setParameter('pw6', 'none');
			SessionController::setParameter('pw7', 'none');
		}
	}
	else
	{
		// Close database
		DatabaseController::disconnect();
		unset($link);
		
		// Return to index page
		header("Location: ../index.html");
		exit();
	}
	
	// Close database
	DatabaseController::disconnect();
	unset($link);
	
	// Forwarding to gameplay site
	header("Location: ../game.html");
	exit();
	
?>
