<?php

defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );
include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');

class BroadcastModelrss extends JModel
{
		
	 function rssstore($uid,$rssobj)
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
		 	$str_title_link = $rssobj->get_title()." <a href=".$shortURL['id']." target='_blank'>".$shortURL['id']."</a>";
		 	if($broadcast_config['status_via'])
		   		$str_title_link	.= " (via RSS)";

 		 	$str_title_link = "<img style='height: 20px;' src=".JURI::base().'modules'.DS.'mod_broadcast'.DS.'images'.DS.'rss.png'."> ".$str_title_link;
			
			combroadcastHelper::inJSAct($uid,$uid,$str_title_link,'', 'rss',$uid, $date);
			combroadcastHelper::intempAct($uid, $rssobj->get_title(), $date,'rss' );
			if($broadcast_config['show_status_rss'])
				combroadcastHelper::updateJSstatus($uid, $rssobj->get_title(),$date );
	  }

		
	}
