<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.component.modellist');

class Form2ContentSearchModelFormFields extends JModelList
{
	protected $formId;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array(
				'ordering', 'a.ordering',
				'id', 'a.id',
				'title', 'a.title',
				'description', 'ft.description');
		}
		
		parent::__construct($config);
		$this->formId = JFactory::getApplication()->input->getInt('formid', 0);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$search = $this->getUserStateFromRequest($this->context.'.formfields.filter.search', 'formfields_filter_search');
		$this->setState('formfields.filter.search', $search);

		// List state information.
		parent::populateState('a.ordering', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('formfields.filter.search');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*, p.fieldname');
		$query->from('`#__f2c_search_formfield` AS a');
		
		// Join over the field for the description.
		$query->select('t.description AS fieldtype');		
		$query->join('LEFT', '`#__f2c_projectfields` p ON a.fieldid = p.id');
		$query->join('LEFT', '`#__f2c_fieldtype` t ON p.fieldtypeid = t.id');
		// Join over the search field for the description.
		$query->select('ft.description');
		$query->join('INNER', '`#__f2c_search_fieldtype` ft ON a.fieldtypeid = ft.id');
		
		// Filter by search in title.
		$search = $this->getState('formfields.filter.search');
		
		// Search filter
		if(!empty($search)) 
		{
			$query->where('(LOWER(a.title) LIKE '.$db->Quote( '%'.$db->escape( $search, true ).'%', false ) . ')');
		}

		// Content Type filter
		$query->where('(formid = '.(int)$this->formId.')');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}	
}
?>