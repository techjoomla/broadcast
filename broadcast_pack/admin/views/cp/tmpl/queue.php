<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
//JHTML::_( 'behavior.mootools' );
?>

<form name="adminForm" method="post" id="queue" action="">
	<div class="width-50 fltlft">
		<fieldset class="queue">
			<legend><?php echo JText::_('QUEUE_FORM_MESSAGE') ?></legend>
			<table width="100%">
				<tr>
					<td><?php echo JText::_('USER') ?></td>
					<td><input type="text" name="userid" value="" id="userid" size="30" />
					<?php echo JHTML::tooltip(JText::_('TOOLTIPUSER'), 'User Id', 'tooltip.png', '',  'http://techjoomla.com'); ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('STATUS') ?></td>
					<td><textarea name="status" id="status" cols="25"></textarea>
					<?php echo JHTML::tooltip(JText::_('TOOLTIPSTATUS'), 'Status', 'tooltip.png', '',  'http://techjoomla.com'); ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COUNT') ?></td>
					<td><input type="text" name="count" value="" id="" size="30" />
					<?php echo JHTML::tooltip(JText::_('TOOLTIPCOUNT'), 'Count', 'tooltip.png', '',  'http://techjoomla.com'); ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('INTERVALS') ?></td>
					<td><input type="text" name="interval" value="" id="" size="30" />
					<?php echo JHTML::tooltip(JText::_('TOOLTIPINTERVAL'), 'Interval', 'tooltip.png', '',  'http://techjoomla.com'); ?></td>
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
