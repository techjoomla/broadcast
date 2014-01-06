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

jimport('joomla.form.formfield');
//jimport('joomla.form.formfield');

class JFormFieldApilist extends JFormField
{
	function getInput()
	{
		return $this->fetchElement($this->name,$this->value,$this->element,$this->options['controls']);
	}

	function fetchElement($name,$value,&$node,$control_name)
	{
		$options = array();
		$api_plg_installed=$this->getAPIpluginData();
		foreach($api_plg_installed as $api)
		{
			$apinames=ucfirst(str_replace('plug_techjoomlaAPI_', '',$api->element));
			$options[] =JHtml::_('select.option',$api->element, $apinames);
		}
		$fieldName = $name;
		return JHtml::_('select.genericlist',  $options, $fieldName, 'class="inputbox"  multiple="multiple" size="5"  ', 'value', 'text', $value, $control_name.$name ); 
	}

	function getAPIpluginData()
	{
		$db=JFactory::getDBO();
		$condtion = array(0 => '\'techjoomlaAPI\'');
		$condtionatype = join(',',$condtion);  
		$query = "SELECT extension_id as id,name,element,enabled as published FROM #__extensions WHERE folder in ($condtionatype) AND enabled=1";
		$db->setQuery($query);
		return $db->loadobjectList();
	}

}

