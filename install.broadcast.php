<?php
/*
	* @package broadcast
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.folder' );
jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');

if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

/**
 * Script file of Ola component

 */
class com_broadcastInstallerScript
{

	/** @var array Obsolete files and folders to remove*/
	private $removeFilesAndFolders = array(
		'files'	=> array(
			'administrator/components/com_broadcast/admin.broadcast.php',
			'administrator/components/com_broadcast/controllers/settings.php',
			'administrator/components/com_broadcast/models/settings.php',
		),
		'folders' => array(
			'administrator/components/com_broadcast/config',
			'components/com_broadcast/bootstrap',
			'components/com_broadcast/assets',
			'administrator/components/com_broadcast/views/settings',
		)
	);
	
	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		if(!defined('DS')){
			define('DS',DIRECTORY_SEPARATOR);
		}
	}
	/**
	 * Runs after install, update or discover_update
	 * @param string $type install, update or discover_update
	 * @param JInstaller $parent
	 */
	function postflight( $type, $parent )
	{
		// Remove obsolete files and folders
		echo '<p><a href="http://techjoomla.com/table/documentation-for-broadcast/">' . JText::_('Read Documentation And FAQs Here') . '</a></p>';

		$removeFilesAndFolders = $this->removeFilesAndFolders;
		$this->_removeObsoleteFilesAndFolders($removeFilesAndFolders);
		// Install FOF
		$fofStatus = $this->_installFOF($parent);
		echo '<p>' . JText::_(' FOF ' . $type . ' successfully') . '</p>';
		// Install Techjoomla Straper
		$straperStatus = $this->_installStraper($parent);
		echo '<p>' . JText::_('  Techjoomla Straper ' . $type . ' successfully') . '</p></span>';
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_(' Broadcast ' . $type . ' successfully') . '</p>';
		


	}
	
	

	/**
	 * Removes obsolete files and folders
	 *
	 * @param array $removeFilesAndFolders
	 */
	private function _removeObsoleteFilesAndFolders($removeFilesAndFolders)
	{
		// Remove files
		jimport('joomla.filesystem.file');
		if(!empty($removeFilesAndFolders['files'])) foreach($removeFilesAndFolders['files'] as $file) {
			$f = JPATH_ROOT.DS.$file;
			if(!JFile::exists($f)) continue;
			JFile::delete($f);
		}
		// Remove folders
		jimport('joomla.filesystem.file');
		if(!empty($removeFilesAndFolders['folders'])) foreach($removeFilesAndFolders['folders'] as $folder) {
			$f = JPATH_ROOT.DS.$folder;
			if(!file_exists($f)) continue;
				JFolder::delete($f);
		}
	}	
private function _installFOF($parent)
	{
		$src = $parent->getParent()->getPath('source');

		// Install the FOF framework
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.date');

		/*$source = $src.'/fof';*/
		//changed by manoj
		$source = $src.'/tj_lib_fof';

		if(!defined('JPATH_LIBRARIES')) {
			$target = JPATH_ROOT.'/libraries/fof';
		} else {
			$target = JPATH_LIBRARIES.'/fof';
		}
		$haveToInstallFOF = false;
		if(!file_exists($target)) {
			$haveToInstallFOF = true;
		} else {
			$fofVersion = array();
			if(JFile::exists($target.'/version.txt')) {
				$rawData = JFile::read($target.'/version.txt');
				$info = explode("\n", $rawData);
				$fofVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$fofVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = JFile::read($source.'/version.txt');
			$info = explode("\n", $rawData);
			$fofVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);

			$haveToInstallFOF = $fofVersion['package']['date']->toUNIX() > $fofVersion['installed']['date']->toUNIX();
		}

		$installedFOF = false;
		if($haveToInstallFOF) {
			$versionSource = 'package';
			$installer = new JInstaller;
			$installedFOF = $installer->install($source);
		} else {
			$versionSource = 'installed';
		}

		if(!isset($fofVersion)) {
			$fofVersion = array();
			if(JFile::exists($target.'/version.txt')) {
				$rawData = JFile::read($target.'/version.txt');
				$info = explode("\n", $rawData);
				$fofVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$fofVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = JFile::read($source.'/version.txt');
			$info = explode("\n", $rawData);
			$fofVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);
			$versionSource = 'installed';
		}

		if(!($fofVersion[$versionSource]['date'] instanceof JDate)) {
			$fofVersion[$versionSource]['date'] = new JDate();
		}

		return array(
			'required'	=> $haveToInstallFOF,
			'installed'	=> $installedFOF,
			'version'	=> $fofVersion[$versionSource]['version'],
			'date'		=> $fofVersion[$versionSource]['date']->format('Y-m-d'),
		);
	}

	private function _installStraper($parent)
	{
		$src = $parent->getParent()->getPath('source');

		// Install the FOF framework
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.date');
		$source = $src.'/tj_strapper';
		$target = JPATH_ROOT.'/media/techjoomla_strapper';

		$haveToInstallStraper = false;
		if(!file_exists($target)) {
			$haveToInstallStraper = true;
		} else {
			$straperVersion = array();
			if(JFile::exists($target.'/version.txt')) {
				$rawData = JFile::read($target.'/version.txt');
				$info = explode("\n", $rawData);
				$straperVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$straperVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = JFile::read($source.'/version.txt');
			$info = explode("\n", $rawData);
			$straperVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);

			$haveToInstallStraper = $straperVersion['package']['date']->toUNIX() > $straperVersion['installed']['date']->toUNIX();
		}

		$installedStraper = false;
		if($haveToInstallStraper) {
			$versionSource = 'package';
			$installer = new JInstaller;
			$installedStraper = $installer->install($source);
		} else {
			$versionSource = 'installed';
		}

		if(!isset($straperVersion)) {
			$straperVersion = array();
			if(JFile::exists($target.'/version.txt')) {
				$rawData = JFile::read($target.'/version.txt');
				$info = explode("\n", $rawData);
				$straperVersion['installed'] = array(
					'version'	=> trim($info[0]),
					'date'		=> new JDate(trim($info[1]))
				);
			} else {
				$straperVersion['installed'] = array(
					'version'	=> '0.0',
					'date'		=> new JDate('2011-01-01')
				);
			}
			$rawData = JFile::read($source.'/version.txt');
			$info = explode("\n", $rawData);
			$straperVersion['package'] = array(
				'version'	=> trim($info[0]),
				'date'		=> new JDate(trim($info[1]))
			);
			$versionSource = 'installed';
		}

		if(!($straperVersion[$versionSource]['date'] instanceof JDate)) {
			$straperVersion[$versionSource]['date'] = new JDate();
		}

		return array(
			'required'	=> $haveToInstallStraper,
			'installed'	=> $installedStraper,
			'version'	=> $straperVersion[$versionSource]['version'],
			'date'		=> $straperVersion[$versionSource]['date']->format('Y-m-d'),
		);
	}
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	 
	function install($parent) 
	{
		$this->com_install();
	}
	
	function com_install()
	{
		$db = JFactory::getDBO();
		$condtion = array(0 => '\'community\'',1 => '\'techjoomlaAPI\'' ,2 => '\'content\'',3 => '\'k2\'',4 => '\'docman\'',5 => '\'easybolg\'',6 => '\'flexicontent\'');
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
		$status = $db->loadColumn();
		$install_status = new JObject();
		 $install_source = dirname(__FILE__);

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
		$modstatus = $db->loadColumn();

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
		echo '<br/><span style="font-weight:bold;">'.JText::_('Installing Plugins:').'</span>';
//install  Broadcast plugin and publish it
		$installer = new JInstaller;
		$result = $installer->install($install_source.DS.'broadcastplugin/broadcast_j3');
		if (!in_array("broadcast_j3", $status)) {
			if(JVERSION >= '1.6.0')
			{
				$query = "UPDATE #__extensions SET enabled=1 WHERE element='broadcast_j3' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
			else
			{
				$query = "UPDATE #__plugins SET published=1 WHERE element='broadcast_j3' AND folder='system'";
				$db->setQuery($query);
				$db->query();
			}
			echo ($result)?'<br/><span style="font-weight:bold; color:green;">'.JText::_(' Broadcast system plugin for joomla 3.0 and above installed and published').'</span>':'<br/><span style="font-weight:bold; color:red;">'.JText::_('JomSocial Broadcast plugin not installed').'</span>'; 	
		}
		else
		{
			echo '<br/><span style="font-weight:bold; color:green;">'.JText::_('Broadcast system plugin for joomla 3.0 and above installed and published').'</span>'; 	
		}
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
		$result = $installer->install($install_source.DS.'techjoomlaAPI/Facebook');
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
		$result = $installer->install($install_source.DS.'techjoomlaAPI/Linkedin');
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
		$result = $installer->install($install_source.DS.'techjoomlaAPI/Twitter');
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
		$result = $installer->install($install_source.DS.'broadcast_extensions/broadcast_content');
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
		$result = $installer->install($install_source.DS.'broadcast_extensions/broadcastdocman');
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
		$result = $installer->install($install_source.DS.'broadcast_extensions/broadcasteasyblog');
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
		$result = $installer->install($install_source.DS.'broadcast_extensions/broadcastflexicontent');
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
		$result = $installer->install($install_source.DS.'broadcast_extensions/broadcastk2');
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



			$errors = FALSE;
			$db = JFactory::getDBO();
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
			
				if($oldbroadcast==1)
				{
						$query = "SELECT user_id,broadcast_rss FROM `#__broadcast_config`";
						$db->setQuery($query);
						$rssdatas = $db->loadobjectlist();

						if(!empty($rssdatas) )
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
				}
			//-- common images
			$img_OK = '<img src="images/publish_g.png" />';
			$img_WARN = '<img src="images/publish_y.png" />';
			$img_ERROR = '<img src="images/publish_r.png" />';
			$BR = '<br />';
			
			if(JFolder::exists(JPATH_SITE.'/components/com_community/'))
			{
				if(!JFolder::exists(JPATH_SITE.'/components/com_community/assets/favicon/'))
				{
					JFolder::create(JPATH_SITE.'/components/com_community/assets/favicon/');
					JFile::move(JPATH_SITE.'/components/com_broadcast/images/twitter.png', JPATH_SITE.'/components/com_community/assets/favicon/twitter.png' );
					JFile::move(JPATH_SITE.'/components/com_broadcast/images/linkedin.png', JPATH_SITE.'/components/com_community/assets/favicon/linkedin.png' );
					JFile::move(JPATH_SITE.'/components/com_broadcast/images/facebook.png', JPATH_SITE.'/components/com_community/assets/favicon/facebook.png' );
					JFile::move(JPATH_SITE.'/components/com_broadcast/images/rss.png', JPATH_SITE.'/components/com_community/assets/favicon/facebook.png' );
				}
				else
				{
					JFile::move(JPATH_SITE.'/components/com_broadcast/images/twitter.png', JPATH_SITE.'/components/com_community/assets/favicon/twitter.png' );
					JFile::move(JPATH_SITE.'/components/com_broadcast/images/linkedin.png', JPATH_SITE.'/components/com_community/assets/favicon/linkedin.png' );
					JFile::move(JPATH_SITE.'/components/com_broadcast/images/facebook.png', JPATH_SITE.'/components/com_community/assets/favicon/facebook.png' );
					JFile::move(JPATH_SITE.'/components/com_broadcast/images/rss.png', JPATH_SITE.'/components/com_community/assets/favicon/facebook.png' );
				}
			}
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
	
	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		// $parent is the class calling this method
		//echo '<p>' . JText::_('COM_BROADCASST_UNINSTALL_TEXT') . '</p>';
	}
 
	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
			$this->com_install();
		// $parent is the class calling this method
		//echo '<p>' . JText::_('COM_OLA_UPDATE_TEXT') . '</p>';
	}
 

}//End of class

