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
if (!class_exists('combroadcastHelper'))
{
class combroadcastHelper
{ 
	function getitemid($link)
	{
		$database = JFactory::getDBO();
		$itemid = 0;
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$items= $menu->getItems('link',$link); // pass the link for which you want the ItemId.
		if(isset($items[0])){
			$itemid = $items[0]->id;
		}
		return $itemid;
	}
	
	function getapistatus(){
		require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'models'.DS.'broadcast.php');
		$BroadcastModelbroadcast=new BroadcastModelbroadcast();
		$apis=$BroadcastModelbroadcast->getapistatus();
		return $apis;
	}
	function expandShortUrl($url) {
 
	$headers = get_headers($url,1);
 
	if (!empty($headers['Location'])) 
	{
		$headers['Location'] = (array) $headers['Location'];
		$url = array_pop($headers['Location']);
	}
 
	return $url;
}
	function seperateurl($url) {
	
	$newurl='';
  $U = explode(' ',$url);

  $W =array();
  foreach ($U as $k => $u) {
    if (stristr($u,'http') || (count(explode('.',$u)) > 1)) {
      $newurl=$U[$k];
      
      $count=1;

    }
  }
  return $newurl;
}

function inSuperaact($userid,$comment,$status_content,$today,$timestamp,$api_nm)
	{
			if (file_exists(JPATH_SITE . DS . 'components' . DS . 'com_cbactivity' . DS . 'onyourmind' . DS . 'shareBroadcast.php')) {
        require_once( JPATH_SITE . DS . 'components' . DS . 'com_cbactivity' . DS . 'onyourmind' . DS . 'shareBroadcast.php');
      } 
			elseif (file_exists(JPATH_SITE . DS . 'components' . DS . 'com_cbsuperwall' . DS . 'onyourmind' . DS . 'shareBroadcast.php')) {
        require_once( JPATH_SITE . DS . 'components' . DS . 'com_cbsuperwall' . DS . 'onyourmind' . DS . 'shareBroadcast.php');
      } 

			$attachment='';

			$type='text';
			$original=$comment;
			$link=combroadcastHelper::makelink($comment,$api_nm);
			$link=trim($link);
			$comment=trim($comment);
			if($link!=$comment)
			{				
				$type='link';
				$attachment=combroadcastHelper::seperateurl($comment);
				//expand url $attachment

				$comment=str_replace($attachment,'',$comment);
				$attachment=combroadcastHelper::expandShortUrl($attachment);
			}
			
			$api=str_replace('plug_techjoomlaAPI_','',$api_nm);

			$imgpath=JURI::base().'/components/com_broadcast/images/'.$api.'.png';
      
			SuperaBroadcastHelper::addtostream($comment,$attachment,$type,$userid,$imgpath,$original);
	}
  
function inJomwallact($userid,$comment,$status_content,$today,$timestamp,$api_nm)
	{
			require_once( JPATH_SITE . DS . 'components' . DS . 'com_awdwall' . DS . 'helpers' . DS . 'user.php'); 
			
			$attachment='';

			$type='text';
			$urlpresent='';
			$params=array();
			$link=combroadcastHelper::makelink($comment,$api_nm);
			$link=trim($link);
			$comment=trim($comment);
			if($api_nm!='rss')
			{
				if($link!=$comment)
				{				
					$type='link';
					 $attachment=combroadcastHelper::seperateurl($comment);
					//expand url $attachment

						$comment=str_replace($attachment,'',$comment);
					$attachment=combroadcastHelper::expandShortUrl($attachment);
				}
			}
			$api=str_replace('plug_techjoomlaAPI_','',$api_nm);

			$imgpath=JURI::base().'/components/com_broadcast/images/'.$api.'.png';
			AwdwallHelperUser::addtostream($comment,$attachment,$type,$userid,$imgpath,$params);
				
		
		
	}
	#populate the Jomsocial activity table called from broadcast & rss models 
	function inJSAct($actor,$target,$title,$content,$api,$cid,$date)
	{


			require_once( JPATH_SITE . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php'); 
		$act = new stdClass();
		$act->cmd 	= 'wall.write';
		$act->actor 	= $actor;
		$act->target 	= 0; // no target
		$act->title 	= $title;
		$act->content 	= $content;
		$act->app 	= $api;
		$act->cid 	= $cid;
		CFactory::load('libraries', 'activities');
		$command = $api.'.myaction';
		$act->comment_type  = $command;
		$act->comment_id    = CActivities::COMMENT_SELF;
		
		$act->like_type     = $command;
		$act->like_id     = CActivities::LIKE_SELF;		
		CActivityStream::add($act);
	}
	
	
	#set the current Jomsocial status, called from broadcast & rss models 
	function updateJSstatus($userid,$status,$date)
	{
		$db =JFactory::getDBO();
		$query= "UPDATE `#__community_users` SET `status` ='{$db->escape($status)}', 
		posted_on='{$date}', points=points +1 WHERE userid='{$userid}'";
		$db->setQuery( $query );
		$result =$db->query();
	}
	
	
	/*This function is used to get parameter of plugins
	*	@group:string  group of plugin 
	*	@api:string name of plugin 
	*	@params:string name of required parameters seperated by comma(,) 
	*/
		function getpluginparams($group,$api,$paramstr)
		{
			if(!$group and !$api	and !$paramstr)
			return '';
			if(JVERSION>=1.6)
				{
					$plugin = JPluginHelper::getPlugin($group, $api);
					$pluginParams = new JRegistry();    
					if(!empty($plugin->params))
					$pluginParams->loadString($plugin->params);				
				}
				else
				{
					$plugin =JPluginHelper::getPlugin($group, $api);
					if(!empty($plugin->params))
					$pluginParams = new JParameter($plugin->params);
				
				}

				if($pluginParams)   
				{
						$params=explode(',',$paramstr);
						$params_data=array();
						foreach($params as $param)
						{
									if($pluginParams->get($param))
									$params_data[$param] = $pluginParams->get($param);	

						}
				}
				return $params_data;
		}
	#populate the temp activity table of broadcast called from broadcast & rss models 
	function intempAct($id, $act, $date, $api='')
	{
			//require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$db 			= JFactory::getDBO();
		$obj			= new StdClass();
		$obj->uid 		= $id;
		$obj->status 	=  combroadcastHelper::stripUrl($act);
		if(!$obj->status)
		$obj->status 	=$act;
		$obj->created_date	= $date;	
		$obj->type		= $api; 

		if(!$db->insertObject('#__broadcast_tmp_activities', $obj)){		
      		$db->stderr();
      		return false;
  		}
		return true;
	}
	function http_parse_headers( $header )
	{
	        $retVal = array();
	        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
	        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
	                $match[1] = preg_replace('/(\?< =^|[\x09\x20\x2D])./e', 'strtoupper("")', strtolower(trim($match[1])));
	                if( isset($retVal[$match[1]]) ) {
	                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
	                } else {
	                    $retVal[$match[1]] = trim($match[2]);
	                }
	            }
	        }
	        return $retVal;
	}
	#strips the long urls to short url with Google shortening
	function givShortURL($string)
	{
		require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'controllers'.DS.'googlshorturl.php');
		//require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$com_params=JComponentHelper::getParams('com_broadcast');
		$api_key=trim($com_params->get('url_apikey'));
		$goo = new Googl($api_key);//if you have an api key
	
		// replacement of url in title
		$regex = "/((https?\:\/\/|ftps?\:\/\/)|(www\.))(\S+)(\w{1,5})(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/i";  // url 
	    preg_match_all($regex, $string ,$matches);
		if( !empty($matches[0]) ){
			foreach ($matches[0] as $match ){
				$shorturl = $goo->set_short($match); 
				$string = str_replace($match, $shorturl['id'], $string);
			}
		}
		return $string;
	}

	function getContentFromUrl($url)
	{
	if (!$url)
		return false;

	$response = '';
	if (function_exists('curl_init'))
	{		
		$ch =curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
	}
	else
	{
		$fp = fsockopen($url, 80, $errno, $errstr, 30);
		if (!$fp) {
			return false;
		} else {
		    $header  = 'GET / HTTP/1.1\r\n';
		    $header .= 'Host: ' . $url . '\r\n';
		    $header .= 'Connection: Close\r\n\r\n';
		    fwrite($fp, $header);
		    while (!feof($fp)) {
		        $response .= fgets($fp, 128);
		    }
		    fclose($fp);
		}
	}
	return $response;
}
 function stripUrl($url)
	{

  $U = explode(' ',$url);

  $W =array();
  foreach ($U as $k => $u) {
    if (stristr($u,'http') || (count(explode('.',$u)) > 1)) {
      unset($U[$k]);
      return combroadcastHelper::stripUrl(implode(' ',$U));
    }
  }
  return implode(' ',$U);
	}

 function checkexistparams($allparams,$column,$id)
	{
if($allparams)
{
		foreach($allparams as  $multipledatas)
		{
		foreach($multipledatas as  $multipledata)
		{
	
				if($multipledata['id']==$id)
				return 1;
			}
		}
}
					return 0;
	}
	 function getallparamsforOtherAccounts($userid,$column)
	 {
			$db 	=JFactory::getDBO();
			$query = "SELECT params FROM #__broadcast_config WHERE user_id ={$userid}";
			$db->setQuery($query);
			$json=$db->loadResult();
			$dataarr=json_decode($json,true);
			if(empty($dataarr['paramsdata'][$column]))
			return 0;
			else 
						return $dataarr['paramsdata'][$column];
			
		}
	#check if the status exist in the temp table of broadcast
	function checkexist($status,$uid,$api='')
	{
		$db 		=JFactory::getDBO();
		$status		= explode('(via',$status);		
		$originalstatus=$newstatus	= trim($status[0]);
		$newstatus 	=  combroadcastHelper::stripUrl($newstatus);
		if(!$newstatus)
		{	
			$newstatus 	=$originalstatus;
		}
		$newstatus	=$db->escape($newstatus);
		$where = '';
		if($api)
			$where = ' AND (type="'.$api.'" OR type="") ';
		$query = "SELECT status FROM #__broadcast_tmp_activities WHERE uid = {$uid} AND status = '{$newstatus}' ".$where;
		$db->setQuery($query);
		
		if($db->loadResult())	
			return 1;
		else
			return 0;
	}
	
	/**
 *  Transforms plain text into valid HTML, escaping special characters and
 *  turning URLs into links.
 */
function htmlEscapeAndLinkUrls($text)
{
    global $rexUrlLinker, $validTlds;
    
$rexProtocol  = '(https?://)?';
$rexDomain    = '(?:[-a-zA-Z0-9]{1,63}\.)+[a-zA-Z][-a-zA-Z0-9]{1,62}';
$rexIp        = '(?:[1-9][0-9]{0,2}\.|0\.){3}(?:[1-9][0-9]{0,2}|0)';
$rexPort      = '(:[0-9]{1,5})?';
$rexPath      = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
$rexQuery     = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
$rexFragment  = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
$rexUsername  = '[^]\\\\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64}';
$rexPassword  = $rexUsername; // allow the same characters as in the username
$rexUrl       = "$rexProtocol(?:($rexUsername)(:$rexPassword)?@)?($rexDomain|$rexIp)($rexPort$rexPath$rexQuery$rexFragment)";
$rexUrlLinker = "{\\b$rexUrl(?=[?.!,;:\"]?(\s|$))}";

/**
 *  $validTlds is an associative array mapping valid TLDs to the value true.
 *  Since the set of valid TLDs is not static, this array should be updated
 *  from time to time.
 *
 *  List source:  http://data.iana.org/TLD/tlds-alpha-by-domain.txt
 *  Last updated: 2011-10-09
 */
$validTlds = array_fill_keys(explode(" ", ".ac .ad .ae .aero .af .ag .ai .al .am .an .ao .aq .ar .arpa .as .asia .at .au .aw .ax .az .ba .bb .bd .be .bf .bg .bh .bi .biz .bj .bm .bn .bo .br .bs .bt .bv .bw .by .bz .ca .cat .cc .cd .cf .cg .ch .ci .ck .cl .cm .cn .co .com .coop .cr .cu .cv .cx .cy .cz .de .dj .dk .dm .do .dz .ec .edu .ee .eg .er .es .et .eu .fi .fj .fk .fm .fo .fr .ga .gb .gd .ge .gf .gg .gh .gi .gl .gm .gn .gov .gp .gq .gr .gs .gt .gu .gw .gy .hk .hm .hn .hr .ht .hu .id .ie .il .im .in .info .int .io .iq .ir .is .it .je .jm .jo .jobs .jp .ke .kg .kh .ki .km .kn .kp .kr .kw .ky .kz .la .lb .lc .li .lk .lr .ls .lt .lu .lv .ly .ma .mc .md .me .mg .mh .mil .mk .ml .mm .mn .mo .mobi .mp .mq .mr .ms .mt .mu .museum .mv .mw .mx .my .mz .na .name .nc .ne .net .nf .ng .ni .nl .no .np .nr .nu .nz .om .org .pa .pe .pf .pg .ph .pk .pl .pm .pn .pr .pro .ps .pt .pw .py .qa .re .ro .rs .ru .rw .sa .sb .sc .sd .se .sg .sh .si .sj .sk .sl .sm .sn .so .sr .st .su .sv .sy .sz .tc .td .tel .tf .tg .th .tj .tk .tl .tm .tn .to .tp .tr .travel .tt .tv .tw .tz .ua .ug .uk .us .uy .uz .va .vc .ve .vg .vi .vn .vu .wf .ws .xn--0zwm56d .xn--11b5bs3a9aj6g .xn--3e0b707e .xn--45brj9c .xn--80akhbyknj4f .xn--90a3ac .xn--9t4b11yi5a .xn--clchc0ea0b2g2a9gcd .xn--deba0ad .xn--fiqs8s .xn--fiqz9s .xn--fpcrj9c3d .xn--fzc2c9e2c .xn--g6w251d .xn--gecrj9c .xn--h2brj9c .xn--hgbk6aj7f53bba .xn--hlcj6aya9esc7a .xn--j6w193g .xn--jxalpdlp .xn--kgbechtv .xn--kprw13d .xn--kpry57d .xn--lgbbat1ad8j .xn--mgbaam7a8h .xn--mgbayh7gpa .xn--mgbbh1a71e .xn--mgbc0a9azcg .xn--mgberp4a5d4ar .xn--o3cw4h .xn--ogbpf8fl .xn--p1ai .xn--pgbs0dh .xn--s9brj9c .xn--wgbh1c .xn--wgbl6a .xn--xkc2al3hye2a .xn--xkc2dl3a5ee0h .xn--yfro4i67o .xn--ygbi2ammx .xn--zckzah .xxx .ye .yt .za .zm .zw"), true);


    $html = '';

    $position = 0;
    while (preg_match($rexUrlLinker, $text, $match, PREG_OFFSET_CAPTURE, $position))
    {
        list($url, $urlPosition) = $match[0];

        // Add the text leading up to the URL.
        $html .= htmlspecialchars(substr($text, $position, $urlPosition - $position));

        $protocol    = $match[1][0];
        $username    = $match[2][0];
        $password    = $match[3][0];
        $domain      = $match[4][0];
        $afterDomain = $match[5][0]; // everything following the domain
        $port        = $match[6][0];
        $path        = $match[7][0];

        // Check that the TLD is valid or that $domain is an IP address.
        $tld = strtolower(strrchr($domain, '.'));
        if (preg_match('{^\.[0-9]{1,3}$}', $tld) || isset($validTlds[$tld]))
        {
            // Do not permit implicit protocol if a password is specified, as
            // this causes too many errors (e.g. "my email:foo@example.org").
            if (!$protocol && $password)
            {
                $html .= htmlspecialchars($username);
                
                // Continue text parsing at the ':' following the "username".
                $position = $urlPosition + strlen($username);
                continue;
            }
            
            if (!$protocol && $username && !$password && !$afterDomain)
            {
                // Looks like an email address.
                $completeUrl = "mailto:$url";
                $linkText = $url;
            }
            else
            {
                // Prepend http:// if no protocol specified
                $completeUrl = $protocol ? $url : "http://$url";
                $linkText = "$domain$port$path";
            }
            
            $linkHtml = '<a href="' . htmlspecialchars($completeUrl) . '" target="_blank">'
                . htmlspecialchars($linkText)
                . '</a>';

            // Cheap e-mail obfuscation to trick the dumbest mail harvesters.
            $linkHtml = str_replace('@', '&#64;', $linkHtml);
            
            // Add the hyperlink.
            $html .= $linkHtml;
        }
        else
        {
            // Not a valid URL.
            $html .= htmlspecialchars($url);
        }

        // Continue text parsing from after the URL.
        $position = $urlPosition + strlen($url);
    }

    // Add the remainder of the text.
    $html .= htmlspecialchars(substr($text, $position));
    return $html;
}
	

	function makelink($text,$thisapi)
	{
	
		/*if(strtolower($thisapi)=="twitter")
		{

			$text = preg_replace('@(https?://([-\w\.]+)+(d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a target="_blank" href="$1">$1</a>',  $text );
			$text = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a target=\"_blank\" href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $text);  
			$text = preg_replace("#(^|[\n ])\#([^ \"\t\n\r<]*)#ise", "'\\1<a target=\"_blank\" href=\"http://hashtags.org/search?query=\\2\" >#\\2</a>'", $text);
	
			 $text;die;
	
		}
		else*/
		{
			$text1=$text;
			$text2=combroadcastHelper::htmlEscapeAndLinkUrls($text1);
			return $text2;
		}
			
	}
	
	/*function to add to queue and also populate the tmp activity table
	 * @param int userid
	 * @param string message
	 * @param datetime date of format Y-m-d H:i:s
	 * @param int number of times to broadcast
	 * @param int interval (in seconds) in which to broadcast
	 * @param array media list of api to broadcast the status  
	 * @param string supplier the one who has called this function
	 * @param int short to shorten the url or not
	*/
	function addtoQueue($userid,$message,$date,$count,$interval,$media='',$supplier,$short){

		if($short==1)	// replacement of url in message with short url	
		{
			$message=combroadcastHelper::givShortURL($message);
		}
		if(is_array($userid) )
		{

			foreach($userid as $id){

			if(trim($id)){
			
				combroadcastHelper::inQueue($id,$message,$count,$interval,$supplier,$media);
				combroadcastHelper::intempAct($id,$message,$date);
				}
			}
		}
		else{


			combroadcastHelper::inQueue($userid,$message,$count,$interval,$supplier,$media);		
			combroadcastHelper::intempAct($userid,$message,$date);
		}
		return true;
	}
	
	function getuserconnectionstatus($config)
	{
		$db = JFactory::getDBO();
		$userid=$config->userid;
		$client=$config->supplier;
		$api=$config->api;
	
		$where='';
		

	 	$query 	= "SELECT token FROM #__techjoomlaAPI_users WHERE token<>'' AND user_id = {$userid}  AND api='{$api}'".$where;
		$db->setQuery($query);
		$result	= $db->loadResult();	
		if(!empty($result))
			return 1;
		else
			return 0;
		
	
	}

	#inQueue function called from plugin as well can be called from custom place	
	function inQueue($userid,$newstatus, $count, $interval, $supplier, $media)
	{

//		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$com_params=JComponentHelper::getParams('com_broadcast');
		if($media == '')
			$touseapi = $com_params->get('api');
		else
			$touseapi = $media;


		if(!$count)	$count = 1;
	    $db = JFactory::getDBO();
		foreach($touseapi as $api){
			
			$obj		   	= new StdClass();
			$obj->id	   	= '';
			$obj->status   	= $newstatus;
			$obj->userid   	= $userid;
			$obj->flag 	   	= 0;
			$obj->count	   	= $count;
			$obj->org_count	= $count;
			$obj->interval	= $interval;
			$obj->api 		= $api;
			$obj->supplier	= $supplier ;
				
			if(!empty($userid))
				{

					$flag=combroadcastHelper::getuserconnectionstatus($obj);
					if($flag)
					{
					if(!$db->insertObject('#__broadcast_queue', $obj)){
			    			continue;
					}
			 	 	}
			 	 }

	}

	 return true;
}
function urlpresent($url) {
		$count=0;
		$U = explode(' ',$url);
		$W =array();
		foreach ($U as $k => $u) {
			if (stristr($u,'http') || (count(explode('.',$u)) > 1)) {
			unset($U[$k]);
			$count=1;
			break;
			}
		}
		
		return $count;
	}
	
}
}
//this class is used to make log for f/l/t controllers 
if (!class_exists('techjoomlaHelperLogs'))
{
class techjoomlaHelperLogs
{	

	/*
	$message['subject']
	$message['message']
	$message['to']
	$message['FROM']
	*/

	function emailtoClient($type,$plugin)
	{
		if($type=='ACCESS_TOKEN_EXPIRE')
		{
		$user=JFactory::getUser();
		$link=JRoute::_(JURI::base()."index.php?option=com_broadcast&view=config");
		$app 		= JFactory::getApplication();
		$sitename	= array();
		$sitename	= $app->getCfg('sitename');
		$subject=JText::sprintf('BC_ACCESS_TOKEN_EXPIRE_SUB',$plugin,$sitename);
		$message=JText::sprintf('BC_ACCESS_TOKEN_EXPIRE_MESSAGE',$user->name,$link);
		$mailfrom = $app->getCfg('mailfrom');
		$fromname = $app->getCfg('fromname');
		$result=JUtility::sendMail($mailfrom,$fromname,$user->email,$subject,$message,$html=1,null,null);
		
		}

	}
	function simpleLog($comment,$userid='',$type,$name,$path="", $display=1,$params=array())
	{
		jimport('joomla.error.log');
		$options = "{DATE}\t{TIME}\t{USER}\t{DESC}";
		if(JVERSION >='1.6.0')
			$path=JPATH_SITE.'/plugins/techjoomlaAPI/logs';
		$my = JFactory::getUser();     
	
		JLog::addLogger(
			array(
				'text_file' => 'broadcast_'.$name.'.log',
				'text_entry_format' => $options ,
				'text_file_path' => $path
			),
			JLog::INFO,
			'broadcast'
		);

		$logEntry = new JLogEntry('', JLog::INFO, 'broadcast');
		$logEntry->user= $my->name.'('.$my->id.')';
		$logEntry->desc=json_encode($comment);

		JLog::add($logEntry);
		
		/*if (is_dir($path)) 
		{
			$objects = @scandir($path);
			foreach ($objects as $object) 
			{
				if ($object != "." && $object != "..") 
				{
					if (filemtime($path."/".$object) <= time()-60*60*24*30)
				   @unlink($path."/".$object);
				}
			}
			//rmdir($dir);
		}*/
		
			
		if(isset($params['desc']) and $display==1)
		echo $my->name .'('.$my->id.')'.$comment.$params['desc']."]"."HTTP CODE:".$params['http_code']."<BR>";   
					 
		if(!isset($params['desc']))
		JError::raiseWarning(500, $comment);
		
	}
    
    function xml2array($contents, $get_attributes=1, $priority = 'tag') {
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble

        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();
        
        if(isset($value)) {
            if($priority == 'tag') $result = $value;
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) {
            foreach($attributes as $attr => $val) {
                if($priority == 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                $repeated_tag_index[$tag.'_'.$level] = 1;

                $current = &$current[$tag];

            } else { //There was another element with the same tag name

                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    $repeated_tag_index[$tag.'_'.$level]++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2;
                    
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }

                }
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                $current = &$current[$tag][$last_item_index];
            }

        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

            } else { //If taken, put all things inside a list(array)
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    
                    if($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag.'_'.$level]++;

                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $get_attributes) {
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }
                        
                        if($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                }
            }

        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }
    
    return($xml_array);
}  
}	


}
?>
