<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldMultiSelectList extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		$html 				= '';
		$valueList 			= array();
		$elementid 			= self::getElementId();
		$elementValue 		= $this->getElementValue();
		$selectedValues 	= explode(',', $elementValue);		
				
		if($this->emptyText)
		{
			$listOptions[] = JHtml::_('select.option', '', $this->emptyText, 'value', 'text');
		}
			
		foreach($this->settings->get('msl_options') as $optionKey => $optionValue)
		{
			$listOptions[] = JHtml::_('select.option', $optionKey, $optionValue);  	
		}
		
		switch($this->displayMode)
		{
			case 0: // single select list
				$attribs = ' onchange="F2CSearchGetHits'.$moduleId.'();" class="elmF2cSearch" ';
				$html .= JHtml::_('select.genericlist', $listOptions, $elementid . '[]', $attribs . $this->attribs, 'value', 'text', $selectedValues);
				break;
			case 1: // multi select list
				$attribs = 'multiple onchange="F2CSearchGetHits'.$moduleId.'();" class="elmF2cSearch" ';
				$html .= JHtml::_('select.genericlist', $listOptions, $elementid . '[]', $attribs . $this->attribs, 'value', 'text', $selectedValues);
				break;
			case 2: // checkbox list
				foreach($this->settings->get('msl_options') as $optionKey => $optionValue)
				{
					$checked = in_array($optionKey, $selectedValues) ? ' checked' : '';
					$html .= '<label><input type="checkbox" name="'.$elementid.'" id="'.$elementid.'_'.$optionKey.'" value="'.$optionKey.'" onchange="F2CSearchGetHits'.$moduleId.'();" class="elmF2cSearch" '. $this->attribs . $checked.'>'.$optionValue.'</label>';
				}
				break;
			default:
				throw new Exception('Unsupported displayMode for multiselectlist');
		}		
					
		return $html;	
	}
	
	public function getFormIds($query)
	{
		$db 			= JFactory::getDBO();
		$elementValue 	= $this->selection;
		
		if($elementValue)
		{
			if($this->searchFieldSettings->get('querymode', 0) == 1)
			{
				// Or the selection
				$tableAlias = 'fc' . $this->fieldId;
				$arrSelection = explode(',', $elementValue);

				foreach ($arrSelection as &$element)
				{
					$element = $db->quote($element);
				}

				$querySelection = implode(' OR ' . $tableAlias . '.content=', $arrSelection);
				
				$query->join('INNER', '#__f2c_fieldcontent '.$tableAlias.' ON '.$tableAlias.'.formid = f.id AND '.$tableAlias.'.fieldid='.$this->fieldId.' AND ('.$tableAlias.'.content='.$querySelection.')');
			}
			else 
			{
				$tableAliasCounter = 0;
				
				// And the selection
				foreach(explode(',', $elementValue) as $selection)
				{					
					// Make sure every join statement has an unique table alias
					$tableAlias = 'fc' . $this->fieldId . $tableAliasCounter++;
					
					$query->join('INNER', '#__f2c_fieldcontent '.$tableAlias.' ON '.$tableAlias.'.formid = f.id AND '.$tableAlias.'.fieldid='.$this->fieldId.' AND '.$tableAlias.'.content='.$db->quote($selection).'');
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
					foreach((array)$this->settings->get('msl_options') as $optionKey => $optionValue)
					{
						$this->selection = $optionKey;
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
		$script[] 	= 'var arrMultiValues = []; var selector = "#"+field.elementName;';
		
		// Check if we have checkboxes or a multiselect list
		$script[]	= 'if(jQuery(selector).is("select")) { selector += " option:selected"; } else { selector = "[name="+field.elementName+"]:checked"; }';
		// Join all the selected options in a string
		$script[]	= 'jQuery(selector).each(function() { arrMultiValues[arrMultiValues.length] = this.value; });';
		$script[]	= 'arrFieldValues[i] = field.elementName+ \'=\' + arrMultiValues.join(\',\');';		
		
		return $script;
	}
	
	public static function getResetControlScript()
	{
		JHtml::script('media/com_form2contentsearch/js/fields/Multiselectlist.js', array('relative' => false));
		return('Form2ContentSearch.Fields.Multiselectlist.ResetControl(field.elementName);');
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