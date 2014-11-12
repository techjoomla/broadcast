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

$document=JFactory::getDocument();
if(JVERSION>=3.0)
jimport('joomla.html.html.bootstrap'); // get bootstrap
$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css' );
$rsslists='';
if(!empty($this->subscribedlists->broadcast_rss))
$rsslists = $this->subscribedlists->broadcast_rss;


$session = JFactory::getSession();

$itemid = JRequest::getVar('Itemid', '','GET');
$session->set('itemid_session',$itemid);
$u = JUri::getInstance();
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
	 var limit="<?php echo $params->get('rss_link_limit'); ?>";
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
<div class="broadcaast_container">
	<h1 class="componentheading">
			 <?php echo JText::_('BC_SETT');?>
	</h1>
	<?php
	if(JVERSION<3.0)
	{
	?>

	<div class="techjoomla-bootstrap">
	<?php
}?>
	<div id="messagesave" ></div>

	<div class="bc_connect">
		<div class="box-container-t">
			<div class="box-tl"></div>
			<div class="box-tr"></div>
			<div class="box-t"></div>
		</div>
		<div class="content_cover">
				<div id="broadcast_connect" class="alert alert-info" onclick="divhide(this);" > <b> <?php echo JText::_('CONN_SER')?> </b></div>

			<div id="broadcast_connect1"  class="broadcast-expands">
			<div> <?php echo JText::_('BC_SER_MSG')?>	</div><br/>
				<?php
				$called_from_component=1;
				include_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');
				$lang=JFactory::getLanguage();
				$lang->load('mod_broadcast', JPATH_SITE);
				$combroadcastHelper=new combroadcastHelper();
				$apidata = $combroadcastHelper->getapistatus();
				$align=1;

				ob_start();
					include(JModuleHelper::getLayoutPath('mod_broadcast'));
					$html = ob_get_contents();
				ob_end_clean();
				echo $html ;

				?>


			</div><!--End of Div Broadcast_Connect -->
		</div>
		<div class="content_bottom">
			<div class="box-bl"></div>
			<div class="box-br"></div>
			<div class="box-b"></div>
		</div>
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
			<div id="broadcast_activity" onclick="divhide(this);" class="alert alert-info"><b><?php echo JText::_('BC_ACT')?></b></div>
			<div id="broadcast_activity1"  class="broadcast-expands">
				<div style="padding-left: 8px;">
					<?php
					if($params->get('integration')=='js')
						$brodfile 	= JPATH_SITE."/components/com_broadcast/jomsocial.ini";
						else if($params->get('integration')=='jwall')
						$brodfile 	= JPATH_SITE."/components/com_broadcast/jomwall.ini";
						else if($params->get('integration')=='easysocial')
						$brodfile 	= JPATH_SITE."/components/com_broadcast/easysocial.ini";
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
					if($params->get('integration')=='js'){
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
		<div id="check_rss_label" onclick="divhide(this);" class="alert alert-info"><b> <?php echo JText::_('ACT_RSS')?></b></div>
		<script type="text/javascript">
			var latestId = counter+1;
			function addNewItem() {
				if(parseInt(counter)>parseInt(limit)){
	        	    alert("<?php echo JText::sprintf('LIMIT_RSS',$params->get('rss_link_limit'));?>");
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

		<div id="check_rss_label1" class="broadcast-expands" style="padding-left: 8px;" class="alert alert-info">
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
		<div class="content_bottom">
			<div class="box-bl"></div>
			<div class="box-br"></div>
			<div class="box-b"></div>
		</div>
	</div>


		<div class="form-actions">
				<input type="hidden" name="option" value="com_broadcast">
				<input type="hidden" id="task" name="task" value="save">
				<div align="center"><input type="button" class="btn btn-primary" value="<?php echo JText::_('BC_SAVE')?>" onclick="submit(this.form);"></div>
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
</div>
