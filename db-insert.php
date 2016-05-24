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
	
	// Variables
	$characters = array('none', 'gardai', 'declan', 'amelia', 'masahiro', 'antoine', 'fatime');
	$sounds = array('none', '', '', '', '', '', '');
	
	// Post arguments
	$post_id = trim($_POST['id']);
	$post_title = trim($_POST['title']);
	$post_content = trim($_POST['content']);
	$post_answer1 = trim($_POST['answer1']);
	$post_answer2 = trim($_POST['answer2']);
	$post_link1 = trim($_POST['id1']);
	$post_link2 = trim($_POST['id2']);
	$post_character1 = trim($_POST['character1']);
	$post_character2 = trim($_POST['character2']);
	$post_ambient = trim($_POST['ambient']);
	
	// Displayed elements
	$title = $post_title;
	$content = $post_content;
	$answer1 = $post_answer1;
	$answer2 = $post_answer2;
	$link1 = $post_link1;
	$link2 = $post_link2;
	$character1 = $post_character1;
	$character2 = $post_character2;
	$ambient = $post_ambient;
	$error = "";
	
	// Complete dataset?
	if((isset($_POST['id']) && (strlen($post_id) > 0)) &&
	   (isset($_POST['title']) && (strlen($post_title) > 0)) &&
	   (isset($_POST['content']) && (strlen($post_content) > 0)) &&
	   (isset($_POST['answer1']) && (strlen($post_answer1) > 0)) &&
	   (isset($_POST['id1']) && (strlen($post_link1) > 0)))
	{
		// Open database
		$link = DatabaseController::connect();
		$answers = array();
		
		$post_answer1 = htmlentities($post_answer1, ENT_QUOTES);
		
		$post_id = mysqli_real_escape_string($link, $post_id);
		$post_title = mysqli_real_escape_string($link, $post_title);
		$post_content = mysqli_real_escape_string($link, $post_content);
		$post_answer1 = mysqli_real_escape_string($link, $post_answer1);
		$post_link1 = mysqli_real_escape_string($link, $post_link1);
		$post_character1 = mysqli_real_escape_string($link, $post_character1);
		$post_character2 = mysqli_real_escape_string($link, $post_character2);
		$post_ambient = mysqli_real_escape_string($link, $post_ambient);

		// ID formatting
		$post_id = str_replace(' ', '', strtolower($post_id));
		$post_link1 = str_replace(' ', '', strtolower($post_link1));
		
		$answers[0]['id'] = $post_link1;
		$answers[0]['answer'] = $post_answer1;
		
		// Second answer?
		if((isset($_POST['answer2']) && (strlen($post_answer2) > 0)) &&
		   (isset($_POST['id2']) && (strlen($post_link2) > 0)))
		{
			$post_answer2 = htmlentities($post_answer2, ENT_QUOTES);
			
			$post_answer2 = mysqli_real_escape_string($link, $post_answer2);
			$post_link2 = mysqli_real_escape_string($link, $post_link2);
			$post_link2 = str_replace(' ', '', strtolower($post_link2));
		
			$answers[1]['id'] = $post_link2;
			$answers[1]['answer'] = $post_answer2;
		}
		
		// Query
		$sql = "SELECT * FROM `story` WHERE `id`='$post_id' LIMIT 1";
		
		$result = mysqli_query($link, $sql);
		
		// Check result
		if(!$result)
		{
			printf("MYSQL: Error %s\n", mysqli_error($link));
		}
		
		// If row was found
		if(mysqli_num_rows($result) > 0)
		{
			$sql = "UPDATE `story` SET `title`='$post_title',`text`='$post_content',`answers`='".json_encode($answers)."', `character1`='$post_character1', `character2`='$post_character2', `ambient`='$post_ambient' WHERE `id`='$post_id' LIMIT 1";
		}
		else
		{
			$sql = "INSERT INTO `story`(`id`, `title`, `text`, `answers`, `character1`, `character2`, `ambient`) VALUES ('$post_id','$post_title','$post_content','".json_encode($answers)."','$post_character1','$post_character2','$post_ambient')";
		}
		
		mysqli_free_result($result);
		
		// Insert
		if(!mysqli_query($link, $sql))
		{
			printf("MYSQL: Error %s\n", mysqli_error($link));
		}
		
		// Close database
		DatabaseController::disconnect();
		unset($link);
		
		// Redirect
		header("Location: db.php");
		exit();
	}
	else
	{
		$error = 'Dataset not complete!<br /><br />';
	}
	
	// Display page
	$output = file_get_contents('template/insert.html');
	$output = str_replace('$=id=$', $post_id, $output);
	$output = str_replace('$=title=$', $title, $output);
	$output = str_replace('$=content=$', $content, $output);
	$output = str_replace('$=answer1=$', $answer1, $output);
	$output = str_replace('$=answer2=$', $answer2, $output);
	$output = str_replace('$=id1=$', $link1, $output);
	$output = str_replace('$=id2=$', $link2, $output);
	
	// Character 1
	for($i = 1;$i <= 7;$i++)
	{
		if($characters[$i] == $post_character1)
		{
			$output = str_replace('$=c1'.(string)$i.'=$', 'required', $output);
		}
		else
		{
			$output = str_replace('$=c1'.(string)$i.'=$', '', $output);
		}
	}
	
	// Character 2
	for($i = 1;$i <= 7;$i++)
	{
		if($characters[$i] == $post_character2)
		{
			$output = str_replace('$=c2'.(string)$i.'=$', 'required', $output);
		}
		else
		{
			$output = str_replace('$=c2'.(string)$i.'=$', '', $output);
		}
	}
	
	// Ambient sound
	for($i = 1;$i <= 7;$i++)
	{
		if($sounds[$i] == $post_ambient)
		{
			$output = str_replace('$=a'.(string)$i.'=$', 'required', $output);
		}
		else
		{
			$output = str_replace('$=a'.(string)$i.'=$', '', $output);
		}
	}
	
	$output = str_replace('$=error=$', $error, $output);
	echo $output;
	
	exit();
	
?>
