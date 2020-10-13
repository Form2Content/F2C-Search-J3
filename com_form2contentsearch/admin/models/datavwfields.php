<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.component.modellist');

class Form2ContentSearchModelDatavwFields extends JModelList
{
	protected $dataviewId;

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 's.title',
				'description', 'ft.description');
		}
		
		parent::__construct($config);
		$this->dataviewId = JFactory::getApplication()->input->getInt('datavwid', 0);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$search = $this->getUserStateFromRequest($this->context.'.datavwfields.filter.search', 'datavwfields_filter_search');
		$this->setState('datavwfields.filter.search', $search);

		// List state information.
		parent::populateState('s.title', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('datavwfields.filter.search');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id');
		$query->from('`#__f2c_search_dataviewfield` AS a');
		
		$query->select('s.title');
		$query->join('INNER', '#__f2c_search_formfield s ON a.search_form_fieldid = s.id');		
			
		// Join F2C Search field info
		$query->select('ft.description');
		$query->join('INNER', '#__f2c_search_fieldtype ft on s.fieldtypeid = ft.id');
 
		// Filter by search in title.
		$search = $this->getState('datavwfields.filter.search');
		
		// Search filter
		if(!empty($search)) 
		{
			$query->where('(LOWER(s.title) LIKE '.$db->Quote( '%'.$db->escape($search, true).'%', false ) . ')');
		}

		// Data View filter
		$query->where('(dataview_id = '.(int)$this->dataviewId.')');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}	
}
?>