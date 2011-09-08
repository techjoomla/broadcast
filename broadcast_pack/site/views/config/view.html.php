<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class broadcastViewconfig extends JView
{
	function display($tpl = null)
	{
		require_once (JPATH_COMPONENT_SITE.DS.'helper.php');
		$data	= combroadcastHelper::getInfo();
		$model	= $this->getModel( 'config' );
		$lists	= $model->getlist();
		$subscribedlists	= $model->getsubscribedlist();
				
		$this->assignRef('lists' , $lists);
		$this->assignRef('data' , $data);
		$this->assignRef('subscribedlists' , $subscribedlists);

		parent::display($tpl);
	}
}

