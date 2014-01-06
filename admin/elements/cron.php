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

class JFormFieldCron extends JFormField
{
	function getInput()
	{
		$this->fetchElement($this->name,$this->value,$this->element,$this->options['controls']);
	}

	function fetchElement($name,$value,&$node,$control_name)
	{
		$params=JComponentHelper::getParams('com_broadcast');
		$private_key_cronjob=$params->get('private_key_cronjob');
		if($name=='jform[cron_get]')
			echo "<label>".JUri::root().'index.php?option=com_broadcast&controller=broadcast&task=get_status&tmpl=component&pkey='.$private_key_cronjob."</label>";//die;
		else if($name=='jform[cron_set]')
			echo "<label>".JUri::root().'index.php?option=com_broadcast&controller=broadcast&task=set_status&tmpl=component&pkey='.$private_key_cronjob."</label>";//die;;
		else if($name=='jform[cron_rss]')
			echo "<label>".JURI::root().'index.php?option=com_broadcast&task=getrssdata&controller=rss&tmpl=component&pkey='.$private_key_cronjob."</label>";//die;;
	}
}
?>
