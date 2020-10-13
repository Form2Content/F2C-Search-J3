<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewProjectSelect extends JViewLegacy
{
	protected $fieldList;
	protected $searchFormId;

	function display($tpl = null)
	{
		$contentTypeId = 0;
		$this->addToolbar();

		$model = $this->getModel('form');
		$this->contentTypeList = $model->getContentTypeSelectList(false);		

		if(count($this->contentTypeList) == 1)
		{
			foreach($this->contentTypeList as $contentType)
			{
				$contentTypeId = $contentType->value;
			}
			
			return $contentTypeId;
		}
		
		parent::display($tpl);
		
		return $contentTypeId;
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_FORMSMANAGER').': '. JText::_('COM_FORM2CONTENTSEARCH_FORM_ADD'));
		JToolBarHelper::custom('form.add','forward','forward',JText::_('COM_FORM2CONTENTSEARCH_NEXT'), false);
		JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CANCEL');	
	}
}

?>