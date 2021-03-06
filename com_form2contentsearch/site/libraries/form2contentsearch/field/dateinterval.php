<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldDateinterval extends F2csearchFieldBase
{	
	public function render($moduleId)
	{	
		$html 					= '';
		$elementid	 			= self::getElementId(); 
		$elementValue 			= $this->getElementValue();		
		$intervalDates			= array();
	
		if($this->emptyText)
		{
			$listOptions[] = JHTML::_('select.option', '', $this->emptyText, 'value', 'text');
		}
		
		// Build the array in the variable $arrInterval
		eval($this->searchFieldSettings->get('script'));
		
		if(count($intervalDates))
		{					
			foreach($intervalDates as $key => $value)
			{
				$listOptions[] = JHTML::_('select.option', $key, htmlspecialchars($value));  	
			}
		}
	
		$html .= JHtml::_('select.genericlist',  $listOptions, $elementid, 'onchange="F2CSearchGetHits'.$moduleId.'();" class="inputbox elmF2cSearch" ' . $this->attribs, 'value', 'text', $elementValue);			
		
		return $html;
	}
	
	public function getFormIds($query)
	{
		$elementValue 	= $this->selection;
		
		if(empty($elementValue[0]))
		{
			// Do not perform a filter action
			return;
		}
		
		$startDate	= new JDate($elementValue[0]);
		$endDate	= new JDate($elementValue[1]);
		$field		= $this->searchFieldSettings->get('field');

		if(is_numeric($field))
		{
			// Content Type Field
			$alias 		= 'fc'.$this->id;
			$startDate	= '\''.$startDate->toISO8601().'\'';
			$endDate	= '\''.$endDate->toISO8601().'\'';			
			
			$query->join('INNER', '#__f2c_fieldcontent '.$alias.' ON '.$alias.'.formid = f.id AND '.$alias.'.fieldid='.$field.' AND '.
							$startDate.' <= '.$alias.'.content AND '.$alias.'.content <= '.$endDate);
		}
		else
		{
			// Joomla field
			$startDate		= '\''.$startDate->toSql().'\'';
			$endDate		= '\''.$endDate->toSql().'\'';			
			$whereClause 	= '('.$startDate.' <= f.'.$field.' AND f.'.$field.' <= '.$endDate;
			
			if($field == 'publish_down')
			{
				$whereClause .= ' OR f.publish_down = \''.JFactory::getDBO()->getNullDate().'\'';
			}
			
			$query->where($whereClause.')');
		}
	}
	
	public function getSelectedValue()
	{
		$input			= JFactory::getApplication()->input;
		$postBack		= $input->getInt('pb', 0) && ($input->getInt('moduleid') == $this->moduleId);
		
		if($postBack)
		{
			$this->selection = explode('|', $input->getString(self::getElementId(), ''));
		}
		else 
		{
			$this->selection = array('', '');
		}
	}
	
	public function getSelectedValueDataView($dataviewFieldList)
	{
		if(array_key_exists($this->id, $dataviewFieldList))
		{
			$registry 			= new JRegistry($dataviewFieldList[$this->id]->value);
			$intervalDates 		= array();
			
			eval($registry->get('script'));
			
			$this->selection = $intervalDates;
		}
		else
		{
			$this->selection = array('', '');
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
			$fieldInfo = new JRegistry(self::$dataView->fields[$this->id]->value);
			
			eval($fieldInfo->get('script'));
			$value = $intervalDates[0].'|'.$intervalDates[1];
		}
		
		return $value;
	}
}
?>