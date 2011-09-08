<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

$u =& JURI::getInstance();
$currentMenu= $u->toString();
if(!stristr($currentMenu, 'index.php'))
	$currentMenu= JURI::base();	

$session =& JFactory::getSession();
$session->set('currentMenu', $currentMenu); 
$status_fb = $data['status_fb'];
$remove_link_fb = $data['remove_link_fb'];
$loginUrl_fb = $data['loginUrl_fb'];
$status_twitter = $data['status_twitter'];
$remove_link_twitter = $data['remove_link_twitter'];
$request_link_twitter = $data['request_link_twitter'];
$status_linkedin = $data['status_linkedin'];
$remove_link_linkedin = $data['remove_link_linkedin'];
$request_link_linkedin = $data['request_link_linkedin'];
$rss_link=JRoute::_('index.php?option=com_broadcast&view=config');
$pretext = $data['pretext'];
$posttext = $data['posttext'];
$user 	= JFactory::getUser();
$msg="";

 /*

if($session->get('statusfblog') != $data['status_fb'])
{
$session->set('statusfblog', $data['status_fb']); 
if($status_fb)
	 $msg=$user->name." has connected with Facebook through Equipalizer.com";
}

if($session->get('statustwitterlog') != $data['status_twitter'])
{
$session->set('statustwitterlog', $data['status_twitter']); 
if($status_twitter)
	 $msg=$user->name." has connected with Twitter through Equipalizer.com";
}

if($session->get('statuslinkedinlog') != $data['status_linkedin'])
{
$session->set('statuslinkedinlog', $data['status_linkedin']); 
if($status_linkedin)
	 $msg=$user->name." has connected with Linkedin through Equipalizer.com";
}

if($session->get('pagecount'))
echo $msg;
$session->set('pagecount', '1');
*/
$doc =& JFactory::getDocument();
$logo=JURI::base();
$doc->addStyleSheet( $logo.'modules/mod_jomsocialbroadcast/mod_jomsocialbroadcast.css' );

if($config['facebook_profile'] or $config['facebook_page'] or $config['twitter'] or  $config['linkedin'])
{
	if($config['facebook_profile'] or $config['facebook_page'] )
	{
	?>
		<div class="broadcast_head"><?php echo $pretext; ?></div>
		<div class="broadcast_left" >
		<img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/facebook_logo.png" height="20"><b style="vertical-align:middle; padding-bottom:25px;">
		<?php 
		    /*if($status_fb)
			echo JText::_('Connected') ;
			else
			echo JText::_('Disconnected'); */
			echo JText::_('Facebook') ;
		?>
		</b></div>
		<?php
		if ($status_fb){
		?>
		<div><a href="<?php echo $remove_link_fb; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a></div>
		<?
		}
		else{?>
		<div><a href="<?php echo $loginUrl_fb; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a></div>
		<?
		}
		?>
		<div style="clear:left;border-bottom:1px solid #CCCCCC;"></div>
<?
}
if($config['twitter'])
{
?>
		<div class="broadcast_left"><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/twitter_logo.png" height="20"><b style="vertical-align:middle; padding-bottom:25px;">
		<?php
        /* if($status_twitter)
		echo JText::_('Connected') ;
		else
		echo JText::_('Disconnected');*/
		echo JText::_('Twitter');
		?>
		</b></div>
		<?php
		if ($status_twitter) {
		?>
		<div><a href="<?php echo $remove_link_twitter; ?>" >
			<img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a>
		</div>
		<?
		}
		else {
		?>
		<div>
			<a href="<?php echo $request_link_twitter; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a>
		</div>
		<?
		}
		?>
		<div style="clear:left;border-bottom:1px solid #CCCCCC;"></div>
<?
}
	if($config['linkedin'])
	{
?>
		<div class="broadcast_left"><img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/linkfinal.jpg" height="20"><b style="vertical-align:middle; padding-bottom:25px;">
			<?php 
			/*if($status_linkedin)
			echo JText::_('Connected') ;
			else
			echo JText::_('Disconnected');*/
			echo JText::_('Linkedin');
			?>
		</b></div>
		<?php
		if ($status_linkedin) {
		?>
			<div><a href="<?php echo $remove_link_linkedin; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a></div>
		<?php
		} else {
		?>
			<div ><a href="<?php echo $request_link_linkedin; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a></div>
		<?php
		}
		?>
		<div style="clear:left;border-bottom:1px solid #CCCCCC;"></div>
		<div class="broadcast_foot" style="width:100%"><?php echo $posttext; ?></div>
<?
	}

}
?>
<div class="broadcast_left"><a href="<?php echo $rss_link; ?>" style="color:black; text-decoration:none;"><img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/rss.png" height="20"><b style="vertical-align:middle; padding-bottom:25px;">
<?php
			echo JText::_('Rss Feeds');
			?>
		</b></a></div>	
			<div ><!--a href="<?php echo $request_link_linkedin; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a--></div>
		
		<div style="clear:left;"></div>
		<div class="broadcast_foot" style="width:100%"><?php echo $posttext; ?></div>




