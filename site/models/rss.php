<?php

defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );
include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');

class BroadcastModelrss extends JModel
{
		
	 function rssstore($uid,$rssobj,$title)
	 {
	 		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
	 		require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'controllers'.DS.'googlshorturl.php');
	 		
			$db	 	= &$this->getDBO();	
			$config =& JFactory::getConfig();
			$offset = $config->getValue('config.offset'); 
			$get_date= & JFactory::getDate($rssobj->get_date(),$offset);	//convert the time into UTC	time
   			$date=$get_date->toMySQL();
						
			$api_key = $broadcast_config['url_apikey'];
			$goo = new Googl($api_key);
			$shortURL = $goo->set_short($rssobj->get_link());
			
	 	//	combroadcastHelper::inQueue($uid,$rssobj->get_title().' '.$shortURL['id'], 1, 0, '','');
		 	$str_title_link = $rssobj->get_title()." <a href=".$shortURL['id']." target='_blank'>".$shortURL['id']."</a>";
		 
		 		//if Jomsocial
				if($broadcast_config['integration']==0)
				{		

					if($broadcast_config['status_via'])
					{
						if($title)
						$str_title_link	.= " (via RSS)->".$title;
						else
						$str_title_link	.= " (via RSS)";

					}
						//$str_title_link .= "<img style='height: 16px;width: 16px;' src=".'modules'.DS.'mod_broadcast'.DS.'images'.DS.'rss.png'."> ";
			
					combroadcastHelper::inJSAct($uid,$uid,$str_title_link,'', 'rss',$uid, $date);
					combroadcastHelper::intempAct($uid, $rssobj->get_title(), $date,'' );
					
					if($broadcast_config['show_status_rss'])
					combroadcastHelper::updateJSstatus($uid, $rssobj->get_title(),$date );
				}
				//if Jomwall
				if($broadcast_config['integration']==1)
				{

					combroadcastHelper::inJomwallact($uid, $str_title_link,$rssobj->get_title(),'',$get_date,'rss');
					combroadcastHelper::intempAct($uid, $rssobj->get_title(), $date,'' );
				}
			
	  }

		
	}
