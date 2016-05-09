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
	
	class SessionController
	{
		// Properties
		private static $initialized = false;
		private static $id;
		
		// Initialization
		private static function init()
		{
			if(self::$initialized)
				return;
			
			
			session_start();
			
			if(isset($_SESSION['CYOA_Engine']['id']))
			{
				$id = $_SESSION['CYOA_Engine']['id'];
			}
			
			session_write_close();
			
			$initialized = true;
		}
		
		// Get session id
		public static function getSessionID()
		{
			self::init();
			
			if(isset(self::$id))
			{
				return self::$id;
			}
			
			return false;
		}
		
		// Set or overwrite session id
		public static function setSessionID($id)
		{
			self::init();
			
			session_start();
			$_SESSION['CYOA_Engine']['id'] = $id;
			session_write_close();
			
			self::$id = $id;
		}
		
		// Get a specific session parameter by name
		public static function getParameter($parameter)
		{
			self::init();
			
			// Check parameter name
			if(!is_string($parameter))
			{
				trigger_error("'getParameter' expected argument 0 to be string.", E_USER_WARNING);
			}
			
			// Check session parameter and return
			session_start();
			if(isset($_SESSION['CYOA_Engine'][$parameter]))
			{
				return $_SESSION['CYOA_Engine'][$parameter];
			}
			session_write_close();
			
			return false;
		}
		
		// Set or overwrite a specific session parameter by name
		public static function setParameter($parameter, $value)
		{
			self::init();
			
			// Check parameter name and value
			if(!is_string($parameter))
			{
				trigger_error("'setParameter' expected argument 0 to be string.", E_USER_WARNING);
			}
			if(!is_string($value))
			{
				trigger_error("'setParameter' expected argument 1 to be string.", E_USER_WARNING);
			}
			
			// Set session parameter
			session_start();
			$_SESSION['CYOA_Engine'][$parameter] = (string)$value;
			session_write_close();
		}
		
		// Delete current session
		public static function destroySession()
		{
			self::init();
			
			session_start();
			
			// Unset custom session variables
			foreach($_SESSION['CYOA_Engine'] as $parameter => $value)
			{
				unset($_SESSION['CYOA_Engine'][$parameter]);
			}
			
			// Complete destroying session and session cookies
			session_unset();
			session_destroy();
			session_write_close();
			setcookie(session_name(),'',0,'/');
			session_regenerate_id(true);
		}
	}
	
?>
