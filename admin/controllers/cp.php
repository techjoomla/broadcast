<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
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
					$msg = JText::_('COM_BROADCAST_QUEUE_SAVED');
				else 
					$msg = JText::_('COM_BROADCAST_QUEUE_SAVE_PROBLEM');			
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
