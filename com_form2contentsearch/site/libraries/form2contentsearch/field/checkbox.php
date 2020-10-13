<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldCheckbox extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		$html 					= '';
		$elementid	 			= self::getElementId(); 
		$elementValue 			= $this->getElementValue();		
		
		switch($this->displayMode)
		{
			case 0: // single select list
			case 1: // multi select list (not implemented, render as single select list)
				if($this->emptyText)
				{
					$listOptions[] = JHtml::_('select.option', '', $this->emptyText, 'value', 'text');
				}
			
				$listOptions[] = JHtml::_('select.option', 'true', JText::_('JYES'), 'value', 'text');
				$listOptions[] = JHtml::_('select.option', 'false', JText::_('JNO'), 'value', 'text');
					
				$html .= JHtml::_('select.genericlist', $listOptions, $elementid, 'class="inputbox elmF2cSearch" size="1" onchange="F2CSearchGetHits'.$moduleId.'();" ' . $this->attribs, 'value', 'text', $elementValue);			
				break;
				
			case 2: // checkbox
				$checked = $elementValue != '' ? ' checked' : '';
				$html .= '<label><input type="checkbox" name="'.$elementid.'" id="'.$elementid.'" value="true" onchange="F2CSearchGetHits'.$moduleId.'();" class="elmF2cSearch" '. $this->attribs . $checked.'> '.self::stringHTMLSafe($this->caption).'</label>';
				break;
			default:
				throw new Exception('Unsupported displayMode for checkbox');
		}		
					
		

		return $html;	
	}
	
	public function getFormIds($query)
	{
		$db 			= JFactory::getDBO();
		$elementValue 	= $this->selection;
		
		switch($elementValue)
		{
			case 'true':
				$query->join('INNER', '#__f2c_fieldcontent fc'.$this->fieldId.' ON fc'.$this->fieldId.'.formid = f.id AND fc'.$this->fieldId.'.fieldid='.$this->fieldId.' AND fc'.$this->fieldId.'.content='.$db->quote($elementValue));								
				break;
			case 'false':
				$query->join('LEFT', '#__f2c_fieldcontent fc'.$this->fieldId.' ON fc'.$this->fieldId.'.formid = f.id AND fc'.$this->fieldId.'.fieldid='.$this->fieldId);
				$query->where('fc'.$this->fieldId.'.content IS NULL');
				break;
			default:
				break;							
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
			switch($this->displayMode)
			{
			case 0: // single select list
			case 1: // multi select list (not implemented, render as single select list)
				$this->selection = ($this->emptyText) ? '' : 'true';
				break;
			case 2: // checkbox
				// select all articles
				$this->selection = '';
				break;
			}			
		}
	}
	
	public function getSelectedValueDataView($dataviewFieldList)
	{
		if(array_key_exists($this->id, $dataviewFieldList))
		{
			$dataviewField = $dataviewFieldList[$this->id];
			$this->selection = $dataviewField->value == 1 ? 'true' : 'false';
		}
		else
		{
			$this->selection = JFactory::getApplication()->input->getString(self::getElementId(), '');
		}
	}

	public static function buildFilterUrl()
	{
		$script = array();
		
		// Check if we have checkboxes or a multiselect list
		$script[]	= 'if(jQuery("#"+field.elementName).is("select")) 
						{ arrFieldValues[i] = field.elementName+ \'=\' + encodeURIComponent(jQuery("#"+field.elementName).val()); } else 
						{ arrFieldValues[i] = field.elementName+ \'=\' + encodeURIComponent(jQuery("#"+field.elementName).is(":checked") ? \'true\' : \'\');}';		
		
		return $script;
	}
	
	protected function getElementValueFromDataView()
	{
		$value = '';
		
		// Check if the element is present in the dataview
		if(array_key_exists($this->id, self::$dataView->fields))
		{
			$value = self::$dataView->fields[$this->id]->value ? 'true' : 'false';
		}
		
		return $value;
	}
	
	/**
	 * Returns whether the caption will be rendered
	 *
	 * @return  bool		Render caption: true or false
	 * 
	 * @since   6.7.0
	 */
	public function renderCaption()
	{
		// Don't render the caption when the display mode is checkbox
		return $this->displayMode != 2;
	}
	
}
?>