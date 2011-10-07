<?php

jimport( 'joomla.filesystem.folder' );
jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');


$db = & JFactory::getDBO();
$condtion = array(0 => '\'community\'',0 => '\'techjoomlaAPI\'');
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
	if(JVERSION >= '1.6.0')
	{
		$query = "UPDATE #__extensions SET enabled=0 WHERE element='mod_broadcast'";
		$db->setQuery($query);
		$db->query();
	}
	else
	{
		$query = "UPDATE #__modules SET published=0 WHERE module='mod_broadcast'";
		$db->setQuery($query);
		$db->query();
	}
	echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_('Broadcast Module installed and').'</span><span style="font-weight:bold; color:red;">'.JText::_(" Not published").'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_('Broadcast Module not installed').'</span>'; 
}
else
{
	echo '<br/><span style="font-weight:bold; color:green;">'.JText::_('Broadcast Module installed').'</span>'; 	
}

echo '<br/><br/><span style="font-weight:bold;">'.JText::_('Installing Payment plugins:').'</span>';
//install JS Broadcast plugin and publish it
$installer = new JInstaller;
$result = $installer->install($install_source.DS.'broadcastplugin');
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

function com_install()
{
	$errors = FALSE;
	$db = & JFactory::getDBO();
	
	//-- common images
	$img_OK = '<img src="images/publish_g.png" />';
	$img_WARN = '<img src="images/publish_y.png" />';
	$img_ERROR = '<img src="images/publish_r.png" />';
	$BR = '<br />';
	$destination = JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_broadcast' . DS . 'config' . DS ;
	$configarray = array(
					'show_name' => '1',
					'status_via' => '0',
					'status_skip' => '#,@',
					'url_apikey' => '',
					'purgelimit' => '50',
					'show_status_rss' => '0',
					'rss_link_limit' => '3',
					'private_key_cronjob' => '1234'
					); 
	   
	if(JFolder::exists($destination))
    {
        JFolder::create(JPATH_SITE.DS.'administrator'. DS . 'components' . DS . 'com_broadcast' . DS . 'oldconfig');
        $old_destination =	JPATH_SITE . DS . 'administrator' . DS . 'components' . DS . 'com_broadcast' . DS . 'oldconfig' . DS ;	
         		
		if(JFile::exists($destination.'config.php')){
			JFile::move($destination.'config.php', $old_destination.'config.php');
		}

		$config2 = getConfig($old_destination.'config.php');
		$result = array_merge($configarray, $config2);
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
						
		$newconfigarray = implode(",\n", $final);
		
		if(JFile::exists($destination.'config.php'))
		{
		  JFile::delete($destination.'config.php');
		}
			$newdata = '<?php $broadcast_config = array('.print_r($newconfigarray, true).') ?>';
			JFile::write($destination.'config.php',$newdata);
		
		JFolder::delete($old_destination);
    }
    				
	else if(!JFile::exists($destination.'config.php'))
	{
		$data = '<?php $broadcast_config = array('.print_r($configarray, true).') ?>';
		JFile::write($destination.'config.php',$data);
	}
		
	if(JFolder::exists(JPATH_SITE.'/components/com_community/assets/favicon/'))
	{
		JFolder::create(JPATH_SITE.'/components/com_community/assets/favicon/');
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/twitter.png', JPATH_SITE.'/components/com_community/assets/favicon/twitter.png' );
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/linkedin.png', JPATH_SITE.'/components/com_community/assets/favicon/linkedin.png' );
		JFile::move(JPATH_SITE.'/components/com_broadcast/images/facebook.png', JPATH_SITE.'/components/com_community/assets/favicon/facebook.png' );
	}
}

	function getConfig($filename)
	{
		include($filename);
		return $broadcast_config;
	}
