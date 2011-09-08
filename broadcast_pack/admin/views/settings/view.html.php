<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class broadcastViewsettings extends JView
{
	function display($tpl = null)
	{
	 	JHTML::_('behavior.mootools');
		$this->_setToolBar();
		$this->setLayout('settings');
		parent::display($tpl);
	}
	function _setToolBar()
	{	
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css'); 
		$bar =& JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'Social Broadcast' ), 'icon-48-social.png' );
		JToolBarHelper::save();
		JToolBarHelper::cancel( 'cancel', 'Close' );
	}
}
?>
