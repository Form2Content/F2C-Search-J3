<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewForm extends JViewLegacy
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
			JApplication::getInstance()->enqueueMessage(implode("\n", $errors), 'error');
			return false;
		}

		$this->addToolbar();
		
		// Load the com_content language file
		JFactory::getLanguage()->load('com_content', JPATH_ADMINISTRATOR);
		
		parent::display($tpl);		
	}
	
	protected function addToolbar()
	{
		$isNew = ($this->item->id == 0);
	
		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_FORM_'.($isNew ? 'ADD' : 'EDIT')), 'article-add.png');
		
		// Built the actions for new and existing records.
		if ($isNew)  
		{
			JToolBarHelper::apply('form.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('form.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CANCEL');
		}
		else 
		{
			JToolBarHelper::apply('form.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('form.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::cancel('form.cancel', 'JTOOLBAR_CLOSE');
		}		
	}	
}
?>