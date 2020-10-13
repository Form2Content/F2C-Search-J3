<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminDate extends F2csearchFieldAdminBase
{
	public function DisplayFormField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('operator1', 'settings'); ?></div>
			<div class="controls"><?php echo JText::_('COM_FORM2CONTENTSEARCH_DATE_SELECTED_BY_USER') . ' ' . $form->getInput('operator1', 'settings').$form->getInput('field1', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('include_null1', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('include_null1', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('operator2', 'settings'); ?></div>
			<div class="controls"><?php echo JText::_('COM_FORM2CONTENTSEARCH_DATE_SELECTED_BY_USER') . ' ' . $form->getInput('operator2', 'settings').$form->getInput('field2', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('include_null2', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('include_null2', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('helptext'); ?></div>
			<div class="controls"><?php echo $form->getInput('helptext'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('attribs'); ?></div>
			<div class="controls"><?php echo $form->getInput('attribs'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('date_format', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('date_format', 'settings'); ?>
			<a href="http://api.jqueryui.com/datepicker/#utility-formatDate" target="_blank"><?php echo JText::_('COM_FORM2CONTENTSEARCH_EXAMPLES'); ?></a>
			</div>
		</div>
		
		<script type="text/javascript">
		jQuery(document).ready()
		{
			jQuery("#jform_settings_operator1").width("40px");
			jQuery("#jform_settings_operator2").width("40px");
		}
		</script>
		<?php		
	}
	
	public function DisplayDataviewField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('date_field'); ?></div>
			<div class="controls"><?php echo $form->getInput('date_field'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('script'); ?></div>
			
			<div class="controls"><?php echo $form->getInput('script'); ?><?php echo JText::_('COM_FORM2CONTENTSEARCH_SINGLEDATE_SCRIPT_HELP'); ?></div>
		</div>
		<script type="text/javascript">
		jQuery(document).ready()
		{
			jQuery("#jform_script").width("400px");
			jQuery("#jform_script").css("float", "left");
			jQuery("#jform_script").css("margin-right", "10px");
		}
		</script>
		<?php 
	}
	
	public function prepareFormField($form, $item)
	{
		$form->setFieldAttribute('field1', 'query', $this->getContentTypeFieldListQuery($item->formid, array('Datepicker')), 'settings');
		$form->setFieldAttribute('field2', 'query', $this->getContentTypeFieldListQuery($item->formid, array('Datepicker')), 'settings');
	}
	
	public function prepareFormDataView($form, $item)
	{
	}
	
	public function saveDataView(&$data)
	{
		$registry = new JRegistry();
		$registry->set('date_field', $data['date_field']);
		$registry->set('script', $data['script']);
		$data['value'] = $registry->toString();
	}
	
	public function getItemDataViewField(&$item)
	{
		$regOptions = new JRegistry($item->value);	
		$item->date_field = $regOptions->get('date_field');
		$item->script = $regOptions->get('script');
	}
	
	public function getValidationScriptDataViewField()
	{
		?>
		var date = jQuery("#jform_date_field").val().trim();
		var script = jQuery("#jform_script").val().trim();
		
		if(date && script)
		{
			alert('<?php echo JText::_('COM_FORM2CONTENTSEARCH_DATAVIEWFIELD_DATE_FIELDS_ERROR_ALL_FILLED', true);?>');
			return false;
		}
		
		if(!date && !script)
		{
			alert('<?php echo JText::_('COM_FORM2CONTENTSEARCH_DATAVIEWFIELD_DATE_FIELDS_ERROR_EMPTY', true);?>');
			return false;
		}
		<?php
	}
}
?>