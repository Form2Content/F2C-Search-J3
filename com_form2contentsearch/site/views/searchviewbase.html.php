<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.html.select');
jimport('joomla.application.component.view');

class Form2ContentSearchViewSearchViewBase extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $pagination;
	protected $params;	
	protected $lead_items = array();
	protected $intro_items = array();
	protected $link_items = array();
	protected $columns = 1;
	
	function display($tpl = null)
	{
		if ((int)$this->params->get('classic_layout', 1))
		{
			$this->setLayout('classic');
		}
		
		// Load the language file for com_content
		JFactory::getLanguage()->load('com_content', JPATH_SITE);
		
		parent::display();	
	}
	
	protected function prepareSorting()
	{
		$this->orderingList = '';
		$input 				= JFactory::getApplication()->input;
		
		if($this->params->get('use_custom_ordering', 0))
		{
			JHtml::script('com_form2contentsearch/f2c_search.js', array('relative' => true));
			
			$defaultsorting 	= explode(';', $this->params->get('custom_ordering_default', 'title;ASC;Title;STRING'));
			$arrCustomOrdering 	= preg_split('/$\R?^/m', $this->params->get('custom_ordering',''));
			$linkSeparator 		= $this->params->get('custom_ordering_separator','');
			$linkCount 			= 0;
			 
			if(count($arrCustomOrdering))
			{
				$this->orderingList .= '<div id="f2c_search_custom_ordering">';
								
				$this->orderingList .= '<ul>';
				
				foreach($arrCustomOrdering as $lineOrdering)
				{
					// Only process sorting lines when they have been specified
					if(empty($lineOrdering)) continue;
					
					if($linkCount != 0 && $linkSeparator)
					{
						$this->orderingList .= '<li>'.$linkSeparator.'</li>';
					}
					
					list($sortField, $sortDirection, $sortLabel, $sortType) = explode(';',$lineOrdering);
					
					if($this->params->get('alternate_sorting',0) && strtolower($sortDirection) != 'rand')
					{
						$activeSortField = $input->getString('order_by', $defaultsorting[0]);
						$activeSortDirection = $input->getString('order_dir', $defaultsorting[1]);
						
						if(strtolower($activeSortField) == strtolower($sortField))
						{ 
							$sortDirection = strtolower($activeSortDirection) == 'asc' ? 'DESC' : 'ASC';
						}
					}
					
					$sortType = trim($sortType);
					$this->orderingList .= '<li><a href="#" onclick="F2C_SetCustomOrdering(\''.$sortField.'\',\''.$sortDirection.'\',\''.$sortType.'\');return false;">'.$sortLabel.'</a></li>';
					$linkCount++;					
				}
				
				$this->orderingList .= '</ul>';
				$this->orderingList .= '<input type="hidden" id="f2csearch_order_by" name="order_by" /><input type="hidden" id="f2csearch_order_dir" name="order_dir" /><input type="hidden" id="f2csearch_order_type" name="order_type" />';
				$this->orderingList .= '</div>';
			}
		}
		else 
		{
			$this->orderingList .= '<div id="f2c_searchresults_ordering" class="right">';
			$this->orderingList .= JText::_('COM_FORM2CONTENTSEARCH_RESULTS_ORDERING') . ': ';

			// create the ordering dropdown
			$listOptions = array();
			$listOptions[] = JHTML::_('select.option', 'front', JText::_('COM_FORM2CONTENTSEARCH_FEATURED_ORDER'));		
			$listOptions[] = JHTML::_('select.option', 'rdate', JText::_('COM_FORM2CONTENTSEARCH_MOST_RECENT_FIRST'));
			$listOptions[] = JHTML::_('select.option', 'date', JText::_('COM_FORM2CONTENTSEARCH_OLDEST_FIRST'));
			$listOptions[] = JHTML::_('select.option', 'alpha', JText::_('COM_FORM2CONTENTSEARCH_TITLE_ALPHABETICAL'));
			$listOptions[] = JHTML::_('select.option', 'ralpha', JText::_('COM_FORM2CONTENTSEARCH_TITLE_REVERSE_ALPHABETICAL'));
			$listOptions[] = JHTML::_('select.option', 'author', JText::_('COM_FORM2CONTENTSEARCH_AUTHOR_ALPHABETICAL'));
			$listOptions[] = JHTML::_('select.option', 'rauthor', JText::_('COM_FORM2CONTENTSEARCH_AUTHOR_REVERSE_ALPHABETICAL'));
			$listOptions[] = JHTML::_('select.option', 'hits', JText::_('COM_FORM2CONTENTSEARCH_MOST_HITS'));
			$listOptions[] = JHTML::_('select.option', 'rhits', JText::_('COM_FORM2CONTENTSEARCH_LEAST_HITS'));
			$listOptions[] = JHTML::_('select.option', 'order', JText::_('COM_FORM2CONTENTSEARCH_ORDERING'));
	
			$ordering = $input->getString('orderby_sec') ? $input->getString('orderby_sec') : $this->params->get('orderby_sec');
			$this->orderingList .= JHTML::_('select.genericlist', $listOptions, 'orderby_sec', 'onchange="this.form.submit();"', 'value', 'text', $ordering);		
			
			$this->orderingList .= '</div>';
			
			// Add the ordering parameter to the pagination object
			$this->pagination->setAdditionalUrlParam('orderby_sec', $ordering);			
		}		
	}
	
	protected function getPageTitle($title)
	{
		$config = JFactory::getConfig();
		$sitename = $config->get('sitename');
		
		if(empty($title))
		{
			$title = $sitename;	
		}
		else
		{
			switch($config->get('sitename_pagetitles', 0))
			{
				case 0: // No
					break;
				case 1: // Before
					$title = JText::sprintf('JPAGETITLE', $sitename, $title);
					break;
				case 2: // After
					$title = JText::sprintf('JPAGETITLE', $title, $sitename);
					break;
			}
		}
				
		return $title;		
	}
}
?>