<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controller');

require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'class.form2contentsearch.php');

class Form2ContentSearchControllerSearch extends JControllerLegacy
{
	function gethits()
	{
		$f2cSearch = new F2cSearch();
		echo count($f2cSearch->getFormIds(JFactory::getApplication()->input->getInt('moduleid', 0),JFactory::getApplication()->input->getInt('searchformid', 0)));
	}
}
?>