<?php

defined('_JEXEC') or die( 'Restricted access' );

require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

$document = &JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css' );
$rsslists = $this->subscribedlists->broadcast_rss;
							/*$rssdts=explode('|',$rsslists);
							$irss=0;
							foreach($rssdts as $rsskey=>$rssvalue)
							{
							
								if(is_int($rsskey))
								{
								$final_rss[$irss]['title']='';

								}
								else
								$final_rss[$irss]['title']=$key;
								$final_rss[$irss]['url']=$rssvalue;
							$irss++;
							}
							$vv=$final_rss;
						echo $ss=	json_encode($vv);die;*/


$session =& JFactory::getSession();

$itemid = JRequest::getVar('Itemid', '','GET');
$session->set('itemid_session',$itemid);	
$u =& JURI::getInstance();
$currentMenu= $u->toString();
$session->set('currentMenu', $currentMenu);
if(isset($this->subscribedlists->broadcast_rss) )
{
	$rsslists = explode('|', $this->subscribedlists->broadcast_rss);
}

$user=JFactory::getUser();
if(!$user->id){
	echo JText::_('BC_LOGIN');
	return false;
}
?>

<script type="text/javascript">
   jQuery.noConflict();
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
			
			<div id="broadcast_connect1"  class="broadcast-expands">
			<div> <?php echo JText::_('BC_SER_MSG')?>	</div>	
				<?php 
				include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
				$lang = & JFactory::getLanguage();
				$lang->load('mod_broadcast', JPATH_SITE);
				$apidata = combroadcastHelper::getapistatus();
				$align=1;			
				ob_start();
					include(JModuleHelper::getLayoutPath('mod_broadcast'));
					$html = ob_get_contents();
				ob_end_clean();
				echo $html ;	
				?>
			
					<?php
						$link = JRoute::_('index.php?option=com_broadcast&view=config&tmpl=component&layout=otheraccounts');
					?>
					<br/><a rel="{handler: 'iframe', size: {x: 800, y: 800}}" href="<?php echo $link; ?>" class="modal">
						<span class="editlinktip hasTip" title="<?php echo JText::_('OTHER_ACCNT');?>" ><?php echo JText::_('OTHER_ACCNT');?></span>
					</a>
					
			</div><!--End of Div Broadcast_Connect -->
		</div>
		<div class="content_bottom">
			<div class="box-bl"></div>
			<div class="box-br"></div>
			<div class="box-b"></div>
		</div>
	</div>

<form action=""  method="POST" name="manualform" >
	<!-- **** Start Added & Modified By Deepak -->
	<div class="bc_connect">
		<div class="box-container-t">
			<div class="box-tl"></div>
			<div class="box-tr"></div>
			<div class="box-t"></div>
		</div>									
		<div class="content_cover">	
			<div id="broadcast_activity" onclick="divhide(this);" ><b><?php echo JText::_('BC_ACT')?></b></div>	      
			<div id="broadcast_activity1"  class="broadcast-expands">
				<div style="padding-left: 8px;">		
					<?php
					if($broadcast_config['integration']==0)
						$brodfile 	= JPATH_SITE."/components/com_broadcast/jomsocial.ini";
						else if($broadcast_config['integration']==1)
						$brodfile 	= JPATH_SITE."/components/com_broadcast/jomwall.ini";
						$activities = parse_ini_file($brodfile);
						$lists 	= array();	
						if (isset($this->subscribedlists->broadcast_activity_config))
							$lists 	=  explode('|', $this->subscribedlists->broadcast_activity_config);
						foreach($activities as $v=>$bractive)
						{								
							if(in_array($v, $lists))
								$brchecked = 'checked="checked"';
							else
								$brchecked = '';
											
							if($bractive != '')							
								echo '<input type="checkbox" name="broadcast_activity[]" '.$brchecked.' value="'.$v.'" >'.$bractive.'<br />';								
						}
					?>
				</div>	
				<?php
					if($broadcast_config['integration']==0){
				?>
				<div style="padding-left: 8px;"><?php echo JText::_('JS_BC_ACT_MSG')?></div>
				<?php
				}
				?>
			</div>
		</div>
		<div class="content_bottom">
			<div class="box-bl"></div>
			<div class="box-br"></div>
			<div class="box-b"></div>
		</div>
	</div>		
	<div class="bc_connect">
		<div class="box-container-t">
			<div class="box-tl"></div>
			<div class="box-tr"></div>
			<div class="box-t"></div>
		</div>									
		<div class="content_cover">	
		<div id="check_rss_label" onclick="divhide(this);" ><b> <?php echo JText::_('ACT_RSS')?></b></div>
		<script type="text/javascript">
			var latestId = counter+1;
			function addNewItem() {
				if(parseInt(counter)>parseInt(limit)){
	        	    alert("<?php echo JText::sprintf('LIMIT_RSS',$broadcast_config['rss_link_limit']);?>");
				}
				else{
					appendElement("container1", "element" + latestId, "Title <input size='50' type='text' class='inputbox' name='rss_title[]' value='' /><br>Link <input size='50' type='text' class='inputbox' name='rss_link[]' value='' /><a href=\"javascript:removeItem(" + latestId + ")\"><?php echo JText::_('REM_RSS');?></a>");		
					latestId++;
					counter++;
				}
			}

			function removeItem(idNumber) {	
				removeElement("container1", "element" + idNumber);
				counter--;
			}

			function removeElement(parentId, elementId) {	
				//Get a reference to the element containgint the element we are removing
			  	var parentElement = document.getElementById(parentId);
			  	//Get a reference to the element we are removing
			  	var childElement = document.getElementById(elementId);
			  	
				//remove the 
			  	parentElement.removeChild(childElement);
			}

			function appendElement(container1Id, newElementId, newElementContent) {	
				//First, we need to create a new DIV element
			  	var newElement=document.createElement("div");
				//New we will give it the specified ID so we can manage it later if necessary
				newElement.setAttribute("id", newElementId);
				//Insert the HTML content into the new element
			  	newElement.innerHTML=newElementContent;
	
				//Get a reference to the element that will contain the new element
			 	var container1 = document.getElementById(container1Id);
				//Now we just need to insert our new element into the containing element
			  	container1.appendChild(newElement, container1);	
			}
		</script>
		
		<div id="check_rss_label1" class="broadcast-expands" style="padding-left: 8px;">
			<?php echo JText::_('ACT_RSS_MSG')?>
			<br />		
			<div id="broadcast_rss">
				<div id="container1" style="padding-left: 20px;">
					<?php 
						if(isset($this->subscribedlists->broadcast_rss) )
						{
							$rsslists = $this->subscribedlists->broadcast_rss;
							$rssdts=json_decode($rsslists,true);

											
							$i=0;
							if(!empty($rssdts))
							{
								foreach($rssdts as $rss)
								{
							


									echo '<div id="element'.$i.'">';													
									echo JText::_('Title').' <input size="50" type="text" class="inputbox" name="rss_title['.$i.']" 
									value="'.$rss['title'].'" /><br/>'.JText::_('Link').' <input size="50" type="text" class="inputbox" name="rss_link['.$i.']" value="'.$rss['link'].'" />';
									echo '<a href="javascript:removeItem('.$i.');" > '.JText::_('REM_RSS').'</a>'."<br />";
									echo '</div>';
									$i++;
								}
							}
						}
					?>
				</div><br />
				<div style="text-align: left; padding-left:8px;"><a id="addButton" href="javascript:addNewItem();">[<?php echo JText::_('ADD_RSS')?>]</a></div>			
			</div>
		</div>	
		</div>
		<div class="content_bottom">
			<div class="box-bl"></div>
			<div class="box-br"></div>
			<div class="box-b"></div>
		</div>
	</div>
	<!-- **** End Added & Modified By Deepak -->

	<div id="manual_div" align="left" style="display:block; padding-top: 10px;">
		<input type="hidden" name="option" value="com_broadcast">		
		<input type="hidden" id="task" name="task" value="save">
		<input type="button" value="<?php echo JText::_('BC_SAVE')?>" onclick="submit(this.form);">
	</div>
 </form>
