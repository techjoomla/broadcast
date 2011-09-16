<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.pane');
$document =& JFactory::getDocument();	
$document->addStyleSheet(JURI::base().'components/com_broadcast/css/broadcast.css'); 
$pane =& JPane::getInstance('tabs', array('startOffset'=>0)); 
#$xml = new JSimpleXML(); 
$xml = JFactory::getXMLParser('Simple');

$currentversion = '';
$xml->loadFile(JPATH_SITE.'/administrator/components/com_broadcast/broadcast.xml');
if($xml->document)
foreach($xml->document->_children as $var)
	{
		if($var->_name=='version')
			$currentversion = $var->_data;
	}
?>
<script type="text/javascript">

	function vercheck()
	{
		callXML('<?php echo $currentversion; ?>');
		if(document.getElementById('NewVersion').innerHTML.length<220)
		{
			document.getElementById('NewVersion').style.display='block';
		}
	}

	function callXML(currversion)
	{
		if (window.XMLHttpRequest)
			{
		 	 xhttp=new XMLHttpRequest();
			}
		else // Internet Explorer 5/6
			{
		 	xhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}

	xhttp.open("GET","<?php echo JURI::base(); ?>index.php?option=com_broadcast&task=getVersion",false);
	xhttp.send("");
	latestver=xhttp.responseText;

	if(latestver!=null)
	  {
		if(currversion == latestver)
		{
			document.getElementById('NewVersion').innerHTML='<span style="display:inline; color:#339F1D;float:right;">:'+latestver+' &nbsp;<?php echo JText::_("VERSION");?> <b>:'+currversion+"</b></span>";
		}
		else
		{
			document.getElementById('NewVersion').innerHTML='<span style="display:inline; color:#FF0000;float:right;">:'+latestver+' &nbsp;<?php echo JText::_("VERSION");?> <b>:'+currversion+"</b></span>";
		}
	  }
     }
</script>

	<div id="cpanel" style="float: left; width: 100%;">
		<div id= "cp1" style="float: left; width: 50%;">
			<div style="float: left;">
				<div class="icon">
				<a href="index.php?option=com_broadcast&view=settings">
				<img src="<?php echo JURI::base()?>components/com_broadcast/images/process.png" alt="Settings"/>
				<span><?php echo JText::_("BC_SETTINGS");?></span>
				</a>
				</div>	
			</div>
			<div style="float: left;">
				<div class="icon">
				<a href="index.php?option=com_broadcast&view=cp&layout=queue">
				<img src="<?php echo JURI::base()?>components/com_broadcast/images/queue.png" alt="Queue"/>
				<span><?php echo JText::_("BC_QUEUE");?></span>
				</a>
				</div>	
			</div>					
		</div>	
		<div id="cp2" class="cp2" style="float: left; width: 50%;">
		<?php
		echo $pane->startPane( 'pane' );
		echo $pane->startPanel( JText::_('ABOUT'), 'panel1' );?>
		<h1 style="color:#0B55C4;"><?php echo JText::_('ABOUT1');?></h1>
		<h3><b><?php echo JText::_('ABOUT2');?></b></h3>
		<b><?php echo JText::_('ABOUT3');?></b>
		<p><?php echo JText::_('ABOUT4');?></p> 
		<fieldset>
		<div onclick="vercheck();"><span style = "border:1px solid;float:left;padding: 5px;cursor: pointer;" class="latestbutton" ><?php echo JText::_('CHECK');?></span></div>
<div id="NewVersion" style="display:none;padding-top:5px;color:#000000;font-weight:bold;padding-left:5px;float:left!important;">
</div>
		</fieldset>
		<?php
		echo $pane->endPanel();		
		?>
		</div>
	</div>


