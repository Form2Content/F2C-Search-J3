<?php defined('JPATH_PLATFORM') or die('Restricted access'); ?>
<?php 
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'viewhelper.form2contentsearch.php');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<style>
.editor
{
	width: 700px;
}

#jform_metatags
{
	width: 700px;
}
</style>
<script type="text/javascript">
Joomla.submitbutton = function(task) 
{
	if (task == 'datavw.cancel') 
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
		return true;
	}
	
	if(!document.formvalidator.isValid(document.id('adminForm')))
	{
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		return false;
	}
	
	Joomla.submitform(task, document.getElementById('adminForm'));
	return true;
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_form2contentsearch&view=datavw&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
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
				<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
			</div>		
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('metatags'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('metatags'); ?></div>
			</div>		
		
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return');?>" />
			<?php echo $this->form->getInput('search_form_id'); ?>
			<?php echo JHtml::_('form.token'); ?>
			<?php echo DisplayCredits(); ?>	
		<!-- End Content -->
		</div>
	</div>
</form>