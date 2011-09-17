<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.'config.php');
$bconfig = new BroadcastConfig;
//require_once JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib'.DS.$bconfig->linkedin_library_path;
require_once  JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'lib_new'.DS.'linkedin_twitter'.DS.'linkedinoAuth.php';

class BroadcastControllerlinkedin extends JController
{
	function display()
	{
		parent::display();
	}
   
	function BroadcastControllerlinkedin()
	{
		parent::__construct();
		$bconfig = new BroadcastConfig;
		$this->bconfig = $bconfig;		
	}
    	
	function authorise2()
	{
		
		$link=JURI::base().'components'.DS.'com_broadcast'.DS.'lib'.DS.'linkedin_twitter/linkedinoAuth.php';
		$linkedin = new LinkedIn($this->bconfig->linkedin_access, $this->bconfig->linkedin_secret);
        
		if (isset($_REQUEST['oauth_verifier']))
		{       	       	
			$_SESSION['oauth_verifier']     = $_REQUEST['oauth_verifier'];
			$linkedin->request_token    =   unserialize($_SESSION['requestToken']);
			$linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
			$linkedin->getAccessToken($_REQUEST['oauth_verifier']);
			$_SESSION['oauth_access_token'] = serialize($linkedin->access_token);
		}
		
		$data['linkedin_oauth']		= $_SESSION['oauth_access_token'];		
		$data['linkedin_secret']	= $_REQUEST['oauth_verifier'];
		$model 		= $this->getModel('linkedin');
		$model->store($data);
		$this->redirect();
		 	
	}
	
	function remove()
	{ 
		$model =	$this->getModel('linkedin');
		$model->remove();
		$this->redirect();
	}
	
	function getstatus()
	{
	    $mylogobj	= new BroadcastHelperLogs();
		require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
		$pkey = JRequest::getVar('pkey', '');
		if($pkey!=$broadcast_config['private_key_cronjob'])		
		{
			echo "This Private Cron Key Doesnot Exist";
			return;
		}
		
		$_REQUEST['tmpl'] = 'component';		
		$database = JFactory::getDBO();
		$fbquery = "SELECT user_id,linkedin_oauth,linkedin_secret 
		FROM #__broadcast_users 
		WHERE linkedin_oauth <> '' AND linkedin_secret <> ''";
		$database->setQuery($fbquery);
		$this->uaccess = $database->loadObjectlist();		
		$log = array();
		
		if($broadcast_config['linkedin'])
		{
			foreach($this->uaccess as $k=>$v)
			{
				try{
					$log[] = 'User: ' . JFactory::getUser($v->user_id)->name;				
					$userid = $v->user_id;
					$linkedin 					= new LinkedIn($this->bconfig->linkedin_access, $this->bconfig->linkedin_secret);
					$requestToken				= $linkedin->getRequestToken();								
					$linkedin->access_token     =   unserialize($v->linkedin_oauth);          
					$currrent_time	=time();
					$one_hr_before	=time()-(60*60);  
					$xml_data=null; 
					$xml_data = $linkedin->getProfile("~:(current-status,current-status-timestamp)");
					$xml = simplexml_load_string($xml_data);
					$xml = get_object_vars($xml);
					$m=0;
					$k=0;
					$actor="";
					
					if($xml['current-status']) 
						$log[] = 'Status: ' . $xml['current-status'];
					
					if($broadcast_config['show_name'])
						$actor='{actor} ';
					else
						$actor='';

					$statusdata[$k]['actor']			= $userid;
					$statusdata[$k]['target']			= $userid;
					
					if($broadcast_config['status_skip'])
					{
						$search=explode(',', $broadcast_config['status_skip']);
						$statusup=str_replace($search, '', $xml['current-status']);
					}
					else
						$statusup=$xml['current-status'];
					
					if($broadcast_config['status_via'])
						$statusdata[$k]['title']			= $actor.$statusup.' (via Linkedin)';
					else
						$statusdata[$k]['title']			= $actor.$statusup. ' <img src='.JURI::base().'modules/mod_jomsocialbroadcast/images/linkedin.png height=19>';
					$statusdata[$k]['content']			= '';
					$statusdata[$k]['app']				= 'profile';
					$statusdata[$k]['cid']				= $userid;
					$statusdata[$k]['created']			= $xml['current-status-timestamp'];
					$statusdata[$k]['access']			= 0;
					$statusdata[$k]['params']			= '';
					$statusdata[$k]['points']			= 1;
					$statusdata[$k]['archived']			= 0;
					$statusdata[$k]['status']			= $status[$m]['status'];
					$model = $this->getModel('linkedin');

					$model->linkstore($statusdata);						
					$k++;
					$m++;
								
					$model =	$this->getModel('linkedin');
					$model->linkedindeletetmpactivity();
				}
				catch(Exception $e){
				$log[]	= $e;
				error_log($e);}			
			}//for each
			
			echo implode('<br />', $log);
			$mylogobj->simpleLog("LtoJS\n");
			$mylogobj->simpleLog("[Linkedin Response]:\n  " . JArrayHelper::toString($log));
   		}//if config
   		die;
	}

	function redirect()
	{	
		$data=combroadcastHelper::getInfo();  

		$user 	= JFactory::getUser();
		$msg="";
		$session =& JFactory::getSession();
		if($session->get('statuslinkedinlog') != $data['status_linkedin'])
		{
			$session->set('statuslinkedinlog', $data['status_linkedin']); 
			global $mainframe;
			$mainframe =& JFactory::getApplication();  
			$sitename=$mainframe->getCfg('sitename');
			if($data['status_linkedin'])
				$msg=$user->name." ".JText::_('L_CONN_MSG')." ".$sitename;
		}		
		
	    $mainframe	= JFactory::getApplication();
	    $currentMenu = $session->get('currentMenu');
	    $mainframe->redirect($currentMenu, $msg);
	}
}
