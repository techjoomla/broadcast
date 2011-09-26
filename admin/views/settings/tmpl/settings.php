<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
require(JPATH_SITE.DS."administrator".DS."components".DS."com_broadcast".DS."config".DS."config.php");
	
	$status_via=$status_via_no='';
	$show_status_update=$show_status_update_no='';
	//$show_status_viarss=$show_status_viarss_no='';
	$show_name=$show_name_no='';
	
	if($broadcast_config['status_via'])
		$status_via='SELECTED=true';
	else
		$status_via_no='SELECTED=true';
	
	/*
	if($broadcast_config['show_status_update'])
		$show_status_update ='SELECTED=true';
	else
		$show_status_update_no ='SELECTED=true';
	if($broadcast_config['show_name'])
		$show_name ='SELECTED=true';
	else
		$show_name_no ='SELECTED=true';	
	if($broadcast_config['show_status_viarss'])
		$show_status_viarss ='SELECTED=true';
	else
		$show_status_viarss_no ='SELECTED=true';	*/

$document =& JFactory::getDocument();
if(JVERSION >= '1.6.0')
	$js_key="
	Joomla.submitbutton = function(task){ ";
else
	$js_key="
	function submitbutton( task ){";

	$js_key.="
		if (task == 'cancel')
		{";
	        if(JVERSION >= '1.6.0')
				$js_key.="Joomla.submitform(task);";
			else		
				$js_key.="document.adminForm.submit();";
	    $js_key.="
	    }else{	
			var validateflag = document.formvalidator.isValid(document.adminForm);
			if(validateflag){";
				if(JVERSION >= '1.6.0'){
					$js_key.="
				Joomla.submitform(task);";
				}else{		
					$js_key.="
				document.adminForm.submit();";
				}
			$js_key.="
			}else{
				return false;
			}
		}
	}
";

	$document->addScriptDeclaration($js_key);	
?>
<?php 
	echo "<form method='POST' name='adminForm' class='form-validate' action='index.php'>";
	$apiselect = array();
	foreach($this->apiplugin as $api)
	{
		$apiname = ucfirst(str_replace('plug_techjoomlaAPI_', '',$api->element));
		$apiselect[] = JHTML::_('select.option',$api->element, $apiname);
	}
	
	// For JPane Tab
	$pane =& JPane::getInstance('tabs', array('startOffset'=>3)); 
	echo $pane->startPane( 'pane' );
	echo $pane->startPanel( JText::_('BC_COMMON'), 'panel1' );
	?>
	<table border="0" width="100%" class="adminlist">
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('SHOW_NAME_BEFORE_DES'), JText::_('SHOW_NAME_BEFORE'), '', JText::_('SHOW_NAME_BEFORE'));?></td>
			<td width="90%"><select class="inputbox" name="data[show_name]">
			<option value="1" <?php echo $show_name; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $show_name_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>
			</td>
		</tr>
	
		<!--<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('SHOW_STATUS_UPDATE_DES'), JText::_('SHOW_STATUS_UPDATE'), '', JText::_('SHOW_STATUS_UPDATE'));?></td>
			<td align="left" width="10%">
			<select class="inputbox" name="data[show_status_update]">
			<option value="1" <?php echo $show_status_update; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $show_status_update_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>	
			</td>
		</tr>

		
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('SHOW_STATUS_VIARSS_DES'), JText::_('SHOW_STATUS_VIARSS'), '', JText::_('SHOW_STATUS_VIARSS'));?></td>
			<td align="left" width="10%">
			<select class="inputbox" name="data[show_status_viarss]">
			<option value="1" <?php echo $show_status_viarss; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $show_status_viarss_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>	
			</td>
		</tr>-->
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('STATUS__VIA_DES'), JText::_('STATUS_VIA'), '', JText::_('STATUS_VIA'));?></td>
			<td width="90%"><select class="inputbox" name="data[status_via]">
			<option value="1" <?php echo $status_via; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $status_via_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>
			</td>
		</tr>
		
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('STATUS_SKIP_CHARACTER_DES'), JText::_('STATUS_SKIP_CHARACTER'), '', JText::_('STATUS_SKIP_CHARACTER'));?></td>
			<td width="90%">
			<input type="text" class="inputbox" name="data[status_skip]" width="90%" value="<?php
			if($broadcast_config['status_skip'])		 
				echo $broadcast_config['status_skip'];
			else
				echo '';
			 ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('URL_API_DES'), JText::_('URL_API'), '', JText::_('URL_API'));?></td>
			<td width="90%">
			<input type="text" class="inputbox " name="data[url_apikey]" width="90%" value="<?php
			if($broadcast_config['url_apikey'])		 
				echo $broadcast_config['url_apikey'];
			else
				echo '';
			 ?>"  >
			</td>
		</tr>
		<tr><td colspan=2><hr/></td></tr>
		<tr>
		<td  width="25%"><?php echo JHTML::tooltip(JText::_('SELECT_API_DES'), JText::_('SELECT_API'), '', JText::_('SELECT_API'));?></td>
		<td class="setting-td">
		<?php echo JHTML::_('select.genericlist', $apiselect, "data[api][]", ' multiple size="3"  ', "value", "text", $broadcast_config['api'] )?>
		
		</td>
		</tr>
		<tr>

			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_PRO_LIMIT_DES'), JText::_('FACEBOOK_PRO_LIMIT'), '', JText::_('FACEBOOK_PRO_LIMIT'));?></td>
			<td width="80%">
			<input type="text" class="inputbox required validate-numeric" name="data[facebook_profile_limit]" width="90%" value="<?php if($broadcast_config['facebook_profile_limit'])		 
			echo $broadcast_config['facebook_profile_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('TWITTER_LIMIT_DES'), JText::_('TWITTER_LIMIT'), '', JText::_('TWITTER_LIMIT'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required validate-numeric" name="data[twitter_limit]" width="90%" value="<?php if($broadcast_config['twitter_limit'])		 
			echo $broadcast_config['twitter_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('LINKEDIN_LIMIT_DES'), JText::_('LINKEDIN_LIMIT'), '', JText::_('LINKEDIN_LIMIT'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required validate-numeric" name="data[linkedin_limit]" width="90%" value="<?php
			 if($broadcast_config['linkedin_limit'])		 
			echo $broadcast_config['linkedin_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('RSS_SETTING_DES'), JText::_('RSS_SETTING'), '', JText::_('RSS_SETTING'));?></td>
			<td align="left" width="10%">
				<input type="text" class="inputbox required validate-numeric" name="data[rss_link_limit]" width="90%" value="<?php
			 if($broadcast_config['rss_link_limit'])		 
			echo $broadcast_config['rss_link_limit'];
			else
			echo '5';
			 ?>" >
			</td>
		</tr>
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('PRIVATE_KEY_CRON_DES'), JText::_('PRIVATE_KEY_CRON'), '', JText::_('PRIVATE_KEY_CRON'));?></td>
			<td><input type="text" class="inputbox required" name="data[private_key_cronjob]" width="90%" value="<?php echo $broadcast_config['private_key_cronjob']; ?>"  ></td>		
		</tr>	
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('CRON_URL_GET_DESC'), JText::_('CRON_URL_GET'), '', JText::_('CRON_URL_GET'));?></td>
			<td><?php echo str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&controller=broadcast&task=get_status&pkey='.$broadcast_config['private_key_cronjob'];?>
			</td>	
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('CRON_URL_SET_DESC'), JText::_('CRON_URL_SET'), '', JText::_('CRON_URL_SET'));?></td>
			<td><?php echo str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&controller=broadcast&task=set_status&pkey='.$broadcast_config['private_key_cronjob'];?>
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('CRON_URL_RSS_DESC'), JText::_('CRON_URL_RSS'), '', JText::_('CRON_URL_RSS'));?></td>
			<td><?php echo str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getrssdata&controller=rss&pkey='.$broadcast_config['private_key_cronjob'];?>
			</td>
		</tr>					
	</table>
	<?php
	echo $pane->endPanel();
	echo $pane->endPane();

	?>
	
<?php			
$option='com_broadcast';
?>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />		
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="settings" />
	<?php echo JHTML::_( 'form.token' ); ?>
	</form>
