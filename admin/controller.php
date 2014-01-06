<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');

class broadcastController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false)
	{
		$vName = JRequest::getCmd('view', 'cp');
		$controllerName = JRequest::getCmd( 'controller', 'cp' );
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
			}
		}
		else
		{
			$layout = $queue;
			$queue	= true;
		}	

		JSubMenuHelper::addEntry(JText::_('COM_BROADCAST_BC_CP'), 'index.php?option=com_broadcast&view=cp',$cp);
		JSubMenuHelper::addEntry(JText::_('COM_BROADCAST_BC_QUEUE'), 'index.php?option=com_broadcast&view=cp&layout=queue',$queue);			
		switch ($vName)
		{
			case 'cp':
				$mName = 'cp';
				$vLayout = JRequest::getCmd( 'layout', $layout );
			break;

			default:
				$vLayout = JRequest::getCmd( 'layout', $layout );
			break;						
		}
	
		$document = JFactory::getDocument();
		$vType	  = $document->getType();
		$view = $this->getView( $vName, $vType);
		
		if ($model =$this->getModel($mName)) 
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
