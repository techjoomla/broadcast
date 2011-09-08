<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

$logo	= JURI::base();
$user 	= JFactory::getUser();

$u =& JURI::getInstance();
$currentMenu= $u->toString();
$session =& JFactory::getSession();
$session->set('currentMenu', $currentMenu); 
$rss_link=JRoute::_('index.php?option=com_broadcast&view=config');
$status_fb= $remove_link_fb=$loginUrl_fb=$status_twitter=$remove_link_twitter=$request_link_twitter=$status_linkedin=$remove_link_linkedin=$request_link_linkedin=$pretext=$posttext='';

if(isset($data['status_fb']))
$status_fb = $data['status_fb'];

if(isset($data['remove_link_fb']))	
$remove_link_fb = $data['remove_link_fb'];

if(isset($data['loginUrl_fb']))
$loginUrl_fb = $data['loginUrl_fb'];

if(isset($data['status_twitter']))
$status_twitter = $data['status_twitter'];

if(isset($data['remove_link_twitter'])) 
$remove_link_twitter = $data['remove_link_twitter'];

if(isset($data['request_link_twitter']))
$request_link_twitter = $data['request_link_twitter']; 

if(isset($data['status_linkedin']))

$status_linkedin = $data['status_linkedin'];

if(isset($data['remove_link_linkedin'])) 
$remove_link_linkedin = $data['remove_link_linkedin'];

if(isset($data['request_link_linkedin']))
$request_link_linkedin = $data['request_link_linkedin'];

if(isset($data['pretext']))
$pretext = $data['pretext'];

if(isset($data['posttext']))
$posttext = $data['posttext'];


$status	= str_replace("\n",'', $user->_status);
$status=strip_tags($status);
	
?>
<script type="text/javascript">
   jQuery.noConflict();
 </script>
<script type="text/javascript">

function editStatus()
{
	jQuery('#shoutedit').hide();
	jQuery('#profile-status-message').hide();
	jQuery('#statustext').show().focus();
	jQuery('#save-status').show();
}

</script>
<?php
$showProfileName='';
$showProfilePicture='';
if ($showProfileName == 'yes')
{
	echo $user->getDisplayName() . '<br><br>';
}?>
<?php 
if ($showProfilePicture == 'yes')
{
	echo $profileLink;
}
 if($config['facebook_profile'] or $config['facebook_page'] or $config['twitter'] or  $config['linkedin'])
{
 if($config['show_status_update'])
{
?>

<h3>Status Update</h3>
<span id="profile-status-message"><?php echo empty( $user->_status ) ? '&nbsp;' : strip_tags($user->_status); ?></span>
<textarea style="width:80%; height:50px" class="inputbox" id="statustext" name="statustext"><? echo  strip_tags($status); ?></textarea>
<a id="shoutedit" href="#" style="text-transform: lowercase;" onclick="editStatus(); return false;">[edit status]</a>
<a id="save-status" href="#" style="display: none; text-transform: lowercase;" >[save]</a>
<script type='text/javascript'>

	var cur_status = '';
	var statusText    = jQuery('#statustext');
	var saveStatus    = jQuery('#save-status');

	joms.utils.textAreaWidth(statusText);
	joms.utils.autogrow(statusText);
	
	jQuery('#statustext').hide();
	jQuery(document).ready(function(){ 
	
saveStatus.click(function()
{
 if ( cur_status != jQuery('#statustext').val() ) {
		var inputVal = jQuery('#statustext').val();
		jax.call('community', 'status,ajaxUpdate', inputVal);
		jQuery('#save-status').hide();
		jQuery('#statustext').hide();
		jQuery('#profile-status-message').show();
		jQuery('#profile-status-message').html(inputVal);
		cur_status = inputVal;
		
	}
	else {
		jQuery('#save-status').hide();
		jQuery('#statustext').hide();
		jQuery('#profile-status-message').show();
	}
    jQuery('#shoutedit').show();
	return false;
 
 }); 
 
 })

</script>

<p><?php echo $posttext; ?></p>
<?php 
} 
?>
<table border="0" cellspacing="10" cellpadding="0">
<tr>
<?php if($config['facebook_profile'] or $config['facebook_page'] )
{
?>
		<td valign="baseline"  align="center">
		<img  src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/facebook_logo.png"><br/>
		<?php
		if ($status_fb){
		?>
			<a href="<?php echo $remove_link_fb; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a>
		<?
		}
		else{?>
			<a href="<?php echo $loginUrl_fb; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a>
		<?
		} 
		?>         
		</td>
<?
}
 if($config['twitter'] )
{
?>
		<td valign="baseline" align="center"><img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/twitter_logo.png"><br />
		<?php
		if ($status_twitter) {
			?>
			<a href="<?php echo $remove_link_twitter; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a>
		<?
		} 
		else {
		?>
			<a href="<?php echo $request_link_twitter; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a>
		<?
		}       
		?>
		</td>
<?
}
 if($config['linkedin'] )
{
?>
		<td  align="center" valign="baseline"><img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/linkfinal.jpg" height="20"><br />

		<?php
			if ($status_linkedin) {
			?>
			<a href="<?php echo $remove_link_linkedin; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/disconn.png" /></a>
		<?php } else { ?>
        
			<a href="<?php echo $request_link_linkedin; ?>" ><img src="<?php echo $logo;?>modules/mod_jomsocialbroadcast/images/conn.png" /></a>
            
		<?php } ?>
		</td>
<?php 
} ?>

<td  align="center" valign="baseline"><a href="<?php echo $rss_link; ?>" style="color:black; text-decoration:none;">
<img src="<?php echo $logo; ?>modules/mod_jomsocialbroadcast/images/rss.png" height="20"></a><br />
	</td></tr>
</table>

<?php 
} ?>

