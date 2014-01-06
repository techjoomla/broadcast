<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class broadcastViewconfig extends JViewLegacy
{
	function display($tpl = null)
	{
		$model	= $this->getModel( 'config' );
		$otherdataArr=$model->renderHTML_other();
		$subscribedlists	= $model->getsubscribedlist();
				
		$this->assignRef('otherdataArr' , $otherdataArr);
		$this->assignRef('subscribedlists' , $subscribedlists);
		$cache = JFactory::getCache('mod_menu');
		$cache->clean();
		parent::display($tpl);
	}
}

