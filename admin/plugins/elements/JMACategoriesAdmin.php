<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
/**

defined('JPATH_BASE') or die();
/**
 * Renders a multiple item select element using SQL result and explicitly specified params
 
 * HOW TO USE IN XML ?? example is given below
 => <param name="catid" type="JMACategoriesAdmin" default="1" label="LBL_CATEGORY_LN_JS" element="jma_latestnews_js" multiple="multiple" description="DESC_CATEGORY_LN_JS" key_field='id' value_field='title' />	
 * where element is the name of your plugin entry file 
 */
 
class JElementJMACategoriesAdmin extends JElement
{
        /**
        * Element name
        *
        * @access       protected
        * @var          string
        */
        var    $_name = 'JMACategoriesAdmin';
 
        function fetchElement($name, $value, &$node, $control_name)
        {
                // Base name of the HTML control.
                $ctrl  = $control_name .'['. $name .']';
 
                // Construct the various argument calls that are supported.
                $attribs       = ' ';
                if ($v = $node->attributes( 'size' )) {
                        $attribs       .= 'size="'.$v.'"';
                }
                if ($v = $node->attributes( 'class' )) {
                        $attribs       .= 'class="'.$v.'"';
                } else {
                        $attribs       .= 'class="inputbox"';
                }
                if ($m = $node->attributes( 'multiple' ))
                {
                        $attribs       .= ' multiple="multiple"';
                        $ctrl          .= '[]';
                }
 				
 				//get plugin name (plugin entryfile name)
 				$element= $node->attributes('element'); 
 				$db  = & JFactory::getDBO();
 				
 				//get plugin params from #__plugins table
 				$plugin = JPluginHelper::getPlugin( 'content',$element);
 				if(!empty($plugin))//if plugins is enabled
 				{
					$pluginParams = new JParameter( $plugin->params );
					$pluginParamsDefault= $pluginParams->_raw;//example: plugintitle=K2-Latest Items category=1|3|4|5|2 no_of_items=5 catid=1|3|4|5|2 
					$sections=0;
					$new1 = explode("\n",$pluginParamsDefault);
					$new2 = explode("=",$new1[0]);
	   				$sections=str_replace('|',',',$new2[1]); 
		          	if($sections){
		          		$sql = "SELECT id ,title FROM #__categories WHERE published = 1 AND section !='com_docman' AND section IN (".$sections.")";
		          	}
		          	else{//if no section is yet selected
		          		$sql = "SELECT id ,title FROM #__categories WHERE published = 1 AND section !='com_docman'";
		          	}
              	}
              	else{//if plugin is not yet enabled load all categories
              		$sql = "SELECT id ,title FROM #__categories WHERE published = 1 AND section !='com_docman'";
              	}

                $db->setQuery($sql);
                
                // Query items for list.
                $key = ($node->attributes('key_field') ? $node->attributes('key_field') : 'value');
                $val = ($node->attributes('value_field') ? $node->attributes('value_field') : $name);
				
                $options = array ();
                foreach ($node->children() as $option)
                {
                        $options[]= array($key=> $option->attributes('value'),$val => $option->data());
                }
 
                $rows = $db->loadAssocList();
                if($rows){
		            foreach ($rows as $row){
		                    $options[]=array($key=>$row[$key],$val=>$row[$val]);
     	           }
     	       	}
                if($options){
                        return JHTML::_('select.genericlist',$options, $ctrl, $attribs, $key, $val, $value, $control_name.$name);
                }
                else{
		                return JText::_('NO_CATS_ADMIN');
                }
        }
}
