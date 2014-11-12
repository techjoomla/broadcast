<?php
/**
	* @package Broadcast
	* @copyright Copyright (C)2010-2011 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link http://www.techjoomla.com
*/
defined('_JEXEC') or die('Restricted access');

jimport ( 'joomla.filesystem.folder');
jimport ('joomla.installer.installer');
jimport ('joomla.filesystem.file');

if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}

/**
 * Script file of Broadcast component

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
			'plugins/techjoomlaAPI/logs',
			'administrator/components/com_broadcast/views/settings',
		)
	);
	private $installation_queue = array(
		// modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules'=>array(
			'admin'=>array(

						),
			'site'=>array(
					'broadcastmodule' => array('position-7', 0)
						)
		),
		// plugins => { (folder) => { (element) => (published) }* }*
		'plugins'=>array(
			'system'=>array(
				'Jomwall Broadcast'=>0,
				'Broadcast System Plugin'=>1,

			),
			'community'=>array(
				'Jomsocial Broadcast'=>0
			),
			'broadcast_extensions'=>array(
				'broadcast_content'=>0,
				'broadcastdocman'=>0,
				'broadcasteasyblog'=>0,
				'broadcastflexicontent'=>0,
				'broadcastk2'=>0,
			),

			'techjoomlaAPI'=>array(
				'Facebook'=>1,
				'Googleplus'=>0,
				'Linkedin'=>1,
				'Twitter'=>1,
			)
			),
			'libraries'=>array(
			'activity'=>1
			)
		);


		private $uninstall_queue = array(
		'applications'=>array(
			'easysocial'=>array(
					'easysocial_broadcast'=>0,
				)),

				// modules => { (folder) => { (module) => { (position), (published) } }* }*
		'modules'=>array(
			'admin'=>array(

						),
			'site'=>array(
					'broadcastmodule' => array('position-7', 0)
						)
		),
		// plugins => { (folder) => { (element) => (published) }* }*
		'plugins'=>array(
			'system'=>array(
				'Jomwall Broadcast'=>0,
				'Broadcast System Plugin'=>1,

			),
			'community'=>array(
				'Jomsocial Broadcast'=>0
			),
			'broadcast_extensions'=>array(
				'broadcast_content'=>0,
				'broadcastdocman'=>0,
				'broadcasteasyblog'=>0,
				'broadcastflexicontent'=>0,
				'broadcastk2'=>0,
			),

			),

		);

	//Since jticketing version 1.5
	function fix_db_on_update()
	{
		$db = JFactory::getDBO();
		$config = JFactory::getConfig();
		if(JVERSION>=3.0)
		$dbprefix=$config->get( 'dbprefix' );
		else
		$dbprefix=$config->getValue( 'config.dbprefix' );

		$xml=JFactory::getXML(JPATH_ADMINISTRATOR.'/components/com_broadcast/broadcast.xml');
		$version=(string)$xml->version;
		$this->version=(float)$version;


		if((float)$this->version<1.5)
		{
			if(file_exists(JPATH_SITE.'/components/com_broadcast/helper.php'))
			{
				require_once JPATH_SITE.'/components/com_broadcast/helper.php';
				$combroadcastHelper=new combroadcastHelper();
				$params = array();
				$params['Facebook'] = 1;

				$qry = "SELECT user_id FROM #__broadcast_config";
				$db->setQuery($qry);
				$userids = $db->loadObjectList();

				if(!empty($userids))
				{
					foreach($userids AS $userid)
					{
						$combroadcastHelper->saveParams($userid->user_id,$params);
					}
				}
			}

		}

		//since version 1.5
		//check if column - paypal_email exists
		$query="SHOW COLUMNS FROM #__broadcast_config WHERE `Field` = 'params'";
		$db->setQuery($query);
		$check=$db->loadResult();
		if(!$check)
		{
			$query="ALTER TABLE  `#__broadcast_config` ADD  `params` text NOT NULL  AFTER  `broadcast_rss`";
			$db->setQuery($query);
			if ( !$db->execute() ) {
				JError::raiseError( 500, $db->stderr() );
			}
		}

	}

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

		// Install subextensions
		$status = $this->_installSubextensions($parent);
		// Install FOF
		$fofStatus = $this->_installFOF($parent);

		// Install Techjoomla Straper
		$straperStatus = $this->_installStraper($parent);
		//$fofStatus=$straperStatus='';

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'/media/techjoomla_strapper/css/bootstrap.min.css' );
		// Do all releated Tag line/ logo etc
		$this->taglinMsg();
		// Show the post-installation page
		$this->_renderPostInstallation($status, $fofStatus, $straperStatus, $parent);
		//Remove non required files and folders
		$removeFilesAndFolders = $this->removeFilesAndFolders;
		$this->_removeObsoleteFilesAndFolders($removeFilesAndFolders);


	}
	/**
	 * Renders the post-installation message
	 */
	private function _renderPostInstallation($status, $fofStatus, $straperStatus, $parent)
	{
		$document = JFactory::getDocument();
			include_once JPATH_ROOT.'/media/techjoomla_strapper/strapper.php';
			TjAkeebaStrapper::bootstrap();

		?>
		<?php $rows = 1;?>
		<link rel="stylesheet" type="text/css" href="<?php echo JURI::root().'media/techjoomla_strapper/css/bootstrap.min.css'?>"/>
		<div class="techjoomla-bootstrap" >
		<table class="table-condensed table">
			<thead>
				<tr class="row1">
					<th class="title" colspan="2">Extension</th>
					<th width="30%">Status</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3">Social Broadcast - Social sync for your Joomla website !</td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row2">
					<td class="key" colspan="2"><strong>Broadcast component</strong></td>
					<td><strong style="color: green">Installed</strong></td>
				</tr>
					<tr class="row2">
					<td class="key" colspan="2">
						<strong>Framework on Framework (FOF) <?php echo $fofStatus['version']?></strong> [<?php echo $fofStatus['date'] ?>]
					</td>
					<td><strong>
						<span style="color: <?php echo $fofStatus['required'] ? ($fofStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
							<?php echo $fofStatus['required'] ? ($fofStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
						</span>
					</strong></td>
				</tr>
				<tr class="row2">
					<td class="key" colspan="2">
						<strong>TechJoomla Strapper <?php echo $straperStatus['version']?></strong> [<?php echo $straperStatus['date'] ?>]
					</td>
					<td><strong>
						<span style="color: <?php echo $straperStatus['required'] ? ($straperStatus['installed']?'green':'red') : '#660' ?>; font-weight: bold;">
							<?php echo $straperStatus['required'] ? ($straperStatus['installed'] ?'Installed':'Not Installed') : 'Already up-to-date'; ?>
						</span>
					</strong></td>
				</tr>

				<?php if (count($status->modules)) : ?>
				<tr class="row1">
					<th>Module</th>
					<th>Client</th>
					<th></th>
					</tr>
				<?php foreach ($status->modules as $module) : ?>
				<tr class="row2 <?php //echo ($rows++ % 2); ?>">
					<td class="key"><?php echo ucfirst($module['name']); ?></td>
					<td class="key"><?php echo ucfirst($module['client']); ?></td>
					<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($module['result'])?'Installed':'Not installed'; ?></strong>
					<?php
						if(!empty($module['result'])) // if installed then only show msg
						{
						echo $mstat=($module['status']? "<span class=\"label label-success\">Enabled</span>" : "<span class=\"label label-important\">Disabled</span>");

						}
					?>

					</td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>
				<?php if (count($status->plugins)) : ?>
				<tr class="row1">
					<th colspan="2">Plugin</th>
			<!--		<th>Group</th> -->
					<th></th>
				</tr>
				<?php
					$oldplugingroup="";
				foreach ($status->plugins as $plugin) :
				//print"<pre>";print_r($status); die("dddd");
					if($oldplugingroup!=$plugin['group'])
					{
						$oldplugingroup=$plugin['group'];
				?>
					<tr class="row0">
						<th colspan="2"><strong><?php echo ucfirst($oldplugingroup)." Plugins";?></strong></th>
						<th></th>
				<!--		<td></td> -->
					</tr>
				<?php
					}

				 ?>
				<tr class="row2 <?php //echo ($rows++ % 2); ?>">
					<td colspan="2" class="key"><?php echo ucfirst($plugin['name']); ?></td>
		<!--			<td class="key"><?php //echo ucfirst($plugin['group']); ?></td> -->
					<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?'Installed':'Not installed'; ?></strong>
					<?php
						if(!empty($plugin['result']))
						{
						echo $pstat=($plugin['status']? "<span class=\"label label-success\">Enabled</span>" : "<span class=\"label label-important\">Disabled</span>");

						}
					?>
					</td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>

					<?php if (count($status->libraries)) : ?>
				<tr class="row1">
					<th>Library</th>
					<th></th>
					<th></th>
					</tr>
				<?php foreach ($status->libraries as $libraries) : ?>
				<tr class="row2 <?php //echo ($rows++ % 2); ?>">
					<td class="key"><?php echo ucfirst($libraries['name']); ?></td>
					<td class="key"></td>
					<td><strong style="color: <?php echo ($libraries['result'])? "green" : "red"?>"><?php echo ($libraries['result'])?'Installed':'Not installed'; ?></strong>
					<?php
						if(!empty($libraries['result'])) // if installed then only show msg
						{
						echo $mstat=($libraries['status']? "<span class=\"label label-success\">Enabled</span>" : "<span class=\"label label-important\">Disabled</span>");

						}
					?>

					</td>
				</tr>
				<?php endforeach;?>
				<?php endif;


				if (isset($status->app_install)) :
					if (count($status->app_install)) : ?>
					<tr class="row1">
						<th>EasySocial App: Broadcast</th>
						<th></th>
						<th></th>
						</tr>
					<?php

						foreach ($status->app_install as $app_install) : ?>
							<tr class="row2">
								<td class="key"><?php echo ucfirst($app_install['name']); ?></td>
								<td class="key"></td>
								<td><strong style="color: <?php echo ($app_install['result'])? "green" : "red"?>"><?php echo ($app_install['result'])?'Installed':'Not installed'; ?></strong>
								<?php

									if(!empty($app_install['result'])) // if installed then only show msg
									{
										echo $mstat=($app_install['status']? "<span class=\"label label-success\">Enabled</span>" : "<span class=\"label label-important\">Disabled</span>");
									}

								?>
								</td>
							</tr>
						<?php endforeach;?>
					<?php endif;
				endif;?>
			</tbody>
		</table>
		</div> <!-- end akeeba bootstrap -->

		<?php

	}

	/**
	 * Installs subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param JInstaller $parent
	 * @return JObject The subextension installation status
	 */
	private function _installSubextensions($parent)
	{
		$src = $parent->getParent()->getPath('source');

		$db = JFactory::getDbo();

		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();

		// Modules installation

		if(count($this->installation_queue['modules'])) {
			foreach($this->installation_queue['modules'] as $folder => $modules) {
				if(count($modules))
					foreach($modules as $module => $modulePreferences)
					{
						// Install the module
						if(empty($folder))
							$folder = 'site';
						$path = "$src/modules/$folder/$module";
						if(!is_dir($path))// if not dir
						{
							$path = "$src/modules/$folder/mod_$module";
						}
						if(!is_dir($path)) {
							$path = "$src/modules/$module";
						}

						if(!is_dir($path)) {
							$path = "$src/modules/mod_$module";
						}
						if(!is_dir($path))
						{

							$fortest='';
							//continue;
						}

						// Was the module already installed?
						$sql = $db->getQuery(true)
							->select('COUNT(*)')
							->from('#__modules')
							->where($db->qn('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($sql);

						$count = $db->loadResult();
						$installer = new JInstaller;
						$result = $installer->install($path);
						$status->modules[] = array(
							'name'=>$module,
							'client'=>$folder,
							'result'=>$result,
							'status'=>$modulePreferences[1]
						);
						// Modify where it's published and its published state
						if(!$count) {
							// A. Position and state
							list($modulePosition, $modulePublished) = $modulePreferences;
							if($modulePosition == 'cpanel') {
								$modulePosition = 'icon';
							}
							$sql = $db->getQuery(true)
								->update($db->qn('#__modules'))
								->set($db->qn('position').' = '.$db->q($modulePosition))
								->where($db->qn('module').' = '.$db->q('mod_'.$module));
							if($modulePublished) {
								$sql->set($db->qn('published').' = '.$db->q('1'));
							}
							$db->setQuery($sql);
							$db->query();

							// B. Change the ordering of back-end modules to 1 + max ordering
							if($folder == 'admin') {
								$query = $db->getQuery(true);
								$query->select('MAX('.$db->qn('ordering').')')
									->from($db->qn('#__modules'))
									->where($db->qn('position').'='.$db->q($modulePosition));
								$db->setQuery($query);
								$position = $db->loadResult();
								$position++;

								$query = $db->getQuery(true);
								$query->update($db->qn('#__modules'))
									->set($db->qn('ordering').' = '.$db->q($position))
									->where($db->qn('module').' = '.$db->q('mod_'.$module));
								$db->setQuery($query);
								$db->query();
							}

							// C. Link to all pages
							$query = $db->getQuery(true);
							$query->select('id')->from($db->qn('#__modules'))
								->where($db->qn('module').' = '.$db->q('mod_'.$module));
							$db->setQuery($query);
							$moduleid = $db->loadResult();

							$query = $db->getQuery(true);
							$query->select('*')->from($db->qn('#__modules_menu'))
								->where($db->qn('moduleid').' = '.$db->q($moduleid));
							$db->setQuery($query);
							$assignments = $db->loadObjectList();
							$isAssigned = !empty($assignments);
							if(!$isAssigned) {
								$o = (object)array(
									'moduleid'	=> $moduleid,
									'menuid'	=> 0
								);
								$db->insertObject('#__modules_menu', $o);
							}
						}
					}
			}
		}

		// Plugins installation
		if(count($this->installation_queue['plugins'])) {
			foreach($this->installation_queue['plugins'] as $folder => $plugins) {
				if(count($plugins))
				foreach($plugins as $plugin => $published) {
					$path = "$src/plugins/$folder/$plugin";
					if(!is_dir($path)) {
						$path = "$src/plugins/$folder/plg_$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/$plugin";
					}
					if(!is_dir($path)) {
						$path = "$src/plugins/plg_$plugin";
					}
					if(!is_dir($path)) continue;

					// Was the plugin already installed?
					$query = $db->getQuery(true)
						->select('COUNT(*)')
						->from($db->qn('#__extensions'))
						->where('( '.($db->qn('name').' = '.$db->q($plugin)) .' OR '. ($db->qn('element').' = '.$db->q($plugin)) .' )')
						->where($db->qn('folder').' = '.$db->q($folder));
					$db->setQuery($query);
					$count = $db->loadResult();

					$installer = new JInstaller;
					$result = $installer->install($path);

					$status->plugins[] = array('name'=>$plugin,'group'=>$folder, 'result'=>$result,'status'=>$published);


					if($published && !$count) {
						$query = $db->getQuery(true)
							->update($db->qn('#__extensions'))
							->set($db->qn('enabled').' = '.$db->q('1'))
							->where('( '.($db->qn('name').' = '.$db->q($plugin)) .' OR '. ($db->qn('element').' = '.$db->q($plugin)) .' )')
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}

		// library installation
		if(!empty($this->installation_queue['libraries']) and count($this->installation_queue['libraries']))
		 {
			foreach($this->installation_queue['libraries']  as $folder=>$status1)
			{

					$path = "$src/libraries/$folder";

					$query = $db->getQuery(true)
						->select('COUNT(*)')
						->from($db->qn('#__extensions'))
						->where('( '.($db->qn('name').' = '.$db->q($folder)) .' OR '. ($db->qn('element').' = '.$db->q($folder)) .' )')
						->where($db->qn('folder').' = '.$db->q($folder));
					$db->setQuery($query);
					$count = $db->loadResult();

					$installer = new JInstaller;
					$result = $installer->install($path);

					$status->libraries[] = array('name'=>$folder,'group'=>$folder, 'result'=>$result,'status'=>$status1);
					//print"<pre>"; print_r($status->plugins); die;

					if($published && !$count) {
						$query = $db->getQuery(true)
							->update($db->qn('#__extensions'))
							->set($db->qn('enabled').' = '.$db->q('1'))
							->where('( '.($db->qn('name').' = '.$db->q($folder)) .' OR '. ($db->qn('element').' = '.$db->q($folder)) .' )')
							->where($db->qn('folder').' = '.$db->q($folder));
						$db->setQuery($query);
						$db->query();
					}
			}
		}
		/*
		 * 'applications'=>array(
			'easysocial'array(
					'quick2cartproducts'=>0,
					'quick2cartstores'=>0

			),
		 * */
		//Application Installations
		if(file_exists(JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php'))
		{
			require_once( JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/foundry.php' );
			$installer     = Foundry::get( 'Installer' );
			// The $path here refers to your application path
			$installer->load( $src."/plugins/easysocial/easysocial_broadcast" );
			$plg_install=$installer->install();
			$status->app_install[] = array('name'=>'easysocial_broadcast','group'=>'easysocial_broadcast', 'result'=>$plg_install,'status'=>'1');
		}

		return $status;
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

/**
	 * Uninstalls subextensions (modules, plugins) bundled with the main extension
	 *
	 * @param JInstaller $parent
	 * @return JObject The subextension uninstallation status
	 */
	private function _uninstallSubextensions($parent)
	{
		jimport('joomla.installer.installer');

		$db =  JFactory::getDBO();

		$status = new JObject();
		$status->modules = array();
		$status->plugins = array();

		$src = $parent->getParent()->getPath('source');

		// Modules uninstallation
		if(count($this->uninstall_queue['modules'])) {
			foreach($this->uninstall_queue['modules'] as $folder => $modules) {
				if(count($modules)) foreach($modules as $module => $modulePreferences) {
					// Find the module ID
					$sql = $db->getQuery(true)
						->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('element').' = '.$db->q('mod_'.$module))
						->where($db->qn('type').' = '.$db->q('module'));
					$db->setQuery($sql);
					$id = $db->loadResult();
					// Uninstall the module
					if($id) {
						$installer = new JInstaller;
						$result = $installer->uninstall('module',$id,1);
						$status->modules[] = array(
							'name'=>'mod_'.$module,
							'client'=>$folder,
							'result'=>$result
						);
					}
				}
			}
		}

		// Plugins uninstallation
		if(count($this->uninstall_queue['plugins'])) {
			foreach($this->uninstall_queue['plugins'] as $folder => $plugins) {
				if(count($plugins)) foreach($plugins as $plugin => $published) {
					$sql = $db->getQuery(true)
						->select($db->qn('extension_id'))
						->from($db->qn('#__extensions'))
						->where($db->qn('type').' = '.$db->q('plugin'))
						->where($db->qn('element').' = '.$db->q($plugin))
						->where($db->qn('folder').' = '.$db->q($folder));
					$db->setQuery($sql);

					$id = $db->loadResult();
					if($id)
					{
						$installer = new JInstaller;
						$result = $installer->uninstall('plugin',$id);
						$status->plugins[] = array(
							'name'=>'plg_'.$plugin,
							'group'=>$folder,
							'result'=>$result
						);
					}
				}
			}
		}

		return $status;
	}

	private function _renderPostUninstallation($status, $parent)
	{
		?>
		<?php $rows = 0;?>
		<h2><?php echo JText::_('Invitex Uninstallation Status'); ?></h2>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
					<th width="30%"><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2"><?php echo 'Invitex '.JText::_('Component'); ?></td>
					<td><strong style="color: green"><?php echo JText::_('Removed'); ?></strong></td>
				</tr>
				<?php if (count($status->modules)) : ?>
				<tr>
					<th><?php echo JText::_('Module'); ?></th>
					<th><?php echo JText::_('Client'); ?></th>
					<th></th>
				</tr>
				<?php foreach ($status->modules as $module) : ?>
				<tr class="row<?php echo (++ $rows % 2); ?>">
					<td class="key"><?php echo $module['name']; ?></td>
					<td class="key"><?php echo ucfirst($module['client']); ?></td>
					<td><strong style="color: <?php echo ($module['result'])? "green" : "red"?>"><?php echo ($module['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
				</tr>
				<?php endforeach;?>
				<?php endif;?>
				<?php if (count($status->plugins)) : ?>
				<tr>
					<th><?php echo JText::_('Plugin'); ?></th>
					<th><?php echo JText::_('Group'); ?></th>
					<th></th>
				</tr>
				<?php foreach ($status->plugins as $plugin) : ?>
				<tr class="row<?php echo (++ $rows % 2); ?>">
					<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
					<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
					<td><strong style="color: <?php echo ($plugin['result'])? "green" : "red"?>"><?php echo ($plugin['result'])?JText::_('Removed'):JText::_('Not removed'); ?></strong></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		$status = $this->_uninstallSubextensions($parent);

		// Show the post-uninstallation page
		$this->_renderPostUninstallation($status, $parent);
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
			$this->com_install();
			$this->fix_db_on_update();
			//$parent is the class calling this method
		//echo '<p>' . JText::_('COM_OLA_UPDATE_TEXT') . '</p>';
	}
 /*	Tag line, version etc
	 *
	 *
	 * */
	function taglinMsg()
	{
/*:TODO*/

	} // end of tagline msg

}//End of class

