<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_SITE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');
require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

$document = &JFactory::getDocument();

$document->addScript( JURI::base().'components/com_community/assets/'.'jquery-1.3.2.pack.js' );
$document->addScript( JURI::base().'components/com_community/assets/'.'joms.jquery.js' );
$document->addScript( JURI::base().'components/com_community/assets/validate-1.5.js' );
$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css' );



$session =& JFactory::getSession();

$itemid = JRequest::getVar('Itemid', '','GET');
$session->set('itemid_session',$itemid);	
$u =& JURI::getInstance();
$currentMenu= $u->toString();
$session->set('currentMenu', $currentMenu);
if(isset($this->subscribedlists->broadcast_rss_url) )
{
	$rsslists = explode('|', $this->subscribedlists->broadcast_rss_url);
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
	 if($this->subscribedlists->broadcast_rss_url)
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

	
</script>

	<h1 class="contentheading">											
			 <?php echo JText::_('BC_SETT');?>
	</h1>
 <?php
	

	if($broadcast_config['facebook_profile'] or $broadcast_config['facebook_page'] or $broadcast_config['twitter'] or  $broadcast_config['linkedin'])
	{
 ?>
	<fieldset class="fieldsetstyle">
		<legend class="legendstyle"><?php echo JText::_('CONN_SER')?></legend>
		<div class="check_connect_border"></div>
		<div id="broadcast_connect">
			<div class="content_cover" id="check_rss_label" style="padding-left: 8px;"><?php echo JText::_('BC_SER_MSG')?></div>
			<?php 
			include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
			$lang = & JFactory::getLanguage();
			$lang->load('mod_jomsocialbroadcast', JPATH_SITE);
			$apidata = combroadcastHelper::getapistatus();
			$align=1;			
			ob_start();
				include(JModuleHelper::getLayoutPath('mod_jomsocialbroadcast'));
				$html = ob_get_contents();
			ob_end_clean();
			echo $html ;	
			?>
		</div><!--End of Div Broadcast_Connect -->
	</fieldset>
	<?php
	}
	?>


<?php
$user	=	JFactory::getUser();

 if($user->id)
 {
	
?>
<form action=""  method="POST" name="manualform" >
	<!-- **** Start Added & Modified By Deepak -->
	<fieldset class="fieldsetstyle">
		<legend class="legendstyle"><?php echo JText::_('BC_ACT')?></legend>             
		<div id="broadcast_activity" >
			<table>
				<tr>
				    <td style="padding-left: 8px;">		
						<?php
							$brodfile 	= JPATH_SITE."/components/com_broadcast/broadcast.ini";
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
				</td>                                       
				</tr>	
			</table>	
		</div>
	<div class="content_cover" style="padding-left: 8px;"><?php echo JText::_('BC_ACT_MSG')?></div>
	</fieldset>			
	<fieldset class="fieldsetstyle">
		<legend class="legendstyle"><?php echo JText::_('ACT_RSS')?></legend>
		<script type="text/javascript">
			var latestId = counter+1;
			function addNewItem() {
				if(counter>limit){
	        	    alert("Only  "+limit+" RSS Links are allowed.");
				}
				else{
				appendElement("container1", "element" + latestId, "<input type='text' class='inputbox' name='rss_link[]' value='' /><a href=\"javascript:removeItem(" + latestId + ")\"> [Remove Link]</a>");		
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
		<div class="content_cover" id="check_rss_label" style="padding-left: 8px;"><?php echo JText::_('ACT_RSS_MSG')?></div>
<br />		
<div id="broadcast_rss">
			<div id="container1" style="padding-left: 20px;">
				<?php 
					if(isset($this->subscribedlists->broadcast_rss_url) )
					{
						$rsslists = explode('|', $this->subscribedlists->broadcast_rss_url);						
						$i=0;
						foreach($rsslists as $rss)
						{
							echo '<div id="element'.$i.'">';													
							echo '<input size="50" type="text" class="inputbox" name="rss_link['.$i.']" value="'.$rss.'" />';
							echo '<a href="javascript:removeItem('.$i.');" > ['.JText::_('REM_RSS').']</a>'."<br />";
							echo '</div>';
							$i++;
						}
					}
				?>
			</div><br />
			<div style="text-align: left; padding-left:8px;"><a id="addButton" href="javascript:addNewItem();">[<?php echo JText::_('ADD_RSS')?>]</a></div>			
		</div>	
	</fieldset>	
	<!-- **** End Added & Modified By Deepak -->
	
	<table cellspacing='5'  align='center' width="40%">	
		<tr>	
			<td align='left' colspan="2">
				<input type="hidden" name="option" value="com_broadcast">		
				<input type="hidden" id="task" name="task" value="save">
				<input type="button" value="<?php echo JText::_('BC_SAVE')?>" onclick="submit(this.form);">
			</td>
		</tr>
	</table>

 </form>
<?php
 }
?>
