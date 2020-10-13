<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminIntSlider extends F2csearchFieldAdminBase
{
	public function DisplayFormField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('fieldid'); ?></div>
			<div class="controls"><?php echo $form->getInput('fieldid'); ?></div>
		</div>		
		<h2><?php echo JText::_('COM_FORM2CONTENTSEARCH_ADDITIONAL_SETTINGS'); ?></h2>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('isl_min_value', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('isl_min_value', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('isl_max_value', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('isl_max_value', 'settings'); ?></div>
		</div>
		<!--
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('isl_num_steps', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('isl_num_steps', 'settings'); ?></div>
		</div>
		-->
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('isl_width', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('isl_width', 'settings'); ?></div>
		</div>
		<?php
	}
	
	public function getValidationScriptFormField()
	{
		?>
		var islMinValue = Number.from($('jform_settings_isl_min_value').value);
		var islMaxValue = Number.from($('jform_settings_isl_max_value').value);
	
		if(islMinValue == null)
		{
			alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENTSEARCH_ERROR_WHOLE_NUMBER', true), JText::_('COM_FORM2CONTENTSEARCH_SLIDER_MIN_VALUE', true)); ?>');
			return false;
		}
		if(islMaxValue == null)
		{
			alert('<?php echo JText::sprintf(JText::_('COM_FORM2CONTENTSEARCH_ERROR_WHOLE_NUMBER', true), JText::_('COM_FORM2CONTENTSEARCH_SLIDER_MAX_VALUE', true)); ?>');
			return false;
		}
		if(islMinValue >= islMaxValue)
		{
			alert('<?php echo JText::_('COM_FORM2CONTENTSEARCH_ERROR_MIN_LARGER_THAN_MAX', true); ?>');
			return false;
		}
		<?php
	}
	
	public function DisplayDataviewField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('sld_minval'); ?></div>
			<div class="controls"><?php echo $form->getInput('sld_minval'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('sld_maxval'); ?></div>
			<div class="controls"><?php echo $form->getInput('sld_maxval'); ?></div>
		</div>
		<?php 
	}
	
	public function prepareFormField($form, $item)
	{
		$form->setFieldAttribute('fieldid', 'query', $this->getContentTypeFieldListQuery($item->formid, array('Singlelinetext')));
		$form->setFieldAttribute('fieldid', 'class', 'inputbox required');
	}
	
	public function prepareFormDataView($form, $item)
	{
		$form->setFieldAttribute('sld_minval', 'class', 'inputbox validate-numeric required');
		$form->setFieldAttribute('sld_maxval', 'class', 'inputbox validate-numeric required');
	}
	
	public function validate($model, $form, $data, $group = null)
	{ 
		$settings = $data['settings'];
		
		if((int)$settings['isl_min_value'] != $settings['isl_min_value'])
		{
			$model->setError(JText::sprintf(JText::_('COM_FORM2CONTENTSEARCH_ERROR_WHOLE_NUMBER'), JText::_('COM_FORM2CONTENTSEARCH_SLIDER_MIN_VALUE')));
			return false;
		}
		
		if((int)$settings['isl_max_value'] != $settings['isl_max_value'])
		{
			$model->setError(JText::sprintf(JText::_('COM_FORM2CONTENTSEARCH_ERROR_WHOLE_NUMBER'), JText::_('COM_FORM2CONTENTSEARCH_SLIDER_MAX_VALUE')));
			return false;
		}
		
		if((int)$settings['isl_min_value'] >= (int)$settings['isl_max_value'])
		{
			$model->setError(JText::_('COM_FORM2CONTENTSEARCH_ERROR_MIN_LARGER_THAN_MAX'));
			return false;				
		}
	}
	
	public function saveDataView(&$data)
	{
		$registry = new JRegistry();
		$registry->set('minval', $data['sld_minval']);
		$registry->set('maxval', $data['sld_maxval']);
		$data['value'] = $registry->toString();
	}
	
	public function getItemDataViewField(&$item)
	{
		$regOptions = new JRegistry($item->value);	
		$item->sld_minval = $regOptions->get('minval');
		$item->sld_maxval = $regOptions->get('maxval');				
	}
}
?>