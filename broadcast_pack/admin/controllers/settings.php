<?php
defined('_JEXEC') or die();

class broadcastControllerSettings extends broadcastController
{
	function __construct()
	  {	
		parent::__construct();
	  }
	
	function save()
	 {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model	=& $this->getModel( 'settings' );
		$post	= JRequest::get('post');
		$model->setState( 'request', $post );	

		if ($model->store()) 
			$msg = JText::_( 'C_SAVE_M_S' );
		else 
			$msg = JText::_( 'C_SAVE_M_NS' );
		
	    switch ( $this->_task ) 
		{
			case 'cancel':
			$cancelmsg = JText::_( 'FIELD_CANCEL_MSG' );
			$this->setRedirect( 'index.php?option=com_broadcast', $msg );
			break;
			case 'save':
			$this->setRedirect( "index.php?option=com_broadcast&view=settings", $msg );
			break;
		}
			
		
	 }//function save ends
}
?>
