<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

/**
 * Custom field admin base class
 * 
 * This class supports admin functionality for custom fields e.g. for rendering them.
 * All custom fields must implement this class.
 * 
 * @package     Joomla.Site
 * @subpackage  com_form2contentsearch
 * @since       6.3.0
 */
abstract class F2csearchFieldAdminBase
{
	abstract public function DisplayFormField($form);
	abstract public function DisplayDataviewField($form);
	abstract public function prepareFormDataView($form, $item);
	abstract public function saveDataView(&$data);
	abstract public function getItemDataViewField(&$item);
	
	public function validateFormField($model, $form, $data, $group = null)
	{
		return $data;
	}
	
	public function validateDataviewField($model, $form, $data, $group = null)
	{
		return $data;
	}
	
	public function prepareFormField($form, $item)
	{
	}
	
	public function getValidationScriptFormField()
	{
		
	}

	public function getValidationScriptDataViewField()
	{
		
	}
	
	protected function loadContentTypeField($searcFormFieldId)
	{
		$db		= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('pf.*');
		$query->from('#__f2c_projectfields pf');
		$query->join('INNER', '#__f2c_search_formfield ff ON ff.fieldid = pf.id');
		$query->where('ff.id = ' . (int)$searcFormFieldId);
		$db->setQuery($query);
						
		$field = $db->loadObject();

		$field->settings = new JRegistry($field->settings);
		
		return $field;
	}
	
	protected function loadSearchFormField($searcFormFieldId)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('ff.*');
		$query->from('#__f2c_search_formfield ff');
		$query->where('ff.id = ' . (int)$searcFormFieldId);
		$db->setQuery($query);
								
		$field = $db->loadObject();

		$field->settings = new JRegistry($field->settings);
		
		return $field;
	}
	
	protected function getContentTypeFieldListQuery($formId, $fields)
	{
		$db 	= JFactory::getDbo();
		$query 	= $db->getQuery(true);
		
		$query->select('pf.id, pf.fieldname');
		$query->from('#__f2c_projectfields pf');
		$query->join('INNER', '#__f2c_fieldtype ft ON pf.fieldtypeid = ft.id');
		$query->join('INNER', '#__f2c_search_form sf ON sf.projectid = pf.projectid');
		$query->where('ft.name IN (\''.join('\',\'', $fields).'\')');
		$query->where('sf.id = '.(int)$formId);
		
		return $query->__toString();
	}
	
}