<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');
class BroadcastController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	function save()
	{
		$model	= $this->getModel( 'config' );
		if($model->save())
			$msg = JText::_( 'CONFIG_SAV' );
		else
			$msg = JText::_( 'ERR_CONFIG_SAV' );

		$this->setRedirect( JURI::base()."index.php?option=com_broadcast&view=config", $msg );
	}

	function saveotheraccounts()
	{

		$model	= $this->getModel( 'config' );
		$res=$model->saveotheraccounts();
		$msg = JText::_( 'CONFIG_SAV' );

		$this->setRedirect( JURI::base()."index.php?option=com_broadcast&view=config", $msg );
	echo '<script type="text/javascript">
               window.setTimeout("closeme();");
               function closeme()
               {


                    parent.SqueezeBox.close();
               }
               </script>';


	}



}
?>
