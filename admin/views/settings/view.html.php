<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class broadcastViewsettings extends JView
{
	function display($tpl = null)
	{
	 	JHTML::_('behavior.mootools');
		$this->_setToolBar();
		$apiplugin = $this->get('APIpluginData');
		$this->assignRef('apiplugin', $apiplugin);
		$this->setLayout('settings');
		parent::display($tpl);
	}
	function _setToolBar()
	{	
		$document =& JFactory::getDocument();
		$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css'); 
		$bar =& JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'BC_SOCIAL' ), 'icon-48-broadcast.png' );
		JToolBarHelper::save('save',JText::_('BC_SAVE') );
		JToolBarHelper::cancel( 'cancel', JText::_('BC_CLOSE') );
	}
}
?>