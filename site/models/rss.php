<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );
include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');

class BroadcastModelrss extends JModelLegacy
{
		
	 function rssstore($uid,$rssobj,$title)
	 {
		 		$params=JComponentHelper::getParams('com_broadcast');

	 		require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'controllers'.DS.'googlshorturl.php');
	 		$combroadcastHelper=new combroadcastHelper();
			$db	 	=$this->getDBO();	
			$config =JFactory::getConfig();
			if(JVERSION<3.0)
			{
				$offset = $config->getValue('config.offset'); 
				$rsstitle=$rssobj->get_title();
				$rsslink=$rssobj->get_link();
				$rssdate=$rssobj->get_date();

			}
			else
			{
				$offset=$config->get( 'offset' );
				$rsstitle=$rssobj->title;
			 	$rsslink=$rssobj->uri;
			 	$rssdate=$rssobj->updatedDate;
			}	
			
			
			$get_date= JFactory::getDate($rssdate,$offset);	//convert the time into UTC	time
   			$date=$get_date->toSql();
						
			$api_key =$params->get('url_apikey');
			$goo = new Googl($api_key);
			$shortURL = $goo->set_short($rsslink);
	 		$combroadcastHelper->inQueue($uid,$rsstitle.' '.$shortURL['id'], 1, 0, '','');
		 	$str_title_link = $rsstitle." <a href=".$shortURL['id']." target='_blank'>".$shortURL['id']."</a>";

		 		//if Jomsocial
				if($params->get('integration')=='js')
				{		

					if($params->get('status_via'))
					{
						if($title)
						$str_title_link	.= " (via RSS)->".$title;
						else
						$str_title_link	.= " (via RSS)";

					}
					$str_title_link .= "<img style='height: 16px;width: 16px;' src=".JURI::base().'modules'.DS.'mod_broadcast'.DS.'images'.DS.'rss.png'."> ";
			
					$combroadcastHelper->inJSAct($uid,$uid,$str_title_link,'', 'rss',$uid, $date);
					$combroadcastHelper->intempAct($uid, $rsstitle, $date,'' );
					
					if($params->get('show_status_rss'))
					combroadcastHelper::updateJSstatus($uid, $rsstitle,$date );
				}
				//if Jomwall
				if($params->get('integration')=='jwall')
				{

					$combroadcastHelper->inJomwallact($uid, $str_title_link,$rsstitle,'',$get_date,'rss');
					$combroadcastHelper->intempAct($uid, $rsstitle, $date,'' );
				}
				//if Superactivity  
				if($params->get('integration')=='supact')
				{
					$today_date=JFactory::getDate($status['timestamp']);
					$today=JFactory::getDate();
					$combroadcastHelper->inSuperaact($userid, $status['comment'],$status_content,$get_date,$status['timestamp'],$api);
					$combroadcastHelper->intempAct($userid, $status['comment'],$date->toSql(),$api);
				}
				
			
	  }

		
	}
