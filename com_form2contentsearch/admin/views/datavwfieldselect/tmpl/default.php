<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'viewhelper.form2contentsearch.php');

JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
<!--
Joomla.submitbutton = function(task) 
{
	if (task == 'datavwfield.cancel') 
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}

	if(document.adminForm.search_form_fieldid.value == 0) 
	{
		alert('<?php echo $this->escape(JText::_('COM_FORM2CONTENTSEARCH_SELECT_SEARCHFORMFIELD', true));?>');
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
				<div class="control-label"><?php echo JText::_('COM_FORM2CONTENTSEARCH_SEARCHFORMFIELD'); ?></div>
				<div class="controls">
					<select name="search_form_fieldid" id="search_form_fieldid" class="inputbox">
						<option value="0">- <?php echo JText::_('COM_FORM2CONTENTSEARCH_SELECT_SEARCHFORMFIELD');?> -</option>
						<?php echo JHtml::_('select.options', $this->fieldList, 'id', 'title', 0);?>
					</select>
				</div>
			</div>
		</div>
		<!-- End Content -->
		<?php echo DisplayCredits(); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="datavwid" value="<?php echo $this->dataviewId; ?>" />		
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>