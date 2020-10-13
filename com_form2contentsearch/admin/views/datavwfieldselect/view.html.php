<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewDatavwFieldSelect extends JViewLegacy
{
	protected $fieldList;
	protected $dataviewId;

	function display($tpl = null)
	{
		$contentTypeId = 0;
		$this->addToolbar();

		$model = $this->getModel('datavwfield');
		
		$this->dataviewId = JFactory::getApplication()->input->getInt('datavwid');
		$this->fieldList = $model->getSearchFormFieldList($this->dataviewId);		

		parent::display($tpl);		
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_DATAVIEWSMANAGER').': '. JText::_('COM_FORM2CONTENTSEARCH_SEARCHFORMFIELD_SELECT'));
		JToolBarHelper::custom('datavwfield.add','forward','forward',JText::_('COM_FORM2CONTENTSEARCH_NEXT'), false);
		JToolBarHelper::cancel('datavwfield.cancel', 'JTOOLBAR_CANCEL');	
	}
}

?>