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
if(isset($data['pretext']))
	$pretext = $data['pretext'];
if(isset($data['posttext']))
	$posttext = $data['posttext'];
$user 	= JFactory::getUser();
$msg="";


if($align==1){	//horizontal
$outclass='broadcast_hori';
$inclass='inbroadcast_hori';
}
else{	//vertical
$outclass='broadcast_ver';
$inclass='inbroadcast_ver';
}

$doc =& JFactory::getDocument();
$logo=JURI::base();
$doc->addStyleSheet( $logo.'modules/mod_jomsocialbroadcast/mod_jomsocialbroadcast.css' );
?>
<?php if($posttext){?>
<div class="broadcast_head"><?php echo $pretext; ?></div>
<?php }?>
<?php 
if($broadcast_config['facebook_profile'] or $broadcast_config['facebook_page'] or $broadcast_config['twitter'] or  $broadcast_config['linkedin'])
{
	if($broadcast_config['facebook_profile'] or $broadcast_config['facebook_page'] )
	{
	?>
	<div class="<?php echo $outclass ?>" >
		<div class="<?php echo $inclass ?>" >
			<img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/facebook_logo.png"  >
		</div>
		<div>
		<?php
		if ($status_fb){
		?>
			<a href="<?php echo $remove_link_fb; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a>
		<?php
		}
		else{?>
			<a href="<?php echo $loginUrl_fb; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a>
		<?php
		}
		?>
		</div>
		<div style="clear:both;"></div>
	</div>
		
<?php
}
if($broadcast_config['twitter'])
{
?>
	<div class="<?php echo $outclass ?>" >
		<div class="<?php echo $inclass ?>">
			<img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/twitter_logo.png"  >
		</div>
		<div>
		<?php
		if ($status_twitter) {
		?>
		<a href="<?php echo $remove_link_twitter; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a>
		<?php
		}
		else {
		?>
			<a href="<?php echo $request_link_twitter; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a>
		<?php
		}
		?>
		</div>
		<div style="clear:both;"></div>
	</div>
		
<?php
}
	if($broadcast_config['linkedin'])
	{
?>
	<div class="<?php echo $outclass ?>" >
		<div class="<?php echo $inclass ?>">
			<img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/linkfinal.jpg" >
		</div>
		<div>
		<?php
		if ($status_linkedin) {
		?>
			<a href="<?php echo $remove_link_linkedin; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a>
		<?php
		} else {
		?>
			<a href="<?php echo $request_link_linkedin; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a>
		<?php
		}
		?>
		</div>
		<div style="clear:both;"></div>
	</div>
		
		
<?php
	}

}
?>
	<div class="<?php echo $outclass ?>" >

		<div class="<?php echo $inclass ?>"><a href="<?php echo $rss_link; ?>" style="color:black; text-decoration:none;">
			<img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/rss.png" height="20"></a>
			<div><a href="<?php echo $rss_link; ?>" style="color:black; text-decoration:none;"><b style="vertical-align:middle; padding-bottom:25px;">
			<?php
			echo JText::_('BC_RSS');
			?>
			</b></div></a>
		</div>	
	</div>
		
		<div style="clear:left;"></div>
		<?php if($posttext){?>
		<div class="broadcast_foot" style="width:100%"><?php echo $posttext; ?></div>
		<?php }?>




