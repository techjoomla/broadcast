<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined('_JEXEC') or die('Restricted access');

$path = dirname(__FILE__).DS.'helper.php';

if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
 
// require helper file

#@TODO check this imp for version 1.0.1
if(!class_exists('techjoomlaHelperLogs'))
{
  //require_once $path;
   JLoader::register('techjoomlaHelperLogs', $path );
   JLoader::load('techjoomlaHelperLogs');
}
#@TODO check this imp for version 1.0.1
if(!class_exists('combroadcastHelper'))
{
  //require_once $path;
   JLoader::register('combroadcastHelper', $path );
   JLoader::load('combroadcastHelper');
}


require_once (JPATH_COMPONENT.DS.'controller.php');
if(phpversion()>=5.3)
{
	include_once JPATH_ROOT.'/media/techjoomla_strapper/strapper.php';
	TjAkeebaStrapper::bootstrap();
}

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}
$classname	= 'BroadcastController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute(JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
?>
