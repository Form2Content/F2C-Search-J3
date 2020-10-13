<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');
jimport('joomla.form.form');

JForm::addFieldPath(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'fields');

class Form2ContentSearchViewFormField extends JViewLegacy
{
	protected $form;
	protected $item;	
	protected $state;
	protected $field;
	
	function display($tpl = null)
	{ 
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$model			= $this->getModel();		
		$this->field	= $model->getField($this->item->fieldtypeid);
			
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}
		
		$this->field->prepareFormField($this->form, $this->item);
		$this->addToolbar();
		
		parent::display($tpl);		
	}
	
	protected function addToolbar()
	{
		JLoader::register('Form2ContentSearchModelForm', JPATH_COMPONENT_ADMINISTRATOR . '/models/form.php');

		$model = new Form2ContentSearchModelForm();
		$searchForm = $model->getItem($this->item->formid);
		
		$isNew = ($this->item->id == 0);
		$formTitle = JText::_('COM_FORM2CONTENTSEARCH_FORMFIELDSMANAGER') . ' : ';
		$formTitle .= $isNew ? JText::_('COM_FORM2CONTENTSEARCH_NEW') : JText::_('COM_FORM2CONTENTSEARCH_EDIT') . ' ';
		$formTitle .= ' ' . JText::_('COM_FORM2CONTENTSEARCH_FORMFIELD');
		$formTitle .= ' ('.$searchForm->title.')';
		
		JToolBarHelper::title($formTitle);
		JToolBarHelper::apply('formfield.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('formfield.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('formfield.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);		
		
		if ($isNew)  
		{
			JToolBarHelper::cancel('formfield.cancel', 'JTOOLBAR_CANCEL');
		} 
		else 
		{
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel('formfield.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
?>