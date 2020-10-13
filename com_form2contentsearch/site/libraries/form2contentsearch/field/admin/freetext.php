<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminFreeText extends F2csearchFieldAdminBase
{
	public function DisplayFormField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('helptext'); ?></div>
			<div class="controls"><?php echo $form->getInput('helptext'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('attribs'); ?></div>
			<div class="controls"><?php echo $form->getInput('attribs'); ?></div>
		</div>

		<h2><?php echo JText::_('COM_FORM2CONTENTSEARCH_ADDITIONAL_SETTINGS'); ?></h2>
		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('txt_size', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('txt_size', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('txt_max_length', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('txt_max_length', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('txt_fieldids', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('txt_fieldids', 'settings'); ?></div>
		</div>
		<?php echo $form->getInput('fieldid');
	}
	
	public function DisplayDataviewField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('fld_text'); ?></div>
			<div class="controls"><?php echo $form->getInput('fld_text'); ?></div>
		</div>
		<?php 
	}
	
	public function prepareFormField($form, $item)
	{
		$db = JFactory::getDbo();
		
		$db->setQuery($this->getContentTypeFieldListQuery($item->formid, array('Singlelinetext', 'Multilinetext', 'Editor')));
		
		$rowlist = $db->loadRowList(0);
		
		$options = array();
		$options[F2CSEARCH_DATAFIELD_JTITLE] 	= JText::_('COM_FORM2CONTENTSEARCH_JOOMLA_TITLE');
		$options[F2CSEARCH_DATAFIELD_JMETAKEYS] = JText::_('COM_FORM2CONTENTSEARCH_JOOMLA_META_KEYWORDS');
		$options[F2CSEARCH_DATAFIELD_JMETADESC] = JText::_('COM_FORM2CONTENTSEARCH_JOOMLA_META_DESC');
		
		foreach($rowlist as $row)
		{
			$options[$row[0]] = $row[1];
		}
		
		$regOptions = new JRegistry();				
		$regOptions->set('options', $options);
		$form->setFieldAttribute('txt_fieldids', 'options', $regOptions->toString(), 'settings');						
	}
	
	public function prepareFormDataView($form, $item)
	{
	}
	
	public function saveDataView(&$data)
	{
		$data['value'] = $data['fld_text'];
	}

	public function getItemDataViewField(&$item)
	{
		$item->fld_text = $item->value;
	}
}
?>