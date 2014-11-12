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
			$originaltitle=$rsstitle=$rssobj->get_title();
			$rsslink=$rssobj->get_link();
			$rssdate=$rssobj->get_date();

		}
		else
		{
			$offset=$config->get( 'offset' );
			$originaltitle=$rsstitle=$rssobj->title;
			$rsslink=$rssobj->uri;
			$rssdate=$rssobj->updatedDate;
		}

		//If Data already exists in queue
		if($combroadcastHelper->checkexist($originaltitle,$uid,''))
		{
			return;
		}

		$get_date= JFactory::getDate($rssdate,$offset);	//convert the time into UTC	time
		$date=$get_date->toSql();

		$goo = new Googl();
		$shortURL = $goo->set_short($rsslink);

		//If params has set to post rss data to social network as well
		if($params->get('show_status_rss'))
		{
			$combroadcastHelper->inQueue($uid,$rsstitle." ".$rsslink, 1, 0, '','');
		}

		$str_title_link = $rsstitle." <a href=".$shortURL['id']." target='_blank'>".$shortURL['id']."</a>";

		$contentdata = array();
		$contentdata['act_access']=0;
		$contentdata['act_description']='';
		$contentdata['act_type']='';
		$contentdata['act_subtype']='';

		if($params->get('status_via'))
		{
			$rsstitle .= " (via RSS)";
		}

		$contentdata['act_originalcontent']=$rsstitle." ".$rsslink;
		$contentdata['act_title']='';
		$contentdata['actor_id']= $uid;
		$contentdata['api_name']= 'rss';

		//if Jomsocial
		if($params->get('integration')=='js')
		{
			//$combroadcastHelper->inJSAct($uid,$uid,$str_title_link,'', 'rss',$uid, $date);
			$contentdata['integration_option']= 'JomSocial';
			$combroadcastHelper->pushtoSocialActivitystream($contentdata,'rss');
			$combroadcastHelper->updateJSstatus($uid, $rsstitle,$date );
		}

		//if Jomwall
		if($params->get('integration')=='jwall')
		{

			//$combroadcastHelper->inJomwallact($uid, $str_title_link,$rsstitle,'',$get_date,'rss');
			$contentdata['integration_option']= 'Jomwall';
			$combroadcastHelper->pushtoSocialActivitystream($contentdata,'rss');
		}

		//if CB
		if($params->get('integration')=='cb')
		{
			$today_date=JFactory::getDate($status['timestamp']);
			$today=JFactory::getDate();
			//$combroadcastHelper->inSuperaact($userid, $status['comment'],$status_content,$get_date,$status['timestamp'],$api);
			$contentdata['integration_option']= 'Community Builder';
			$combroadcastHelper->pushtoSocialActivitystream($contentdata,'rss');
		}

		//if Easysocial
		if($params->get('integration')=='easysocial')
		{
			$contentdata['integration_option']= 'EasySocial';
			$today_date	= JFactory::getDate($status['timestamp']);
			$combroadcastHelper->pushtoSocialActivitystream($contentdata,'rss');
			//$combroadcastHelper->inEasysocialact($userid,$userid,'broadcast',$status_content, $api_name,$userid,$today_date->toSql() );
		}

		$combroadcastHelper->intempAct($uid, $rssobj->title, $date,'' );


	}


}
