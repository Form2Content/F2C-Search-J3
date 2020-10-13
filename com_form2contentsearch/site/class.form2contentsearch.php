<?php 
defined('JPATH_PLATFORM') or die;

class F2cSearch
{
	/*
	 * Get the fields on the Search form plus their FieldType and Settings
	 */
	function getSearchformFields($searchFormId, $moduleId)
	{
		// TODO: remove
		JLoader::registerPrefix('F2csearch', JPATH_SITE.'/components/com_form2contentsearch/libraries/form2contentsearch');
		
		$searchFields 	= array();
		$db				= JFactory::getDBO();
		$query 			= $db->getQuery(true);
		
		$query->select('ff.id, ff.fieldid, ff.caption, ff.helptext, ff.ordering, ff.formid, ff.attribs, ff.emptytext, ff.displaymode, ff.settings as searchfieldsettings, ff.fieldtypeid as searchfieldtypeid');
		$query->from('#__f2c_search_formfield ff');
		// join the Content Type Field type and settings information
		$query->select('pf.fieldtypeid, pf.settings');
		$query->join('LEFT', '#__f2c_projectfields pf ON ff.fieldid = pf.id');
		// join the field information
		$query->select('ft.name as fieldName');
		$query->join('INNER', '#__f2c_search_fieldtype ft ON ff.fieldtypeid = ft.id');
		$query->where('ff.formid = ' . (int)$searchFormId);
		$query->order('ff.ordering');
		
		$db->setQuery($query);

		$list = $db->loadObjectList();
		
		if(count($list))
		{
			foreach($list as $listItem)
			{
				$listItem->searchfieldsettings	= new JRegistry($listItem->searchfieldsettings);
				$listItem->fieldtypeid 			= $listItem->searchfieldtypeid;
				$searchFieldClass 				= 'F2csearchField'.$listItem->fieldName;
				$searchFields[] 				= new $searchFieldClass($listItem, $moduleId); 
			}	
		}

		return $searchFields;
	}	
	
	function getFormIds($moduleId = 0, $searchFormId = 0, $dataViewId = null)
	{
		$db 					= JFactory::getDBO();		
		$currentDate 			= JFactory::getDate();
		$nullDate 				= $db->getNullDate();
		$multiSelectFields 		= array();
		$joinStatement 			= '';	
		$dataViewFields			= null;	
				
		if(!$searchFormId)
		{
			$searchFormId = JFactory::getApplication()->input->getInt('searchformid', 0);
		}

		if(!$moduleId)
		{
			$moduleId = JFactory::getApplication()->input->getInt('moduleid', 0);
		}
		
		// load the ContentType Id
		$query = $db->getQuery(true);
		$query->select('projectid');
		$query->from('#__f2c_search_form');
		$query->where('id=' . (int)$searchFormId);	
		$db->setQuery($query);
		$contentTypeId = $db->loadResult();
		
		// load fields for the search form
		$searchFieldList 	= self::getSearchformFields($searchFormId, $moduleId);
		$dataViewFields 	= $this->getDataViewFields($dataViewId);

		$query = $db->getQuery(true);
		
		$query->select('f.id AS formid, f.reference_id');
		$query->from('#__f2c_form f');
		$query->where('f.projectid = ' . (int)$contentTypeId);

		// Only select published articles
		$query->join('INNER', '#__content c ON f.reference_id = c.id');
		$query->where('c.state = 1');
		$query->where('(c.publish_up < \''.$currentDate->toSql().'\' OR c.publish_up = \''.$nullDate.'\')');
		$query->where('(\''.$currentDate->toSql().'\' < c.publish_down OR c.publish_down = \''.$nullDate.'\')');
		
		if(count($searchFieldList))
		{
			foreach($searchFieldList as $searchField)
			{			
				$searchField->getFormIdList($query, $dataViewFields);
			}
		}
		
//echo(str_ireplace('#__', 'jos_', $query->__toString()));//die();

		$db->setQuery($query);
		
		$fieldContentList = $db->loadObjectList('formid');
		
		return $fieldContentList;
	}
	
	private function getDataViewFields($dataViewId)
	{
		$dataViewFields = null;
		
		if($dataViewId)
		{
			$db	= JFactory::getDbo();
			
			// load the field values as specified in the data view
			$query = $db->getQuery(true);
	
			$query->select('search_form_fieldid, value');
			$query->from('#__f2c_search_dataviewfield');
			$query->where('dataview_id = ' . (int)$dataViewId);
			
			$db->setQuery($query);
			$dataViewFields = $db->loadObjectList('search_form_fieldid');
		}
		
		return $dataViewFields;
	}
}
?>