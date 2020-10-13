<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewDatavw extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	
	function display($tpl = null)
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JApplication::getInstance()->enqueueMessage(implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		
		parent::display($tpl);		
	}
	
	protected function addToolbar()
	{
		$isNew = ($this->item->id == 0);
	
		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_DATAVIEW_'.($isNew ? 'ADD' : 'EDIT')), 'article-add.png');
		
		// Built the actions for new and existing records.
		if ($isNew)  
		{
			JToolBarHelper::apply('datavw.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('datavw.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel('datavw.cancel', 'JTOOLBAR_CANCEL');
		}
		else 
		{
			JToolBarHelper::apply('datavw.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('datavw.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel('datavw.cancel', 'JTOOLBAR_CLOSE');
		}		
	}	
}
?>