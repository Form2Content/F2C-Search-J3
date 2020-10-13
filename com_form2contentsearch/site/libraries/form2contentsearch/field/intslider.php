<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2cSearchFieldIntSlider extends F2csearchFieldBase
{		
	public function render($moduleId)
	{
		static $initialized = false;
		
		$input				= JFactory::getApplication()->input;
		$html 				= '';
		$elementid 			= self::getElementId(); 
		$elementValue		= $this->getElementValue();
		$width 				= $this->searchFieldSettings->get('isl_width');
		$minValue			= $this->searchFieldSettings->get('isl_min_value');
		$maxValue			= $this->searchFieldSettings->get('isl_max_value');
		$doc				= JFactory::getDocument();
		
		// Add the min and max value to the script for use in the reset function
		$doc->addScriptDeclaration('arrSliderValues["'.$this->getElementId().'_min"]='.$this->searchFieldSettings->get('isl_min_value').';
									arrSliderValues["'.$this->getElementId().'_max"]='.$this->searchFieldSettings->get('isl_max_value').';');

		$currMinValue = $elementValue[0];
		$currMaxValue = $elementValue[1];
		
		if(is_numeric($width))
		{
			$width .= 'px;';
		}
		
		$html .= '<div style="width:'.$width.'">';
		$html .= '<div id="'.$elementid.'_valuelabel"></div>';
		$html .= '<div id="'.$elementid.'" class="elmF2cSearch"></div>';
		$html .= '</div>';
			
		JFactory::getDocument()->addScriptDeclaration('
			(function ($){
				$(document).ready(function (){

			/* Due to a Mootools conflict, remove all existing slide handlers */
			$("#'.$elementid.'").each(function(){ this.slide=null; });
				
			$("#'.$elementid.'").slider({
						range: true,
						values: ['.$currMinValue.','.$currMaxValue.'],
						min: '.$minValue.',
						max: '.$maxValue.',
						slide: function( event, ui ) { $("#'.$elementid.'_valuelabel").text(ui.values[0] + " - " + ui.values[1]); },
						stop: function(event,ui) { F2CSearchGetHits'.$moduleId.'(); }
					});
		
					$("#'.$elementid.'_valuelabel").text($("#'.$elementid.'").slider("values")[0] + " - " + $("#'.$elementid.'").slider("values")[1]);										
				});
			})(jQuery);
			');

		// Create Javascript/CSS initialization
		if(!$initialized)
		{
			// Make sure initialization occurs only once for all fields
			$initialized = true;
			JHtml::script('mod_form2contentsearch/jquery.ui.slider.js', array('relative' => true));
			JHtml::stylesheet('mod_form2contentsearch/jquery.ui.slider.css', array('relative' => true));
		}
		
		return $html;
	}
	
	public function getFormIds($query)
	{
		$elementValue = $this->selection;
		
		if($elementValue)
		{
			$fieldId 	= $this->id;
			$tableAlias = 'fcslide'.$fieldId;
	
			list($sliderMin, $sliderMax) = explode(';', $elementValue);							
			$query->join('INNER', '#__f2c_fieldcontent '.$tableAlias.' ON '.$tableAlias.'.formid = f.id AND '.$tableAlias.'.fieldid='.$this->fieldId.' AND CAST('.$tableAlias.'.content AS SIGNED) >= '.$sliderMin.' AND CAST('.$tableAlias.'.content AS SIGNED) <= '.$sliderMax);
		}
	}
	
	public function getSelectedValue()
	{
		$input			= JFactory::getApplication()->input;
		$postBack		= $input->getInt('pb', 0) && ($input->getInt('moduleid') == $this->moduleId);
		
		if($postBack)
		{
			$elementId = self::getElementId();
			$this->selection = $input->getInt($elementId.'_min', 0) . ';'. $input->getInt($elementId.'_max', 0);
		}
		else 
		{
			// get the minimum and maximum values of the control
			$this->selection = $this->searchFieldSettings->get('isl_min_value').';'.$this->searchFieldSettings->get('isl_max_value'); 
		}
	}
	
	public function getSelectedValueDataView($dataviewFieldList)
	{
		if(array_key_exists($this->id, $dataviewFieldList))
		{
			$dataviewField = $dataviewFieldList[$this->id];
			$selections = new JRegistry($dataviewField->value);
			$this->selection = implode(';', $selections->toArray());
		}
		else
		{
			$this->selection = JFactory::getApplication()->input->getString(self::getElementId(), '');
		}
	}
	
	public static function buildFilterUrl()
	{
		$script 	= array();
		$script[] 	= 'arrFieldValues[i] = field.elementName+\'_min=\' + jQuery("#"+field.elementName).slider("values")[0] + \'&\' + field.elementName + \'_max=\' + jQuery("#"+field.elementName).slider("values")[1];';
		
		return $script;
	}
	
	public static function getResetControlScript()
	{
		$script = 'obj.slider("values", 0, arrSliderValues[obj.attr(\'id\')+"_min"]);';
		$script .= 'obj.slider("values", 1, arrSliderValues[obj.attr(\'id\')+"_max"]);';
		$script .= 'jQuery("#"+obj.attr(\'id\')+"_valuelabel").text(arrSliderValues[obj.attr(\'id\')+"_min"] + " - " + arrSliderValues[obj.attr(\'id\')+"_max"]);';
		return $script;
	}
	
	/*
	 * Get the min and max value for the the slider from the submitted data
	 */
	protected function getElementValueFromInput()
	{
		$input 		= JFactory::getApplication()->input;
		$elementid 	= self::getElementId();
		$postBack	= $input->getInt('pb', 0) && ($input->getInt('moduleid') == $this->moduleId);
		$value		= array();
			 
		if($postBack)
		{
			$value[0] = $input->getInt($elementid.'_min');
			$value[1] = $input->getInt($elementid.'_max');
		}
		else
		{
			$value[0] = $this->searchFieldSettings->get('isl_min_value');
			$value[1] = $this->searchFieldSettings->get('isl_max_value');
		}
		
		return $value;
	}
	
	protected function getElementValueFromDataView()
	{
		$value = array();
		
		// Check if the element is present in the dataview
		if(array_key_exists($this->id, self::$dataView->fields))
		{
			$registry = new JRegistry(self::$dataView->fields[$this->id]->value);
			$value[0] = $registry->get('minval');
			$value[1] = $registry->get('maxval');
		}
		else
		{
			// set the defaults
			$value[0]	= $this->searchFieldSettings->get('isl_min_value');
			$value[1]	= $this->searchFieldSettings->get('isl_max_value');
		}

		return $value;
	}
}
?>