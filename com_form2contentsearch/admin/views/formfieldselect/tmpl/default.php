<?php defined('JPATH_PLATFORM') or die('Restricted access'); ?>
<?php
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'viewhelper.form2contentsearch.php');

JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
<!--
Joomla.submitbutton = function(task) 
{
	if (task == 'formfield.cancel') 
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}

	if(document.adminForm.fieldtypeid.value == '') 
	{
		alert('<?php echo $this->escape(JText::_('COM_FORM2CONTENTSEARCH_SELECT_SEARCHFIELD', true));?>');
		return false;
	}

	Joomla.submitform(task, document.getElementById('adminForm'));
	return true;		
}
-->	
</script>
<form action="<?php echo JRoute::_('index.php?option=com_form2contentsearch'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span12 form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_FORM2CONTENTSEARCH_SEARCHFIELD'); ?></div>
				<div class="controls">
					<select name="fieldtypeid" id="fieldtypeid" class="inputbox">
						<option value="">- <?php echo JText::_('COM_FORM2CONTENTSEARCH_SELECT_SEARCHFIELD');?> -</option>
						<?php echo JHtml::_('select.options', $this->fieldList, 'id', 'fieldname', 0);?>
					</select>
				</div>
			</div>
		</div>
		<!-- End Content -->
		<?php echo DisplayCredits(); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="formid" value="<?php echo $this->searchFormId; ?>" />		
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>