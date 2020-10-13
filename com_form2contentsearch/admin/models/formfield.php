<?php
defined('_JEXEC') or die('Restricted acccess');

jimport('joomla.application.component.modeladmin');
jimport('joomla.filesystem.file');

class Form2ContentSearchModelFormField extends JModelAdmin
{
	protected $text_prefix = 'COM_FORM2CONTENTSEARCH';
	protected $formId = 0;
	
	public function getTable($type = 'FormField', $prefix = 'Form2ContentSearchTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getItem($pk = null)
	{
		$input	= JFactory::getApplication()->input;
		$item 	= parent::getItem($pk); 

		// Convert the settings field to an array.
		$registry = new JRegistry;
		$registry->loadString($item->settings);			
		$item->settings = $registry->toArray();			
		
		if(!$item->formid)
		{
			$item->formid = $input->getInt('formid');
		}

		if(!$item->fieldid)
		{
			$item->fieldid = $input->getString('fieldid');
		}

		if(!$item->fieldtypeid)
		{
			$item->fieldtypeid = $input->getInt('fieldtypeid');
		}
		
		$this->formId = $item->formid;
		
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// get the field name
		$input 	= JFactory::getApplication()->input;
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		if($input->getInt('id', null) == null)
		{
			// new field
			$fieldTypeId = array_key_exists('fieldtypeid', $data) ? $data['fieldtypeid'] : $input->get('fieldtypeid');
			$query->select('name')->from('#__f2c_search_fieldtype')->where('id='.$fieldTypeId);
		}
		else 
		{
			// existing field
			$query->select('flt.name');
			$query->from('#__f2c_search_formfield ff');
			$query->join('INNER', '#__f2c_search_fieldtype flt ON ff.fieldtypeid = flt.id');
			$query->where('ff.id='.$input->getInt('id'));
		}
		
		$db->setQuery($query);
		$fieldname = strtolower($db->loadResult());
		
		// Get the form.
		$form = $this->loadForm('com_form2contentsearch.formfield', JPATH_COMPONENT_SITE.'/libraries/form2contentsearch/field/admin/forms/formfield/'.$fieldname.'.xml', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) 
		{
			return false;
		}

		$query = $this->_db->getQuery(true);
		
		$form->setFieldAttribute('fieldid', 'formid', $this->formId);
		
		return $form;
	}	

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   6.3.0
	 */
	public function validate($form, $data, $group = null)
	{
		$field = $this->getField((int)$data['fieldtypeid']);
		
		if($data = $field->validateFormField($this, $form, $data, $group))
		{
			return parent::validate($form, $data, $group);
		}
		else 
		{
			return false;
		}
	}
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_form2contentsearch.edit.formfield.data', array());

		if (empty($data)) 
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function prepareTable($table)
	{
		if (empty($table->id)) 
		{
			$table->reorder('formid = '.(int) $table->formid);
		}
	}
	
	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'formid = '.(int) $table->formid;
		return $condition;
	}

	private function getContentTypeFieldId($fieldId)
	{
		$db = $this->getDbo();
		$db->setQuery('SELECT fieldtypeid FROM #__f2c_projectfields WHERE id = ' . (int)$fieldId);
		
		return $db->loadResult();
	}
	
	public function getSearchFormFieldList($searchFormId)
	{
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->select('id, description AS fieldname')->from('#__f2c_search_fieldtype')->order('description ASC');
		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	public function getField($fieldtypeid)
	{
		$db		= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('name')->from('#__f2c_search_fieldtype')->where('id = '. $fieldtypeid);
		$db->setQuery($query);
		
		$fieldClassName = 'F2csearchFieldAdmin'.$db->loadResult();
		return new $fieldClassName();
	}
	
	/**
	 * Load the default form with mandatory fields and add them to the user defined form
	 *
	 * @param	JForm	$form	Form object
	 * @param	object	$data	array of submitted data
	 * @param	string	$group	group name
	 * 
	 * @return  void
	 * 
	 * @since   6.3.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		// load the default form with the mandatory controls
		$defaultForm = new SimpleXMLElement(JFile::read(JPATH_COMPONENT_ADMINISTRATOR.'/models/forms/formfield.xml'));
		
		foreach($defaultForm->fieldset->field as $xmlField)
		{
			// Add the field to the current form
			$form->setField($xmlField);
		}
	}
}
?>