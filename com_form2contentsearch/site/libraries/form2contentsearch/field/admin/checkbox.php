<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminCheckbox extends F2csearchFieldAdminBase
{
	public function DisplayFormField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('fieldid'); ?></div>
			<div class="controls"><?php echo $form->getInput('fieldid'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('helptext'); ?></div>
			<div class="controls"><?php echo $form->getInput('helptext'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('emptytext'); ?></div>
			<div class="controls"><?php echo $form->getInput('emptytext'); ?></div>
		</div>		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('attribs'); ?></div>
			<div class="controls"><?php echo $form->getInput('attribs'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('displaymode'); ?></div>
			<div class="controls"><?php echo $form->getInput('displaymode'); ?></div>
		</div>
		<?php		
	}
	
	public function DisplayDataviewField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('fld_boolean'); ?></div>
			<div class="controls"><?php echo $form->getInput('fld_boolean'); ?></div>
		</div>
		<?php 
	}
	
	public function prepareFormField($form, $item)
	{
		$form->setFieldAttribute('fieldid', 'query', $this->getContentTypeFieldListQuery($item->formid, array('Checkbox')));
	}
	
	public function prepareFormDataView($form, $item)
	{
	}
	
	public function saveDataView(&$data)
	{
		$data['value'] = $data['fld_boolean'];
	}
	
	public function getItemDataViewField(&$item)
	{
		$item->fld_boolean = $item->value;
	}
}
?>