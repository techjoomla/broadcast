<?php

defined('_JEXEC') or die( 'Restricted access' );

require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

$document = &JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css' );
$rsslists = $this->subscribedlists->broadcast_rss;
$model	= $this->getModel( 'config' );					
$session =& JFactory::getSession();

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
	 var limit="<?php echo $broadcast_config['rss_link_limit']; ?>";
	 <?php
	 if(isset($this->subscribedlists->broadcast_rss))
	 {
	 ?>
	 	var counter="<?php echo count($rsslists)+1; ?>";
	 <?php
	 }
	 else
	 {
	 ?>
		var counter=1;
	<?php
	}
	?>
	function divhide(thischk){

		if(document.getElementById(thischk.id+'1').style.display == "none" ){
			document.getElementById(thischk.id+'1').style.display="block";
		}
		else{
			document.getElementById(thischk.id+'1').style.display="none";
		}
	}
	
	
</script>
<form action=""  method="POST" name="manualform1" >
	<h1 class="contentheading">											
			 <?php echo JText::_('BC_SETT');?>
	</h1>
	<div class="bc_connect">
		<div class="box-container-t">
			<div class="box-tl"></div>
			<div class="box-tr"></div>
			<div class="box-t"></div>
		</div>									
		<div class="content_cover">	
				<div id="broadcast_connect" onclick="divhide(this);" > <b> <?php echo JText::_('CONN_SER')?> </b></div>	
			
			
				<?php 
						$otherdataArr=$model->renderHTML_other();
					if(empty($otherdataArr))
					{
					echo "No Other Account Data</div></div>";
					return;
					}
							$otherdataArr=$session->set("API_otherAccountData",$otherdataArr);
						foreach($otherdataArr as $otherdata)
							{
								foreach($otherdata as $dts)
								{?>
								<div id="broadcast_connect1"  class="broadcast-expands" style="float:left;padding-left:40px">
								<div> <?php echo $dts[0]['displayname'];?>	</div>	
								<?php
									$i=0;
									foreach($dts as $singledata)
									{
										$i++;
							
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
				 						$data.='<div class="page_status_config_inner" style="float:left;padding-left:40px">
				 							<input class="api_checkbox" type="checkbox"  id="'.$singledata['fieldname'].'[]" name="'.$singledata['fieldname'].'[]" "'.$checked.'" value="'.$singledata['id'].'" />';
										$data.='<img class="bcapi_img"  src="'.$image.'" class="page_picture"  >'.$title;
										//$data.='<img class="bcapi_img" src="'.JURI::base().'components/com_broadcast/images/'.$singledata['techjoomlaapiname'].'.png" border="0" alt="Tooltip" />';
										$data.='</div>';
										echo $data;
										//if($i%3==0)
										//echo "<br/>";
									}
									?>

									<div style="clear:both">
							</div>

								<?php
								}
							?>
														

													
							<?php
						}
				



				?>

			
		



	

	<div id="manual_div" align="left" style="display:block; padding-top: 10px;">
		<input type="hidden" name="option" value="com_broadcast">		
		<input type="hidden" id="task" name="task" value="saveotheraccounts">
		<input type="button" value="<?php echo JText::_('BC_SAVE')?>" onclick="submit(this.form);">
	</div>
	<div class="content_bottom">
			<div class="box-bl"></div>
			<div class="box-br"></div>
			<div class="box-b"></div>
	</div>
	</div></div>
									<div style="clear:both">

 </form>
