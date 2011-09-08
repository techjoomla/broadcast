<?php
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'helper.php');

$type	= trim( $params->get('show_horizontal') );
$pretext	= trim( $params->get('pretext') );
$posttext	= trim( $params->get('posttext') );
$data = combroadcastHelper::getInfo();
$data['pretext']=$pretext;
$data['posttext']=$posttext;

if($type==1)
	require( JModuleHelper::getLayoutPath('mod_jomsocialbroadcast', 'horizontal'));
else
  require( JModuleHelper::getLayoutPath( 'mod_jomsocialbroadcast','vertical' ) );
  
?>

