<?php
defined('_JEXEC') or die();

jimport('joomla.application.component.modellist');

class Form2ContentSearchModelForms extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title', 'c.title');
		}
				
		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context.'.forms.filter.search', 'forms_filter_search');
		$this->setState('forms.filter.search', $search);
		
		// List state information.
		parent::populateState('a.title', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('forms.filter.search');

		return parent::getStoreId($id);
	}
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id, a.title');
		$query->from('`#__f2c_search_form` AS a');
		
		// Join over the Content Types for the Content Type title.
		$query->select('c.title AS contenttype');
		$query->join('LEFT', '`#__f2c_project` c ON a.projectid = c.id');

		// Filter by search in title.
		$search = $this->getState('forms.filter.search');

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
