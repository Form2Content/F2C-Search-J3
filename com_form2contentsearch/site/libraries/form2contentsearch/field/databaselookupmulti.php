<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldDatabaseLookupMulti extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		$html 				= '';
		$elementid 			= self::getElementId(); 
		$elementValue 		= $this->getElementValue();
		$selectedValues 	= explode(',', $elementValue);
		
		if($this->emptyText)
		{
			$listOptions[] = JHtml::_('select.option', '', $this->emptyText, 'value', 'text');
		}
			
		$db 	= JFactory::getDBO();
		$user 	= JFactory::getUser();
		$sql 	= $this->settings->get('dlm_query');
		$sql 	= str_replace('{$CURRENT_USER_ID}', $user->id, $sql);
		$sql 	= str_replace('{$CURRENT_USER_GROUPS}', implode(',', $user->groups), $sql);
		$sql 	= str_replace('{$LANGUAGE}', JFactory::getLanguage()->getTag(), $sql);
		
		$db->setQuery($sql);
	
		$rowList = $db->loadRowList();
		
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
		$db 			= JFactory::getDBO();
		$elementValue 	= $this->selection;
		
		if($elementValue)
		{
			$tableAliasCounter = 0;
			
			foreach(explode(',', $elementValue) as $selection)
			{
				// Make sure every join statement has an unique table alias
				$tableAlias = 'fc' . $this->fieldId . $tableAliasCounter++;
				
				$query->join('INNER', '#__f2c_fieldcontent '.$tableAlias.' ON '.$tableAlias.'.formid = f.id AND '.$tableAlias.'.fieldid='.$this->fieldId.' AND '.$tableAlias.'.content='.$db->quote($selection));
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
					$db = JFactory::getDBO();
					$db->setQuery($this->settings->get('dlm_query'));
					$rowList = $db->loadRowList();
					
					if(count($rowList))
					{
						foreach($rowList as $row)
						{
							$this->selection = $row[0];
							break;
						}
					}
					else
					{
						$this->selection = '';								
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