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
	
	// Get arguments
	$id = trim($_GET['id']);
	
	// Displayed elements
	$title = "";
	$content = "";
	$answer1 = "";
	$answer2 = "";
	$link1 = "";
	$link2 = "";
	$error = "";
	
	if(isset($_GET['id']) && (strlen($id) > 0))
	{
		// Open database
		$link = DatabaseController::connect();
		
		$id = mysqli_real_escape_string($link, $id);
		
		// Query
		$sql = "SELECT * FROM `story` WHERE `id`='$id' LIMIT 1";
		
		$result = mysqli_query($link, $sql);
		
		// Check result
		if(!$result)
		{
			printf("MYSQL: Error %s\n", mysqli_error($link));
		}
		
		// If row was found
		if(mysqli_num_rows($result) > 0)
		{
			$row = mysqli_fetch_array($result);
			
			// Get data
			$title = $row['title'];
			$content = $row['text'];
			$answers = json_decode($row['answers'], true);
			
			if(Count($answers) > 0)
			{
				$answer1 = $answers[0]['answer'];
				$link1 = $answers[0]['id'];
			}
			
			if(Count($answers) > 1)
			{
				$answer2 = $answers[1]['answer'];
				$link2 = $answers[1]['id'];
			}
			
			mysqli_free_result($result);
		}
		else
		{
			$error = "No database entry found!<br /><br />";
		}
		
		// Close database
		DatabaseController::disconnect();
		unset($link);
	}
	
	// Display page
	$output = file_get_contents('template/insert.html');
	$output = str_replace('$=id=$', $id, $output);
	$output = str_replace('$=title=$', $title, $output);
	$output = str_replace('$=content=$', $content, $output);
	$output = str_replace('$=answer1=$', $answer1, $output);
	$output = str_replace('$=answer2=$', $answer2, $output);
	$output = str_replace('$=id1=$', $link1, $output);
	$output = str_replace('$=id2=$', $link2, $output);
	$output = str_replace('$=error=$', $error, $output);
	echo $output;
	
	exit();
	
?>
