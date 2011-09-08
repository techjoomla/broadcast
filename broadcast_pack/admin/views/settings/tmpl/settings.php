<?php

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
require(JPATH_SITE.DS."administrator".DS."components".DS."com_broadcast".DS."config".DS."config.php");
require(JPATH_SITE.DS."components".DS."com_broadcast".DS."lib".DS."config.php");
$apiconf = new BroadcastConfig();

	$facebook_no=$facebook = '';
	$facebook_page_no=$facebook_page = '';
	$twitter_no=$twitter= '';	
	$status_via_no='';
	$linkedin_no=$linkedin= '';
	$connect_cron=$connect_cron_no='';
	$show_status_update=$show_status_update_no='';
	//$show_status_viarss=$show_status_viarss_no='';
	
	if($config['facebook_profile'])
		$facebook='SELECTED=true';
	else
		$facebook_no='SELECTED=true';
	if($config['facebook_page'])
		$facebook_page='SELECTED=true';
	else
		$facebook_page_no='SELECTED=true';
		
	if($config['twitter'])
		$twitter ='SELECTED=true';
	else
		$twitter_no='SELECTED=true';
if($config['status_via'])
		$status_via='SELECTED=true';
	else
		$status_via_no='SELECTED=true';

	if($config['linkedin'])
		$linkedin ='SELECTED=true';
	else
		$linkedin_no ='SELECTED=true';
	if($config['connect_cron'])
		$connect_cron ='checked';
	else
		$connect_cron_no ='checked';	

	if($config['show_status_update'])
		$show_status_update ='SELECTED=true';
	else
		$show_status_update_no ='SELECTED=true';
	if($config['show_name'])
		$show_name ='SELECTED=true';
	else
		$show_name_no ='SELECTED=true';	
		
	/*if($config['show_status_viarss'])
		$show_status_viarss ='SELECTED=true';
	else
		$show_status_viarss_no ='SELECTED=true';	*/

	echo "<form method='POST' name='adminForm' action='index.php'>";
	// For JPane Tab
	$pane =& JPane::getInstance('tabs', array('startOffset'=>3)); 
	echo $pane->startPane( 'pane' );
	echo $pane->startPanel( 'Facebook', 'panel1' );
	?>
		<table border='0' width='100%' class='adminlist'>
		<tr>
			<td align="left" width="20%"><strong><span class="hasTip" title="<?php echo JText::_('FACEBOOK'); ?>::<?php echo JText::_('FACEBOOK_DES'); ?>">
			<?php echo JText::_('FACEBOOK'); ?></span></strong></td>
			<td width="80%"><select class="inputbox" name="data[facebook_profile]">
			<option value="1" <? echo $facebook; ?>> <?php echo JText::_('Yes');?> </option>
			<option value="0" <? echo $facebook_no; ?>> <?php echo JText::_('No');?> </option>
			</select>
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><strong><span class="hasTip" title="<?php echo JText::_('FACEBOOK_PAGE'); ?>::<?php echo JText::_('FACEBOOK_PAGE_DES'); ?>">
			<?php echo JText::_('FACEBOOK_PAGE'); ?></span></strong></td>
			<td width="80%"><select class="inputbox" name="data[facebook_page]">
			<option value="1" <? echo $facebook_page; ?>> <?php echo JText::_('Yes');?> </option>
			<option value="0" <? echo $facebook_page_no; ?>> <?php echo JText::_('No');?> </option>
			</select>
			</td>
		</tr>
			<tr>
			<td align="left" width="20%"><strong>
			<span class="hasTip" title="<?php echo JText::_('FACEBOOK_LIMIT_PAGE'); ?>::<?php echo JText::_('FACEBOOK_LIMIT_PAGE_DES'); ?>">
			<?php echo JText::_('FACEBOOK_LIMIT_PAGE'); ?></span></strong></td>
			<td width="80%">
			<input type="text" class="inputbox" name="data[facebook_page_limit]" width="90%" value="<?php
			 if($config['facebook_page_limit'])		 
			echo $config['facebook_page_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><strong>
			<span class="hasTip" title="<?php echo JText::_('FACEBOOK_LIMIT'); ?>::<?php echo JText::_('FACEBOOK_LIMIT_DES'); ?>">
			<?php echo JText::_('FACEBOOK_LIMIT'); ?></span></strong></td>
			<td width="80%">
			<input type="text" class="inputbox" name="data[facebook_profile_limit]" width="90%" value="<?php
			 if($config['facebook_profile_limit'])		 
			echo $config['facebook_profile_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="20%"><strong>
			<span class="hasTip" title="<?php echo JText::_('FACEBOOK_API'); ?>::<?php echo JText::_('FACEBOOK_API_DES'); ?>">
			<?php echo JText::_('FACEBOOK_API'); ?></span></strong></td>
			<td width="80%">
			<input type="text" class="inputbox" name="apidata[fb_api]" width="90%" value="<?php echo $apiconf->fb_api ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><strong>
			<span class="hasTip" title="<?php echo JText::_('FACEBOOK_SECRET'); ?>::<?php echo JText::_('FACEBOOK_SECRET_DES'); ?>">
			<?php echo JText::_('FACEBOOK_SECRET'); ?></span></strong></td>
			<td width="80%">
			<input type="text" class="inputbox" name="apidata[fb_secret]" width="90%" value="<?php echo $apiconf->fb_secret ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><strong>
			<span class="hasTip" title="<?php echo JText::_('FACEBOOK_LIBRARY_PATH'); ?>::<?php echo JText::_('FACEBOOK_LIBRARY_PATH_DES'); ?>">
			<?php echo JText::_('FACEBOOK_LIBRARY_PATH'); ?></span></strong></td>
			<td width="80%">
			<input type="text" class="inputbox" name="apidata[facebook_library_path]" width="90%" value="<?php 			 	echo $apiconf->facebook_library_path ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><strong>
			<span class="hasTip" title="<?php echo JText::_('FACEBOOK_CALLBACK_URL'); ?>::<?php echo JText::_('FACEBOOK_CALLBACK_URL_DES'); ?>">
			<?php echo JText::_('FACEBOOK_CALLBACK_URL'); ?></span></strong></td>
			<td width="80%">
			<input type="text" class="inputbox" name="apidata[callback_url_facebook]" width="90%" value="<?php echo $apiconf->callback_url_facebook ?>"  >
			</td>
		</tr>
				
		</table>
	<?
	echo $pane->endPanel();
	echo $pane->startPanel( 'Twitter', 'panel2' );
	?>
		<table border='0' width='100%' class='adminlist'>
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('TWITTER'); ?>::<?php echo JText::_('TWITTER_DES'); ?>">
			<?php echo JText::_('TWITTER'); ?></span></strong></td>
			<td width="90%"><select class="inputbox" name="data[twitter]">
			<option value="1" <? echo $twitter; ?>> <?php echo JText::_('Yes');?> </option>
			<option value="0" <? echo $twitter_no; ?>> <?php echo JText::_('No');?> </option>
			</select>
			</td>
		</tr>
	
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('TWITTER_LIMIT'); ?>::<?php echo JText::_('TWITTER_LIMIT_DES'); ?>">
			<?php echo JText::_('TWITTER_LIMIT'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="data[twitter_limit]" width="90%" value="<?php
			 if($config['twitter_limit'])		 
			echo $config['twitter_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('TWITTER_CONSUMER'); ?>::<?php echo JText::_('TWITTER_CONSUMER_DES'); ?>">
			<?php echo JText::_('TWITTER_CONSUMER'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="apidata[twitter_consumer]" width="90%" value="<?php 			echo $apiconf->twitter_consumer ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('TWITTER_SECRET'); ?>::<?php echo JText::_('TWITTER_SECRET_DES'); ?>">
			<?php echo JText::_('TWITTER_SECRET'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="apidata[twitter_secret]" width="90%" value="<?php 			echo $apiconf->twitter_secret ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('TWITTER_LIBRARY_PATH'); ?>::<?php echo JText::_('TWITTER_LIBRARY_PATH_DES'); ?>">
			<?php echo JText::_('TWITTER_LIBRARY_PATH'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="apidata[twitter_library_path]" width="90%" value="<?php 			echo $apiconf->twitter_library_path ?>"  >
			</td>
		</tr>
		
		</table>
	<?
	echo $pane->endPanel();
	echo $pane->startPanel( 'Linkedin', 'panel3' );
	?>
		<table border='0' width='100%' class='adminlist'>
		<tr>
			<td align="left" width="10%"><strong><span class="hasTip" title="<?php echo JText::_('LINKEDIN'); ?>::<?php echo JText::_('LINKEDIN_DES'); ?>">
			<?php echo JText::_('LINKEDIN'); ?></span></strong></td>
			<td width="90%"><select class="inputbox" name="data[linkedin]">
			<option value="1" <? echo $linkedin; ?>> <?php echo JText::_('Yes');?> </option>
			<option value="0" <? echo $linkedin_no; ?>> <?php echo JText::_('No');?> </option>
			</select>
			</td>
		</tr>
	
		<tr>
			<td align="left" width="10%"><strong><span class="hasTip" title="<?php echo JText::_('LINKEDIN_LIMIT'); ?>::<?php echo JText::_('LINKEDIN_LIMIT_DES'); ?>">
			<?php echo JText::_('LINKEDIN_LIMIT'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="data[linkedin_limit]" width="90%" value="<?php
			 if($config['linkedin_limit'])		 
			echo $config['linkedin_limit'];
			else
			echo '5';
			 ?>"  >
			</td>
		</tr>
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="10%"><strong><span class="hasTip" title="<?php echo JText::_('LINKEDIN_ACCESS'); ?>::<?php echo JText::_('LINKEDIN_ACCESS_DES'); ?>">
			<?php echo JText::_('LINKEDIN_ACCESS'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="apidata[linkedin_access]" width="90%" value="<?php 			echo $apiconf->linkedin_access ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><strong><span class="hasTip" title="<?php echo JText::_('LINKEDIN_SECRET'); ?>::<?php echo JText::_('LINKEDIN_SECRET_DES'); ?>">
			<?php echo JText::_('LINKEDIN_SECRET'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="apidata[linkedin_secret]" width="90%" value="<?php 			echo $apiconf->linkedin_secret ?>">
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><strong><span class="hasTip" title="<?php echo JText::_('LINKEDIN_LIBRARY_PATH'); ?>::<?php echo JText::_('LINKEDIN_LIBRARY_PATH_DES'); ?>">
			<?php echo JText::_('LINKEDIN_LIBRARY_PATH'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="apidata[linkedin_library_path]" width="90%" value="<?php 			 echo $apiconf->linkedin_library_path ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="10%"><strong><span class="hasTip" title="<?php echo JText::_('LINKEDIN_CALLBACK_URL'); ?>::<?php echo JText::_('LINKEDIN_CALLBACK_URL_DES'); ?>">
			<?php echo JText::_('LINKEDIN_CALLBACK_URL'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="apidata[callback_url_linkedin]" width="90%" value="<?php 			 echo $apiconf->callback_url_linkedin ?>"  >
			</td>
		</tr>
		
		</table>
	<?
	echo $pane->endPanel();
	echo $pane->startPanel( 'Common', 'panel4' );
	?>
		<table border="0" width="100%" class="adminlist">
	
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('PRIVATE_KEY_CRON'); ?>::<?php echo JText::_('PRIVATE_KEY_CRON_DES'); ?>">
			<?php echo JText::_('PRIVATE_KEY_CRON'); ?></span></strong></strong></td>
			<td><input type="text" class="inputbox" name="data[private_key_cronjob]" width="90%" value="<?php echo $config['private_key_cronjob']; ?>"  ></td>		
		</tr>
		
			<tr>
			<td align="left" width="10%"><strong><span class="hasTip" title="<?php echo JText::_('URL_LIMIT'); ?>::<?php echo JText::_('URL_LIMIT_DES'); ?>">
			<?php echo JText::_('URL_LIMIT'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="data[url_limit]" width="90%" value="<?php
			 if($config['url_limit'])		 
			echo $config['url_limit'];
			else
			echo '10';
			 ?>"  >
			</td>
		</tr>
	
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('SHOW_NAME_BEFORE'); ?>::<?php echo JText::_('SHOW_NAME_BEFORE_DES'); ?>">
			<?php echo JText::_('SHOW_NAME_BEFORE'); ?></span></strong></td>
			<td width="90%"><select class="inputbox" name="data[show_name]">
			<option value="1" <? echo $show_name; ?>> <?php echo JText::_('Yes');?> </option>
			<option value="0" <? echo $show_name_no; ?>> <?php echo JText::_('No');?> </option>
			</select>
			</td>
		</tr>
	
		<!--<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('CRON_SETTING'); ?>::<?php echo JText::_('CRON_SETTING_DES'); ?>">
			<?php echo JText::_('CRON_SETTING'); ?></span></strong>	
			</td>
			<td align="left" width="10%">
				<input type="radio" name="data[connect_cron]" <? echo $connect_cron; ?> value="1" ><?php echo JText::_('CONNECT_USING_CRON'); ?>
				<input type="radio" name="data[connect_cron]" <? echo $connect_cron_no; ?> value="0"><?php echo JText::_('DIRECT_RUN'); ?>
	
			</td>
		</tr>-->
	
			<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('SHOW_STATUS_UPDATE'); ?>::<?php echo JText::_('SHOW_STATUS_UPDATE_DES'); ?>">
			<?php echo JText::_('SHOW_STATUS_UPDATE'); ?></span></strong>	
			</td>
			<td align="left" width="10%">
			<select class="inputbox" name="data[show_status_update]">
			<option value="1" <? echo $show_status_update; ?>> <?php echo JText::_('Yes');?> </option>
			<option value="0" <? echo $show_status_update_no; ?>> <?php echo JText::_('No');?> </option>
			</select>	
			</td>
		</tr>
		</tr>	
		
		<!--<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('SHOW_STATUS_VIARSS'); ?>::<?php echo JText::_('SHOW_STATUS_VIARSS_DES'); ?>">
			<?php echo JText::_('SHOW_STATUS_VIARSS'); ?></span></strong>	
			</td>
			<td align="left" width="10%">
			<select class="inputbox" name="data[show_status_viarss]">
			<option value="1" <? echo $show_status_viarss; ?>> <?php echo JText::_('Yes');?> </option>
			<option value="0" <? echo $show_status_viarss_no; ?>> <?php echo JText::_('No');?> </option>
			</select>	
			</td>
		</tr>-->
		
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('RSS_SETTING'); ?>::<?php echo JText::_('RSS_SETTING_DES'); ?>">
			<?php echo JText::_('RSS_SETTING'); ?></span></strong>	
			</td>
			<td align="left" width="10%">
				<input type="text" class="inputbox" name="data[rss_link_limit]" width="90%" value="<?php
			 if($config['rss_link_limit'])		 
			echo $config['rss_link_limit'];
			else
			echo '5';
			 ?>" >
			</td>
		</tr>
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="10%"><strong><span class="hasTip" title="<?php echo JText::_('STATUS_VIA'); ?>::<?php echo JText::_('STATUS__VIA_DES'); ?>">
			<?php echo JText::_('STATUS_VIA'); ?></span></strong></td>
			<td width="90%"><select class="inputbox" name="data[status_via]">
			<option value="1" <? echo $status_via; ?>> <?php echo JText::_('Yes');?> </option>
			<option value="0" <? echo $status_via_no; ?>> <?php echo JText::_('No');?> </option>
			</select>
			</td>
		</tr>
		
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('STATUS_SKIP_CHARACTER'); ?>::<?php echo JText::_('STATUS_SKIP_CHARACTER'); ?>">
			<?php echo JText::_('STATUS_SKIP_CHARACTER'); ?></span></strong></td>
			<td width="90%">
			<input type="text" class="inputbox" name="data[status_skip]" width="90%" value="<?php
			 if($config['status_skip'])		 
			echo $config['status_skip'];
			 ?>"  >
			</td>
		</tr>
		<tr>
			<td align="left" width="20%"><strong><?php echo JText::_('CRON_URL');?>:</strong>
		
			</td>
			<td>
				<?php
					echo "1) ".str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getstatus&controller=facebook&pkey='.$config['private_key_cronjob'];
					echo "<br>";
					echo "2) ".str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getstatus&controller=twitter&pkey='.$config['private_key_cronjob'];
					echo "<br>";
					echo "3) ".str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getstatus&controller=linkedin&pkey='.$config['private_key_cronjob'];
					echo "<br>";
					echo "4) ".str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=getrssdata&controller=rss&pkey='.$config['private_key_cronjob'];
					echo "<br>";
					echo "5) ".str_replace('administrator/', '', JURI::base()).'index.php?option=com_broadcast&task=broadcast&controller=broadcast&pkey='.$config['private_key_cronjob'];
					echo "<br>";
				?>
			</td>	
		</tr>		
		
		<tr><td colspan=2><hr/></td></tr>
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('APPLICATION_TITLE'); ?>::<?php echo JText::_('APPLICATION_TITLE_DES'); ?>">
			<?php echo JText::_('APPLICATION_TITLE'); ?></span></strong>	
			</td>
			<td align="left" width="10%">
				<input type="text" class="inputbox" name="apidata[app_title]" width="90%" value="<?php 			 echo $apiconf->app_title ?>" >
			</td>
		</tr>
		
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('BASE_URL'); ?>::<?php echo JText::_('BASE_URL_DES'); ?>">
			<?php echo JText::_('BASE_URL'); ?></span></strong>	
			</td>
			<td align="left" width="10%">
				<input type="text" class="inputbox" name="apidata[base_url]" width="90%" value="<?php 			 echo $apiconf->base_url ?>" >
			</td>
		</tr>
		
		<tr>
			<td align="left" width="10%"><strong>
			<span class="hasTip" title="<?php echo JText::_('CALLBACK_URL'); ?>::<?php echo JText::_('CALLBACK_URL_DES'); ?>">
			<?php echo JText::_('CALLBACK_URL'); ?></span></strong>	
			</td>
			<td align="left" width="10%">
				<input type="text" class="inputbox" name="apidata[callback_url]" width="90%" value="<?php echo $apiconf->callback_url ?>" >
			</td>
		</tr>
					
		</table>
	<?
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
