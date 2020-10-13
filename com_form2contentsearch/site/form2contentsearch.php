<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

defined('F2CSEARCH_DATAFIELD_JTITLE')		or define('F2CSEARCH_DATAFIELD_JTITLE', -1);
defined('F2CSEARCH_DATAFIELD_JMETAKEYS')	or define('F2CSEARCH_DATAFIELD_JMETAKEYS', -2);
defined('F2CSEARCH_DATAFIELD_JMETADESC')	or define('F2CSEARCH_DATAFIELD_JMETADESC', -3);

require_once JPATH_SITE.'/components/com_content/helpers/route.php';

// Include dependancies
jimport('joomla.application.component.controller');

$app 		= JFactory::getApplication();
$menu		= $app->getMenu();
$activeMenu	= $menu->getActive();
$Itemid 	= $app->input->get('Itemid');

if($activeMenu == null && !empty($Itemid))
{
	$menu->setActive($Itemid);
	$app->input->set('view', 'datavw');
}

$view 		= $app->input->get('view');
$task		= $app->input->getCmd('task');

if($activeMenu && $activeMenu->component = 'com_form2contentsearch' && empty($view) && empty($task))
{
	throw new Exception('Invalid request', 404);
}

// Execute the task.
$controller	= JControllerLegacy::getInstance('Form2ContentSearch');
$controller->execute($app->input->getCmd('task'));
$controller->redirect();
?>