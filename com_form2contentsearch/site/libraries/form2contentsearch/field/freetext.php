<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldFreeText extends F2csearchFieldBase
{	
	public function render($moduleId)
	{
		static $initialized = false;
		
		$html 			= '';
		$elementid	 	= self::getElementId(); 
		$elementValue 	= $this->getElementValue();		
		$attribs 		= $this->attribs;
		$maxlength		= $this->searchFieldSettings->get('txt_max_length');
		$size			= $this->searchFieldSettings->get('txt_size');
		$class 			= 'class="inputbox F2cSearchFreetextField elmF2cSearch"';
				
		$html .= '<input type="text" '.$class.' name="'.$elementid.'" id="'.$elementid.'"';
		$html .= ($elementValue != '') ? ' value= "' . self::stringHTMLSafe($elementValue) . '"' : '';
		$html .= $size ? ' size= "' . $size . '"' : '';
		$html .= $maxlength ? ' maxlength= "' . $maxlength . '"' : '';
		$html .= $attribs . '/>';
		$html .= '<input type="hidden" id="hid_' . $elementid . '" value="' . self::stringHTMLSafe($elementValue) . '" />';
		
		// Create Javascript initialization
		if(!$initialized)
		{
			// Make sure initialization occurs only once for all fields
			$initialized = true;
			JHtml::script('mod_form2contentsearch/freetext.js', array('relative' => true));
		}
		
		return $html;		
	}
	
	public function getFormIds($query)
	{
		$elementValue 	= trim($this->selection);
		$db 			= JFactory::getDbo();
		
		if($elementValue)
		{
			// slashes cause errors, <> get stripped anyway later on. # causes problems.
			$badchars = array('#','>','<','\\');
			$elementValue = trim(str_replace($badchars, '', $elementValue));
	
			// Get the search words
			$words = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[\s,]+/", $elementValue, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			
			$fieldIds 	= (array)$this->searchFieldSettings->get('txt_fieldids');
			$conditions = array();
										
			if(count($fieldIds))
			{
				if(in_array(F2CSEARCH_DATAFIELD_JTITLE, $fieldIds) || in_array(F2CSEARCH_DATAFIELD_JMETAKEYS, $fieldIds) || in_array(F2CSEARCH_DATAFIELD_JMETADESC, $fieldIds))
				{
					// Make sure every join statement has an unique table alias
					$tableAlias 	= 'fc' . $this->id . '_title';
					$compareJoomla	= array();
					
					foreach ($words as $word) 
					{
						$word = $db->Quote('%'.$db->escape($word, true).'%', false);
						
						if(in_array(F2CSEARCH_DATAFIELD_JTITLE, $fieldIds))
						{
							$compareJoomla[] = $tableAlias.'.title like '.$word;
						}
						
						if(in_array(F2CSEARCH_DATAFIELD_JMETAKEYS, $fieldIds))
						{
							$compareJoomla[] = $tableAlias.'.metakey like '.$word;
						}
						
						if(in_array(F2CSEARCH_DATAFIELD_JMETADESC, $fieldIds))
						{
							$compareJoomla[] = $tableAlias.'.metadesc like '.$word;
						}
					}
					
					$query->join('LEFT', '#__f2c_form '.$tableAlias.' ON '.$tableAlias.'.id = f.id AND ('. implode(' OR ', $compareJoomla). ')');
					$conditions[] = $tableAlias.'.id IS NOT NULL';
				}
				
				$tableAliasCounter = 0;								
									
				foreach($fieldIds as $fieldId)
				{
					$compareField = array();
					
					// Make sure every join statement has an unique table alias
					$tableAlias = 'fc' . $this->id . '_' . $tableAliasCounter++;
					
					foreach ($words as $word) 
					{
						$word = $db->Quote('%'.$db->escape($word, true).'%', false);
						$compareField[] = $tableAlias.'.content like '.$word;
					}
					
					$query->join('LEFT', '#__f2c_fieldcontent '.$tableAlias.' ON '.$tableAlias.'.formid = f.id AND '.$tableAlias.'.fieldid='.$fieldId.' AND ('. implode(' OR ', $compareField). ')');
					$conditions[] = $tableAlias.'.content IS NOT NULL';
				}
	
				$query->where('(' . implode(' OR ', $conditions) . ')');
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
			$this->selection = '';
		}
	}
	
	public function getSelectedValueDataView($dataviewFieldList)
	{
		if(array_key_exists($this->id, $dataviewFieldList))
		{
			$dataviewField = $dataviewFieldList[$this->id];
			$this->selection = $dataviewField->value;
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
	
	public static function getResetControlScript()
	{
		return 'obj.val(\'\');';
	}
	
	protected function getElementValueFromDataView()
	{
		$value = '';
		
		// Check if the element is present in the dataview
		if(array_key_exists($this->id, self::$dataView->fields))
		{
			$value = self::$dataView->fields[$this->id]->value;
		}
		
		return $value;
	}
}
?>