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
		$integration=$broadcast_config['integration'];
		if($pkey!=$broadcast_config['private_key_cronjob'])
		{
			echo  JText::_("NOT_AUTH_KEY");
			return;
		}

		$database = JFactory::getDBO();
		$fbquery = "SELECT user_id FROM #__broadcast_config where broadcast_rss<>'' ";
		$database->setQuery($fbquery);
		$this->uaccess = $database->loadObjectlist();
    	$model = $this->getModel('rss');
		
		$arrFeeds = array();
		foreach($this->uaccess as $k=>$v)
		{
			echo '<h3>User: ' . JFactory::getUser($v->user_id)->name.'</h3>';
			echo '<br>';
			
			$userid = $v->user_id;
			$fbquery = "SELECT user_id,broadcast_rss
			FROM #__broadcast_config where broadcast_rss <> ''  AND user_id=".$v->user_id;
			$database->setQuery($fbquery);
			$rsslists = $database->loadObjectlist();

				if(empty($rsslists[0]))
				continue;
			$rssdts=json_decode($rsslists[0]->broadcast_rss,true);

									

			//$links=explode('|',$this->links[0]->broadcast_rss);
			if(!empty($broadcast_config['rss_limit_per_user']))
			$rss_limit_per_user=$broadcast_config['rss_limit_per_user'];
			else
			$rss_limit_per_user=5;
			foreach($rssdts as $rss)
			{

		echo		$link=$rss['link'];
		echo		$title=$rss['title'];
			
				if(empty($link) || $link=='')
				    continue;
				jimport( 'joomla.html.parameter' );
				$params = new JParameter('');
				$params->set('rssurl', trim($link));
				try{
						$feed = modFeedHelper::getFeed($params);
						if(!$feed) 
						 continue;   
								  
						for ($j = 0; $j < $rss_limit_per_user; $j ++)
						{	
								if(!empty($feed->items[$j]))
								{				
									$currItem = & $feed->items[$j];      
									if ( !is_null( $currItem->get_link() ) ) 
									{       
										$statuslog=$currItem->get_title();
									 	if($statuslog != "")
									 	{                
											if(!combroadcastHelper::checkexist($statuslog,$userid,'rss'))
											{
												$model->rssstore($userid,$currItem,$title);
											}
										 }
											echo "\n";
									}
								}
						}
				}catch(Exception $e){echo 'Caught exception: '.$e->getMessage(); "\n";}
	
			}//for each link
			
		}//foreach
	}

}//class
