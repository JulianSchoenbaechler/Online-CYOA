<?php
	// This file exists only for testing purposes...
	namespace CYOA_Engine;
	
	// Error reporting
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	require_once 'php/Includes.php';
	
	
	// Database connect
	$link = DatabaseController::connect();
	
	echo 'hello!';
	$myPlayer = new Player(0);
	
	$myPlayer->newData($link);
	
	DatabaseController::disconnect($link);
?>
