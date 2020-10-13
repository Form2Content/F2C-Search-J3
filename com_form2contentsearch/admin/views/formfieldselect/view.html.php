<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewFormFieldSelect extends JViewLegacy
{
	protected $fieldList;
	protected $searchFormId;

	function display($tpl = null)
	{
		$contentTypeId = 0;
		$this->addToolbar();

		$model = $this->getModel('formfield');
		
		$this->searchFormId = JFactory::getApplication()->input->getInt('formid');
		$this->fieldList = $model->getSearchFormFieldList($this->searchFormId);		

		parent::display($tpl);		
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_FORMSMANAGER').': '. JText::_('COM_FORM2CONTENTSEARCH_SEARCHFIELD_SELECT'));
		JToolBarHelper::custom('formfield.add','forward','forward',JText::_('COM_FORM2CONTENTSEARCH_NEXT'), false);
		JToolBarHelper::cancel('formfield.cancel', 'JTOOLBAR_CANCEL');	
	}
}

?>