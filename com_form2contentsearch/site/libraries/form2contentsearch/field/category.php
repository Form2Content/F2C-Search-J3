<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldCategory extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		$html 			= '';
		$listOptions	= array();
		$elementid 		= self::getElementId(); 
		$elementValue 	= $this->getElementValue();
		$selectedValues = explode(',', $elementValue);
		$items 			= self::getCategoryList();
		$minIndent		= 1000;
		
		if($this->emptyText)
		{
			$listOptions[] = JHtml::_('select.option', '', $this->emptyText, 'value', 'text');
		}
		
		if(count($items))
		{
			if((int)$this->searchFieldSettings->get('cat_display') == 0)
			{
				// determine the lowest indention level for the tree
				foreach ($items as &$item)
				{
					if($item->level < $minIndent)
					{
						$minIndent = $item->level;
					}
				}
			}
			
			$minIndent--;
			
			foreach ($items as &$item) 
			{
				if((int)$this->searchFieldSettings->get('cat_display') == 0)
				{
					// indented tree view
					$repeat = ( $item->level - 1 >= 0 ) ? $item->level - 1 : 0;
					$item->title = str_repeat('- ', $repeat - $minIndent).$item->title;
				}
				else 
				{
					$item->title = $item->title;
				}
				
				$listOptions[] = JHtml::_('select.option', $item->id, $item->title);
			}
		}
				
		// define whether we're rendering a single or a multiple select list
		$attribs = ($this->displayMode ? 'multiple ' : '') . '  onchange="F2CSearchGetHits'.$moduleId.'();" class="elmF2cSearch" ';
		
		$html .= JHtml::_('select.genericlist', $listOptions, $elementid . '[]', $attribs . $this->attribs, 'value', 'text', $selectedValues);

		return $html;		
	}
	
	public function getFormIds($query)
	{
		$elementValue = $this->selection;
		
		if($this->searchFieldSettings->get('cat_search_below'))
		{
			$subCatClause	= array();
			$db				= JFactory::getDbo();
			
			// Join the categories for the parent/child information
			$query->join('INNER', '#__categories cat ON c.catid = cat.id');
			
			// Determine the list of categories that will be searched
			$querySearchCats = $db->getQuery(true);
			$querySearchCats->select('id, lft, rgt, parent_id');
			$querySearchCats->from('#__categories');
			
			if($elementValue)
			{
				// Prevent SQL injection
				$elementValue = implode(',', array_map('intval', explode(',', $elementValue)));

				$querySearchCats->where('id IN ('. $elementValue. ')');
			}
			else 
			{
				$catIds = (array)$this->searchFieldSettings->get('cat_ids');
				
				if(count($catIds))
				{
					// no selection, see if we have to limit the categories
					switch((int)$this->searchFieldSettings->get('cat_filter'))
					{
						case 1:
							// exclude selected categories
							$querySearchCats->where('id NOT IN ('.implode(',', $catIds).')');
							break;
						case 2:
							// limit to selected categories
							$querySearchCats->where('id IN ('.implode(',', $catIds).')');
							break;
					}
				}
			}
			
			$db->setQuery($querySearchCats);
			
			$searchCatList = $db->loadObjectList('id');
			
			if(count($searchCatList))
			{
				foreach($searchCatList as &$searchCat)
				{
					// skip child categories when its parent is also in the list
					if(!array_key_exists($searchCat->parent_id, $searchCatList))
					{
						$subCatClause[] = '(cat.lft >= ' . $searchCat->lft . ' AND cat.rgt <= ' . $searchCat->rgt . ')';
					}
				}
			}
	
			if(count($subCatClause))
			{
				$query->where('(' . implode(' OR ', $subCatClause) . ')');
			}							
		}
		else 
		{
			// we don't search the categories below
			if($elementValue)
			{						
				$query->where('c.catid IN ('.$elementValue.')');
			}
			else 
			{
				$catIds = (array)$this->searchFieldSettings->get('cat_ids');
				
				// no selection, see if we have to limit the categories
				switch((int)$this->searchFieldSettings->get('cat_filter'))
				{
					case 1:
						// exclude selected categories
						$query->where('c.catid NOT IN ('.implode(',', $catIds).')');
						break;
					case 2:
						// limit to selected categories
						$query->where('c.catid IN ('.implode(',', $catIds).')');
						break;
				}
			}
		}
	}
	
	public function getSelectedValue()
	{
		$input			= JFactory::getApplication()->input;
		$postBack		= $input->getInt('pb', 0) && ($input->getInt('moduleid') == $this->moduleId);
		
		if($postBack)
		{
			$this->selection = $input->getString(self::getElementId(), '');
		}
		else 
		{
			if($this->displayMode == 1)
			{
				// multiselect list
				$this->selection  = null;
			}
			else
			{
				// dropdown list
				if($this->emptyText)
				{
					$this->selection = null;
				}
				else
				{
					// Get the first value from the selection list
					$catList = self::getCategoryList($this);
	
					if(count($catList))
					{
						$this->selection = $catList[0]->id;
					}
				}
			}
		}
	}
	
	public function getSelectedValueDataView($dataviewFieldList)
	{
		if(array_key_exists($this->id, $dataviewFieldList))
		{
			$dataviewField = $dataviewFieldList[$this->id];
			$selections = new JRegistry($dataviewField->value);
			$this->selection = implode(',', $selections->toArray());
		}
		else
		{
			$this->selection = JFactory::getApplication()->input->getString(self::getElementId(), '');
		}
	}	
	
	public static function buildFilterUrl()
	{
		$script 	= array();
		$script[] 	= 'var arrMultiValues = [];';
		$script[]	= 'jQuery("#"+field.elementName+" option:selected").each(function() { arrMultiValues[arrMultiValues.length] = this.value; });';
		$script[]	= 'arrFieldValues[i] = field.elementName+ \'=\' + arrMultiValues.join(\',\');';		
		
		return $script;
	}
	
	private function getCategoryList()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('a.id, a.title, a.level');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');
		$query->where('extension = \'com_content\'');
		$query->where('a.published = 1');
				
		$catIds = (array)$this->searchFieldSettings->get('cat_ids');
		
		switch((int)$this->searchFieldSettings->get('cat_filter'))
		{
			case 1:
				// exclude selected categories
				$query->where('a.id NOT IN ('.implode(',', $catIds).')');
				break;
			case 2:
				// limit to selected categories
				$query->where('a.id IN ('.implode(',', $catIds).')');
				break;
		}
		
		switch((int)$this->searchFieldSettings->get('cat_display'))
		{
			case 0: // tree view
				$query->order('a.lft');
				break;
			case 1: // alphabetically sorted list
				$query->order('a.title');
				break;
		}

		$db->setQuery($query);
		return $db->loadObjectList();		
	}

	protected function getElementValueFromDataView()
	{
		$value = '';
		
		// Check if the element is present in the dataview
		if(array_key_exists($this->id, self::$dataView->fields))
		{
			$registry = new JRegistry(self::$dataView->fields[$this->id]->value);
			$value = join(',', $registry->toArray());
		}
		
		return $value;
	}
}
?>