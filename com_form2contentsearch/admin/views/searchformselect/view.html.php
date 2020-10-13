<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewSearchformSelect extends JViewLegacy
{
	protected $searchFormList;

	function display($tpl = null)
	{
		$this->addToolbar();

		$model = $this->getModel('form');
		$this->searchFormList = $model->getSearchformList();	

		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_FORMSMANAGER').': '. JText::_('COM_FORM2CONTENTSEARCH_DATAVIEW_ADD'));
		JToolBarHelper::custom('datavw.add','forward','forward',JText::_('COM_FORM2CONTENTSEARCH_NEXT'), false);
		JToolBarHelper::cancel('datavw.cancel', 'JTOOLBAR_CANCEL');	
	}
}

?>