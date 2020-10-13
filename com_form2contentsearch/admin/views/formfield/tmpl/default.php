<?php defined('JPATH_PLATFORM') or die('Restricted access'); ?>
<?php
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'viewhelper.form2contentsearch.php');

JHtml::_('behavior.framework');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

JForm::addFieldPath(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'fields');

$editor 		= JFactory::getEditor();
?>
<script type="text/javascript">
//<!--
Joomla.submitbutton = function(task) 
{
	if (task == 'formfield.cancel')
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}
	
	if(!document.formvalidator.isValid(document.id('adminForm')))
	{
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		return false;
	}

	<?php echo $this->field->getValidationScriptFormField(); ?>
	
	Joomla.submitform(task, document.getElementById('adminForm'));
	return true;		
}
//-->	
</script>
<form action="<?php echo JRoute::_('index.php?option=com_form2contentsearch&view=formfields&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('caption'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('caption'); ?></div>
			</div>
			<?php echo $this->field->DisplayFormField($this->form); ?>
		</div>
		<?php echo DisplayCredits(); ?>
	</div>
	<?php echo $this->form->getInput('formid'); ?>
	<?php echo $this->form->getInput('fieldtypeid'); ?>
	<?php echo $this->form->getInput('ordering'); ?>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return');?>" />
	<input type="hidden" name="task" value="" />	
</form>