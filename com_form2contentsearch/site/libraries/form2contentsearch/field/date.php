<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldDate extends F2csearchFieldBase
{	
	public function render($moduleId)
	{		
		static $initialized = false;
		
		$html 					= '';
		$elementid	 			= self::getElementId(); 
		$elementValue 			= $this->getElementValue();		
		$attribs 				= $this->attribs;
		$class 					= 'class="inputbox F2cSearchDateField elmF2cSearch"';
		$dateFormat				= $this->searchFieldSettings->get('date_format', 'dd-mm-yy');
		$setDateScript			= '';
		
		if(!$initialized)
		{
			$initialized = true;
			JHtml::script('mod_form2contentsearch/date.js', array('relative' => true));
			JHtml::script('mod_form2contentsearch/jquery.ui.datepicker.js', array('relative' => true));						
			JHtml::stylesheet('mod_form2contentsearch/jquery.ui.datepicker.css', array(), array('relative' => true));
			JFactory::getDocument()->addScriptDeclaration('var msgInvalidDate="'.JText::_('MOD_FORM2CONTENTSEARCH_INVALID_DATE', true).'";');
		}
		
		if($elementValue)
		{
			// Initialize the date with the selected value (taken from the post-back)
			$arrDate 		= explode('-', $elementValue);
			$formattedDate 	= 'jQuery.datepicker.formatDate("'.$dateFormat.'", new Date('.$arrDate[0].','.$arrDate[1].'-1,'.$arrDate[2].'))';
			$setDateScript 	= 'jQuery("#'.$elementid.'").datepicker("setDate", '.$formattedDate.');';
		}
		
		JFactory::getDocument()->addScriptDeclaration('jQuery(function() { jQuery("#'.$elementid.'").
		datepicker({dateFormat: "'.$dateFormat.'", altFormat: "yy-mm-dd", altField: "#'.$elementid.'_hidden"});'.$setDateScript.'});');
			
		$html .= '<div class="input-append">';
		$html .= '<input type="text" id="'.$elementid.'" '.$class.' onchange="f2cConvertDate(this);" />';
		$html .= '<button id="'.$elementid.'_btn" class="btn" type="button" onclick="jQuery('.$elementid.').datepicker(\'show\');"><i class="icon-calendar"></i></button>';
		$html .= '</div>';
		$html .= '<input type="hidden" id="'.$elementid.'_hidden" value=""/>';
		return $html;
	}
	
	public function getFormIds($query)
	{
		$elementValue 	= $this->selection;
		$date			= new JDate($elementValue);
		$field1			= $this->searchFieldSettings->get('field1');
		$field2			= $this->searchFieldSettings->get('field2');
		
		if(empty($elementValue))
		{
			// Do not perform a filter action
			return;
		}
		
		$this->buildQuery($query, $field1, $date, 1);
		
		if(!empty($field2))
		{
			$this->buildQuery($query, $field2, $date, 2);
		}
	}
	
	private function buildQuery($query, $field, $date, $index)
	{
		$includeNulls = $this->searchFieldSettings->get('include_null'.$index, 0);
		
		if(is_numeric($field))
		{
			// Content Type Field
			$alias = 'fc'.$this->id.'_'.$index;
			
			if($includeNulls)
			{
				$query->join('LEFT', '#__f2c_fieldcontent '.$alias.' ON '.$alias.'.formid = f.id AND '.$alias.'.fieldid='.$field);		
				$query->where('(\''.$date-> toISO8601().'\''.$this->getOperator($index).$alias.'.content OR '.$alias.'.content is null)');
			}
			else 
			{
				$query->join('INNER', '#__f2c_fieldcontent '.$alias.' ON '.$alias.'.formid = f.id AND '.$alias.'.fieldid='.$field.' AND \''.$date-> toISO8601().'\''.$this->getOperator($index).$alias.'.content');		
			}			
		}
		else
		{
			// Joomla field
			$whereClause = '\''.$date->toSql().'\''.$this->getOperator($index).'f.'.$field;
			
			if($includeNulls)
			{
				$whereClause .= ' OR f.'.$field.' = \''.JFactory::getDBO()->getNullDate().'\'';
			}
			
			$query->where('('.$whereClause.')');
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
			$this->selection = '';
		}
	}
	
	public function getSelectedValueDataView($dataviewFieldList)
	{
		if(array_key_exists($this->id, $dataviewFieldList))
		{
			$registry = new JRegistry($dataviewFieldList[$this->id]->value);
			
			if(trim($registry->get('script')) != '')
			{
				eval($registry->get('script'));
				$this->selection = $filterDate;
			}
			else 
			{
				$date				= new JDate($registry->get('date_field'));
				$this->selection 	= $date->format('y-m-d');
			}
		}
		else
		{
			$this->selection = JFactory::getApplication()->input->getString(self::getElementId(), '');
		}
	}

	public static function buildFilterUrl()
	{
		$script 	= array();
		$script[]	= 'if(jQuery("#"+field.elementName).hasClass("invalid")){alert(msgInvalidDate.replace("%s", jQuery("#"+field.elementName).datepicker("option", "dateFormat")));return false;} ';
		$script[] 	= 'arrFieldValues[i] = field.elementName+ \'=\' + encodeURIComponent(jQuery("#"+field.elementName+"_hidden").val());';
		
		return $script;
	}
	
	public static function getResetControlScript()
	{
		return 'obj.val(\'\');obj.removeClass(\'invalid\');jQuery("#"+obj.attr(\'id\')+"_hidden").val(\'\');';
	}
	
	private function getOperator($operatorNumber)
	{
		switch($this->searchFieldSettings->get('operator'.$operatorNumber))
		{
			case 'LT': return ' < ';
			case 'LTEQ': return ' <= ';
			case 'EQ': return ' = ';
			case 'GT': return ' > ';
			case 'GTEQ': return ' >= ';
		}
	}
	
	protected function getElementValueFromDataView()
	{
		$value = '';
		
		// Check if the element is present in the dataview
		if(array_key_exists($this->id, self::$dataView->fields))
		{
			$fieldInfo = new JRegistry(self::$dataView->fields[$this->id]->value);
			
			if($fieldInfo->get('script'))
			{
				$value = eval($fieldInfo->get('script'));
			}
			else
			{
				$datetime = explode(' ', $fieldInfo->get('date_field'));
				$value = $datetime[0];
			}
		}
		
		return $value;
	}
}
?>