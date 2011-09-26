<?php

defined('_JEXEC') or die();
jimport( 'joomla.application.component.model' );
include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');

class BroadcastModelrss extends JModel
{
		
	 function rssstore($uid,$rssobj)
	 {
	 		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
			$db	 	= &$this->getDBO();	
			$today_date = & JFactory::getDate($rssobj->get_date());	
			$date = $today_date->toMySQL();
			
			$str_title_link	= "<a href=".$rssobj->get_link()." target='_blank'>".$rssobj->get_description()."</a>";
		 	
		 	if($broadcast_config['status_via'])
		   		$str_title_link	.= " (via RSS)";
		 							 	
			echo $str_title_link."<br>";
                            //$html_reg = '/<+\s*\/*\s*([A-Z][A-Z0-9]*)\b[^>]*\/*\s*>+/i';
                          //htmlentities( preg_replace( $html_reg, '', $str_title_link ) );
 		 	$str_title_link = "<img style='height: 20px;' src=".JURI::base().'modules'.DS.'mod_jomsocialbroadcast'.DS.'images'.DS.'rss.png'."> ".$str_title_link;
			
			combroadcastHelper::inJSAct($uid,$uid,$str_title_link,'', 'rss',$uid, $date);
			combroadcastHelper::intempAct($uid, $rssobj->get_description(), $date,'rss' );
			combroadcastHelper::updateJSstatus($uid, $str_title_link,$date );
	  }

		function rssdeletetmpactivity()
		{
			$curtime=time();
		 	$previousdt1= $curtime-(3600*24*2);		 	
		 	$previousdt=date('Y-m-d',$previousdt1);
			$db	 	= &$this->getDBO();		
			$query = "DELETE FROM #__broadcast_tmp_activities WHERE created_date<'$previousdt'";
			$db->setQuery($query);	
			$db->query();		
		}
	}