<?php defined('JPATH_PLATFORM') or die('Restricted access'); ?>
<?php 
require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'viewhelper.form2contentsearch.php');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$fieldSets = $this->form->getFieldsets('attribs');
?>
<script type="text/javascript">
Joomla.submitbutton = function(task) 
{
	if (task == 'form.cancel') 
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

<form action="<?php echo JRoute::_('index.php?option=com_form2contentsearch&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Content -->
		<div class="span10 form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('COM_FORM2CONTENTSEARCH_DETAILS');?></a></li>
				<?php 
				foreach ($fieldSets as $name => $fieldSet)
				{
				?>
					<li><a href="#<?php echo $name; ?>" data-toggle="tab"><?php echo $this->escape(JText::_($fieldSet->description)); ?></a></li>
				<?php
				}
				?>
			</ul>
				
			<div class="tab-content">
				<!-- Begin Tabs -->
				<div class="tab-pane active" id="general">
					<div class="row-fluid">
						<div class="span12">
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('num_cols'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('num_cols'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('mod_pre_text'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('mod_pre_text'); ?></div>
							</div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('totals_pre_text'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('totals_pre_text'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('submit_pre_text'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('submit_pre_text'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('submit_caption'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('submit_caption'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('reset_caption', 'attribs'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('reset_caption', 'attribs'); ?></div>
							</div>
						</div>
						<div class="span6">
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('totals_post_text'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('totals_post_text'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('submit_post_text'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('submit_post_text'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('submit_class'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('submit_class'); ?></div>
							</div>
							<div class="control-group">
								<div class="control-label"><?php echo $this->form->getLabel('reset_class', 'attribs'); ?></div>
								<div class="controls"><?php echo $this->form->getInput('reset_class', 'attribs'); ?></div>
							</div>
						</div>
					</div>
				</div>
				<?php 
				foreach ($fieldSets as $name => $fieldSet)
				{
				?>
					<div class="tab-pane" id="<?php echo $name; ?>">
						<div class="row-fluid">
							<div class="span12">
								<?php foreach ($this->form->getFieldset($name) as $field) : ?>
								<div class="control-group">
									<div class="control-label"><?php echo $field->label; ?></div>
									<div class="controls"><?php echo $field->input; ?></div>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					
				<?php
				}
				?>
				<!--  end tabs -->
			</div>
		
		</div>
		<!-- End Sidebar -->
		<?php
		echo $this->form->getInput('projectid'); 
		echo DisplayCredits(); 
		?>	
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getCmd('return');?>" />
		<?php echo JHtml::_('form.token'); ?>		
	</div>
</form>