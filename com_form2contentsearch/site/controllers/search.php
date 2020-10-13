<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'class.form2contentsearch.php');
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'query.php';

class Form2ContentSearchControllerSearch extends JControllerLegacy
{
	public function &getModel($name = 'Search', $prefix = 'Form2ContentSearchModel', $config = array())
	{
		//$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		$model = parent::getModel($name, $prefix, array());
		
		return $model;
	}
	
	function display($cachable = false, $urlparams = array())
	{
		JFactory::getApplication()->input->set('view', 'search');
		parent::display();
	}
}
?>