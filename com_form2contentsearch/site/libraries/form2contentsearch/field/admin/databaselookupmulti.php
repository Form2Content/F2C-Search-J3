<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminDatabaseLookupMulti extends F2csearchFieldAdminBase
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
			<div class="control-label"><?php echo $form->getLabel('fld_multiselect'); ?></div>
			<div class="controls"><?php echo $form->getInput('fld_multiselect'); ?></div>
		</div>
		<?php 
	}
	
	public function prepareFormField($form, $item)
	{
		$form->setFieldAttribute('fieldid', 'query', $this->getContentTypeFieldListQuery($item->formid, array('Databaselookupmulti')));
		$form->setFieldAttribute('fieldid', 'class', 'inputbox required');
	}
	
	public function prepareFormDataView($form, $item)
	{
		$regOptions = new JRegistry();	
		$optionList = array();
		$db			= JFactory::getDbo();
		$field 		= $this->loadContentTypeField($item->search_form_fieldid);
		
		$db->setQuery($field->settings->get('dlm_query'));
		$list = $db->loadRowList();
		
		if(count($list))
		{
			foreach($list as $listItem)
			{
				$optionList[$listItem[0]] = $listItem[1]; 
			}
		}
		
		$regOptions->set('options', $optionList);
		$form->setFieldAttribute('fld_multiselect', 'options', $regOptions->toString());
	}
	
	public function saveDataView(&$data)
	{
		$registry = new JRegistry();
		$registry->loadArray($data['fld_multiselect']);
		$data['value'] = $registry->toString();
	}
	
	public function getItemDataViewField(&$item)
	{
			$regValues = new JRegistry($item->value);
			$item->fld_multiselect = $regValues->toArray();
	}
}
?>
