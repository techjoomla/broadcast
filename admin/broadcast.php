<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined( '_JEXEC' ) or die( ';)' );

if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
 
// require helper file
JLoader::register('OlaHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'ola.php');
require_once( JPATH_COMPONENT.DS.'controller.php' );
//require_once( JPATH_COMPONENT.DS.'config'.DS.'config.php' ); 

if( $controller = JRequest::getWord('controller'))
	{
		$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
		if( file_exists($path))
			require_once $path;
		else
			$controller = '';
	}
// Create the controller
$classname    = 'broadcastController'.$controller;
$controller   = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
