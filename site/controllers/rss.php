<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.application.component.controller');
require_once (JPATH_SITE.DS.'modules'.DS.'mod_feed'.DS.'helper.php'); // Need this to parse feeds

class BroadcastControllerrss extends JController
{

	function display()
	{
		parent::display();
	}

	function BroadcastControllerrss()
	{
		parent::__construct(); 
	}

	function getrssdata()
	{	
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$pkey = JRequest::getVar('pkey', '');
		if($pkey!=$broadcast_config['private_key_cronjob'])
		{
			echo  JText::_("NOT_AUTH_KEY");
			return;
		}

		$database = JFactory::getDBO();
		$fbquery = "SELECT user_id FROM #__broadcast_config where broadcast_rss_url<>'' ";
		$database->setQuery($fbquery);
		$this->uaccess = $database->loadObjectlist();
    	$model = $this->getModel('rss');
		
		$arrFeeds = array();
		foreach($this->uaccess as $k=>$v)
		{
			echo '<h3>User: ' . JFactory::getUser($v->user_id)->name.'</h3>';
			echo '<br>';
			
			$userid = $v->user_id;
			$fbquery = "SELECT user_id,broadcast_rss_url
			FROM #__broadcast_config where broadcast_rss_url <> '' and user_id=".$v->user_id;
			$database->setQuery($fbquery);
			$this->links = $database->loadObjectlist();
			$links=explode('|',$this->links[0]->broadcast_rss_url);
			
			foreach($links as $link)
			{
				if(empty($link) || $link=='')
				    continue;
				jimport( 'joomla.html.parameter' );
				$params = new JParameter('');
				$params->set('rssurl', trim($link));
				try{
					$feed = modFeedHelper::getFeed($params);
					if(!$feed) 
					 continue;   
					      
					for ($j = 0; $j < 5; $j ++)
					{					
						$currItem = & $feed->items[$j];      
						if ( !is_null( $currItem->get_link() ) ) 
						{       
							$statuslog=$currItem->get_description();
						 	if($statuslog != "")
						 	{                
								if(!combroadcastHelper::checkexist($statuslog,$userid,'rss'))
								{
									$model->rssstore($userid,$currItem);
								}
							 }
						  	echo "\n";
						}
					}
				}catch(Exception $e){echo 'Caught exception: '.$e->getMessage(); "\n";}
	
			}//for each link
			
		}
	}

}//class
