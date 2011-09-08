<?php

defined('_JEXEC') or die( 'Restricted access' );

require_once(JPATH_SITE.DS.'components'.DS.'com_community'.DS.'libraries'.DS.'core.php');
require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');

$document = &JFactory::getDocument();
$itemid = JRequest::getVar('Itemid', '','GET');
$_SESSION['itemid_session']=$itemid;
$document->addScript( JURI::base().'components/com_community/assets/'.'jquery-1.3.2.pack.js' );
$document->addScript( JURI::base().'components/com_community/assets/'.'joms.jquery.js' );
$document->addScript( JURI::base().'components/com_community/assets/validate-1.5.js' );
$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css' );

$lists =  explode('|',$this->subscribedlists->broadcast_activity_config);

if (in_array('profile', $lists)) 
	$connect_check = 'checked="checked"';
else
	$connect_check = '';

if (count($lists))
	$activity_check = 'checked="checked"';
 else 
	$activity_check = '';

	$logo	= JURI::base();
	$user 	= CFactory::getUser();
	$u =& JURI::getInstance();
	$currentMenu= $u->toString();
	$session =& JFactory::getSession();
	$session->set('currentMenu', $currentMenu);
	$status_fb= $remove_link_fb=$loginUrl_fb=$status_twitter=$remove_link_twitter=$request_link_twitter=$status_linkedin=$remove_link_linkedin='';
	$request_link_linkedin=$pretext=$posttext='';
	if($config['facebook_profile'] or $config['facebook_page'] )
	{
		$status_fb								= $this->data['status_fb'];		
		$remove_link_fb				    =  $this->data['remove_link_fb'];
		$loginUrl_fb					        =  $this->data['loginUrl_fb'];
	}
	if($config['twitter'])
	{
		$status_twitter					 = $this->data['status_twitter'];		
		$remove_link_twitter			 = $this->data['remove_link_twitter'];
		$request_link_twitter			 = $this->data['request_link_twitter']; 
	}
	if($config['linkedin'])
	{
		$status_linkedin		        = $this->data['status_linkedin'];		
		$remove_link_linkedin		= $this->data['remove_link_linkedin'];
		$request_link_linkedin		= $this->data['request_link_linkedin'];
		$pretext 						= $this->data['pretext'];
		$posttext 						= $this->data['posttext'];
	}	
	$status								= str_replace("\n",'', $user->_status);
	if($this->subscribedlists->broadcast_rss_url)
	$links=explode('|',$this->subscribedlists->broadcast_rss_url);
?>

	<script type="text/javascript">
   jQuery.noConflict();
 	</script>
 
	<script type="text/javascript">
	function checkAll(checkname) {
		for (i = 0; i < checkname.length; i++)
		checkname[i].checked = true;
	}


	function editStatus()
	{
		jQuery('#shoutedit').hide();
		jQuery('#profile-status-message').hide();
		jQuery('#statustext').show().focus();
		jQuery('#save-status').show();
	}

		 var limit="<?echo $config['rss_link_limit']; ?>";
		 <?php
		 if($this->subscribedlists->broadcast_rss_url)
		 {
		 ?>
		 	var counter="<?php echo count($links)+1; ?>";
		 <?php
		 }
		 else
		 {
		 ?>
			var counter=1;
		<?
		}
		?>

	jQuery(document).ready(function(){

  jQuery("#addButton").click(function () {
	if(counter>limit){
            alert("Only  "+limit+" RSS Links are allowed.");
            return false;
	}   
 
/*	var newTextBoxDiv = jQuery(document.createElement('div'))
	     .attr("id", 'TextBoxDiv' + counter);
 
	newTextBoxDiv.after().html('<b><label>RSS Link : </label></b>' +
	      '<input type="text" name="rss_link[' + counter + ']" class="inputbox"  id="rss_link[' + counter + ']"  ><br>');
 
	newTextBoxDiv.appendTo("#TextBoxesGroup");
 
 */
	counter++;
     });
 
/*     jQuery("#removeButton").click(function () {
		if(counter==1)
		{
		        alert("No more textbox to remove");
		        return false;
		}   
		counter--;
        jQuery("#TextBoxDiv" + counter).remove();
 
     });
*/     jQuery("#getButtonValue").click(function () {
 
	var msg = '';
	for(i=1; i<counter; i++){
   	  msg += "\n Textbox #" + i + " : " + jQuery('#textbox' + i).val();
	}
    	  alert(msg);
     });
  });
</script>

	<h1 class="contentheading">											
			 Social Broadcast Settings	
	</h1>
 <?php
	$showProfileName='';
	$showProfilePicture='';
	if ($showProfileName == 'yes')
	{
		echo $user->getDisplayName() . '<br><br>';
	}
	if ($showProfilePicture == 'yes')
	{
		echo $profileLink;
	}

	if($config['facebook_profile'] or $config['facebook_page'] or $config['twitter'] or  $config['linkedin'])
	{
 ?>

	<fieldset class="fieldsetstyle">
	<legend class="legendstyle">Connect to a service</legend>
		<div class="check_connect_border"></div>
	<div id="broadcast_connect">

	<table border="0" cellspacing="2" cellpadding="2">
	<?
		if($config['show_status_update'])
		{
	?>
	<span id="profile-status-message"><?php echo empty( $user->_status ) ? '&nbsp;' : $user->_status; ?></span>
	<textarea style="width:80%; height:50px" class="inputbox" id="statustext" name="statustext"><? echo $status; ?></textarea>
	<a id="shoutedit" href="#" style="text-transform: lowercase;" onclick="editStatus(); return false;">[edit status]</a>
	<a id="save-status" href="#" style="display: none; text-transform: lowercase;" >[save]</a>
	<tr>
	<td colspan="4" valign="baseline">
	</td></tr>
		<?
		}
		?>
	<script type='text/javascript'>

	var cur_status = '';
	var statusText    = jQuery('#statustext');
	var saveStatus    = jQuery('#save-status');

	jQuery('#statustext').hide();
	jQuery(document).ready(function(){ 
	
	saveStatus.click(function()
	{
	 	if ( cur_status != jQuery('#statustext').val() ) 
	 	{
			var inputVal = jQuery('#statustext').val();
			jax.call('community', 'status,ajaxUpdate', inputVal);
			jQuery('#save-status').hide();
			jQuery('#statustext').hide();
			jQuery('#profile-status-message').show();
			jQuery('#profile-status-message').html(inputVal);
			cur_status = inputVal;
		
		}
		else 
		{
			jQuery('#save-status').hide();
			jQuery('#statustext').hide();
			jQuery('#profile-status-message').show();
		}
		  jQuery('#shoutedit').show();
			return false;
	 
	 }); 
 })

	</script>
	<div class="content_cover" id="check_rss_label" style="padding-left: 8px;">You can broadcast your equipalizer activities to your other social networks. Click connect to connect each service.</div>
	<tr style="padding-left:8px;">
	<?php 
		if($config['facebook_profile'] or $config['facebook_page'] )
		{
	?>
		<td valign="baseline">
		<img  src="<?php echo JURI::base(); ?>components/com_broadcast/images/facebook_logo.png"><br/>
		<?php
		if ($status_fb)
		{
		?>
			<a href="<?php echo $remove_link_fb; ?>" ><img src="<?php echo $logo;?>components/com_broadcast/images/disconn.png" /></a>
		<?
		}
		else
		{
		?>
              
			<a href="<?php echo $loginUrl_fb; ?>" ><img src="<?php echo $logo;?>components/com_broadcast/images/conn.png" /></a>
		<?
		} 
		?>         
		</td>
		<?
		}
		
	 if($config['twitter'] )
		{
		?>
		<td valign="baseline"><img src="<?php echo JURI::base(); ?>components/com_broadcast/images/twitter_logo.png"><br />
		<?php
		if ($status_twitter) 
		{
		?>
			<a href="<?php echo $remove_link_twitter; ?>" ><img src="<?php echo $logo;?>components/com_broadcast/images/disconn.png" /></a>
		<?
		} 
		else
		 {
		?>
			<a href="<?php echo $request_link_twitter; ?>" ><img src="<?php echo $logo;?>components/com_broadcast/images/conn.png" /></a>
		<?
		}       
		?>
		</td>
		<?
		}
		
		if($config['linkedin'] )
		{
		?>
		<td valign="baseline"><img src="<?php echo JURI::base(); ?>components/com_broadcast/images/linkfinal.jpg" height="20"><br />
		<?php
			if ($status_linkedin)
			 {
			?>
			<a href="<?php echo $remove_link_linkedin; ?>" ><img src="<?php echo $logo;?>components/com_broadcast/images/disconn.png" /></a>
		<?php
				}
				else 
				{
		?>
			<a href="<?php echo $request_link_linkedin; ?>" ><img src="<?php echo $logo;?>components/com_broadcast/images/conn.png" /></a>
		<?php 
				}     
		?>
		</td>
		<?
		}
		?>
	</tr></table>
	</div><!--End of Div Broadcast_Connect -->
	<?php
		}
	?>
</fieldset>

<?php
$user	=	JFactory::getUser();

 if($user->id)
 {
	$subsc_list=explode('|',$this->subscribedlists->broadcast_activity_config);
?>
<form action=""  method="POST" name="manualform" >
	<!-- **** Start Added & Modified By Deepak -->
	<fieldset class="fieldsetstyle">
		<legend class="legendstyle">Select activity to broadcast</legend>             
		<div id="broadcast_activity" >
			<table>
				<tr>
				    <td style="padding-left: 8px;">		
						<?php
							$brodfile 	= JPATH_SITE."/components/com_broadcast/broadcast.ini";
							$activities = parse_ini_file($brodfile);	
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
	<div class="content_cover" style="padding-left: 8px;">Group activities include joining, submitting articles, etc.</div>
	</fieldset>			
	<fieldset class="fieldsetstyle">
		<legend class="legendstyle">Automatic profile updates via RSS</legend>
		<script type="text/javascript">
			var latestId = 0;
			function addNewItem() {
				appendElement("container1", "element" + latestId, "<input type='text' class='inputbox' name='rss_link[]' value='' /><a href=\"javascript:removeItem(" + latestId + ")\"> [Remove Link]</a>");		
				latestId++;
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
		<div class="content_cover" id="check_rss_label" style="padding-left: 8px;">Update your equipalizer profile with feeds from a blog or website. Just enter the feed URL.</div>
<br />		
<div id="broadcast_rss">
			<div id="container1" style="padding-left: 20px;">
				<?php 
					if($this->subscribedlists->broadcast_rss_url)
					{
						$rsslists = explode('|', $this->subscribedlists->broadcast_rss_url);						
						$i=0;
						foreach($rsslists as $rss)
						{
							echo '<div id="element'.$i.'">';													
							echo '<input size="50" type="text" class="inputbox" name="rss_link['.$i.']" value="'.$rss.'" />';
							echo '<a href="javascript:removeItem('.$i.');" > [Remove RSS Feed]</a>'."<br />";
							echo '</div>';
							$i++;
						}
					}
				?>
			</div><br />
			<div style="text-align: left; padding-left:8px;"><a id="addButton" href="javascript:addNewItem();">[Add New RSS Feed]</a></div>			
		</div>	
	</fieldset>	
	<!-- **** End Added & Modified By Deepak -->
	
	<table cellspacing='5'  align='center' width="40%">	
		<tr>	
			<td align='left' colspan="2">
				<input type="hidden" name="option" value="com_broadcast">		
				<input type="hidden" id="task" name="task" value="save">
				<input type="button" value="Save" onclick="submit(this.form);">
			</td>
		</tr>
	</table>

 </form>
<?php
 }
?>
