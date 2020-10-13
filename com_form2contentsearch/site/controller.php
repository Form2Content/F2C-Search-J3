<?php
// No direct access
defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.controller');

class Form2ContentSearchController extends JControllerLegacy
{
	protected $default_view = 'search';

	public function display($cachable = false, $urlparams = false)
	{ 
		parent::display();
		return $this;
	}	
}