<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');

//require(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_broadcast'.DS.'config'.DS.'config.php');
$params=JComponentHelper::getParams('com_broadcast');
//print_r($params);
$document =JFactory::getDocument();
if(JVERSION >= '1.6.0')
	$js_key="
	Joomla.submitbutton = function(task){ ";
else
	$js_key="
	function submitbutton( task ){";

	$js_key.="
		if (task == 'cancel')
		{";
	        if(JVERSION >= '1.6.0')
				$js_key.="Joomla.submitform(task);";
			else		
				$js_key.="document.adminForm.submit();";
	    $js_key.="
	    }else{	
			var validateflag = document.formvalidator.isValid(document.adminForm);
			if(validateflag){";
				if(JVERSION >= '1.6.0'){
					$js_key.="
				Joomla.submitform(task);";
				}else{		
					$js_key.="
				document.adminForm.submit();";
				}
			$js_key.="
			}else{
				return false;
			}
		}
	}
	function checkforinverval(el)
	{
		if(el.value<3600){
			alert('".JText::_('COM_BROADCAST_INTERVAL_INV')." 3600 ".JText::_('COM_BROADCAST_BC_SECS')."'); 
			el.value = '';
			
		}
	}
";

	$document->addScriptDeclaration($js_key);
?>
<form name="adminForm" method="post" id="adminForm" class="form-validate" action="">
	<div  class="leftdiv"	>
		<fieldset class="queue">
			<legend><?php echo JText::_('COM_BROADCAST_PUSH_TO_Q') ?></legend>
			<table width="100%">
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_TOOLTIPUSER'), JText::_('COM_BROADCAST_BC_USER'), '', JText::_('COM_BROADCAST_BC_USER'));?></td>
					<td><input type="text" class="inputbox required validate-numeric" name="userid" value="" id="userid" size="30" /></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_TOOLTIPSTATUS'), JText::_('COM_BROADCAST_BC_MSG'), '', JText::_('COM_BROADCAST_BC_MSG'));?></td>
					<td><textarea class="inputbox required" name="status" id="status" cols="25"></textarea></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_TOOLTIPSELAPI'), JText::_('COM_BROADCAST_BC_SEL_API'), '', JText::_('COM_BROADCAST_BC_SEL_API'));?></td>
					<td><?php
					$api_plgs='';
					$api_plgs=$params->get('api');
					if(isset($api_plgs))
					{
						foreach($api_plgs as $api){
						?>
						<span syle="vertical-align:text-top;"> 
							<input style="float:none;" type="checkbox" name="api_status[]" value="<?php echo $api; ?>" /><span><?php echo ucfirst(str_replace('plug_techjoomlaAPI_','', $api)); ?></span>
						</span>
						<?php 
						}
					}
					else
						echo JText::_('COM_BROADCAST_NO_API_PLUG');
					?>
					</td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_TOOLTIPCOUNT'), JText::_('COM_BROADCAST_BC_COUNT'), '', JText::_('COM_BROADCAST_BC_COUNT'));?></td>
					<td><input type="text" class="inputbox required validate-numeric"  name="count" value="" id="" size="30" /></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_TOOLTIPINTERVAL')." 3600 ".JText::_('COM_BROADCAST_BC_SECS'), JText::_('COM_BROADCAST_INTERVALS'), '', JText::_('COM_BROADCAST_INTERVALS'));?></td>
					<td><input type="text" class="inputbox required validate-numeric" name="interval" value="" id="" size="30" OnChange= checkforinverval(this); /></td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div>
		<fieldset class="queue">
			<legend><?php echo JText::_('COM_BROADCAST_QUEUE_FORM_MESSAGE') ?></legend>
			<table class="adminlist" width="100%">
			<thead>
				<tr>
				<th><?php echo JText::_('COM_BROADCAST_BC_ID');?></th>
					<th><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_DESC_BC__MSG'), JText::_('COM_BROADCAST_BC_MSG'), '', JText::_('COM_BROADCAST_BC_MSG'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_DESC_BC_USER'), JText::_('COM_BROADCAST_BC_USER'), '', JText::_('COM_BROADCAST_BC_USER'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_DESC_BC_LAS_DATE'), JText::_('COM_BROADCAST_BC_LAS_DATE'), '', JText::_('COM_BROADCAST_BC_LAS_DATE'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_DESC_BC_PEN_CNT'), JText::_('COM_BROADCAST_BC_PEN_CNT'), '', JText::_('COM_BROADCAST_BC_PEN_CNT'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_DESC_BC_INT_TIM'), JText::_('COM_BROADCAST_BC_INT_TIM'), '', JText::_('COM_BROADCAST_BC_INT_TIM'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_DESC_BC_PEN_API'), JText::_('COM_BROADCAST_BC_PEN_API'), '', JText::_('COM_BROADCAST_BC_PEN_API'));?></th>
					<th><?php echo JHTML::tooltip(JText::_('COM_BROADCAST_DESC_BC_SUPPLIER'), JText::_('COM_BROADCAST_BC_SUPPLIER'), '', JText::_('COM_BROADCAST_BC_SUPPLIER'));?></th>
				</tr>
			</thead>
		<?php
			foreach($this->queues as $queue){
		?>
			<tr>
				<td align="center"><?php echo $queue->id;?></td>
				<td align="center"><?php echo $queue->status;?></td>
				<td align="center"><?php echo JFactory::getUser($queue->userid)->name;?></td>
				<td align="center"><?php echo $queue->date;?></td>
				<td align="center"><?php echo $queue->count;?></td>
				<td align="center"><?php echo $queue->interval;?></td>
				<td ><?php echo $queue->api;?></td>
				<td align="center"><?php echo $queue->supplier;?></td>
			</tr>
<?php } ?>
			</table>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="com_broadcast" />		
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="cp" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
