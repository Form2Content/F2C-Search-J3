<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewDatavwFields extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $dataviewId;
	protected $pageTitle;

	function display($tpl = null)
	{
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->dataviewId	= JFactory::getApplication()->input->getInt('datavwid');

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
		JLoader::register('Form2ContentSearchModelDatavw', JPATH_COMPONENT_ADMINISTRATOR . '/models/datavw.php');

		$model = new Form2ContentSearchModelDatavw();
		$dataView = $model->getItem($this->dataviewId);
		
		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_FORM2CONTENTSEARCH') . ': ' . JText::_('COM_FORM2CONTENTSEARCH_DATAVIEWFIELDS'), 'generic.png');
		JToolBarHelper::addNew('datavwfield.fieldselect','JTOOLBAR_NEW');
		JToolBarHelper::editList('datavwfield.edit','JTOOLBAR_EDIT');
		JToolBarHelper::trash('datavwfields.delete','JTOOLBAR_TRASH');
		
		$this->pageTitle = JText::_('COM_FORM2CONTENTSEARCH_DATAVIEWFIELDS') . ' - '. $dataView->title;
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
			's.title' => JText::_('JGLOBAL_TITLE'),
			'ft.description' => JText::_('COM_FORM2CONTENTSEARCH_FIELDTYPE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
?>