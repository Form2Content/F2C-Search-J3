<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminTextCombo extends F2csearchFieldAdminBase
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
			<div class="control-label"><?php echo $form->getLabel('fld_textcombo'); ?></div>
			<div class="controls"><?php echo $form->getInput('fld_textcombo'); ?></div>
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
		$field 	= $this->loadContentTypeField($item->search_form_fieldid);
		$db		= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('DISTINCT content');
		$query->from('#__f2c_fieldcontent fc');
		$query->join('INNER', '#__f2c_form frm ON fc.formid = frm.id AND frm.state = 1');
		$query->where('fieldid = ' . (int)$field->id);
		$query->order('content ASC');

		$form->setFieldAttribute('fld_textcombo', 'query', $query->__toString());
	}

	public function saveDataView(&$data)
	{
		$registry = new JRegistry();
		$registry->loadArray($data['fld_textcombo']);
		$data['value'] = $registry->toString();
	}
	
	public function getItemDataViewField(&$item)
	{
		$regOptions = new JRegistry($item->value);
		$regOptions = $regOptions->toArray();
		
		if(count($regOptions))
		{
			$item->fld_textcombo = $regOptions[0];
		}	
	}
}
?>