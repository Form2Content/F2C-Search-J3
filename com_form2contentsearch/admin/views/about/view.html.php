<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewAbout extends JViewLegacy
{
	function display($tpl = null)
	{
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') 
		{
			Form2ContentSearchHelperAdmin::addSubmenu('about');
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JHtmlSidebar::setAction('index.php?option=com_form2contentsearch&view=about');
		
		$title = JText::_('COM_FORM2CONTENTSEARCH_FORM2CONTENTSEARCH') . ': ' . JText::_('COM_FORM2CONTENTSEARCH_ABOUT');			
		JToolBarHelper::title($title, 'generic.png');		
		
	}
}

?>