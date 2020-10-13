<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.modeladmin');

class Form2ContentSearchModelDatavw extends JModelAdmin
{
	protected $text_prefix 	= 'COM_FORM2CONTENTSEARCH';
	public $searchFormId	= 0;
	
	function __construct($config = array())
	{
		parent::__construct($config);
	
		// try to load the contentType
		$this->searchFormId = JFactory::getApplication()->input->getInt('searchformid');
		
		if($this->searchFormId == 0)
		{
			if(array_key_exists('jform', $_POST))
			{
				$this->searchFormId = (int)$_POST['jform']['search_form_id'];
			}
		}
	}
	
	public function getTable($type = 'Datavw', $prefix = 'Form2ContentSearchTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);
		
		if(!$item->id)
		{	
			// new Form: initialize some values	
			$item->search_form_id = $this->searchFormId;
		}		
		
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_form2contentsearch.datavw', 'datavw', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) 
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_form2contentsearch.edit.datavw.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function delete(&$pks)
	{
		// remove all dataview fields
		if(count($pks))
		{
			$query = $this->_db->getQuery(true);			
			$query->delete('#__f2c_search_dataviewfield');
			$query->where('dataview_id IN (' . implode(',', $pks) . ')');
			$this->_db->setQuery($query);
			$this->_db->query();			
		}
		
		return parent::delete($pks);
	}
	
}
?>