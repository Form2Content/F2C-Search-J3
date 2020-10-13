<?php
defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.controller');

class Form2ContentSearchController extends JControllerLegacy
{
	protected $default_view = 'forms';

	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/form2contentsearch.php';
		
		parent::display();

		return $this;
	}
}