<?php
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'helper.php');
$user=JFactory::getUser();
if($user->id){
$align	= trim( $params->get('show_horizontal') );
$pretext	= trim( $params->get('pretext') );
$posttext	= trim( $params->get('posttext') );
$data = combroadcastHelper::getInfo();
$data['pretext']=$pretext;
$data['posttext']=$posttext;


  require( JModuleHelper::getLayoutPath( 'mod_jomsocialbroadcast' ) );
}
  
?>

