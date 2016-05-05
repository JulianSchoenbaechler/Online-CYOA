<?php
	// This file exists only for testing purposes...
	namespace CYOA_Engine;
	
	require_once 'php/Includes.php';
	
	
	// Database connect
	$link = DatabaseController::connect();
	echo $link;
	
	$myPlayer = new Player(0);
	
	$myPlayer->newData($link);
	
	DatabaseController::disconnect($link);
?>
