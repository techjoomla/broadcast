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
	jimport('joomla.html.html');
	jimport('joomla.form.formfield');

	$lang = & JFactory::getLanguage();
	$lang->load('plug_techjoomlaAPI_twitter', JPATH_ADMINISTRATOR);
	if(JVERSION>=1.6)
	{
			class JFormFieldPathapi extends JFormField
			{
				/**
				 * The form field type.
				 *
				 * @var		string
				 * @since	1.6
				 */
				public $type = 'Pathapi';

				/**
				 * Method to get the field input markup.
				 *
				 * TODO: Add access check.
				 *
				 * @return	string	The field input markup.
				 * @since	1.6
				 */
				 
				protected function getInput()
				{
					
					if($this->id=='jform_params_pathapi_twitter')
						$return	=	'<div style="clear:both"></div>
											<div class="instructions">
											Go to <a href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>.<br />
											Give name of your site for <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Application Name</span><br />
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for  <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">website</span> and  <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Callback URL</span><br />
											Accept the "Developer Rules of the Road".<br /> 
											Click on <input style="float:none" type="button" value="Create your Twitter Application"/><br />
											Click on  <input style="float:none" type="button" value=" settings tab "/> of  Twitter Apps.<br />
											Scroll down to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Application type</span><br />
											Select <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Read, Write and Access direct messages</span><br />
											Click <input style="float:none" type="button" value="Update this Twitter Application setting"/>.<br />
											Copy the values of <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Consumer key</span> and <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Consumer secret</span> to their respective fields above.<br />
											</div>';
						return $return;
		
				} //function
				
			}
	}
	else
	{
		class JElementPathapi extends JElement
		{
		
				var $_name = 'pathapi';
				function fetchElement($name, $value, &$node, $control_name)
				{
					$return	=	'<div style="clear:both"></div>
											<div class="instructions">
											Go to <a href="https://dev.twitter.com/apps" target="_blank">https://dev.twitter.com/apps</a>.<br />
											Give name of your site for <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Application Name</span><br />
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for  <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">website</span> and  <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Callback URL</span><br />
											Accept the "Developer Rules of the Road".<br /> 
											Click on <input style="float:none" type="button" value="Create your Twitter Application"/><br />
											Click on  <input style="float:none" type="button" value=" settings tab "/> of  Twitter Apps.<br />
											Scroll down to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Application type</span><br />
											Select <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Read, Write and Access direct messages</span><br />
											Click <input style="float:none" type="button" value="Update this Twitter Application setting"/>.<br />
											Copy the values of <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Consumer key</span> and <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Consumer secret</span> to their respective fields above.<br />
											</div>';
						return $return;
				
				}//function
		}//class

	}


