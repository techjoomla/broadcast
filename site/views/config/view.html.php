<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class broadcastViewconfig extends JView
{
	function display($tpl = null)
	{
		$model	= $this->getModel( 'config' );
		$otherdataArr=$model->renderHTML_other();
		$lists	= $model->getlist();
		$subscribedlists	= $model->getsubscribedlist();
				
		$this->assignRef('otherdataArr' , $otherdataArr);
		$this->assignRef('lists' , $lists);
		$this->assignRef('subscribedlists' , $subscribedlists);
$cache = JFactory::getCache('mod_menu');
$cache->clean();

		$cache->clean();

		parent::display($tpl);
	}
}

