<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');
class BroadcastController extends JController
{
	function display()
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
}		
?>
