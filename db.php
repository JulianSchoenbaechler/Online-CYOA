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
	
	// Include library files
	require_once 'php/Includes.php';
	
	// Displayed list
	$list = "";
	
	// Open database
	$link = DatabaseController::connect();
	
	// Query
	$sql = 'SELECT * FROM `story` WHERE 1 ORDER BY `id` ASC';
	
	$result = mysqli_query($link, $sql);
	
	// Check result
	if(!$result)
	{
		printf("MYSQL: Error %s\n", mysqli_error($link));
	}
	
	// If row was found
	if(mysqli_num_rows($result) > 0)
	{
		// Display all database results
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$list .= '<a href="insert.php?id='.$row['id'].'">'.$row['id'].'</a><br />';
		}
		
		mysqli_free_result($result);
	}
	
	// Display page
	$output = file_get_contents('template/list.html');
	$output = str_replace('$=list=$', $list, $output);
	echo $output;
	
	// Close database
	DatabaseController::disconnect();
	unset($link);
	
	exit();
	
?>
