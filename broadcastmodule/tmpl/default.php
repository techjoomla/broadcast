<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal', 'a.modal');
$u =& JURI::getInstance();
$currentMenu= $u->toString();
if(!stristr($currentMenu, 'index.php'))
	$currentMenu= JURI::base();	

$session =& JFactory::getSession();
$session->set('currentMenu', $currentMenu); 

$rss_link=JRoute::_('index.php?option=com_broadcast&view=config');
$pretext = $posttext = '';
if(isset($data['pretext']))
	$pretext = $data['pretext'];
if(isset($data['posttext']))
	$posttext = $data['posttext'];

if($align==1){	//horizontal orientation
	$outclass='broadcast_hori';
	$inclass='inbroadcast_hori';
}
else{	//vertical orientation
	$outclass='broadcast_ver';
	$inclass='inbroadcast_ver';
}

$doc =& JFactory::getDocument();
$base=JURI::base()."modules/mod_broadcast/";
$doc->addStyleSheet( $base.'mod_broadcast.css' );
?>

<?php if($pretext){?>
<div class="broadcast_head"><?php echo $pretext; ?></div>
<?php }
if(empty($apidata))
{
	echo JText::_('NO_API_PLUG');
}
for($i=0; $i<count($apidata); $i++)
{
	if(!isset( $apidata[$i]['error_message']) )
	{
		$getTokenURL = JRoute::_("index.php?option=com_broadcast&controller=broadcast&task=get_request_token&api=".$apidata[$i]['api_used']);
		$removeTokenURL= JRoute::_('index.php?option=com_broadcast&controller=broadcast&task=remove_token&api='.$apidata[$i]['api_used']);
?>
		<div class="<?php echo $outclass ?>" >
			<div class="<?php echo $inclass ?>" >
				<img src="<?php echo $base; ?>images/<?php echo $apidata[$i]['img_file_name'] ?>"  >
			</div>
			<div>
			<?php
			if ($apidata[$i]['apistatus']){
			?>
				<a href="<?php echo $removeTokenURL; ?>" ><img src="<?php echo $base;?>images/disconn.png" /></a>
			<?php
			}
			else{?>
				<a href="<?php echo $getTokenURL; ?>" ><img src="<?php echo $base;?>images/conn.png" /></a>
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

<?php 
$view= JRequest::getVar('view');
$option= JRequest::getVar('option');

	if($option!='com_broadcast' and $view!='config')
	{
?>
	<div class="<?php echo $outclass ?>" >
		<div class="<?php echo $inclass ?>">
			<a href="<?php echo $rss_link; ?>" style="color:black; text-decoration:none;">
				<img src="<?php echo $base; ?>images/setting.png" height="20">
			</a>
			<div><a href="<?php echo $rss_link; ?>" style="color:black; text-decoration:none;">
			<b style="vertical-align:middle; padding-bottom:25px;">
			<?php
			echo JText::_('BC_RSS');
			?>
			</b></a>
			</div>
		</div>	
		</div>
		<?php
		}
		?>
		

	
	<div style="clear:both;"></div>

	
	<?php if($posttext){?>
	<div class="broadcast_foot" style="width:100%"><?php echo $posttext; ?></div>
	<?php }?>
