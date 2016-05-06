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
	
	// Empty process associative array (memory or experience)
	define('EMPTY_PROCESS', array(
								  'intro' => false,
								  'outro' => false,
								  ));
	
	// Empty history (associative array)
	define('EMPTY_HISTORY', array(
								  'layer' => false,
								  'position' => false,
								  'branches' => 'branch0.png'
								  ));
	
?>
