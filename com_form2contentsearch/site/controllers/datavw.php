<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'class.form2contentsearch.php');
require_once JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'query.php';

class Form2ContentSearchControllerDatavw extends JControllerLegacy
{
	public function &getModel($name = 'DataVw', $prefix = 'Form2ContentSearchModel')
	{
		$model = parent::getModel($name, $prefix, array());
		
		return $model;
	}
		
	function display()
	{
		JFactory::getApplication()->input->set('view', 'datavw');
		parent::display();
	}
}
?>