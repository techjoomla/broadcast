<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');

class broadcastController extends JController
{
	function display()
	{
		$vName = JRequest::getCmd('view', 'cp');
		$controllerName = JRequest::getCmd( 'controller', 'cp' );
		$settings		=	'';
		$importfields	=	'';
		$approveads	=	'';
		$adorders	=	'';
		$cp = '';
		
		$queue	= JRequest::getCmd('layout');
		if(!$queue)
		{	$layout = 'default';
			switch($vName)
			{
				case 'cp':
				   $cp = true;
				break;
			
				case 'settings':
					$settings	=	true;
				break;
			}
		}
		else
		{
			$layout = $queue;
			$queue	= true;
		}	

		JSubMenuHelper::addEntry(JText::_('BC_CP'), 'index.php?option=com_broadcast&view=cp',$cp);
		JSubMenuHelper::addEntry(JText::_('BC_SETTINGS'), 'index.php?option=com_broadcast&view=settings',$settings);
		JSubMenuHelper::addEntry(JText::_('BC_QUEUE'), 'index.php?option=com_broadcast&view=cp&layout=queue',$queue);			
		switch ($vName)
		{
			case 'cp':
				$mName = 'cp';
				$vLayout = JRequest::getCmd( 'layout', $layout );
			break;
						
			case 'settings':
			default:
				$vName = 'settings';
				$vLayout = JRequest::getCmd( 'layout', $layout );
				$mName = 'settings';
			break;						
		}
	
		$document = &JFactory::getDocument();
		$vType	  = $document->getType();
		$view = &$this->getView( $vName, $vType);
		
		if ($model = &$this->getModel($mName)) 
		{
			$view->setModel($model, true);
		}
		$view->setLayout($vLayout);
		$view->display();
	}// function

	function getVersion()
	{
		echo $recdata = file_get_contents('http://techjoomla.com/vc/index.php?key=abcd1234&product=broadcast');
		jexit();
	}	
}// class
