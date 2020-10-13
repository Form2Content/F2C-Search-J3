<?php defined('JPATH_PLATFORM') or die('Restricted access'); ?>
<?php
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'viewhelper.form2contentsearch.php');

JHtml::_('behavior.framework');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('jquery.framework');

JForm::addFieldPath(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'fields');
?>
<script type="text/javascript">
//<!--
Joomla.submitbutton = function(task) 
{
	if (task == 'datavwfield.cancel')
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}
	
	if(!document.formvalidator.isValid(document.id('adminForm')))
	{
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		return false;
	}

	<?php echo $this->field->getValidationScriptDataViewField(); ?>
	
	Joomla.submitform(task, document.getElementById('adminForm'));
	return true;		
}
//-->	
</script>
<form action="<?php echo JRoute::_('index.php?option=com_form2contentsearch&view=datavwfields&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<?php echo $this->field->DisplayDataviewField($this->form); ?>
			<!-- End Content -->
			<?php echo DisplayCredits(); ?>
			<input type="hidden" name="task" value="" />
			<?php echo $this->form->getInput('search_form_fieldid'); ?>
			<?php echo $this->form->getInput('datavwid'); ?>
			<?php echo $this->form->getInput('fieldtypeid'); ?>
			<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return');?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>