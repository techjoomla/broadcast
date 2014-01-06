<?php 
 /**
	* @package JomSocial Network Suggest
	* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link     http://www.techjoomla.com
	*/ 

	// Check to ensure this file is within the rest of the framework
	defined('JPATH_BASE') or die();
	if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
	}
	jimport("joomla.html.parameter.element");	
	jimport('joomla.html.html');
	jimport('joomla.form.formfield');

	$lang =  JFactory::getLanguage();
	$lang->load('plug_techjoomlaAPI_facebook', JPATH_ADMINISTRATOR);
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
					
					if($this->id=='jform_params_pathapi_facebook')
						$return	=	'<div style="clear:both"></div>
											<div class="instructions">
											Go to <a href="https://developers.facebook.com/apps" target="_blank">https://developers.facebook.com/apps</a>.<br />
											Click on <input style="float:none" type="button" value="Create New APP"/>.<br />
											Enter an <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">App display name</span>.Give the Application a Generalised name for example name of your site.<br />
											Accept the "Facebook platform policies" and click on <input style="float:none" type="button" value="Continue"/><br />
											Enter <input style="float:none" type="text" value="'. str_replace( "http://",'',JURI::root()). '" readonly="true"/> for the App Domain.<br />
											Scroll down to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Select how your app integrates with Facebook</span>. Click <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Website</span> to expand it.<br />
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for the Site URL.<br />
											Click <input style="float:none" type="button" value="Save changes"/>.<br />
											Copy the values of <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">App ID</span> and <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">App secret</span> to their respective fields above.<br />
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
											Go to <a href="https://developers.facebook.com/apps" target="_blank">https://developers.facebook.com/apps</a>.<br />
											Click on <input style="float:none" type="button" value="Create New APP"/>.<br />
											Enter an <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">App display name</span>.Give the Application a Generalised name for example name of your site.<br />
											Accept the "Facebook platform policies" and click on <input style="float:none" type="button" value="Continue"/><br />
											Enter <input style="float:none" type="text" value="'. str_replace( "http://",'',JURI::root()). '" readonly="true"/> for the App Domain.<br />
											Scroll down to <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Select how your app integrates with Facebook</span>. Click <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">Website</span> to expand it.<br />
											Enter <input style="float:none" type="text" value="'. JURI::root(). '" readonly="true"/> for the Site URL.<br />
											Click <input style="float:none" type="button" value="Save changes"/>.<br />
											Copy the values of <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">App ID</span> and <span style="background-color: #EDEFF4;border: 1px dotted #CC3333;">App secret</span> to their respective fields above.<br />
											</div>';
						return $return;
				
				}//function
		}//class

	}


