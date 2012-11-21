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
	$lang->load('plug_techjoomlaAPI_gmail', JPATH_ADMINISTRATOR);
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
					
					if($this->id=='jform_params_pathapi_gmail')
						$return	=	'<div style="clear:both"></div>
											<div class="instructions">
											Go to <a href="https://www.google.com/accounts/ManageDomains" target="_blank">https://www.google.com/accounts/ManageDomains</a>.<br />
											Enter <input style="float:none" type="text" value="'. str_replace( "http://",'',JURI::root()). '" readonly="true"/> for the Add domain.<br />
											Scroll down to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Manage Registration</span>.<br />
											Click on the domain you entered.<br />
											Follow the given instructions to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Verify Ownership</span>.<br />
											Click on <input style="float:none" type="button" value="Verify"/>
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for the <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> Target URL path prefix</span>.<br />
											Click <input style="float:none" type="button" value="Save"/>.<br />
											Copy the values of <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> OAuth consumer Key </span> and <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> OAuth Consumer Secret </span> to their respective fields above.<br />
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
											Go to <a href="https://www.google.com/accounts/ManageDomains" target="_blank">https://www.google.com/accounts/ManageDomains</a>.<br />
											Enter <input style="float:none" type="text" value="'. str_replace( "http://",'',JURI::root()). '" readonly="true"/> for the Add domain.<br />
											Scroll down to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Manage Registration</span>.<br />
											Click on the domain you entered.<br />
											Follow the given instructions to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Verify Ownership</span>.<br />
											Click on <input style="float:none" type="button" value="Verify"/>
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for the <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> Target URL path prefix</span>.<br />
											Click <input style="float:none" type="button" value="Save"/>.<br />
											Copy the values of <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> OAuth consumer Key </span> and <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> OAuth Consumer Secret </span> to their respective fields above.<br />
											</div>';
						return $return;
				
				}//function
		}//class

	}



?>
