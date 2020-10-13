<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewDatavws extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display($tpl = null)
	{
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			JFactory::getApplication()->enqueueMessage('JERROR_ALERTNOAUTHOR', 'error');
			return false;
		}
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors));
			return false;
		}
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') 
		{
			Form2ContentSearchHelperAdmin::addSubmenu('datavws');
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}
				
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_FORM2CONTENTSEARCH') . ': ' . JText::_('COM_FORM2CONTENTSEARCH_DATAVIEWSMANAGER'), 'article.png');

		JToolBarHelper::addNew('datavw.searchformselect','JTOOLBAR_NEW');
		JToolBarHelper::editList('datavw.edit','JTOOLBAR_EDIT');
		JToolBarHelper::divider();
		JToolBarHelper::trash('datavws.delete','JTOOLBAR_TRASH');
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_form2contentsearch', 550, 800);
	}	
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   6.3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.title' => JText::_('JGLOBAL_TITLE'),
			's.title' => JText::_('COM_FORM2CONTENTSEARCH_SEARCHFORM'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
?>