<?php 
 /**
	* @package JomSocial Network Suggest
	* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link     http://www.techjoomla.com
	*/ 

	// Check to ensure this file is within the rest of the framework
	defined('_JEXEC') or die( 'Restricted access' );
	jimport("joomla.html.parameter.element");


	class JElementPathapi extends JElement
	{
		var $_name = 'Pathapi';
		function fetchElement($name, $value, &$node, $control_name)
		{

			return '<a href="https://www.linkedin.com/secure/developer" target="_blank">'.JText::_('API_KEY_PATH').'</a>';
		}
	}

