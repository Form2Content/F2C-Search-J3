<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldTextCombo extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		$html 				= '';
		$elementid 			= self::getElementId(); 
		$selectedValue 		= $this->getElementValue();
		$listOptions		= array();
		
		if($this->emptyText)
		{
			$listOptions[] = JHtml::_('select.option', '', $this->emptyText, 'value', 'text');
		}
					
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		$query->select('DISTINCT content');
		$query->from('#__f2c_fieldcontent fc');
		$query->join('INNER', '#__f2c_form frm ON fc.formid = frm.id AND frm.state = 1');
		$query->where('fieldid = ' . (int)$this->fieldId);
		$query->order('content ASC');

		$db->setQuery($query);
	
		$rowList = $db->loadRowList();
		
		if(count($rowList))
		{
			foreach($rowList as $row)
			{
				$listOptions[] = JHtml::_('select.option', $row[0], $row[0], 'value', 'text');
			}
		}

		// define whether we're rendering a single or a multiple select list
		$attribs = ($this->displayMode ? 'multiple ' : '') . '  onchange="F2CSearchGetHits'.$moduleId.'();"  class="inputbox elmF2cSearch" ';
		
		$html .= JHtml::_('select.genericlist', $listOptions, $elementid.'[]', $attribs . $this->settings->get('dbl_attributes'), 'value', 'text', $selectedValue);
		
		return $html;		
	}
	
	public function getFormIds($query)
	{
		$elementValue = $this->selection;
		
		if($elementValue)
		{
			$db = JFactory::getDbo();
			// slashes cause errors, <> get stripped anyway later on. # causes problems.
			$badchars = array('#','>','<','\\');
			$elementValue = trim(str_replace($badchars, '', $elementValue));
	
			$fieldId 		= $this->fieldId;														
			$tableAlias 	= 'fctc' . $this->id;
			$elementValue 	= $db->Quote($db->escape($elementValue, true), false);
			$condition 		= $tableAlias.'.content = '.$elementValue;
			
			$query->join('INNER', '#__f2c_fieldcontent '.$tableAlias.' ON '.$tableAlias.'.formid = f.id AND '.$tableAlias.'.fieldid='.$fieldId.' AND '.$condition);
			$conditions[] = $tableAlias.'.content IS NOT NULL';
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
				$db 	= JFactory::getDBO();
				$query 	= $db->getQuery(true);
				
				$query->select('content');
				$query->from('#__f2c_fieldcontent fc');
				$query->join('INNER', '#__f2c_form frm ON fc.formid = frm.id AND frm.state = 1');
				$query->where('fieldid = ' . (int)$this->fieldId);
				$query->order('content ASC LIMIT 1');
				
				$db->setQuery($query);
				
				if($result = $db->loadResult())
				{
					$this->selection = $result;
				}
				else
				{
					$this->selection = '';								
				}
			}
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
			$registry 	= new JRegistry(self::$dataView->fields[$this->id]->value);
			$value 		= $registry->toArray();
		}
		
		return $value;
	}
}
?>