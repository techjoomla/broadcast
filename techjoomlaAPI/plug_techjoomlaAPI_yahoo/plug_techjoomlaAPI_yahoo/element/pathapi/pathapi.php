
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
	$lang->load('plug_techjoomlaAPI_yahoo', JPATH_ADMINISTRATOR);
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
					
					if($this->id=='jform_params_pathapi_yahoo')
					$return	=	'<div style="clear:both"></div>
											<div class="instructions">
											Login to <a href="https://developer.apps.yahoo.com/projects" target="_blank">https://developer.apps.yahoo.com/projects</a>.<br />
											Click on  <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">My projects</span>.<br />
											Click on <input style="float:none" type="button" value="New project"/>.<br />
											Choose the type of Application as <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Standard</span>.<br />
											Click <input style="float:none" type="button" value="Continue"/>.<br />
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for Application URL.<br />
											Scroll down to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Security and privacy</span>.<br />
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for Application Domain.<br />
											Select <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">This app requires access to private user data.</span>as Access Scopes:<br />
											Click <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Contacts</span> to expand it.<br />
											Select <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">	Read/Write </span><br />
											Accept the "Terms of use"<br />
											Click <input style="float:none" type="button" value="Get API keys"/>.<br />
											Copy the values of <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> Application ID </span><span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> Consumer Key </span> and <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Consumer Secret</span> to their respective fields above.<br />
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
											Login to <a href="https://developer.apps.yahoo.com/projects" target="_blank">https://developer.apps.yahoo.com/projects</a>.<br />
											Click on  <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">My projects</span>.<br />
											Click on <input style="float:none" type="button" value="New project"/>.<br />
											Choose the type of Application as <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Standard</span>.<br />
											Click <input style="float:none" type="button" value="Continue"/>.<br />
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for Application URL.<br />
											Scroll down to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Security and privacy</span>.<br />
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for Application Domain.<br />
											Select <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">This app requires access to private user data.</span>as Access Scopes:<br />
											Click <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Contacts</span> to expand it.<br />
											Select <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">	Read/Write </span><br />
											Accept the "Terms of use"<br />
											Click <input style="float:none" type="button" value="Get API keys"/>.<br />
											Copy the values of <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> Application ID </span><span style="background-color: #EDEFF4;border: 1px dotted #CC3333;"> Consumer Key </span> and <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Consumer Secret</span> to their respective fields above.<br />
											</div>';
						return $return;
				
				}//function
		}//class

	}



?>
