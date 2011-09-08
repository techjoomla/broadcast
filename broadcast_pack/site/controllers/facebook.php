<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.'config.php');
$bconfig = new BroadcastConfig;
require_once JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.$bconfig->facebook_library_path;

class BroadcastControllerfacebook extends JController
{
	function authorise2()
	{ 	
		$bconfig = new BroadcastConfig;
		$menus =& JSite::getMenu();
		$facebook = new Facebook(array(
		'appId' => $bconfig->fb_api,
		'secret' => $bconfig->fb_secret,
		'cookie' => true,
		));

		$me = null;
		$session = $facebook->getSession();

		if ($session)
		{
			try
			{
				$uid = $facebook->getUser();
				$facebook_secret = $facebook->getAccessToken();

				$data['facbook_uid'] = $uid;
				$data['facebook_secret'] = $facebook_secret;
				$me = $facebook->api('/me');
			}
			catch (FacebookApiException $e)
			{	
				print_r($e); 
				error_log($e); 
			}
		}

		$model = $this->getModel('facebook');
		$model->store($data);
		BroadcastControllerfacebook::redirect();
	}
	
	
	function remove()
	{ 
		$model = $this->getModel('facebook');
		$model->remove();
		$this->redirect();
	}
	
	function getstatus()
	{
		$mylogobj	= new BroadcastHelperLogs();
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$bconfig = new BroadcastConfig;
		$_REQUEST['tmpl'] = 'component';
		$pkey = JRequest::getVar('pkey', '');
		$model = $this->getModel('facebook');

		$today = date('Y-m-d');
		$database = JFactory::getDBO();
		$fbquery = "SELECT user_id,facbook_uid,facebook_secret FROM #__broadcast_users where facbook_uid<>'' AND facebook_secret<>''";
		$database->setQuery($fbquery);
		$uaccess = $database->loadObjectlist();
		$m=0;
		$log = array();
		if(isset($uaccess))
		foreach($uaccess as $k=>$v)
		{

			$log[] = 'User: ' . JFactory::getUser($v->user_id)->name;
			$userid = $v->user_id;
			$facebook = new Facebook(array(
				'appId' => $bconfig->fb_api,
				'secret' => $bconfig->fb_secret,
				'cookie' => true,
			));
			$attachment2 = array( 'access_token' => $v->facebook_secret );

			$page=null;

			////////start facebook page/////////////////
/*		
			if($config['facebook_page'])
			{  
					$facebook_page_limit=$config['facebook_page_limit'];
					if(!$facebook_page_limit)
					$facebook_page_limit=10;
					
					$page = $facebook->api('/me/accounts', 'get', $attachment2);
					$flag='';
					for($n=0;$n<count($page['data']);$n++)
					{
						try
						{	
							$url1 = "http://graph.facebook.com/{$page['data'][$n]['id']}/feed?limit=".$facebook_page_limit;
							$dtpage = BroadcastControllerfacebook::getRemotedata($url1);
						
							$pagedata=@json_decode($dtpage)->data;
				//print_r($dtpage);echo "<br>";
						if(isset($pagedata))
						foreach($pagedata as $pag=>$p)
						{ 
								$j = 0;
								$pagename = '';
								$pagename = $p->from->name;
								$status_item_page = '';
								$status_item_page = $p->message;

						if($status_item_page)
						{
							$statuspage = $p->message;
							$statuscreated = strtotime($p->created_time);
							$currrent_time = time();
							$one_hr_before = time()-(60*60);

							if($statuscreated>$one_hr_before and $statuspage!='')
							{
								$log[] = 'Page: ' . $statuspage;
									
								if($flag=='')
								$flag='true';
								$statusdatapage[$m]['actor'] = $userid;
								$statusdatapage[$m]['target'] = $userid;
								if($config['status_via'])
								{
									$statusdatapage[$m]['title'] = $statuspage.' (via '.$pagename.' on facebook)';
								}
								else
								{
									$statusdatapage[$m]['title'] = $statuspage.' ('.$pagename.' on facebook)';
								}
								$statusdatapage[$m]['content'] = '';
								$statusdatapage[$m]['app'] = 'profile';
								$statusdatapage[$m]['cid'] = $userid;
								$statusdatapage[$m]['created'] = $statuscreated;
								$statusdatapage[$m]['access'] = 0;
								$statusdatapage[$m]['params'] = '';
								$statusdatapage[$m]['points'] = 1;
								$statusdatapage[$m]['archived'] = 0;
								$statusdatapage[$m]['status'] = $statuspage;
								$m++;
							}
							else
							break;
						}
					}//for each
				}
				catch (FacebookApiException $e)
		 		{ 
					  $mylogobj->simpleLog("FPGtoJS\n ");
					  $mylogobj->simpleLog("[Facebook page Response]:\n  ". JArrayHelper::toString($e)); 
					  error_log($e);
				 }
				 catch(OAuthException $oe)
				 {
					 	$mylogobj->simpleLog("FPGtoJS\n ");
						$mylogobj->simpleLog("[Facebook page Response]:\n  ". JArrayHelper::toString($oe)); 
						error_log($oe);
				 }
		 }
		 
		 		
	//	if facebook_page 

			$pagedata='';
			$page='';
			$cn=0;

			if($statusdatapage)
			{
				foreach($statusdatapage as $page=>$pagedata)
				{
					$cn++;
					if($flag=='true')
					{
						$model = $this->getModel('facebook');
						$model->fbstore($pagedata,'page');
					}

				}
			}
		 
	  }
	  */
	  ////////end facebook page/////////////////

	 ////////start facebook profile/////////////////
		if($config['facebook_profile'])
		{
			try
			{
				$facebook_profile_limit=$config['facebook_profile_limit'];
				if(!$facebook_profile_limit)
				$facebook_profile_limit=10;
				$url="https://graph.facebook.com/".$v->facbook_uid."/statuses?since={$today}&limit=".$facebook_profile_limit."&access_token=".$v->facebook_secret;
				$dt=BroadcastControllerfacebook::getRemotedata($url);
				$data=@json_decode($dt)->data;
				$i = 0;
				$currrent_time =time();
				$one_hr_before =time()-(60*60);

				$k=0;
				while($data)
				{
					$status_item = $data[$i]->message;

					if($status_item)
					{
						$status[$i]['status'] = $data[$i]->message;
						$status[$i]['created_at'] = strtotime($data[$i]->updated_time);

						if($status[$i]['created_at']>$one_hr_before)
						{
							$log[] = 'Status: ' . $status[$i]['status'];
						
							$k=0;
							$statusdata[$k]['actor'] = $userid;
							$statusdata[$k]['target'] = $userid;
						
							if($config['status_skip'])
							{
								$search=explode(',', $config['status_skip']);
								$statusup=str_replace($search, '', $status[$i]['status']);
							}
							else
							{
								$statusup=$status[$i]['status'];
							}
							if($config['status_via'])
							{
								$statusdata[$k]['title'] = $statusup.' (via facebook)';
							}
							else
							{
								$statusdata[$k]['title'] =	$statusup;//.' <img src='.JURI::base().'modules/mod_jomsocialbroadcast/images/facebook.png height=19>';
							}
							$statusdata[$k]['content'] = '';
							$statusdata[$k]['app'] = 'profile';
							$statusdata[$k]['cid'] = $userid;
							$statusdata[$k]['created'] = $status[$i]['created_at'];
							$statusdata[$k]['access'] = 0;
							$statusdata[$k]['params'] = '';
							$statusdata[$k]['points'] = 1;
							$statusdata[$k]['archived'] = 0;
							$statusdata[$k]['status'] = $status[$i]['status'];
							$model->fbstore($statusdata,'profile');
						}
		  			}
					else
						break;
				$i++;
				}
		 
			}catch (FacebookApiException $e)
			{
				$mylogobj->simpleLog("FPRtoJS\n ");
		  		$mylogobj->simpleLog("[Facebook profile Response]:\n  " . JArrayHelper::toString($e)); 
			error_log($e);
			}
		 }
	  ////////end facebook profile/////////////////  
	
		}
	echo implode('<br />', $log);
	$model = $this->getModel('facebook');
	$model->fbdeletetmpactivity();
	die;	
	}

	function redirect()
	{
		$data=combroadcastHelper::getInfo(); 
		$user 	= JFactory::getUser();
		$msg="";
		$session =& JFactory::getSession();
		if($session->get('statusfblog') != $data['status_fb'])
		{
			$session->set('statusfblog', $data['status_fb']); 		
			$app =& JFactory::getApplication();  
			$sitename=$app->getCfg('sitename');
			if($data['status_fb'])
			$msg=$user->name." has connected with facebook through $sitename";
		}
		$app	= JFactory::getApplication();
		$currentMenu = $session->get('currentMenu');
		$app->redirect($currentMenu,  $msg);
	}
	
	function redirect_authorise()
	{
		$auth_loc = JRequest::getVar('redirect_url');
		$this->app->redirect($auth_loc);
	}

	function getRemotedata($URL)
	{
	
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $URL); 
		$data = curl_exec($ch); 
		curl_close($ch);
		return $data;
		
	}

}
