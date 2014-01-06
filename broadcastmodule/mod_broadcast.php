<?php
 /**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
 if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'helper.php');
$user=JFactory::getUser();
if($user->id){
	$data = array();
	$data['pretext']=trim( $params->get('pretext') );
	$data['posttext']=trim( $params->get('posttext') );
	$align	= trim( $params->get('show_horizontal') );
	$show_rss	= trim( $params->get('show_rss') );

	$combroadcastHelper=new combroadcastHelper();
	$apidata = $combroadcastHelper->getapistatus();
	require(JModuleHelper::getLayoutPath( 'mod_broadcast'));
}
else
	echo JText::_('BC_USER_LOGIN');

