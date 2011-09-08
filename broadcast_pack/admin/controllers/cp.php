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
		$app	= JFactory::getApplication();
		if($this->getModel('cp')->store(JRequest::get('post')))
		{
			$app->redirect('index.php?option=com_broadcast&view=cp&layout=queue');
		}
	}
	
}
?>
