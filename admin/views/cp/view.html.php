<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
class broadcastViewcp extends JViewLegacy
{
	function display($tpl = null)
	{
		$this->_setToolBar();
		if(!JRequest::getVar('layout'))
			$this->setLayout('default');
		else{
			$queue = $this->get('queue');
			$this->assignRef('queues', $queue);
		}
		parent::display($tpl);
	}

	function _setToolBar()
	{
		$document =JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css');
		$bar =JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'COM_BROADCAST_BC_SOCIAL' ), 'icon-48-broadcast.png' );

		if(JRequest::getVar('layout'))
		{
			JToolBarHelper::save('save',JText::_('COM_BROADCAST_BC_TOOL_QUEUE') );
			JToolBarHelper::cancel( 'cancel', JText::_('COM_BROADCAST_BC_CLOSE') );
		}
		if (JFactory::getUser()->authorise('core.admin', 'com_broadcast')) {
			JToolBarHelper::preferences('com_broadcast');
		}
	}
}
?>
