<?php

jimport( 'joomla.filesystem.folder' );
jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');


$db = & JFactory::getDBO();
$condtion = array(0 => '\'community\'',1 => '\'techjoomlaAPI\'' ,2 => '\'content\'',3 => '\'k2\'',4 => '\'docman\'',5 => '\'easybolg\'',6 => '\'flexicontent_fields\'');
$condtionatype = join(',',$condtion);
if(JVERSION >= '1.6.0')
{
	$query = "SELECT element FROM #__extensions WHERE  folder in ($condtionatype)";
}
else
{
	$query = "SELECT element FROM #__plugins WHERE folder in ($condtionatype)";
}
$db->setQuery($query);
$status = $db->loadResultArray();

$install_status = new JObject();
$install_source = $this->parent->getPath('source');

//install Broadcast Module 
echo '<br/><span style="font-weight:bold;">'.JText::_('Installing Module:').'</span>';
if(JVERSION >= '1.6.0')
{
	$query = "SELECT element FROM #__extensions WHERE  element='mod_broadcast'";
}
else
{
	$query = "SELECT module FROM #__modules WHERE module='mod_broadcast'";
}
$db->setQuery($query);
$modstatus = $db->loadResultArray();

$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcastmodule');
if (!in_array("mod_broadcast", $modstatus)) {
		$query = "UPDATE #__modules SET published=0 WHERE module='mod_broadcast'";
		$db->setQuery($query);
		$db->query();
		echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_('Broadcast Module installed and').'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_('Broadcast Module not installed').'</span>'; 
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_('Broadcast Module installed').'</span>'; 	
}

echo '<br/><br/><span style="font-weight:bold;">'.JText::_('Installing Payment plugins:').'</span>';
//install JS Broadcast plugin and publish it
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcastplugin/jomsocialbroadcast');
if (!in_array("jomsocialbroadcast", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=1 WHERE element='jomsocialbroadcast' AND folder='community'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=1 WHERE element='jomsocialbroadcast' AND folder='community'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_('JomSocial Broadcast plugin installed and published').'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_('JomSocial Broadcast plugin not installed').'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_('JomSocial Broadcast plugin installed').'</span>'; 	
}
	
//install JOMWALL Broadcast plugin and publish it
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcastplugin/jomwallbroadcast');
if (!in_array("jomwall", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=1 WHERE element='jomwallbroadcast' AND folder='system'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=1 WHERE element='jomwallbroadcast' AND folder='system'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_('Jomwall Broadcast plugin installed and published').'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_('Jomwall Broadcast plugin not installed').'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_('Jomwall Broadcast plugin installed').'</span>'; 	
}



//install SUPERA Broadcast plugin and publish it
/*$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcastplugin/superabroadcast');
if (!in_array("supera", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=1 WHERE element='superabroadcast' AND folder='system'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=1 WHERE element='superabroadcast' AND folder='system'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_('Super Activity Broadcast plugin installed and published').'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_('Super Activity Broadcast plugin not installed').'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_('Super Activity Broadcast plugin installed').'</span>'; 	
}*/




	
//install techjoomlaAPI plugins 
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_facebook');
if (!in_array("plug_techjoomlaAPI_facebook", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='plug_techjoomlaAPI_facebook' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='plug_techjoomlaAPI_facebook' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Facebook API plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Techjoomla's Facebook API plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Facebook API plugin installed").'</span>'; 	
}

$installer = new JInstaller;
$result = $installer->install($install_source.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_linkedin');
if (!in_array("plug_techjoomlaAPI_linkedin", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='plug_techjoomlaAPI_linkedin' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='plug_techjoomlaAPI_linkedin' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Linkedin API plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Techjoomla's Linkedin API plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Linkedin API plugin installed").'</span>'; 	
}

$installer = new JInstaller;
$result = $installer->install($install_source.DS.'techjoomlaAPI'.DS.'plug_techjoomlaAPI_twitter');
if (!in_array("plug_techjoomlaAPI_twitter", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='plug_techjoomlaAPI_twitter' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='plug_techjoomlaAPI_twitter' AND folder='techjoomlaAPI'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Twitter API plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Techjoomla's Twitter API plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Techjoomla's Twitter API plugin installed").'</span>'; 	
}

//install broadcast_external plugins

/*$rssdts=explode('|',$rsslists);
							$irss=0;
							foreach($rssdts as $rsskey=>$rssvalue)
							{
							
								if(is_int($rsskey))
								{
								$final_rss[$irss]['title']='';

								}
								else
								$final_rss[$irss]['title']=$key;
								$final_rss[$irss]['url']=$rssvalue;
							$irss++;
							}
							$vv=$final_rss;
						$ss=	json_encode($vv);
*/

echo '<br/><br/><span style="font-weight:bold;">'.JText::_('Installing Extensions Intregration plugins:').'</span>';
//bradcast_content
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcast_extensions'.DS.'broadcast_content');
if (!in_array("broadcast_content", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='broadcast_content' AND folder='content'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='broadcast_content' AND folder='content'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast content plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Broadcast_content plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast content plugin installed").'</span>'; 	
}

//Broadcastdocman
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcast_extensions'.DS.'broadcastdocman');
if (!in_array("Broadcastdocman", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='broadcastdocman' AND folder='docman'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='broadcastdocman' AND folder='docman'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast Docman plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Broadcast Docman plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast Docman plugin installed").'</span>'; 	
}

//Broadcastdocman
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcast_extensions'.DS.'broadcasteasyblog');
if (!in_array("broadcasteasyblog", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='broadcasteasyblog' AND folder='easyblog'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='broadcasteasyblog' AND folder='easyblog'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast Easyblog plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Broadcast Easyblog plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast Easyblog plugin installed").'</span>'; 	
}

//broadcastflexicontent
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcast_extensions'.DS.'broadcastflexicontent');
if (!in_array("broadcastflexicontent", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='broadcastflexicontent' AND folder='flexicontent_fields'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='broadcastflexicontent' AND folder='flexicontent_fields'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast Flexicontent plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Broadcast Flexicontent plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast Flexicontent plugin installed").'</span>'; 	
}

//broadcastk2
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcast_extensions'.DS.'broadcastk2');
if (!in_array("broadcastk2", $status)) {
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='broadcastk2' AND folder='k2'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__plugins SET published=0 WHERE element='broadcastk2' AND folder='k2'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast K2 plugin installed and").'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_("Broadcast K2 plugin not installed").'</span>'; 	
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_("Broadcast K2 plugin installed").'</span>'; 	
}

function com_install()
{

	$errors = FALSE;
	$db = & JFactory::getDBO();
	$query = "CREATE TABLE IF NOT EXISTS `#__broadcast_config` (
  `user_id` int(11) NOT NULL,
  `broadcast_activity_config` varchar(500) NOT NULL,
	`params` text NOT NULL
);";

$db->setQuery($query);
	$db->query();
	
	$query = "SHOW COLUMNS FROM `#__broadcast_config`";
	$db->setQuery($query);
	$columns = $db->loadobjectlist();
	
	for($i = 0; $i < count($columns); $i++) {
				$field_array[] = $columns[$i]->Field;
	}
	

	$oldbroadcast=0;
	if (in_array('broadcast_rss_url', $field_array)) { 
	$oldbroadcast=1;
		$query = "ALTER TABLE #__broadcast_config CHANGE `broadcast_rss_url` `broadcast_rss` TEXT NOT NULL  ";
		$db->setQuery($query);
		if(!$db->query() )
		{
			echo $img_ERROR.JText::_('Unable to Alter #__broadcast_config').$BR;
			echo $db->getErrorMsg();
			return FALSE;
		}			
	}
	
	if (!in_array('params', $field_array)) { 
	$oldbroadcast=1;
		$query = "ALTER TABLE #__broadcast_config ADD    `params` text NOT NULL ";
		$db->setQuery($query);
		if(!$db->query() )
		{
			echo $img_ERROR.JText::_('Unable to ADD Column #__broadcast_config').$BR;
			echo $db->getErrorMsg();
			return FALSE;
		}			
	}
	
	$query = "SELECT user_id,broadcast_rss FROM `#__broadcast_config`";
	$db->setQuery($query);
	$rssdatas = $db->loadobjectlist();
	if(!empty($rssdatas) and $oldbroadcast==1)
	{
		foreach($rssdatas as $rss)
		{
				$json_arr=array();
				$rssurlarrs=explode('|',$rss->broadcast_rss);
				$i=0;
				foreach($rssurlarrs as $rssurlarr)
				{
				
					$json_arr[$i]['title']='';
					$json_arr[$i]['link']=$rssurlarr;			
					$i++;
				}
					
				$dat = new stdClass;
				$dat->user_id  = $rss->user_id;		
				$dat->broadcast_rss = json_encode($json_arr);		
					
				$db->updateObject('#__broadcast_config',$dat,'user_id');						
		
		
		}
	}
	
	//-- common images
	$img_OK = '<img src="images/publish_g.png" />';
	$img_WARN = '<img src="images/publish_y.png" />';
	$img_ERROR = '<img src="images/publish_r.png" />';
	$BR = '<br />';
	$destination = JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_broadcast' . DS . 'config' . DS ;
	$configarraydata = getConfig($destination.'configdefault.php'); 
	$configarray = getformattedarray($configarraydata); 
	
	if(JFolder::exists($destination.'config.php'))
    {         		
		$oldconfig = getConfig($destination.'config.php');
		$result = array_merge($configarray, $oldconfig); 
		$newconfigarray = getformattedarray($result); 
		
		if(JFile::exists($destination.'config.php'))
		{
		  JFile::delete($destination.'config.php');
		}
		$newdata = '<?php $broadcast_config = array('.print_r($newconfigarray, true).') ?>';
		JFile::write($destination.'config.php',$newdata);
    }
    				
	else if(!JFile::exists($destination.'config.php'))
	{
		$data = '<?php $broadcast_config = array('.print_r($configarray, true).') ?>';
		JFile::write($destination.'config.php',$data);
	}
		
	if(!JFolder::exists(JPATH_SITE.'/components/com_community/assets/favicon/'))
	{
		JFolder::create(JPATH_SITE.'/components/com_community/assets/favicon/');
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/twitter.png', JPATH_SITE.'/components/com_community/assets/favicon/twitter.png' );
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/linkedin.png', JPATH_SITE.'/components/com_community/assets/favicon/linkedin.png' );
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/facebook.png', JPATH_SITE.'/components/com_community/assets/favicon/facebook.png' );
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/rss.png', JPATH_SITE.'/components/com_community/assets/favicon/rss.png' );
	}
	else
	{
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/twitter.png', JPATH_SITE.'/components/com_community/assets/favicon/twitter.png' );
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/linkedin.png', JPATH_SITE.'/components/com_community/assets/favicon/linkedin.png' );
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/facebook.png', JPATH_SITE.'/components/com_community/assets/favicon/facebook.png' );
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/rss.png', JPATH_SITE.'/components/com_community/assets/favicon/rss.png' );
	}
	
	JFile::delete($destination.'configdefault.php');
	
}
function getformattedarray($result){
	foreach($result as $k=>$v)
	{
		if(is_array($v))
		{
			$str = 'array(';
			foreach ($v as $kk => $vv)
			{
				$str1[]= "'{$kk}' => '" . $vv . "'";
			} 	
			$str.= implode(",", $str1);;
			$str .= ')';
			$final[] ="'{$k}' => " . $str ;
		}
		else
			$final[]= "'{$k}' => '{$v}'" ;
	}
					
	return $configarray = implode(",\n", $final);
} 
	function getConfig($filename)
	{
		include($filename);
		return $broadcast_config;
	}
