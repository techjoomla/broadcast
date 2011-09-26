<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');

class broadcastModelSettings extends JModel
{
	function getAPIpluginData()
	{
		$condtion = array(0 => '\'techjoomlaAPI\'');
		$condtionatype = join(',',$condtion);  
		if(JVERSION >= '1.6.0')
		{
			$query = "SELECT extension_id as id,name,element,enabled as published FROM #__extensions WHERE folder in ($condtionatype)";
		}
		else
		{
			$query = "SELECT id,name,element,published FROM #__plugins WHERE folder in ($condtionatype)";
		}
		$this->_db->setQuery($query);
		return $this->_db->loadobjectList();
	}
	function store()
	{
		global $mainframe;	
		$mainframe = JFactory::getApplication();
		$config	= JRequest::getVar('data', '', 'post', 'array', JREQUEST_ALLOWRAW );
		//code to enable the techjoomlaAPI plugins
		$condtion = array(0 => '\'techjoomlaAPI\'');
		$condtionatype = join(',',$condtion);  
		if(JVERSION>='1.6.0')
			$query = "UPDATE #__extensions SET enabled=1";
		else
			$query = "UPDATE #__plugins SET published=1";
		$query .=" WHERE element IN ('".join("','",$config['api'])."') AND folder in ($condtionatype)";
		$this->_db->setQuery($query);
		$this->_db->query();
		
		$file = 	JPATH_SITE.DS."administrator".DS."components".DS."com_broadcast".DS."config".DS."config.php";
		if ($config)
		{
			$file_contents[] = '<?php';
			$file_contents[] = "\n";
			$file_contents[] = '$broadcast_config = array(';
			foreach ($config as $k => $v) 
			{
				if(is_array($v))
				{
					
					$str = 'array(';
					$str1=array();
					foreach ($v as $kk => $vv)
					{
						$str1[]= "'{$kk}' => '" . $vv . "'";
					} 	
					$str.= implode(",", $str1);;
					$str .= ')';
					$opts[] ="'{$k}' => " . $str ;
				}
				else
					$opts[] = "'{$k}' => '" . addslashes($v) . "'";
			}
	
			$file_contents[] = implode(",\n", $opts);
			$file_contents[] = ')';		
			$file_contents[] = "\n";
			$file_contents[] = "?>";
			$file_content = implode("\n", $file_contents);
			if (JFile::write($file, $file_content)) 
				return true;
			else
				return false;
		}
		return false;
		
		//$mainframe->redirect('index.php?option=com_broadcast&view=settings', $msg);
	}//store() ends

}
?>
