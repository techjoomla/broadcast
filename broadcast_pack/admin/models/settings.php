<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');

class broadcastModelSettings extends JModel
{
	function store()
	{
		$app = JFactory::getApplication();
		$config	= JRequest::getVar('data', '', 'post', 'array', JREQUEST_ALLOWRAW );
		$file = 	JPATH_SITE.DS."administrator".DS."components".DS."com_broadcast".DS."config".DS."config.php";

		if ($config)
		{
			$file_contents="<?php \n\n";
			$file_contents.="\$config=array(\n".$this->row2text($config)."\n);\n";
			$file_contents.="\n?>";
			$img_path = 'img src=\"'.JURI::root(); 
	    $file_contents=str_replace( 'img src=\"', $img_path, $file_contents );
			if (JFile::write($file, $file_contents)) 
				$msg = JText::_('CONFIG_SAVED');
			else
				$msg = JText::_('CONFIG_SAVE_PROBLEM');
		}
		
		$apiconfig	= JRequest::getVar('apidata', '', 'post', 'array', JREQUEST_ALLOWRAW );
		$file = 	JPATH_SITE.DS."components".DS."com_broadcast".DS."lib".DS."config.php";

		if ($apiconfig)
		{
			$file_contents="<?php \n\n";
			$file_contents.="class BroadcastConfig \n {\n".$this->row2var($apiconfig).";\n}\n";
			$file_contents.="\n?>";
		
			$img_path = 'img src=\"'.JURI::root(); 
	    $file_contents=str_replace( 'img src=\"', $img_path, $file_contents );
			if (JFile::write($file, $file_contents)) 
				$msg = JText::_('CONFIG_SAVED');
			else
				$msg = JText::_('CONFIG_SAVE_PROBLEM');
		}
		
		$app->redirect('index.php?option=com_broadcast&view=settings', $msg);
	}//store() ends

	function row2text($row,$dvars=array())
	{
		reset($dvars);
		while(list($idx,$var)=each($dvars))
		unset($row[$var]);
		$text='';
		reset($row);
		$flag=0;
		$i=0;
		while(list($var,$val)=each($row))
		{
			if($flag==1)
				$text.=",\n";
			elseif($flag==2)
				$text.=",\n";
				$flag=1;

				if(is_numeric($var))
					if($var{0}=='0')
					$text.="'$var'=>";
				else
				{
					if($var!==$i)
					$text.="$var=>";
					$i=$var;
				}
			else
				$text.="'$var'=>";
				$i++;

			if(is_array($val))
				{
					$text.="array(".$this->row2var($val,$dvars).")";
					$flag=2;
				}
			else
					$text.="\"".addslashes($val)."\"";
		}
		
		return($text);
	}
	
	function row2var($row,$dvars=array())
	{
		reset($dvars);
		while(list($idx,$var)=each($dvars))
		unset($row[$var]);
		$text='';
		reset($row);
		$flag=0;
		$i=0;
		while(list($var,$val)=each($row))
		{
			if($flag==1)
				$text.=";\n";
			elseif($flag==2)
				$text.=";\n";
				$flag=1;

				if(is_numeric($var))
					if($var{0}=='0')
					$text.="var \$$var=";
				else
				{
					if($var!==$i)
					$text.="var \$$var=";
					$i=$var;
				}
			else
				$text.="var \$$var=";
				$i++;

			if(is_array($val))
				{
					$text.="array(".$this->row2text($val,$dvars).")";
					$flag=2;
				}
			else
					$text.="\"".addslashes($val)."\"";
		}
		
		return($text);
	}
}
?>
