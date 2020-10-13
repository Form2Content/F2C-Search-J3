<?php
defined('JPATH_PLATFORM') or die();

jimport('joomla.application.component.modellist');

class Form2ContentSearchModelDatavws extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title', 's.title');
		}
				
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$search = $this->getUserStateFromRequest($this->context.'.datavws.filter.search', 'datavws_filter_search');
		$this->setState('datavws.filter.search', $search);
		
		// List state information.
		parent::populateState('a.title', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('datavws.filter.search');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id, a.title');
		$query->from('`#__f2c_search_dataview` AS a');
		
		// Join over the Content Types for the Content Type title.
		$query->select('s.title AS searchform');
		$query->join('LEFT', '`#__f2c_search_form` s ON a.search_form_id = s.id');

		// Filter by search in title.
		$search = $this->getState('datavws.filter.search');

		// Search filter
		if(!empty($search)) 
		{
			$query->where('(LOWER(a.title) LIKE '.$db->Quote( '%'.$db->escape($search, true).'%', false ) . ')');
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}	
}
?>
