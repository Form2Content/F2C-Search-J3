<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldDatabaseLookup extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		$html 				= '';
		$elementid 			= self::getElementId(); 
		$selectedValue 		= $this->getElementValue();
		
		if($this->emptyText)
		{
			$listOptions[] = JHtml::_('select.option', '', $this->emptyText, 'value', 'text');
		}
					
		$db 	= JFactory::getDBO();
		$user 	= JFactory::getUser();
		$sql 	= $this->settings->get('dbl_query');
		$sql 	= str_replace('{$CURRENT_USER_ID}', $user->id, $sql);
		$sql 	= str_replace('{$CURRENT_USER_GROUPS}', implode(',', $user->groups), $sql);
		$sql = str_replace('{$LANGUAGE}', JFactory::getLanguage()->getTag(), $sql);
			
		$db->setQuery($sql);
		
		$rowList = $db->loadRowList();
		
		if(count($rowList))
		{
			foreach($rowList as $row)
			{
				$listOptions[] = JHtml::_('select.option', $row[0], $row[1], 'value', 'text');
			}
		}

		$html .= JHtml::_('select.genericlist', $listOptions, $elementid, 'onchange="F2CSearchGetHits'.$moduleId.'();" class="inputbox elmF2cSearch" ' . $this->settings->get('dbl_attributes'), 'value', 'text', $selectedValue);
		
		return $html;		
	}
	
	public function getFormIds($query)
	{
		$elementValue	= $this->selection;
		$db 			= JFactory::getDbo();
		
		if($elementValue)						
		{
			if(is_array($elementValue) && count($elementValue))
			{
				foreach ($elementValue as &$element)
				{
					$element = $db->quote($element);
				}
				
				$condition = '(fc'.$this->fieldId.'.content='.implode(' OR fc'.$this->fieldId.'.content=', $elementValue).')';
				
				$query->join('INNER', '#__f2c_fieldcontent fc'.$this->fieldId.' ON fc'.$this->fieldId.'.formid = f.id AND fc'.$this->fieldId.'.fieldid='.$this->fieldId.' AND ' . $condition);
			}
			else 
			{
				$query->join('INNER', '#__f2c_fieldcontent fc'.$this->fieldId.' ON fc'.$this->fieldId.'.formid = f.id AND fc'.$this->fieldId.'.fieldid='.$this->fieldId.' AND fc'.$this->fieldId.'.content='.$db->quote($elementValue));
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
			if($this->emptyText)
			{
				$this->selection = '';
			}
			else
			{
				// Select the first available option
				$db = JFactory::getDBO();
				$db->setQuery($this->settings->get('dbl_query'));
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
	
	public function getSelectedValueDataView($dataviewFieldList)
	{
		if(array_key_exists($this->id, $dataviewFieldList))
		{
			$dataviewField = $dataviewFieldList[$this->id];
			$selections = new JRegistry($dataviewField->value);
			$this->selection = $selections->toArray();
		}
		else
		{
			$this->selection = JFactory::getApplication()->input->getString(self::getElementId(), '');
		}
	}

	public static function buildFilterUrl()
	{
		$script 	= array();
		$script[] 	= 'arrFieldValues[i] = field.elementName+ \'=\' + encodeURIComponent(jQuery("#"+field.elementName).val());';
		
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