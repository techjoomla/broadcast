<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined('_JEXEC') or die( 'Restricted access' );

$params=JComponentHelper::getParams('com_broadcast');

$document = &JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css' );
if(JVERSION>=3.0)
jimport('joomla.html.html.bootstrap'); // get bootstrap 

$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css' );
$rsslists = $this->subscribedlists->broadcast_rss;
$model	= $this->getModel( 'config' );					
$session =& JFactory::getSession();
$cache = & JFactory::getCache();
$cache->clean();
$itemid = JRequest::getVar('Itemid', '','GET');
$session->set('itemid_session',$itemid);	
$u =& JURI::getInstance();
$currentMenu= $u->toString();
$user=JFactory::getUser();
$session->set('currentMenu', $currentMenu);
if(isset($this->subscribedlists->broadcast_rss) )
{
	$rsslists = explode('|', $this->subscribedlists->broadcast_rss);
}


if(!$user->id){
	echo JText::_('BC_LOGIN');
	return false;
}
?>

<script type="text/javascript">

</script>
 
<script type="text/javascript">
	 var limit="5";
		var counter=1;	
	function divhide(thischk){
thischk.id
		if(document.getElementById(thischk.id+'1').style.display == "none" ){
			document.getElementById(thischk.id+'1').style.display="block";
		}
		else{
			document.getElementById(thischk.id+'1').style.display="none";
		}
	}
	
	
</script>
<?php
	if(JVERSION<3.0)
	{
	?>

	<div class="techjoomla-bootstrap">
	<?php
}?>	
<form action=""  method="POST" name="manualform1" >
	<h3 class="componentheading">											
			 <?php echo JText::_('BC_SETT');?>
	</h3>
		<div >
	<?php 

	if(empty($this->otherdataArr))
	{
		echo "No Other Account Data</div></div>";
		return;
	}
	
	$jj=0;
	$session->set("API_otherAccountData",$this->otherdataArr);
	foreach($this->otherdataArr as $otherdata)
		{
		
			foreach($otherdata as $dts)
			{

					$divnm="broadcast_connect".$jj;
				?>
				<div class="bc_connect" id="<?php echo $divnm;?>"  >
					<div class="box-container-t">
						<div class="box-tl"></div>
						<div class="box-tr"></div>
						<div class="box-t"></div>
					</div>									
					<div class="content_cover">	
					<div id="<?php echo $dts[0]['displayname'];?>" onclick="divhide(this);" ><b><?php echo $dts[0]['displayname'];?></b></div>	      
						<div id="<?php echo $dts[0]['displayname'].'1';?>"  class="broadcast-expands">
							<?php
									$i=1;
									$jj++;
									foreach($dts as $singledata)
									{
							
				
										if($singledata['connectionstatus']==1)						
										$checked=" checked='checked'";
										else
										$checked='';			
										$sessiondata[]=$singledata;
										$image=$singledata['image'];
										$title=$singledata['name'];
										$connectionstatus=0;
										$action="connect";
										$fieldname=$singledata['fieldname'];

										$finaldata['data'][$fieldname][]=$singledata;
										$data='';
										if($i%3!=0)
										{
										$i=1;
										$float="float:left;margin-top:20px;";
										}
										else
										$float="";
										$i++;
				 						$data.='<div class="page_status_config_inner" style="'.$float.'padding-left:30px">
				 							<input class="api_checkbox" type="checkbox"  id="'.$singledata['fieldname'].'[]" name="'.$singledata['fieldname'].'[]" "'.$checked.'" value="'.$singledata['id'].'" />';
										$data.='<img class="bcapi_img"  src="'.$image.'"  >'.$title.'</div>';


										echo $data;

										?>
							

										<?php
				
									}
									?>

								</div><!--content-cover-->
							</div><!--content-cover-->
							<div class="content_bottom">
								<div class="box-bl"></div>
								<div class="box-br"></div>
								<div class="box-b"></div>
							</div>
				</div><!--bc-connect-->		

			<?php
			}
		
	}
?>

		<div class="form-actions">
					<input type="hidden" name="option" value="com_broadcast">		
					<input type="hidden" id="task" name="task" value="saveotheraccounts">
					<div align="center"><input class="btn btn-primary" type="button" value="<?php echo JText::_('BC_SAVE')?>" onclick="submit(this.form);"></div>
				</div>
		

 </form>
<?php
if(JVERSION<3.0)
{
?>
</div>

<?php
}
?>
