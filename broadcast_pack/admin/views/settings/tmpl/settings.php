<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');
require(JPATH_SITE.DS."administrator".DS."components".DS."com_broadcast".DS."config".DS."config.php");
require(JPATH_SITE.DS."components".DS."com_broadcast".DS."lib".DS."config.php");
$apiconf = new BroadcastConfig();

	$facebook_no=$facebook = '';
	$facebook_page_no=$facebook_page = '';
	$twitter_no=$twitter= '';	
	$status_via=$status_via_no='';
	$linkedin_no=$linkedin= '';
	$show_status_update=$show_status_update_no='';
	//$show_status_viarss=$show_status_viarss_no='';
	$show_name=$show_name_no='';
	if($broadcast_config['facebook_profile'])
		$facebook='SELECTED=true';
	else
		$facebook_no='SELECTED=true';
	if($broadcast_config['facebook_page'])
		$facebook_page='SELECTED=true';
	else
		$facebook_page_no='SELECTED=true';
		
	if($broadcast_config['twitter'])
		$twitter ='SELECTED=true';
	else
		$twitter_no='SELECTED=true';
if($broadcast_config['status_via'])
		$status_via='SELECTED=true';
	else
		$status_via_no='SELECTED=true';

	if($broadcast_config['linkedin'])
		$linkedin ='SELECTED=true';
	else
		$linkedin_no ='SELECTED=true';
	
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
	// For JPane Tab
	$pane =& JPane::getInstance('tabs', array('startOffset'=>3)); 
	echo $pane->startPane( 'pane' );
	echo $pane->startPanel( JText::_('FACEBOOK'), 'panel1' );
	?>
		<table border='0' width='100%' class='adminlist'>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_PRO_DES'), JText::_('FACEBOOK_PRO'), '', JText::_('FACEBOOK_PRO'));?></td>
			<td width="80%"><select class="inputbox" name="data[facebook_profile]">
			<option value="1" <?php echo $facebook; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $facebook_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_PAGE_DES'), JText::_('FACEBOOK_PAGE'), '', JText::_('FACEBOOK_PAGE'));?></td>
			<td width="80%"><select class="inputbox" name="data[facebook_page]">
			<option value="1" <?php echo $facebook_page; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $facebook_page_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>
			</td>
		</tr>
			<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_LIMIT_PAGE_DES'), JText::_('FACEBOOK_LIMIT_PAGE'), '', JText::_('FACEBOOK_LIMIT_PAGE'));?></td>
			<td width="80%">
			<input type="text" class="inputbox required validate-numeric" name="data[facebook_page_limit]" width="90%" value="<?php
			 if($broadcast_config['facebook_page_limit'])		 
			echo $broadcast_config['facebook_page_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_LIMIT_DES'), JText::_('FACEBOOK_LIMIT'), '', JText::_('FACEBOOK_LIMIT'));?></td>
			<td width="80%">
			<input type="text" class="inputbox required validate-numeric" name="data[facebook_profile_limit]" width="90%" value="<?php if($broadcast_config['facebook_profile_limit'])		 
			echo $broadcast_config['facebook_profile_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_API_DES'), JText::_('FACEBOOK_API'), '', JText::_('FACEBOOK_API'));?></td>
			<td width="80%">
			<input type="text" class="inputbox required" name="apidata[fb_api]" width="90%" value="<?php echo $apiconf->fb_api ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_SECRET_DES'), JText::_('FACEBOOK_SECRET'), '', JText::_('FACEBOOK_SECRET'));?></td>
			<td width="80%">
			<input type="text" class="inputbox required" name="apidata[fb_secret]" width="90%" value="<?php echo $apiconf->fb_secret ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_LIBRARY_PATH_DES'), JText::_('FACEBOOK_LIBRARY_PATH'), '', JText::_('FACEBOOK_LIBRARY_PATH'));?></td>
			<td width="80%">
			<input type="text" class="inputbox required" name="apidata[facebook_library_path]" readonly="true" width="90%" value="<?php echo $apiconf->facebook_library_path ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('FACEBOOK_CALLBACK_URL_DES'), JText::_('FACEBOOK_CALLBACK_URL'), '', JText::_('FACEBOOK_CALLBACK_URL'));?></td>
			<td width="80%">
			<input type="text" class="inputbox required" name="apidata[callback_url_facebook]" readonly="true" size="100%" value="<?php echo $apiconf->callback_url_facebook ?>"  >
			</td>
		</tr>
				
		</table>
	<?php
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('TWITTER'), 'panel2' );
	?>
		<table border='0' width='100%' class='adminlist'>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('TWITTER_DES'), JText::_('TWITTER'), '', JText::_('TWITTER'));?></td>
			<td width="90%"><select class="inputbox" name="data[twitter]">
			<option value="1" <?php echo $twitter; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $twitter_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>
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
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('TWITTER_CONSUMER_DES'), JText::_('TWITTER_CONSUMER'), '', JText::_('TWITTER_CONSUMER'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required" name="apidata[twitter_consumer]" width="90%" value="<?php echo $apiconf->twitter_consumer ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('TWITTER_SECRET_DES'), JText::_('TWITTER_SECRET'), '', JText::_('TWITTER_SECRET'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required" name="apidata[twitter_secret]" width="90%" value="<?php echo $apiconf->twitter_secret ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('TWITTER_LIBRARY_PATH_DES'), JText::_('TWITTER_LIBRARY_PATH'), '', JText::_('TWITTER_LIBRARY_PATH'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required" name="apidata[twitter_library_path]" readonly="true"  width="90%" value="<?php echo $apiconf->twitter_library_path ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('TWITTER_CALLBACK_URL_DES'), JText::_('TWITTER_CALLBACK_URL'), '', JText::_('TWITTER_CALLBACK_URL'));?></td>
			<td width="80%">
			<input type="text" class="inputbox required" name="apidata[callback_url_twitter]" readonly="true" size="100%" value="<?php echo $apiconf->callback_url_twitter ?>"  >
			</td>
		</tr>
		</table>
	<?php
	echo $pane->endPanel();
	echo $pane->startPanel( JText::_('LINKEDIN'), 'panel3' );
	?>
		<table border='0' width='100%' class='adminlist'>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('LINKEDIN_DES'), JText::_('LINKEDIN'), '', JText::_('LINKEDIN'));?></td>
			<td width="90%"><select class="inputbox" name="data[linkedin]">
			<option value="1" <?php echo $linkedin; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $linkedin_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>
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
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('LINKEDIN_ACCESS_DES'), JText::_('LINKEDIN_ACCESS'), '', JText::_('LINKEDIN_ACCESS'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required" name="apidata[linkedin_access]" width="90%" value="<?php echo $apiconf->linkedin_access ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('LINKEDIN_SECRET_DES'), JText::_('LINKEDIN_SECRET'), '', JText::_('LINKEDIN_SECRET'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required" name="apidata[linkedin_secret]" width="90%" value="<?php echo $apiconf->linkedin_secret ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('LINKEDIN_LIBRARY_PATH_DES'), JText::_('LINKEDIN_LIBRARY_PATH'), '', JText::_('LINKEDIN_LIBRARY_PATH'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required" name="apidata[linkedin_library_path]" readonly="true" width="90%" value="<?php echo $apiconf->linkedin_library_path ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('LINKEDIN_CALLBACK_URL_DES'), JText::_('LINKEDIN_CALLBACK_URL'), '', JText::_('LINKEDIN_CALLBACK_URL'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required" name="apidata[callback_url_linkedin]" readonly="true" size="100%" value="<?php echo $apiconf->callback_url_linkedin ?>"  >
			</td>
		</tr>
		
		</table>
	<?php
	echo $pane->endPanel();
	echo $pane->startPanel( JText::_('BC_COMMON'), 'panel4' );
	?>
		<table border="0" width="100%" class="adminlist">
	
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('PRIVATE_KEY_CRON_DES'), JText::_('PRIVATE_KEY_CRON'), '', JText::_('PRIVATE_KEY_CRON'));?></td>
			<td><input type="text" class="inputbox required" name="data[private_key_cronjob]" width="90%" value="<?php echo $broadcast_config['private_key_cronjob']; ?>"  ></td>		
		</tr>
		
			<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('URL_LIMIT_DES'), JText::_('URL_LIMIT'), '', JText::_('URL_LIMIT'));?></td>
			<td width="90%">
			<input type="text" class="inputbox required validate-numeric" name="data[url_limit]" width="90%" value="<?php
			 if($broadcast_config['url_limit'])		 
			echo $broadcast_config['url_limit'];
			else
			echo '10';
			 ?>"  >
			</td>
		</tr>
	
		<!--<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('SHOW_NAME_BEFORE_DES'), JText::_('SHOW_NAME_BEFORE'), '', JText::_('SHOW_NAME_BEFORE'));?></td>
			<td width="90%"><select class="inputbox" name="data[show_name]">
			<option value="1" <?php echo $show_name; ?>> <?php echo JText::_('BC_YES');?> </option>
			<option value="0" <?php echo $show_name_no; ?>> <?php echo JText::_('BC_NO');?> </option>
			</select>
			</td>
		</tr>
	
		<tr>
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
			 ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('CRON_URL_FB'), JText::_('CRON_URL_FB'), '', JText::_('CRON_URL_FB'));?></td>
			<td><?php echo str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getstatus&controller=facebook&pkey='.$broadcast_config['private_key_cronjob'];?>
			</td>	
		</tr>		
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('CRON_URL_T'), JText::_('CRON_URL_T'), '', JText::_('CRON_URL_T'));?></td>
			<td><?php echo str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getstatus&controller=twitter&pkey='.$broadcast_config['private_key_cronjob'];?>
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('CRON_URL_L'), JText::_('CRON_URL_L'), '', JText::_('CRON_URL_L'));?></td>
			<td><?php echo str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getstatus&controller=linkedin&pkey='.$broadcast_config['private_key_cronjob'];?>
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('CRON_URL_RSS'), JText::_('CRON_URL_RSS'), '', JText::_('CRON_URL_RSS'));?></td>
			<td><?php echo str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getrssdata&controller=rss&pkey='.$broadcast_config['private_key_cronjob'];?>
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><?php echo JHTML::tooltip(JText::_('CRON_URL_B'), JText::_('CRON_URL_B'), '', JText::_('CRON_URL_B'));?></td>
			<td><?php echo str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=broadcast&controller=broadcast&pkey='.$broadcast_config['private_key_cronjob'];?>
			</td>
		</tr>
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('APPLICATION_TITLE_DES'), JText::_('APPLICATION_TITLE'), '', JText::_('APPLICATION_TITLE'));?></td>
			<td align="left" width="10%">
				<input type="text" class="inputbox required" name="apidata[app_title]" width="90%" value="<?php echo $apiconf->app_title ?>" >
			</td>
		</tr>
		
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('BASE_URL_DES'), JText::_('BASE_URL'), '', JText::_('BASE_URL'));?></td>
			<td align="left" width="10%">
				<input type="text" class="inputbox required" name="apidata[base_url]" size="50%" readonly="true" width="90%" value="<?php echo $apiconf->base_url ?>" >
			</td>
		</tr>
		
		<tr>
			<td align="left" width="10%"><?php echo JHTML::tooltip(JText::_('CALLBACK_URL_DES'), JText::_('CALLBACK_URL'), '', JText::_('CALLBACK_URL'));?></td>
			<td align="left" width="10%">
				<input type="text" class="inputbox required" name="apidata[callback_url]" size="50%" readonly="true" value="<?php echo $apiconf->callback_url ?>" >
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
