<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
class broadcastViewcp extends JView
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
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css'); 
		$bar =& JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'BC_SOCIAL' ), 'icon-48-broadcast.png' );
		
		if(JRequest::getVar('layout'))
		{
			JToolBarHelper::save('save',JText::_('BC_QUEUE') );
			JToolBarHelper::cancel( 'cancel', JText::_('BC_CLOSE') );
		}	
	}
}
?>
