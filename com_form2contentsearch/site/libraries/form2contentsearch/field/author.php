<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldAuthor extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		$html 				= '';
		$listOptions		= array();
		$elementid 			= self::getElementId();
		$elementValue 		= $this->getElementValue();
		$selectedValues 	= explode(',', $elementValue);
		
		if($this->emptyText)
		{
			$listOptions[] = JHTML::_('select.option', '', $this->emptyText, 'value', 'text');
		}
				
		$rowList = self::getAuthorList();

		if(count($rowList))
		{
			foreach($rowList as $row)
			{
				$listOptions[] = JHtml::_('select.option', $row[0], $row[1], 'value', 'text');
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
		
		if($elementValue)
		{
			// Prevent SQL injection
			$elementValue = implode(',', array_map('intval', explode(',', $elementValue)));

			$query->where('c.created_by IN ('.$elementValue.')');
		}
		else 
		{
			$authorIds = (array)$this->searchFieldSettings->get('aut_ids');
			
			if(count($authorIds))
			{
				// no selection, see if we have to limit the authors
				switch((int)$this->searchFieldSettings->get('aut_filter'))
				{
					case 1:
						// exclude selected authors
						$query->where('c.created_by NOT IN ('.implode(',', $authorIds).')');
						break;
					case 2:
						// limit to selected authors
						$query->where('c.created_by IN ('.implode(',', $authorIds).')');
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
				$this->selection = null;
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
					$rowList = self::getAuthorList();
					
					foreach($rowList as $row)
					{
						$this->selection = $row[0];
						break;
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
	
	private function getAuthorList()
	{
		$db 	= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		if($this->searchFieldSettings->get('aut_display_name', 0))
		{
			$query->select('id as value, username as text');
			$query->order('username ASC');
		}
		else
		{
			$query->select('id as value, name as text');
			$query->order('name ASC');
		}
		
		$query->from('#__users');
		
		switch((int)$this->searchFieldSettings->get('aut_filter'))
		{
			case 0:
				// show all authors
				break;
			case 1:
				// exclude selected authors
				$query->where('id NOT IN ('. implode(',', $this->searchFieldSettings->get('aut_ids')) .')');
				break;
			case 2:
				// only show selected authors
				$query->where('id IN ('. implode(',', $this->searchFieldSettings->get('aut_ids')) .')');
				break;
		}

		$db->setQuery($query);
		
		return $db->loadRowList();
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