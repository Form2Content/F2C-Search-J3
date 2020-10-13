<?php defined('JPATH_PLATFORM') or die('Restricted access'); ?>
<?php
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'viewhelper.form2contentsearch.php');

JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
<!--
Joomla.submitbutton = function(task) 
{
	if (task != 'datavw.cancel' && document.adminForm.searchformid.value == -1) 
	{
		alert('<?php echo $this->escape(JText::_('COM_FORM2CONTENTSEARCH_SELECT_SEARCHFORM', true));?>');
		return false;
	}
	else 
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}
}
-->	
</script>
<form action="<?php echo JRoute::_('index.php?option=com_form2contentsearch'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span12 form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_FORM2CONTENTSEARCH_SEARCHFORM'); ?></div>
				<div class="controls">
					<select name="searchformid" id="searchformid" class="inputbox">
						<option value="-1">- <?php echo JText::_('COM_FORM2CONTENTSEARCH_SELECT_SEARCHFORM');?> -</option>
						<?php echo JHtml::_('select.options', $this->searchFormList, 'value', 'text', -1);?>
					</select>
				</div>
			</div>
		</div>
		<!-- End Content -->
		<?php echo DisplayCredits(); ?>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>