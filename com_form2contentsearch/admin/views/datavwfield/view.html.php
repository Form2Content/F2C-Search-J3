<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');
jimport('joomla.form.form');

JForm::addFieldPath(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'fields');

class Form2ContentSearchViewDatavwField extends JViewLegacy
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
		$db				= JFactory::getDbo();
		$model			= $this->getModel();
		$this->field	= $model->getField($this->item->fieldtypeid);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JApplication::getInstance()->enqueueMessage(implode("\n", $errors));
			return false;
		}
		
		$this->field->prepareFormDataView($this->form, $this->item);
		
		$this->addToolbar();
		
		parent::display($tpl);		
	}
	
	protected function addToolbar()
	{
		JLoader::register('Form2ContentSearchModelDatavw', JPATH_COMPONENT_ADMINISTRATOR . '/models/datavw.php');
		JLoader::register('Form2ContentSearchModelFormField', JPATH_COMPONENT_ADMINISTRATOR . '/models/formfield.php');
		
		$model = new Form2ContentSearchModelDatavw();
		$dataView = $model->getItem($this->item->dataview_id);
		$model = new Form2ContentSearchModelFormField();
		$formField = $model->getItem($this->item->search_form_fieldid);
		
		$isNew = ($this->item->id == 0);
		$formTitle = JText::_('COM_FORM2CONTENTSEARCH_FORM2CONTENTSEARCH') . ': ';
		$formTitle .= $isNew ? JText::_('COM_FORM2CONTENTSEARCH_NEW') : JText::_('COM_FORM2CONTENTSEARCH_EDIT') . ' ';
		$formTitle .= ' ' . JText::_('COM_FORM2CONTENTSEARCH_DATAVIEWFIELD');
		$formTitle .= ' ('.$dataView->title.' - '.$formField->title.')';
		
		JToolBarHelper::title($formTitle);
		JToolBarHelper::apply('datavwfield.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('datavwfield.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('datavwfield.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		
		if ($isNew)  
		{
			JToolBarHelper::cancel('datavwfield.cancel', 'JTOOLBAR_CANCEL');
		} 
		else 
		{
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel('datavwfield.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	
	private function getField($searchFormFieldId)
	{
		$db		= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('pf.*');
		$query->from('#__f2c_projectfields pf');
		$query->join('INNER', '#__f2c_search_formfield ff ON ff.fieldid = pf.id');
		$query->where('ff.id = ' . (int)$this->item->search_form_fieldid);
		$db->setQuery($query);
						
		$field = $db->loadObject();
		$field->settings = new JRegistry($field->settings);
		
		return $field;
	}
}
?>