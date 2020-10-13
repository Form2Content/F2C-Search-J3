<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2contentsearch'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'searchviewbase.html.php');

jimport('joomla.application.component.view');

class Form2ContentSearchViewDatavw extends Form2ContentSearchViewSearchViewBase
{
	public $formAction = '';
	public $menuParms;
	
	function display($tpl = null)
	{
		$document 		= JFactory::getDocument();
		$app			= JFactory::getApplication();
		$input			= $app->input;
		$extension		= $input->getString('option');
		$createdField 	= 'created';
		$titleField 	= 'title';
		$contentType 	= 'com_content.article';

		//		$document->link = JRoute::_(JHelperRoute::getCategoryRoute($input->getInt('id'), $language = 0, $extension));
		
		$app->input->set('limit', $app->get('feed_limit'));
		
		$siteEmail        = $app->get('mailfrom');
		$fromName         = $app->get('fromname');
		$feedEmail        = $app->get('feed_email', 'author');
		$document->editor = $fromName;

		if ($feedEmail != 'none')
		{
			$document->editorEmail = $siteEmail;
		}
				
		if($input->getInt('Itemid'))
		{
			// Set the Itemid so we don't loose it during pagination
			$config = JFactory::getConfig();
	    	$router = JRouter::getInstance('site');
	    	$router->setVar('Itemid', $input->getInt('Itemid'), true);
		}
		
		$app					= JFactory::getApplication();
		$this->model			= $this->getModel();
		$this->state			= $this->get('State');		
		$this->items			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');		
		$this->params			= $this->model->getParams($this->model->searchFormId);
		$activeMenu				= $app->getMenu()->getActive();
		$this->menuParms		= $activeMenu->params;
		
		$this->params->set('item_count', $this->model->itemCount);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JApplication::getInstance()->enqueueMessage(implode("\n", $errors));
			return false;
		}
		
		// PREPARE THE DATA
		// Get the metrics for the structural page layout.
		$numLeading	= $this->params->def('num_leading_articles', 1);
		$numIntro	= $this->params->def('num_intro_articles', 4);
		$numLinks	= $this->params->def('num_links', 4);
		
		// Fake the com_content component
		$bakOption 	= $input->getString('option');
		$bakView 	= $input->getString('view');
		$bakLayout 	= $input->getString('layout');

		$input->set('option', 'com_content');
		$input->set('view', 'category');
		$input->set('layout', 'blog');
		
		// Compute the article slugs and prepare introtext (runs content plugins).
		for ($i = 0, $n = count($this->items); $i < $n; $i++)
		{
			$item = &$this->items[$i];
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

			// No link for ROOT category
			if ($item->parent_alias == 'root')
			{
				$item->parent_slug = null;
			}

			$item->catslug = $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;
			
			$item->event = new stdClass();

			$dispatcher = JDispatcher::getInstance();

			// Ignore content plugins on links.
			if ($i < $numLeading + $numIntro) 
			{
				$item->introtext = JHtml::_('content.prepare', $item->introtext, '', 'com_content.category');		
				$item->fulltext = null;
				
				$results = $dispatcher->trigger('onContentAfterTitle', array('com_content.article', &$item, &$item->params, 0));
				$item->event->afterDisplayTitle = trim(implode("\n", $results));
				
				$results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.article', &$item, &$item->params, 0));
				$item->event->beforeDisplayContent = trim(implode("\n", $results));

				$results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$item, &$item->params, 0));
				$item->event->afterDisplayContent = trim(implode("\n", $results));
			}
		}
		
		// Reset to com_form2contentsearch component
		$input->set('option', $bakOption);
		$input->set('view', $bakView);
		$input->set('layout', $bakLayout);
				

//		$this->params->set('layout_type', 'blog');
		
		// For blog layouts, preprocess the breakdown of leading, intro and linked articles.
		// This makes it much easier for the designer to just interrogate the arrays.
//		if (($this->params->get('layout_type') == 'blog') || ($this->getLayout() == 'blog')) 
		{
			$max = count($this->items);

			// The first group is the leading articles.
			$limit = $numLeading;
			
			for ($i = 0; $i < $limit && $i < $max; $i++) 
			{
				$this->lead_items[$i] = &$this->items[$i];
			}

			// The second group is the intro articles.
			$limit = $numLeading + $numIntro;
			
			// Order articles across, then down (or single column mode)
			for ($i = $numLeading; $i < $limit && $i < $max; $i++) 
			{
				$this->intro_items[$i] = &$this->items[$i];
			}

			$this->columns = max(1, $this->params->def('num_columns', 1));
			$order = $this->params->def('multi_column_order', 1);

			if ($order == 0 && $this->columns > 1) 
			{
				// call order down helper
				$this->intro_items = ContentHelperQuery::orderDownColumns($this->intro_items, $this->columns);
			}

			$limit = $numLeading + $numIntro + $numLinks;
			// The remainder are the links.
			for ($i = $numLeading + $numIntro; $i < $limit && $i < $max;$i++)
			{
					$this->link_items[$i] = &$this->items[$i];
			}
		}

		// Prepare the form action
		if(JFactory::getConfig()->get('sef'))
		{
			// SEF is enabled
			$this->formAction = 'index.php?option=com_form2contentsearch&view=datavw&Itemid=' . $input->getInt('Itemid');

			if($_SERVER['QUERY_STRING'])
			{
				$this->formAction .= '&' . $_SERVER['QUERY_STRING'];
			}			
		}
		else 
		{
			// No SEF
			$this->formAction = 'index.php?' . $_SERVER['QUERY_STRING'];
		}
		
		$this->prepareSorting();
		
		for ($i = 0, $n = count($this->items); $i < $n; $i++)
		{
			$this->reconcileNames($item);

			// Strip html from feed item title
			if ($titleField)
			{
				$title = $this->escape($item->$titleField);
				$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
			}
			else
			{
				$title = '';
			}

			// URL link to article
			$router = new JHelperRoute;
			$link   = JRoute::_($router->getRoute($item->id, $contentType, null, null, $item->catid));

			// Strip HTML from feed item description text.
			
			//$description = $item->description;
			$description = $item->introtext;
			$author      = $item->created_by_alias ? $item->created_by_alias : $item->author;

			if ($createdField)
			{
				$date = isset($item->$createdField) ? date('r', strtotime($item->$createdField)) : '';
			}
			else
			{
				$date = '';
			}

			// Load individual item creator class.
			$feeditem              = new JFeedItem;
			$feeditem->title       = $title;
			$feeditem->link        = $link;
			$feeditem->description = $description;
			$feeditem->date        = $date;
			$feeditem->category    = $category->title;
			$feeditem->author      = $author;

			// We don't have the author email so we have to use site in both cases.
			if ($feedEmail == 'site')
			{
				$feeditem->authorEmail = $siteEmail;
			}
			elseif ($feedEmail === 'author')
			{
				$feeditem->authorEmail = $item->author_email;
			}

			// Loads item information into RSS array
			$document->addItem($feeditem);			
		}
	}
	
	/**
	 * Method to reconcile non standard names from components to usage in this class.
	 * Typically overriden in the component feed view class.
	 *
	 * @param   object  $item  The item for a feed, an element of the $items array.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function reconcileNames($item)
	{
		if (!property_exists($item, 'title') && property_exists($item, 'name'))
		{
			$item->title = $item->name;
		}
	}
}
?>