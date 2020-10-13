<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.modeladmin');

class Form2ContentSearchModelForm extends JModelAdmin
{
	protected $text_prefix = 'COM_FORM2CONTENTSEARCH';
	public $contentTypeId			= 0;
	
	function __construct($config = array())
	{
		parent::__construct($config);
	
		// try to load the contentType
		$input 					= JFactory::getApplication()->input;
		$this->contentTypeId 	= $input->getInt('projectid', $input->getInt('contenttypeid'));
		
		if($this->contentTypeId == 0)
		{
			if(array_key_exists('jform', $_POST))
			{
				$this->contentTypeId = (int)$_POST['jform']['projectid'];
			}
		}
	}
	
	public function getTable($type = 'Form', $prefix = 'Form2ContentSearchTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	
	public function getItem($pk = null)
	{
		if($item = parent::getItem($pk))
		{
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->attribs);
			$item->attribs = $registry->toArray();
		}
		
		if(!$item->id)
		{	
			// new Form: initialize some values	
			$item->projectid = $this->contentTypeId;
		}		
		
		return $item;
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_form2contentsearch.form', 'form', array('control' => 'jform', 'load_data' => $loadData));
		
		if (empty($form)) 
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_form2contentsearch.edit.form.data', array());
		if (empty($data)) 
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function delete(&$pks)
	{
		// Initialise variables.
		$dispatcher	= JDispatcher::getInstance();
		$user		= JFactory::getUser();
		$pks		= (array) $pks;
		$table		= $this->getTable();
		$db 		= JFactory::getDBO();
		
		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk) 
		{
			if ($table->load($pk)) 
			{
				// check if there are Filtered Article Lists attached to this form
				$query = $db->getQuery(true);
				$query->select('COUNT(*)');
				$query->from('#__f2c_search_dataview');
				$query->where('search_form_id = ' . $pk);
				$db->setQuery($query);
				
				if($db->loadResult())
				{
					unset($pks[$i]);
					JFactory::getApplication()->enqueueMessage(sprintf(JText::_('COM_FORM2CONTENTSEARCH_ERROR_CANT_DELETE_FORM_LINKED_WITH_FAL'), $pk), 'error');
					continue;
				}
				
				if ($this->canDelete($table)) 
				{
					$context = $this->option.'.'.$this->name;

					// Trigger the onContentBeforeDelete event.
					$result = $dispatcher->trigger($this->event_before_delete, array($context, $table));
					if (in_array(false, $result, true)) 
					{
						$this->setError($table->getError());
						return false;
					}

					// Delete the child records
					$query 	= $db->getQuery(true);
					$query->delete('#__f2c_search_formfield');
					$query->where('formid = '.$pk);
					$db->setQuery($query);
					$db->query();
					
					if (!$table->delete($pk)) 
					{
						$this->setError($table->getError());
						return false;
					}

					// Trigger the onContentAfterDelete event.
					$dispatcher->trigger($this->event_after_delete, array($context, $table));

				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error) 
					{
						JFactory::getApplication()->enqueueMessage($error, 'error');
					}
					else 
					{
						JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), 'error');
					}
				}

			}
			else 
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
	
	public function getContentTypeSelectList($publishedOnly = true, $authorizedOnly = true)
	{
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$user 	= JFactory::getUser();
		
		$query->select('id AS value, title AS text');
		$query->from('`#__f2c_project`');				
		if($publishedOnly) $query->where('published = 1');
		$query->order('title'); 
		$db->setQuery($query);
		
		return $db->loadObjectList();			
	}
	
	public function getSearchformList()
	{
		$db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		$query->select('id AS value, title AS text');
		$query->from('`#__f2c_search_form`');				
		$query->order('title'); 
		$db->setQuery($query);
		
		return $db->loadObjectList();			
	}
	
	public function copy(&$pks)
	{
		$db 		= JFactory::getDBO();
		$formTable 	= $this->getTable();	
		$fieldTable = JTable::getInstance('FormField', 'Form2ContentSearchTable');
		
		// Attempt to copy the forms.
		foreach ($pks as $i => $pk) 
		{
			$this->setState($this->getName().'.id', $pk);

			$formTable->reset();

			if(!$formTable->load($pk))
			{
				$this->setError($formTable->getError());
				return false;
			}
						
			$formTable->title = JText::_('COM_FORM2CONTENTSEARCH_COPY_OF') . ' ' . $formTable->title;
			$formTable->id = 0; // force insert
			$formTable->asset_id = null; // force insert
			
			// Perform a check, because this will create the title alias
			if(!$formTable->check())
			{
				$this->setError($formTable->getError());
				return false;
			}
			
			if(!$formTable->store())
			{
				$this->setError($formTable->getError());
				return false;
			}
			
			// Copy the search fields
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__f2c_search_formfield');
			$query->where('formid = ' . (int)$pk);
			$db->setQuery($query);

			$fields = $db->loadObjectList();

			if(count($fields))
			{
				foreach($fields as $field)
				{
					$fieldTable->reset();
		
					if(!$fieldTable->load($field->id))
					{
						$this->setError($fieldTable->getError());
						return false;
					}
								
					$fieldTable->id = 0; // force insert
					$fieldTable->formid = $formTable->id;
					
					// Perform a check, because this will create the title alias
					if(!$fieldTable->check())
					{
						$this->setError($fieldTable->getError());
						return false;
					}
					
					if(!$fieldTable->store())
					{
						$this->setError($fieldTable->getError());
						return false;
					}
				}
			}
		}
				
		return true;
	}
}
?>