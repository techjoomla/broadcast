<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.formvalidation');


?>
<?php
$document =& JFactory::getDocument();
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
			alert('".JText::_('INTERVAL_INV')." 3600 ".JText::_('BC_SECS')."'); 
			el.value = '';
			
		}
	}
";

	$document->addScriptDeclaration($js_key);	
?>
<form name="adminForm" method="post" id="queue" class="form-validate" action="">
	<div class="width-50 fltlft">
		<fieldset class="queue">
			<legend><?php echo JText::_('QUEUE_FORM_MESSAGE') ?></legend>
			<table width="100%">
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPUSER'), JText::_('USER'), '', JText::_('USER'));?></td>
					<td><input type="text" class="inputbox required validate-numeric" name="userid" value="" id="userid" size="30" /></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPSTATUS'), JText::_('STATUS'), '', JText::_('STATUS'));?></td>
					<td><textarea class="inputbox required" name="status" id="status" cols="25"></textarea></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPCOUNT'), JText::_('COUNT'), '', JText::_('COUNT'));?></td>
					<td><input type="text" class="inputbox required validate-numeric"  name="count" value="" id="" size="30" /></td>
				</tr>
				<tr>
					<td><?php echo JHTML::tooltip(JText::_('TOOLTIPINTERVAL')." 3600 ".JText::_('BC_SECS'), JText::_('INTERVALS'), '', JText::_('INTERVALS'));?></td>
					<td><input type="text" class="inputbox required validate-numeric" name="interval" value="" id="" size="30" OnChange= checkforinverval(this); /></td>
				</tr>
			</table>
		</fieldset>
	</div>
	<input type="hidden" name="option" value="com_broadcast" />		
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="cp" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>


<script>
/*
var a = new Ajax( {$url}, {
	method: 'get',
	update: $('ajax-container')
}).request();


window.addEvent( 'domready', function() {
 
	$('userid').addEvent( 'click', function() {
 
		$('ajax-container').empty().addClass('ajax-loading');
 
		var a = new Ajax( {url: 'index.php'}, {
			method: 'get',
			onComplete: function( response ) {
				var resp = Json.evaluate( response );
 
				// Other code to execute when the request completes.
 
				$('ajax-container').removeClass('ajax-loading').setHTML( output );
 
			}
		}).request();
	});
});
*/
</script>
