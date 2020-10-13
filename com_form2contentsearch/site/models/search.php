<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2contentsearch'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'searchbase.php');

jimport('joomla.application.component.modellist');

class Form2ContentSearchModelSearch extends Form2ContentSearchModelSearchBase
{
	var $globalParams = null;
	var $itemCount = 0;
	
	public function __construct($config = array())
	{
		$this->globalParams = $this->getParams(JFactory::getApplication()->input->getInt('searchformid'));		
		parent::__construct($config);
	}
	
	public function getListQuery()
	{
		$f2cSearch		= new F2cSearch();
		$contentIds 	= array();
		$formDataList 	= $f2cSearch->getFormIds();
		$db 			= $this->getDbo();
		$query 			= $db->getQuery(true);
		
		if(count($formDataList))
		{
			foreach($formDataList as $formData)
			{
				$contentIds[$formData->reference_id] = $formData->reference_id;
			}
		}
		else
		{
			// non-existing ID to create an empty resultset
			$contentIds[-1] = -1;
		}
		
		$query->select('a.id, a.title, a.alias, NULL as parent_alias, a.introtext');
		$query->select('a.fulltext, a.state, a.catid, a.created');
		$query->select('a.created_by, a.created_by_alias, a.modified');
		$query->select('a.modified_by, a.checked_out, a.checked_out_time');
		$query->select('a.publish_up, a.publish_down, a.attribs, a.hits');
		$query->select('a.images, a.urls, a.ordering, a.metakey, a.metadesc, a.access');
		$query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug');
		$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as catslug');
		$query->select('LENGTH(a.fulltext) AS readmore');
		$query->from('#__content AS a');
		$query->where('a.state = 1');
		
		// Restrict to the list of filtered content Ids provided by the search function
		$query->where('a.id IN (' . implode(',', $contentIds) .')');
		
		// Join over the users for the author and modified_by names.
		$query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author");
		$query->select("ua.email AS author_email");
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');
		
		// Join on contact table
		$query->select('contact.id as contactid' ) ;
		$query->join('LEFT','#__contact_details AS contact on contact.user_id = a.created_by AND a.created_by <> 0');
		
		// Join over the categories.
		$query->select('c.title AS category_title, c.path AS category_route, c.access AS category_access, c.alias AS category_alias');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		
		// Join over the categories to get parent category titles
		$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
		$query->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');
		
		// Join on voting table
		$query->select('ROUND( v.rating_sum / v.rating_count ) AS rating, v.rating_count as rating_count');
		$query->join('LEFT', '#__content_rating AS v ON a.id = v.content_id');
		
		// Join over the frontpage articles.
		$query->join('LEFT', '#__content_frontpage AS fp ON fp.content_id = a.id');
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) 
		{
			$user	= JFactory::getUser();
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}
		
		// See if we need to join extra fields for the sorting
		if($this->globalParams->get('use_custom_ordering', 0))
		{
			$sortField = $this->getState('list.sortfield');
			$sortDirection = strtolower($this->getState('list.sortdir'));
			
			if($sortDirection == 'rand')
			{
				// perform random sorting
				$query->select('RAND() AS sortfield');
			}
			else
			{
				if(is_numeric($sortField))
				{
					$query->join('INNER', '#__f2c_form AS frm ON a.id = frm.reference_id');
					$query->join('LEFT', '#__f2c_fieldcontent AS srt ON frm.id = srt.formid AND srt.fieldid = ' . (int)$sortField);
					
					switch(strtolower($this->getState('list.sorttype')))
					{
						case 'date':
							$query->select('CAST(srt.content AS DATETIME) AS sortfield');
							break;
						case 'int':
							$query->select('CAST(srt.content AS SIGNED) AS sortfield');
							break;
						case 'float':
							$query->select('CAST(srt.content AS DECIMAL(12,6)) AS sortfield');
							break;
						case 'string':
							$query->select('srt.content AS sortfield');
							break;
					}
				}
			}
		}
		
		$query->order($this->_buildContentOrderBy());
		
		return $query;
	}
}
?>
