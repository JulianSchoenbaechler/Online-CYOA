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
	
	final class History
	{
		public $element;		// Associative array of this history element
		
		public function __construct($layer, $position, $branches)
		{
			// Create associative array
			$this->element = array('layer' => (int)$layer, 'position' => (int)$position, 'branches' => (string)$branches);
		}
		
	}
	
?>
