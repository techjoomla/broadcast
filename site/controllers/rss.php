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
			echo "This Private Cron Key Doesnot Exist";
			return;
		}

		$baseuri=JURI::base();
		$database = JFactory::getDBO();
		$fbquery = "SELECT user_id FROM #__broadcast_config where broadcast_rss_url<>'' ";
		$database->setQuery($fbquery);
		$this->uaccess = $database->loadObjectlist();
    
		$arrFeeds = array();
		foreach($this->uaccess as $k=>$v){
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
                                
						$today_date = & JFactory::getDate($currItem->get_date());
						
						if(!$this->checkexist($str_title_link,$userid))
						{
							$statuslog=$currItem->get_description();
						 	if($statuslog != "")
						 	{
						 	$str_title_link	= "<a href=".$currItem->get_link()." target='_blank'>".$currItem->get_description()."</a>";
						 	
						 	if($broadcast_config['status_via'])
						   $str_title_link	.= " (via RSS)";
						 							 	
							echo $str_title_link."<br>";
                            //$html_reg = '/<+\s*\/*\s*([A-Z][A-Z0-9]*)\b[^>]*\/*\s*>+/i';
                          //htmlentities( preg_replace( $html_reg, '', $str_title_link ) );
 						 	
							$act = new stdClass($currItem->get_date());
							$act->actor = $userid;
							$act->target = $userid; // no target
							$act->title = "{actor} ".$str_title_link;
				
							$act->title = "<img style='height: 20px;' src=".$baseuri.DS.'components'.DS.'com_broadcast'.DS.'images'.DS.'rss.png'."> ".$str_title_link;
							$act->content = '';
							$act->app = 'rss';
							$act->cid = $userid;
							$act->created = $today_date->toMySQL();
							$act->access = 0;
							$act->params = '';
							$act->points = 1;
							$act->archived = 0;

							$model = $this->getModel('rss');
							$model->rssstore($act);
							}
						 }

				  	echo "\n";
					}
				}
			}catch(Exception $e){echo 'Caught exception: '.$e->getMessage(); "\n";}

		}//for each link
			$model = $this->getModel('rss');
			$model->rssdeletetmpactivity();
	 }
  }	
  
	function checkexist($status,$uid)
	{
		$model = $this->getModel('rss');
		$result =$model->checkexist($status,$uid);
		if($result)
			return 1;
		else
			return 0;
	}

}//class
