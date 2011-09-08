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
		parent::display($tpl);
	}
	
	function _setToolBar()
	{	
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css'); 
		$bar =& JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'Social Broadcast' ), 'icon-48-social.png' );
		
		if(JRequest::getVar('layout'))
		{
			JToolBarHelper::save();
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}	
	}
}
?>
