<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');
require_once (JPATH_SITE.DS.'modules'.DS.'mod_feed'.DS.'helper.php'); // Need this to parse feeds

class BroadcastControllerrss extends JControllerLegacy
{

	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	function BroadcastControllerrss()
	{
		parent::__construct();
	}

	function getrssdata()
	{
		$com_params=JComponentHelper::getParams('com_broadcast');
		$pkey = JRequest::getVar('pkey', '');
		$integration=$com_params->get('integration');
		if($pkey!=$com_params->get('private_key_cronjob'))
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
			$combroadcastHelper=new combroadcastHelper();

			$rss_limit=$com_params->get('rss_limit_per_user');

			if(!empty($rss_limit))
				$rss_limit_per_user=$com_params->get('rss_limit_per_user');
			else
				$rss_limit_per_user=5;

			if(empty($rssdts))
			continue;
			foreach($rssdts as $rss)
			{
				$link=$rss['link'];
				$title=$rss['title'];

				if(empty($link) || $link=='')
				    continue;
				jimport( 'joomla.html.parameter' );
				$params = new JRegistry('');
				$params->set('rssurl', trim($link));
				try{
						$feed = modFeedHelper::getFeed($params);

						if(!$feed)
						 continue;

						for ($j = 0; $j < $rss_limit_per_user; $j ++)
						{
							if(JVERSION<3.0)
							{
								if(!empty($feed->items[$j]))
								{
									$currItem = & $feed->items[$j];


									if ( !is_null( $currItem->get_link() ) )
									{
										$statuslog=$currItem->get_title();
									 	if($statuslog!="" and !empty($statuslog))
									 	{
											$model->rssstore($userid,$currItem,$title);

										 }
											echo $statuslog."<br/>";
									}
								}
							}
							else  //IF jooomla version is greater than equal to 3.0
							{
								$currItem=new stdClass();
								$uri='';
								$uri = (!empty($feed[$j]->guid) || !is_null($feed[$j]->guid)) ? $feed[$j]->guid : $feed[$j]->uri;

								$uri = substr($uri, 0, 4) != 'http' ? $params->get('rsslink') : $uri;
								$currItem->title=$feed[$j]->title;
								$currItem->uri=$uri;
								$currItem1=$feed[$j]->updatedDate;
								$feedDatearray=JArrayHelper::fromObject($currItem1,true);
								$currItem->updatedDate=$feedDatearray['date'];

								$statuslog=$currItem->title;
								if($statuslog!="" and !empty($statuslog))
								{
									$model->rssstore($userid,$currItem,$title);
								}

							}
						}
				}catch(Exception $e){echo 'Caught exception: '.$e->getMessage(); "\n";}

			}//for each link

		}//foreach
	}

}//class
