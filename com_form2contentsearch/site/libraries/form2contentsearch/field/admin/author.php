<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminAuthor extends F2csearchFieldAdminBase
{
	public function DisplayFormField($form)
	{
		?>
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
		
		<h2><?php echo JText::_('COM_FORM2CONTENTSEARCH_ADDITIONAL_SETTINGS'); ?></h2>
		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('aut_display_name', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('aut_display_name', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('aut_filter', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('aut_filter', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('aut_ids', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('aut_ids', 'settings'); ?></div>
		</div>
		<?php echo $form->getInput('fieldid');	
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
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('id, username')->from('#__users')->order('username');

		$db->setQuery($query);
		
		$rowlist = $db->loadRowList(0);
		
		$options = array();
		
		foreach($rowlist as $row)
		{
			$options[$row[0]] = $row[1];
		}
		
		$regOptions = new JRegistry();				
		$regOptions->set('options', $options);
		$form->setFieldAttribute('aut_ids', 'options', $regOptions->toString(), 'settings');						
	}
	
	public function prepareFormDataView($form, $item)
	{
		$regOptions = new JRegistry();	
		$authorList = array();
		$db			= JFactory::getDbo();
		$field 		= $this->loadSearchFormField($item->search_form_fieldid);
		
		$query = $db->getQuery(true);
		
		if($field->settings->get('aut_display_name', 0))
		{
			$query->select('id as value, username as text');
			$query->order('username ASC');
		}
		else
		{
			$query->select('id as value, name as text');
			$query->order('name ASC');
		}
		
		$query->from('#__users');
		
		switch((int)$field->settings->get('aut_filter'))
		{
			case 0:
				// show all authors
				break;
			case 1:
				// exclude selected authors
				$query->where('id NOT IN ('. implode(',', $field->settings->get('aut_ids')) .')');
				break;
			case 2:
				// only show selected authors
				$query->where('id IN ('. implode(',', $field->settings->get('aut_ids')) .')');
				break;
		}

		$db->setQuery($query);
		
		$rowList = $db->loadRowList();
		
		if(count($rowList))
		{
			foreach($rowList as $row)
			{
				$authorList[$row[0]] = $row[1];
			}
		}
		
		$regOptions->set('options', $authorList);
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