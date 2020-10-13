<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2contentsearch'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'searchviewbase.html.php');

jimport('joomla.application.component.view');

class Form2ContentSearchViewSearch extends Form2ContentSearchViewSearchViewBase
{	
	public $menuParms;
	
	public function __construct($config = array())
	{
		// To keep Search view compatible with Filtered article list view
		$this->menuParms = new JRegistry();
		parent::__construct($config);
	}
	
	function display($tpl = null)
	{
		$document 	= JFactory::getDocument();
		$input		= JFactory::getApplication()->input;
		
		$document->addStyleSheet(JURI::root(true) . '/media/com_form2contentsearch/css/f2csearch.css');
		
		if($input->getInt('Itemid'))
		{
			// Set the Itemid so we don't loose it during pagination
			$config = JFactory::getConfig();
	    	$router = JRouter::getInstance('site');
	    	$router->setVar('Itemid', $input->getInt('Itemid'), true);
		}
		
		$app				= JFactory::getApplication();
		$model 				= $this->getModel();
		$this->state		= $this->get('State');		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');		
		$this->params		= $model->getParams($input->getInt('searchformid'));
		$activeMenu			= $app->getMenu()->getActive();
		
		if($activeMenu)
		{
			$this->menuParms = $activeMenu->params;
		}
		else 
		{
			$this->menuParms = $this->params;
		}
		
		$this->params->set('item_count', $model->itemCount);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JApplication::getInstance()->enqueueMessage(implode("\n", $errors));
			return false;
		}
						
		$this->document->setTitle($this->getPageTitle($this->params->get('page_title')));		
		
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
				
		if($bakLayout)
		{
    		$this->pagination->setAdditionalUrlParam('layout', $bakLayout);
		}
		
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
				
		/*
		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		$active	= $app->getMenu()->getActive();
		if ((!$active) || ((strpos($active->link, 'view=category') === false) || (strpos($active->link, '&id=' . (string) $category->id) === false))) {
			// Get the layout from the merged category params
			if ($layout = $category->params->get('category_layout')) {
				$this->setLayout($layout);
			}
		}		
		// At this point, we are in a menu item, so we don't override the layout
		elseif (isset($active->query['layout'])) {
			// We need to set the layout from the query in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}
		*/

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

		$this->prepareSorting();
				
		parent::display($tpl);
	}
	
}
?>