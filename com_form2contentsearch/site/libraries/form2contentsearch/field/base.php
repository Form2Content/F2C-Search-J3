<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

/**
 * Custom field base class
 * 
 * This class supports functionality for custom fields e.g. for rendering them.
 * All custom fields must implement this class.
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2contentsearch
 * @since       6.3.0
 */
abstract class F2csearchFieldBase
{
	/**
	 * The id of the field.
	 *
	 * @var    int
	 * @since  6.2.0
	 */
	public $id;
	
	/**
	 * The id of the form the field belongs to.
	 *
	 * @var    int
	 * @since  6.2.0
	 */
	public $formId;
	
	/**
	 * The type id of the field.
	 *
	 * @var    int
	 * @since  6.2.0
	 */
	public $fieldId;
	
	/**
	 * The id of module the field is shown on.
	 *
	 * @var    int
	 * @since  6.2.0
	 */
	protected $moduleId;
		
	/**
	 * Object with project field settings
	 *
	 * @var    JRegistry
	 * @since  6.2.0
	 */
	protected $settings;

	/**
	 * Object with search field settings
	 *
	 * @var    JRegistry
	 * @since  6.2.0
	 */
	public $searchFieldSettings;
	
	/**
	 * Text to show when user has not made a selection
	 *
	 * @var    string
	 * @since  6.2.0
	 */
	protected $emptyText;
	
	/**
	 * CSS attributes for the field
	 *
	 * @var    string
	 * @since  6.2.0
	 */
	protected $attribs;
	
	/**
	 * Selected value(s) for the field
	 *
	 * @var    mixed
	 * @since  6.2.0
	 */
	protected $selection;
	
	/**
	 * Display mode for the field
	 *
	 * @var    string
	 * @since  6.2.0
	 */
	protected $displayMode;
	
	/**
	 * Dataview object for initialization of fields
	 *
	 * @var    string
	 * @since  6.2.0
	 */
	public static $dataView = null;
	
	public $fieldtypeid;
	public $searchfieldtypeid;
	public $caption;
	public $helptext;
		
	/**
	 * Renders the control in the search module
	 *
	 * @param	int			$moduleId	Id of the module the control is rendered in
	 * 
	 * @return  string		Generated HTML for the control
	 * 
	 * @since   6.2.0
	 */
	abstract public function render($moduleId);
	
	/**
	 * Build the "where" part of the query to filter the results on the criteria selected in the control
	 *
	 * @param	JDatabaseQuery	$query		The select query to append the where clause to
	 * 
	 * @return  void
	 * 
	 * @since   6.2.0
	 */
	abstract public function getFormIds($query);
	
	/**
	 * Fill the $this->selection property with the control's filter value submitted by the user.
	 *
	 * @return  void
	 * 
	 * @since   6.2.0
	 */
	abstract public function getSelectedValue();
	
	/**
	 * Constructor. This method will initialize the basic field parameters
	 *
	 * @param	object		$field		Field object as created from the database information
	 * @param	int			$moduleId	Id of the module the control is rendered in
	 * 
	 * @return  void
	 * 
	 * @since   6.2.0
	 */
	function __construct($field, $moduleId)
	{
		$this->id 					= $field->id;
		$this->formId 				= $field->formid;
		$this->fieldId 				= $field->fieldid;
		$this->emptyText 			= $field->emptytext;
		$this->attribs 				= $field->attribs;
		$this->displayMode			= $field->displaymode;		
		$this->searchFieldSettings	= $field->searchfieldsettings;
		$this->settings 			= new JRegistry($field->settings);
		$this->moduleId 			= $moduleId;
				
		// TODO: remove?
		$this->fieldtypeid			= $field->fieldtypeid;
		$this->searchfieldtypeid	= $field->searchfieldtypeid;
		$this->caption 				= $field->caption;
		$this->helptext 			= $field->helptext;

	}
	
	public function getFormIdList($query, $dataViewFields)
	{
		if($dataViewFields)
		{
			$this->getSelectedValueDataView($dataViewFields);
		}
		else
		{
			$this->getSelectedValue();
		}
		
		return $this->getFormIds($query);
	}
	
	public function getSelectedValueDataView($dataviewFieldList)
	{
		if(array_key_exists($this->id, $dataviewFieldList))
		{
			$dataviewField = $dataviewFieldList[$this->id];
			
			if($dataviewField->value)
			{
				$registry = new JRegistry($dataviewField->value);
				$this->selection = implode(',', $registry->toArray());
			}
			else
			{
				$this->selection = '';
			}		
		}
		else
		{
			$this->selection = JFactory::getApplication()->input->getString(self::getElementId(), '');
		}
	}	
	
	/**
	 * Get the Id of the element. This id is also used in the Javascript code.
	 *
	 * @return  string		Id of the element
	 * 
	 * @since   6.2.0
	 */
	public function getElementId()
	{
		return 'f2cs_'.$this->moduleId.'_'.$this->id.'_'.$this->formId;
	}
	
	/**
	 * Detect if the input string contains UTF-8 characters
	 *
	 * @param	string		Id of the element
	 *
	 * @return  boolean		True if string contains UTF-8 characters, else False.
	 * 
	 * @since   6.2.0
	 */
	protected function detectUTF8($string)
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

	protected function stringHTMLSafe($string)
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
	
	public static function getResetControlScript()
	{
		return 'obj.prop(\'selectedIndex\', (obj.prop(\'type\') == \'select-one\') ? 0 : -1);';
	}
	
	protected function getElementValue()
	{
		if(self::$dataView != null)
		{
			return $this->getElementValueFromDataView();
			//print_r(self::$dataView);die();
		}
		else 
		{
			return $this->getElementValueFromInput();
		}
	}
	
	protected function getElementValueFromInput()
	{
		return JFactory::getApplication()->input->getString(self::getElementId(), '');
	}
	
	protected function getElementValueFromDataView()
	{
			// Check if the element is present in the dataview
			if(array_key_exists($this->id, self::$dataView->fields))
			{
				die('id '.$this->id.' is present');				
			}
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
		return true;
	}
}