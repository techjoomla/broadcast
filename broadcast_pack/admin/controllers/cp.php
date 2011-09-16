<?php
defined('_JEXEC') or die();

class broadcastControllercp extends broadcastController
{
	function __construct()
	{		
		parent::__construct();	
	}
	
	function save()
	{
		switch (JRequest::getCmd('task') ) 
		{
			case 'cancel':
				$this->setRedirect( 'index.php?option=com_broadcast');
			break;
			case 'save':
				if($this->getModel('cp')->store(JRequest::get('post')))
					$msg = JText::_('QUEUE_SAVED');
				else 
					$msg = JText::_('QUEUE_SAVE_PROBLEM');			
				$this->setRedirect( "index.php?option=com_broadcast&view=cp&layout=queue", $msg );
			break;
		}
	}
	function cancel()
	{
	$this->setRedirect( 'index.php?option=com_broadcast');
	}
}
?>
