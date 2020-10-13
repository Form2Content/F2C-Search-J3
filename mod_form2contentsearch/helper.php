<?php 
// no direct access
defined('JPATH_PLATFORM') or die;

class modForm2ContentSearchHelper
{
	private $f2cSearch = null;
	
	public function __construct()
	{
		$this->f2cSearch = new F2cSearch();
	}
	
	public function getSearchForm($id)
	{
		$db		= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$result	= null;

		$query->select('*');		
		$query->from('`#__f2c_search_form`');				
		$query->where('id = '.(int)$id);

		$db->setQuery($query);
		
		$result = $db->loadObject();
	
		if ($db->getErrorNum()) 
		{
			throw new Exception($db->stderr());
		}

		$result->attribs = new JRegistry($result->attribs);
		
		return $result;
	}
	
	public function buildSearchForm($moduleId, $searchForm, $searchFields)
	{
		$dataView = $this->DetectDataView($moduleId);
		
		if($dataView && $dataView->search_form_id == $searchForm->id)
		{
			// Store the dataview in the fields base class
			F2csearchFieldBase::$dataView = $dataView;
		}
				
		$form = new stdClass();

		$form->numElements = count($searchFields);
		$form->numCols = (int)$searchForm->num_cols;
		$form->numRows = (int)$this->intDivide($form->numElements, $form->numCols);

		$form->topText = ($searchForm->mod_pre_text ? self::stringHTMLSafe($searchForm->mod_pre_text) : '');

		$form->elements = array();

		for($i = 0; $i < $form->numElements; $i++)
		{
			$searchField 		= $searchFields[$i];
			
			$element 				= new JObject();
			$element->id			= $searchField->id;
			$element->caption 		= self::stringHTMLSafe($searchField->caption);
			$element->renderCaption = $searchField->renderCaption();
			$element->helptext 		= ($searchField->helptext ? JHTML::tooltip($searchField->helptext) : '');
			$element->element 		= $searchField->render($moduleId);
			
			$form->elements[] 	= $element;
		}
		
		return $form;
	}
	
	public function buildSearchFormClassic($moduleId, $searchForm, $searchFields)
	{
		$html			= '';
		$elementCounter = 0;
		$numElements 	= count($searchFields);
		$numCols 		= $searchForm->num_cols;
		$numRows 		= $this->intDivide($numElements, $numCols);
		$f2cSearch		= new F2cSearch();
		
		if($searchForm->mod_pre_text)
		{
			$html .= '<p>' . self::stringHTMLSafe($searchForm->mod_pre_text) . '</p>';
		}
		
		if ($numElements % $numCols != 0) $numRows++;
		
		$html .= '<table id="f2cs_elements_table_'.$moduleId.'">';

		for($i = 0; $i < $numRows; $i++)
		{
			// construct a row
			$elements = array();
		
			for($j = 0; $j < $numCols; $j++)
			{
				$elements[$j] = new JObject();

				if($elementCounter < $numElements)
				{
					$searchField = $searchFields[$elementCounter];
					
					$elements[$j]->caption = self::stringHTMLSafe($searchField->caption);
					
					if($searchField->helptext)
					{
						$elements[$j]->helptext = JHTML::tooltip($searchField->helptext);				
					}
					else
					{
						$elements[$j]->helptext = '&nbsp;';
					} 

					$elements[$j]->element 	= $searchField->render($moduleId);
				}
				else
				{
					$elements[$j]->caption = '&nbsp;'; 
					$elements[$j]->helptext = '&nbsp;'; 
					$elements[$j]->element = '&nbsp;'; 
				}
				
				$elementCounter++;
			}
			
			$html .= '<tr>';
				
			foreach($elements as $element)
			{
				$html .= '<td colspan="2">' . $element->caption . '</td>';
			}
		
			$html .= '</tr>';
			$html .= '<tr>';
		
			foreach($elements as $element)
			{
				$html .= '<td valign="top">' . $element->element . '</td>';
				$html .= '<td valign="top">' . $element->helptext . '</td>';
			}
			
			$html .= '</tr>';
		}
		
		$html .= '</table>';

		return $html;		
	}
	
	public function initializeScript()
	{
		static $initialized = false;

		if(!$initialized)
		{
			$script 		= array();
			$initialized 	= true;
			
			// TODO: remove
			JLoader::registerPrefix('F2csearch', JPATH_SITE.'/components/com_form2contentsearch/libraries/form2contentsearch');
			
			$db 	= JFactory::getdbo();
			$query 	= $db->getQuery(true);
	
			$query->select('id, name')->from('#__f2c_search_fieldtype');
			
			$db->setQuery($query);
			
			$lstFields = $db->loadObjectList('id'); 

			$script[] = 'function F2CSearchBuildFilterUrl(moduleId) {';
			$script[] = 'var elm;var arrFieldValues = [];var fields = searchFields[moduleId];';
			$script[] = 'for(i = 0; i < fields.length; i++) { ';
			$script[] = 'var field = fields[i];';
			$script[] = 'switch(field.fieldType) { ';
	
			foreach ($lstFields as $field)
			{
				$searchFieldClass = 'F2csearchField'.$field->name;
				
				$script[] 	= 'case '.$field->id.': ';
				$script 	= array_merge($script, call_user_func($searchFieldClass.'::buildFilterUrl'));
				$script[] 	= 'break;';
			}
			
			$script[] = '}';
			$script[] = '}';
			$script[] = 'return arrFieldValues.join(\'&\');';
			$script[] = '}';
			
			$script[] = 'function F2CSearchResetControls(moduleId, refreshResults) {';
			$script[] = 'var elm;var arrFieldValues = [];var fields = searchFields[moduleId];';
			$script[] = 'for(i = 0; i < fields.length; i++) { ';
			$script[] = 'var field = fields[i];';
			$script[] = 'var obj = jQuery("#"+field.elementName)';
			$script[] = 'switch(field.fieldType) { ';
	
			foreach ($lstFields as $field)
			{
				$searchFieldClass = 'F2csearchField'.$field->name;
				
				$script[] 	= 'case '.$field->id.': ';
				$script[] 	= call_user_func($searchFieldClass.'::getResetControlScript');
				$script[] 	= 'break;';
			}
			
			$script[] = '}';
			$script[] = '}';
			
			$script[] = 'if (refreshResults) { eval(\'F2CSearchGetResults\'+moduleId+\'();\'); }';
			$script[] = ' else ';
			$script[] = '{ eval(\'F2CSearchGetHits\'+moduleId+\'();\');	}';
			$script[] = '}';
			
			JFactory::getDocument()->addScriptDeclaration(join("\n", $script));
		}
	}
	
	private function intDivide($x, $y) 
	{
	    if ($x == 0) return 0;
	    if ($y == 0) return FALSE;
	    return ($x - ($x % $y)) / $y;
	}
	
	public function detectUTF8($string)
	{
	    return preg_match('%(?:
	        [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	        |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
	        |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	        |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	        |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
	        |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	        )+%xs', 
	    $string);
	}

	public function stringHTMLSafe($string)
	{
		if(self::detectUTF8($string))
		{
			$safeString = htmlentities($string, ENT_COMPAT, 'UTF-8');
		}
		else
		{
			$safeString = htmlentities($string, ENT_COMPAT);
		}
		
		return $safeString;
	}
	
	private function DetectDataView($moduleId)
	{
		$dataView = null;
		
		$input = JFactory::getApplication()->input;
		$postBack = $input->getInt('pb', 0) && ($input->getInt('moduleid') == $moduleId);
		$option = $input->getCmd('option');
		$view = $input->getCmd('view');
		
		if(!$postBack && $option = 'com_form2contentsearch' && $view == 'datavw')
		{
			// Get the DataviewId
			$app			= JFactory::getApplication();
			$menu			= $app->getMenu();
			$activeMenu		= $menu->getActive();
			$id 			= $activeMenu->params->get('datavw_id');
			
			$dataView = self::getDataView($id);
		}
		
		return $dataView;
	}
	
	private function getDataView($id)
	{
		$db	= JFactory::getDbo();
		
		$query = $db->getQuery(true);
		
		$query->select('*')->from('#__f2c_search_dataview')->where('id='.(int)$id);
		$db->setQuery($query);
		
		$dataView = $db->loadObject();
		
		// load the field values as specified in the data view
		$query = $db->getQuery(true);

		$query->select('search_form_fieldid, value');
		$query->from('#__f2c_search_dataviewfield');
		$query->where('dataview_id = ' . (int)$id);
		
		$db->setQuery($query);
		$dataView->fields = $db->loadObjectList('search_form_fieldid');
		
		return $dataView;
	}
}
?>