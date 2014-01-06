<?php 
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

$lang =JFactory::getLanguage();
$extension = 'mod_broadcast';
$base_dir = JPATH_SITE;
$language_tag = 'en-GB';
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal','a.modal');
$u = JUri::getInstance();
$currentMenu= $u->toString();
if(!stristr($currentMenu, 'index.php'))
	$currentMenu= JUri::base();	

$session = JFactory::getSession();
$session->set('currentMenu', $currentMenu); 
require_once(JPATH_SITE.DS.'components'.DS.'com_broadcast'.DS.'helper.php');
$combroadcastHelper=new combroadcastHelper();
//pass the link for which you want the ItemId.	
$in_itemid	= $combroadcastHelper->getitemid('index.php?option=com_broadcast&view=config');
$rss_link=JRoute::_(JUri::base().'index.php?option=com_broadcast&view=config&Itemid='.$in_itemid);
$pretext = $posttext = '';
if(isset($data['pretext']))
	$pretext = $data['pretext'];
if(isset($data['posttext']))
	$posttext = $data['posttext'];

if($align=='hr'){	//horizontal orientation
	$outclass='broadcast_hori';
	$inclass='inbroadcast_hori';
}
else{	//vertical orientation
	$outclass='broadcast_ver';
	$inclass='inbroadcast_ver';
}

$doc = JFactory::getDocument();
$base=JUri::base()."modules/mod_broadcast/";
$doc->addStyleSheet($base.'mod_broadcast.css');
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
	if(!isset( $apidata[$i]['error_message']))
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
$jinput=JFactory::getApplication()->input;
$view=$jinput->get('view','','STRING');
$option=$jinput->get('option','','STRING');
	if($option!='com_broadcast' AND $view!='config')
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
		

	
	<div style="clear:left;"></div>

	

	
	<?php if($posttext){?>
	<div class="broadcast_foot" style="width:100%"><?php echo $posttext; ?></div>
	<?php }?>
