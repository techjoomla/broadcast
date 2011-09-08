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
		$model				= $this->getModel( 'config' );
		if($model->save())
			$msg = JText::_( 'Configuration Saved.' );
		else
			$msg = JText::_( 'Error In  Saving Configuration.' );
			
		$this->setRedirect( JURI::base()."index.php?option=com_broadcast&view=config", $msg );
	}
}		
?>
