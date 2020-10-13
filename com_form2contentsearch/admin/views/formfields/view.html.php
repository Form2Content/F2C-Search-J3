<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.view');

class Form2ContentSearchViewFormFields extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $formId;
	protected $pageTitle;

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
		$this->formId		= JFactory::getApplication()->input->getInt('formid');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JFactory::getApplication()->enqueueMessage(implode("\n", $errors));
			return false;
		}
		
		if(count($this->items))
		{
			foreach($this->items as $item)
			{
				if(!$item->fieldname)
				{
					// Use the description when no F2C field is directly connected to the search field
					$item->fieldname = $item->description;
				}
			}
		}
		
		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') 
		{
			Form2ContentSearchHelperAdmin::addSubmenu('forms');
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}
				
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JLoader::register('Form2ContentSearchModelForm', JPATH_COMPONENT_ADMINISTRATOR . '/models/form.php');

		$model = new Form2ContentSearchModelForm();
		$searchForm = $model->getItem($this->formId);
		
		JToolBarHelper::title(JText::_('COM_FORM2CONTENTSEARCH_FORM2CONTENTSEARCH') . ': ' . JText::_('COM_FORM2CONTENTSEARCH_FORMFIELDS'), 'generic.png');
		JToolBarHelper::addNew('formfield.fieldselect','JTOOLBAR_NEW');
		JToolBarHelper::editList('formfield.edit','JTOOLBAR_EDIT');
		JToolBarHelper::trash('formfields.delete','JTOOLBAR_TRASH');
		
		$this->pageTitle = JText::_('COM_FORM2CONTENTSEARCH_FORMFIELDS') . ' - ' . $searchForm->title;
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
			'ft.description' => JText::_('COM_FORM2CONTENTSEARCH_FIELDTYPE'),
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING')
		);
	}
}
?>