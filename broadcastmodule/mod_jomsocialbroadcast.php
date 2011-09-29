<?php
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'helper.php');
$user=JFactory::getUser();
if($user->id){
	$data = array();
	$data['pretext']=trim( $params->get('pretext') );
	$data['posttext']=trim( $params->get('posttext') );
	$align	= trim( $params->get('show_horizontal') );
	$show_rss	= trim( $params->get('show_rss') );
	
	$apidata = combroadcastHelper::getapistatus();
	require( JModuleHelper::getLayoutPath( 'mod_jomsocialbroadcast' ) );
}
?>