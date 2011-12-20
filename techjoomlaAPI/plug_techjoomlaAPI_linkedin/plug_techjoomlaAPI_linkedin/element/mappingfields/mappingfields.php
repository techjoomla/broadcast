<?php 
 /**
	* @package JomSocial Network Suggest
	* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link     http://www.techjoomla.com
	*/ 

	// Check to ensure this file is within the rest of the framework
	defined('JPATH_BASE') or die();
	jimport('joomla.html.html');
	jimport( 'joomla.plugin.helper' );
	jimport( 'joomla.form' );


	$lang = & JFactory::getLanguage();
	$lang->load('plug_techjoomlaAPI_linkedin', JPATH_ADMINISTRATOR);
	if(JVERSION>=1.6)
	{
	require_once(JPATH_SITE.DS.'libraries/joomla/form/fields/textarea.php');
	class JFormFieldMappingfields extends JFormFieldTextarea
	{
				/**
				 * The form field type.
				 *
				 * @var		string
				 * @since	1.6
				 */
				public $type = 'Mappingfields';

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
					require_once(JPATH_SITE.DS.'libraries/joomla/form/fields/textarea.php');
					$FieldValue		=new FieldValueLinkedin();
					
					$firstinstall	=$FieldValue->checkfirstinstall();
					
					$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
					$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
					$columns	= $this->element['cols'] ? ' cols="'.(int) $this->element['cols'].'"' : '';
					$rows		= $this->element['rows'] ? ' rows="'.(int) $this->element['rows'].'"' : '';
					$onchange	= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
					
						if($this->id=='jform_params_pathapi_linkedin_docs')
						{
						
								return  '<a href="http://techjoomla.com/documentation-for-profile-import/setup-mapping-fields-for-linkedin.html" target="_blank">'.JText::_('API_DOCS_PATH').'</a>';
						}
			
					if($this->id=='jform_params_mapping_field_0'){ 	//joomla	
							if($firstinstall)
							$fieldvalue=htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
							else
							{				
								$fieldname	=$FieldValue->getMappingValue(0);		
								$fieldvalue	=$FieldValue->RenderField($fieldname,0);
							}

								return '<textarea name="'.$this->name.'" id="'.$this->id.'"' .
								$columns.$rows.$class.$disabled.$onchange.'>' .
								htmlspecialchars($fieldvalue, ENT_COMPAT, 'UTF-8') .
								'</textarea>';
					}
					
					if($this->id=='jform_params_mapping_field_1'){	//jomsocial
						
						if(!JFolder::exists(JPATH_SITE . DS .'components'. DS .'com_community') )
						{ 
							return JText::_('JS_NOT_INSTALLED');
						}
						if($firstinstall)
							$fieldvalue=htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
							else
							{				
								$fieldname	=$FieldValue->getMappingValue(1);		
								$fieldvalue	=$FieldValue->RenderField($fieldname,1);
							}

								return '<textarea name="'.$this->name.'" id="'.$this->id.'"' .
								$columns.$rows.$class.$disabled.$onchange.'>' .
								htmlspecialchars($fieldvalue, ENT_COMPAT, 'UTF-8') .
								'</textarea>';
					}
		
					if($this->id=='jform_params_mapping_field_2'){	//CB
					if(!JFolder::exists(JPATH_SITE . DS .'components'. DS .'com_comprofiler') )
						{ 
							return JText::_('CB_NOT_INSTALLED');
						}
						if($firstinstall)
							$fieldvalue=htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8');
							else
							{				
								$fieldname	=$FieldValue->getMappingValue(2);		
								$fieldvalue	=$FieldValue->RenderField($fieldname,2);
							}

								return '<textarea name="'.$this->name.'" id="'.$this->id.'"' .
								$columns.$rows.$class.$disabled.$onchange.'>' .
								htmlspecialchars($fieldvalue, ENT_COMPAT, 'UTF-8') .
								'</textarea>';
					}
		
				}
		
		}
	}
	else
	{
	class JElementMappingfields extends JElement
	{
		public $type = 'mappingfields';
		var $_name = 'mappingfields';
		function fetchElement($name, $value, &$node, $control_name)
		{
			require_once(JPATH_SITE.DS.'libraries/joomla/html/parameter/element/textarea.php');
			$rows = $node->attributes('rows');
			$cols = $node->attributes('cols');
			$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
			// convert <br /> tags so they are not visible when editing
			$value = str_replace('<br />', "\n", $value);		
			$FieldValue		=new FieldValueLinkedin();			
			$firstinstall	=$FieldValue->checkfirstinstall();
			
				if($name=='pathapi_linkedin_docs')
				{
						return  '<a href="http://techjoomla.com/documentation-for-profile-import/setup-mapping-fields-for-linkedin.html" target="_blank">'.JText::_('API_DOCS_PATH').'</a>';
				}
				
				if($name=='mapping_field_0'){ 	//joomla
									
					if($firstinstall)
						$fieldvalue=$value;
					else
					{				
						$fieldname	=$FieldValue->getMappingValue(0);		
						$fieldvalue	=$FieldValue->RenderField($fieldname,0);				
						
					}
					
		
				return '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$fieldvalue.'</textarea>';
				
				}
				
				if($name=='mapping_field_1'){	//jomsocial
				if(!JFolder::exists(JPATH_SITE . DS .'components'. DS .'com_community') )
						{ 
							return JText::_('JS_NOT_INSTALLED');
						}
					if($firstinstall)
						$fieldvalue=$value;
					else
					{				
						$fieldname	=$FieldValue->getMappingValue(1);		
						$fieldvalue	=$FieldValue->RenderField($fieldname,1);				
						
					}
					
		
				return '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$fieldvalue.'</textarea>';

				}
		
				if($name=='mapping_field_2'){	//CB
					if(!JFolder::exists(JPATH_SITE.DS.'components'. DS .'com_comprofiler') )
						{ 
							return JText::_('CB_NOT_INSTALLED');
						}
				
					if($firstinstall)
						$fieldvalue=$value;
					else
					{				
						$fieldname	=$FieldValue->getMappingValue(2);		
						$fieldvalue	=$FieldValue->RenderField($fieldname,2);				
						
					}
					
		
				return '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$fieldvalue.'</textarea>';

				}
		
			}//function
	}//class

	}

	class FieldValueLinkedin
	{
		public function checkfirstinstall()
		{
		
			$pluginParams = '';				
			$mapping_field_0 = ''; 
			$mapping_field_1 = ''; 
			$mapping_field_2 = ''; 
			if(JVERSION>=1.6)
			{
				$plugin = JPluginHelper::getPlugin('techjoomlaAPI', 'plug_techjoomlaAPI_linkedin');
				
				$pluginParams = new JRegistry();  
				if($plugin)  
				$pluginParams->loadString($plugin->params);
				if($pluginParams)   
				{
				$mapping_field_0 = $pluginParams->get('mapping_field_0'); 
				$mapping_field_1 = $pluginParams->get('mapping_field_1'); 
				$mapping_field_2 = $pluginParams->get('mapping_field_2'); 
				}
			}
			else
			{
				$plugin = &JPluginHelper::getPlugin('techjoomlaAPI', 'plug_techjoomlaAPI_linkedin');
				if($plugin)  
				$pluginParams = new JParameter($plugin->params);
				if($pluginParams)   
				{
				$mapping_field_0 = $pluginParams->get('mapping_field_0'); 
				$mapping_field_1 = $pluginParams->get('mapping_field_1'); 
				$mapping_field_2 = $pluginParams->get('mapping_field_2'); 
				}
			}
			
			if(($mapping_field_0) or ($mapping_field_1) or ($mapping_field_2))
			return 1;
			else
			return 0;
 
		}
		
		public function getMappingValue($fieldcode)
		{
			if(!JFolder::exists(JPATH_SITE . DS .'components'. DS .'com_profileimport') )
			{ 
				return '';
			}
			require_once(JPATH_SITE.DS.'components'.DS.'com_profileimport'.DS.'helper.php');	
			$fieldnameA=comprofileimportHelper::getFieldNames($fieldcode);
			return	$fieldnameA;
		}
	
		public function RenderField($fieldnameR,$integration)
		{
			if($integration==0)
			{
				$renderedfield	=	FieldValueLinkedin::RenderField_joomla($fieldnameR);
				return $renderedfield;
			}
			if($integration==1)
			{
				$renderedfield	=	FieldValueLinkedin::RenderField_js($fieldnameR);
				return $renderedfield;
			}
			
			if($integration==2)
			{
				$renderedfield	=	FieldValueLinkedin::RenderField_cb($fieldnameR);
				return $renderedfield;
			}
			
		}
		
		public function RenderField_joomla($fieldnamej)
		{
			if(JVERSION>=1.6)
			{
				
			}
			
		}
		
		public function RenderField_js($fieldnamejs)
		{
			$defaultvalue='';
			$linkfields=array('first-name','last-name','picture-url','summary','gender','location','educations','phone-numbers','main-address','current-status');
			foreach($fieldnamejs as $key=>$value)
			{
			/// what about the picture url ?? avatar=picture-url
				if($value=='FIELD_ABOUTME')
				$defaultvalue.=$value.'=summary'."\n";
				
				if($value=='FIELD_ADDRESS')
				$defaultvalue.=$value.'=main-address'."\n";
				
				if($value=='FIELD_BIRTHDATE')
				$defaultvalue.=$value.'=date-of-birth'."\n";
				
				
				if($value=='FIELD_COLLEGE')
				$defaultvalue.=$value.'=educations'."\n";
				
				if($value=='FIELD_COUNTRY')
				$defaultvalue.=$value.'=location'."\n"; //location:(name)
				
				if($value=='FIELD_GRADUATION')
				$defaultvalue.=$value.'=educations'."\n"; //education|field-of-study
				
				if($value=='FIELD_MOBILE')
				$defaultvalue.=$value.'=phone-numbers'."\n";
				
				if($value=='FIELD_WEBSITE')
				$defaultvalue.=$value.'=member-url-resources'."\n";
							

				
				}
				return $defaultvalue;
				
			}
		
		
		
		public function RenderField_cb($fieldnamecb)
		{
			$defaultvalue='';
			foreach($fieldnamecb as $key=>$value)
			{
				if($value=='firstname')
				$defaultvalue.=$value.'=first-name'."\n";
				
				if($value=='lastname')
				$defaultvalue.=$value.'=last-name'."\n";
				
				if($value=='onlinestatus')
				$defaultvalue.=$value.'=current-share'."\n";						

				if($value=='website')
				$defaultvalue.=$value.'=member-url-resources'."\n";

				if($value=='location')
				$defaultvalue.=$value.'=location'."\n";	//location:(name)
				
				if($value=='country')
				$defaultvalue.=$value.'=location'."\n"; //location:(name)
				
				if($value=='occupation')
				$defaultvalue.=$value.'=position'."\n";	 ///position|title

				if($value=='company')
				$defaultvalue.=$value.'=position'."\n";	//company|name

				if($value=='interests')
				$defaultvalue.=$value.'=interests'."\n";				
				
				if($value=='avatar')
				$defaultvalue.=$value.'=picture-url'."\n";
				
				}
				return $defaultvalue;
		
		}
	
	}