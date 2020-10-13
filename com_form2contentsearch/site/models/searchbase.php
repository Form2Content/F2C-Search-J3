<?php
defined('_JEXEC') or die('Restricted acccess');

jimport('joomla.application.component.modellist');

class Form2ContentSearchModelSearchBase extends JModelList
{

	var $globalParams = null;
	var $itemCount = 0;
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	4.0.0
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		// List state information
		$value = (int)$this->globalParams->get('num_leading_articles', 0) +
				 (int)$this->globalParams->get('num_intro_articles', 0) +
				 (int)$this->globalParams->get('num_links', 0);

		$this->setState('list.limit', $value);

		$value = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $value);

		if($this->globalParams->get('use_custom_ordering', 0))
		{
			$defaultsorting = explode(';', $this->globalParams->get('custom_ordering_default', 'title;ASC;Title;STRING'));
			
			$sortField = $this->getUserStateFromRequest($this->context.'.list.sortfield', 'order_by', $defaultsorting[0]);			
			$this->setState('list.sortfield', $sortField);
			
			$sortDir = $this->getUserStateFromRequest($this->context.'.list.sortdir', 'order_dir', $defaultsorting[1]);
			$this->setState('list.sortdir', $sortDir);
			
			$sortType = $this->getUserStateFromRequest($this->context.'.list.sorttype', 'order_type', $defaultsorting[3]);
			$this->setState('list.sorttype', $sortType);
		}
		else 
		{
			$orderCol = $app->input->getCmd('filter_order', 'a.ordering');
			if (!in_array($orderCol, $this->filter_fields)) {
				$orderCol = 'a.ordering';
			}
			$this->setState('list.ordering', $orderCol);
	
			$listOrder	=  $app->input->getCmd('filter_order_Dir', 'ASC');
			if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
				$listOrder = 'ASC';
			}
			$this->setState('list.direction', $listOrder);
		}
		
		// Only show published articles in the search results view
		$this->setState('filter.published', 1);
		
		$this->setState('filter.language', $app->getLanguageFilter());

		// process show_noauth parameter
		if (!$this->globalParams->get('show_noauth')) 
		{
			$this->setState('filter.access', true);
		}
		else 
		{
			$this->setState('filter.access', false);
		}
		
		$this->setState('layout', $app->input->getCmd('layout'));
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	4.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.published');
		$id .= ':'.$this->getState('filter.access');

		return parent::getStoreId($id);
	}
	
	/**
	 * Method to get a list of articles.
	 *
	 * Overriden to inject convert the attribs field into a JParameter object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	2.0.0
	 */
	public function getItems()
	{
		$items	= parent::getItems();		
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');
		$groups	= $user->getAuthorisedViewLevels();

		$this->itemCount = $this->getTotal();
		
		// Convert the parameter fields into objects.
		foreach ($items as &$item)
		{
			$articleParams = new JRegistry;
			$articleParams->loadString($item->attribs);

			// Unpack readmore and layout params
			$item->alternative_readmore = $articleParams->get('alternative_readmore');
			$item->layout = $articleParams->get('layout');
			$item->params = clone $this->globalParams;
			
			// For blogs, article params override menu item params only if menu param = 'use_article'
			// Otherwise, menu item params control the layout
			// If menu item is 'use_article' and there is no article param, use global
			// create an array of just the params set to 'use_article'
			$articleArray = array();
			$globalParmsArray = $item->params->toArray();
			
			foreach ($globalParmsArray as $key => $value)
			{
				if ($value === 'use_article') 
				{
					// if the article has a value, use it
					if ($articleParams->get($key) != '') 
					{
						// get the value from the article
						$articleArray[$key] = $articleParams->get($key);
					}
					else 
					{
						// otherwise, use the global value
						$articleArray[$key] = $this->globalParams->get($key);
					}
				}
			}
			
			// merge the selected article params
			if (count($articleArray) > 0) 
			{
				$articleParams = new JRegistry;
				$articleParams->loadArray($articleArray);
				$item->params->merge($articleParams);
			}
			
			// get display date
			switch ($item->params->get('show_date'))
			{
				case 'modified':
					$item->displayDate = $item->modified;
					break;

				case 'published':
					$item->displayDate = ($item->publish_up == 0) ? $item->created : $item->publish_up;
					break;

				default:
				case 'created':
					$item->displayDate = $item->created;
					break;
			}

			// Items from the search results view are not supposed to be edit from that view
			$item->params->set('access-edit', false);
			
			$access = $this->getState('filter.access');

			if ($access) 
			{
				// If the access filter has been set, we already have only the articles this user can view.
				$item->params->set('access-view', true);
			}
			else 
			{
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				if ($item->catid == 0 || $item->category_access === null) 
				{
					$item->params->set('access-view', in_array($item->access, $groups));
				}
				else 
				{
					$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}
		}

		//print_r($items);die();
		return $items;
	}
	
	
	protected function _buildContentOrderBy()
	{
		$app		= JFactory::getApplication('site');
		$db			= $this->getDbo();
		$orderby	= ' ';
		$orderCol 	= null;
		$orderDirn 	= 'ASC';

		if($this->globalParams->get('use_custom_ordering', 0))
		{
			if(strtolower($this->getState('list.sortdir')) == 'rand')
			{
				$orderby .= ' RAND()';
			}
			else 
			{
				if(!is_numeric($this->getState('list.sortfield')))
				{
					switch(strtolower($this->getState('list.sortfield')))
					{
						case 'featured':
							$orderby .= 'fp.ordering';
							break;
						case 'created':
							$orderby .= 'a.created';
							break;
						case 'modified':
							$orderby .= 'a.modified';
							break;
						case 'published':
							$orderby .= 'a.publish_up';
							break;
						case 'title':
							$orderby .= 'a.title';
							break;
						case 'author':
							$orderby .= 'author';
							break;
						case 'hits':
							$orderby .= 'a.hits';
							break;
						case 'ordering':
							$orderby .= 'a.ordering';
							break;
					}
				}
				else 
				{
					$orderby .= 'sortfield';
				}
				
				$orderby .= ' ' . $this->getState('list.sortdir') . ', a.created DESC';
			}
		}
		else
		{		
			$articleOrderby 	= $app->input->getString('orderby_sec') ? $app->input->getString('orderby_sec') : $this->globalParams->get('orderby_sec', 'rdate');
			$articleOrderDate	= $this->globalParams->get('order_date');
			$categoryOrderby	= '';
			$secondary			= ContentHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
			$primary			= ContentHelperQuery::orderbyPrimary($categoryOrderby);
						
			$orderby .= $primary . ' ' . $secondary . ' a.created ';
		}

		return $orderby;
	}
	
	public function getParams($id)
	{
		$db = JFactory::getDBO();
		
		// Load the global parameters from com_content
		$db->setQuery('SELECT params FROM #__extensions WHERE name = \'com_content\'');
		$params = new JRegistry();
		$params->loadString($db->loadResult());

		// Load the parameters from the Search form
		$db->setQuery('SELECT attribs FROM #__f2c_search_form WHERE id = ' . (int)$id);
		$searchFormParams = new JRegistry();
		$searchFormParams->loadString($db->loadResult());
		
		// Merge both parameter sets: Search form parms overrule com_content parms
		$params->merge($searchFormParams);
		
		// Make sure the page heading has a default value
		if(!$params->get('page_heading'))
		{
			$params->def('page_heading', JText::_('COM_FORM2CONTENTSEARCH_SEARCH_RESULTS'));
		}
		
		return $params;
	}
}
?>
