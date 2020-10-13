<?php
defined('_JEXEC') or die('Restricted acccess');

jimport('joomla.application.component.controller');

defined('F2CSEARCH_DATAFIELD_JTITLE')		or define('F2CSEARCH_DATAFIELD_JTITLE', -1);
defined('F2CSEARCH_DATAFIELD_JMETAKEYS')	or define('F2CSEARCH_DATAFIELD_JMETAKEYS', -2);
defined('F2CSEARCH_DATAFIELD_JMETADESC')	or define('F2CSEARCH_DATAFIELD_JMETADESC', -3);

// Access check.
if (!JFactory::getUser()->authorise('core.admin', 'com_form2contentsearch')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

JLoader::register('Form2ContentSearchHelperAdmin', __DIR__ . '/helpers/form2contentsearch.php');
JLoader::registerPrefix('F2csearch', JPATH_SITE.'/components/com_form2contentsearch/libraries/form2contentsearch');

$controller = JControllerLegacy::getInstance('Form2ContentSearch');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();
?>