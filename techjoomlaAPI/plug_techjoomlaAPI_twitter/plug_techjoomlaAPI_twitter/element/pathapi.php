<?php 
 /**
	* @package JomSocial Network Suggest
	* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link     http://www.techjoomla.com
	*/ 

	// Check to ensure this file is within the rest of the framework
	defined('JPATH_BASE') or die();
	jimport("joomla.html.parameter.element");
	$lang = & JFactory::getLanguage();
	$lang->load('plug_techjoomlaAPI_twitter', JPATH_ADMINISTRATOR);
	class JElementpathapi extends JElement
	{
		var $_name = 'Pathapi';
		function fetchElement($name, $value, &$node, $control_name)
		{

			return '<a href="https://dev.twitter.com/apps/" target="_blank">'.JText::_('CLK_HERE_API_KEY').'</a>';
		}
	}
?>
