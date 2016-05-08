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
	
	final class Story
	{
		private $id;
		private $title;
		private $text;
		private $answers;
		
		// Class constructor
		public function __construct($id)
		{
			$this->id = $id;
		}
		
		// Load story element
		public function load($link)
		{
			$dc = new DatabaseController($link);
			$dbArray = $dc->getRow('story', array('id' => $this->id));
			
			// Load player data
			if(!is_null($dbArray))
			{
				$this->points = (string)$dbArray['title'];
				$this->points = (string)$dbArray['text'];
				$this->answers = json_decode($dbArray['answers']);
			}
			
			unset($dc);
		}
		
		// Create story element
		public function create($link, $title, $text, $answers)
		{
			// Check arguments
			if(!is_string($title))
			{
				trigger_error("'create' expected argument 1 to be string.", E_USER_WARNING);
			}
			if(!is_string($text))
			{
				trigger_error("'create' expected argument 2 to be string.", E_USER_WARNING);
			}
			
			// Update private variables
			$this->title = $title;
			$this->text = $text;
			$this->answers = $answers;
			
			// Database insert new story element
			$dc = new DatabaseController($link);
			$insert = array('id' => $this->id,
							'title' => $this->title,
							'text' => $this->title,
							'answers' => json_encode($this->answers)
							);
			$dc->insertRow('story', $insert);
			
			unset($dc);
		}
		
		// Get title of story element
		public function getTitle()
		{
			return $this->title;
		}
		
		// Get text of story element
		public function getText()
		{
			return $this->text;
		}
		
		// Get associative array of answers encoded as JSON string
		public function getAnswers()
		{
			return json_encode($this->answers);
		}
	}
	
?>
