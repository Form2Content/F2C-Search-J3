<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.modeladmin');

class Form2ContentSearchModelDatavwField extends JModelAdmin
{
	protected $text_prefix = 'COM_FORM2CONTENTSEARCH';
	protected $formId = 0;
	
	public function getTable($type = 'DatavwField', $prefix = 'Form2ContentSearchTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk); 
		
		if(!$item->dataview_id)
		{
			$item->dataview_id = JFactory::getApplication()->input->getInt('datavwid');
		}
		
		if(!$item->search_form_fieldid)
		{
			$item->search_form_fieldid = JFactory::getApplication()->input->getInt('search_form_fieldid');
		}
		
		// Add the fieldtypeId
		$query = $this->_db->getQuery(true);

		$query->select('ff.fieldtypeid');
		$query->from('#__f2c_search_formfield ff');
		$query->where('ff.id = ' . (int)$item->search_form_fieldid);
		// Join F2C Search field type
		$query->select('ft.name');
		$query->join('INNER', '#__f2c_search_fieldtype ft ON ff.fieldtypeid = ft.id');
		
		$this->_db->setQuery($query);
		
		$objField 			= $this->_db->loadObject();
		$item->fieldtypeid 	= $objField->fieldtypeid;
		$item->datavwid 	= $item->dataview_id;
		$fieldClassName 	= 'F2csearchFieldAdmin'.$objField->name;
		$field				= new $fieldClassName();
		
		$field->getItemDataViewField($item);
		
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
			$formFieldId = array_key_exists('search_form_fieldid', $data) ? $data['search_form_fieldid'] : $input->get('search_form_fieldid');
			
			$query->select('ft.name');
			$query->from('#__f2c_search_formfield ff');
			$query->join('INNER', '#__f2c_search_fieldtype ft ON ff.fieldtypeid = ft.id');
			$query->where('ff.id='.$formFieldId);	
		}
		else 
		{
			// existing field
			$query->select('ft.name');
			$query->from('#__f2c_search_dataviewfield df');
			$query->join('INNER', '#__f2c_search_formfield ff ON df.search_form_fieldid = ff.id');
			$query->join('INNER', '#__f2c_search_fieldtype ft ON ff.fieldtypeid = ft.id');
			$query->where('df.id='.$input->getInt('id'));
		}
		
		$db->setQuery($query);
		$fieldname = strtolower($db->loadResult());
		
		// Get the form.
		$form = $this->loadForm('com_form2contentsearch.datavwfield', JPATH_COMPONENT_SITE.'/libraries/form2contentsearch/field/admin/forms/dataviewfield/'.$fieldname.'.xml', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) 
		{
			return false;
		}

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
		
		if($data = $field->validateDataviewField($this, $form, $data, $group))
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
		$data = JFactory::getApplication()->getUserState('com_form2contentsearch.edit.datavwfield.data', array());

		if (empty($data)) 
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function getReorderConditions($table = null)
	{
		$condition = array();
		$condition[] = 'datavwid = '.(int) $table->datavwid;
		return $condition;
	}

	public function getSearchFormFieldList($dataViewId)
	{
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->select(' ff.id, ff.title, ft.description');
		$query->from('#__f2c_search_dataview dv');
		$query->join('INNER', '#__f2c_search_formfield ff ON dv.search_form_id = ff.formid');
		$query->join('INNER', '#__f2c_search_fieldtype ft ON ff.fieldtypeid = ft.id');	
		$query->where('dv.id = ' . (int)$dataViewId);
		$query->order('ff.title');
		$db->setQuery($query);		
	
		$objList = $db->loadObjectList();

		if(count($objList))
		{
			foreach($objList as $obj)
			{
				$obj->title .= ' ('.$obj->description.')';				
			}
		}
		
		return 	$objList;
	}
	
	public function save($data)
	{
		$field = $this->getField((int)$data['fieldtypeid']);
		
		$field->saveDataView($data);

		$data['dataview_id'] = $data['datavwid'];

		return parent::save($data);
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
}
?>