<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldSingleSelectList extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		$html 					= '';
		$elementid	 			= self::getElementId(); 
		$elementValue 			= $this->getElementValue();		
		
		if($this->emptyText)
		{
			$listOptions[] = JHTML::_('select.option', '', $this->emptyText, 'value', 'text');
		}
	
		$arrOptions = (array)$this->settings->get('ssl_options');
		
		if(count($arrOptions))
		{
			foreach($arrOptions as $key => $value)
			{
				$listOptions[] = JHTML::_('select.option', $key, htmlspecialchars($value));
			}
		}
	
		$attribs = 'onchange="F2CSearchGetHits'.$moduleId.'();" ';
		$attribs .= $this->attribs ? $this->attribs : 'class="inputbox elmF2cSearch"';
		
		$html .= JHtml::_('select.genericlist',  $listOptions, $elementid, $attribs, 'value', 'text', $elementValue);			
		
		return $html;		
	}
	
	public function getFormIds($query)
	{
		$elementValue 	= $this->selection;
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
				if(count($this->settings->get('ssl_options')))
				{					
					foreach($this->settings->get('ssl_options') as $key => $value)
					{
						$this->selection = $key;
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
			$registry 	= new JRegistry(self::$dataView->fields[$this->id]->value);
			$value 		= $registry->toArray();
		}
		
		return $value;
	}
}
?>