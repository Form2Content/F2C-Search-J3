<?php
// no direct access
defined('JPATH_PLATFORM') or die;

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2contentsearch'.DIRECTORY_SEPARATOR.'class.form2contentsearch.php');
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');

if(!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2contentsearch'.DIRECTORY_SEPARATOR.'form2contentsearch.php'))
{
  echo '<span class="alert">Fatal Error: the component <b>com_form2contentsearch</b> is not installed. Please install this component in order to use this module.</span>';
  return;
}

if(!file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_form2content'.DIRECTORY_SEPARATOR.'form2content.php'))
{
  echo '<span class="alert">Fatal Error: the component <b>com_form2content</b> is not installed. Please install this component first in order to use the Form2ContentSearch component and module.<br/>';
  echo 'The Form2Content component can be downloaded from the <a href="http://form2content.com" target="_blank">Opensource Design website</a>.</span>';
  return;
}

$helper					= new modForm2ContentSearchHelper();
$searchForm 			= $helper->getSearchForm($params->get('id', 0));
$moduleId				= $module->id;
$comParams 				= JComponentHelper::getParams( 'com_form2contentsearch');
$autoSearch				= $params->get('auto_search', '0');
$searchFormId 			= $params->get('id', 0);
$forcedItemId 			= $params->get('itemid', '');
$showResultCount		= $autoSearch ? 0 : $params->get('show_result_count', '1');
$showInitialResultCount	= $params->get('show_result_count_start', '1');
$showReset				= $params->get('show_reset', '0');
$resetRefreshResults	= $params->get('reset_refresh_results', '0');
$db 					= JFactory::getDBO();
$searchDelay 			= $comParams->get('searchdelay', 2000);
$searchMinChar 			= $comParams->get('searchminchar', 2);
$f2cSearch 				= new F2cSearch();

$helper->initializeScript();

// only get the results from the querystring when the moduleid = this module
$searchResult = (JFactory::getApplication()->input->getInt('moduleid') == $moduleId) ? JFactory::getApplication()->input->getString('results', '') : '';

if(!$searchResult)
{
	
	$formList = $f2cSearch->getFormIds($moduleId, $searchFormId);
	$numResults = count($formList);
}
else
{
	$numResults = (int)$searchResult;
}

$searchResult = $helper->stringHTMLSafe($searchForm->totals_pre_text) . ' ' . $numResults . ' ' . $helper->stringHTMLSafe($searchForm->totals_post_text);

if($searchDelay != (int)$searchDelay)
{
	$searchDelay = 2000;
}

if($searchMinChar != (int)$searchMinChar)
{
	$searchMinChar = 2;
}

$preResultText = $searchForm->totals_pre_text;

if($preResultText) 
{
	$preResultText .= ' ';
}

$postResultText = $searchForm->totals_post_text;

if($postResultText)
{
	$postResultText = ' ' . $postResultText;
}

// load fields for the search form
$searchFields = $f2cSearch->getSearchformFields($searchFormId, $moduleId);

$jsSearchFields = '';

if(count($searchFields))
{
	$jsSearchFields .= 'var arrMod = new Array();';
	
	foreach($searchFields as $searchField)
	{
		$jsSearchFields .= 'arrMod.push(new F2CSearchField("'.$searchField->getElementId().'",'.$searchField->fieldtypeid.'));';
	}
	
	$jsSearchFields .= 'searchFields['.$moduleId.'] = arrMod;';	
}

require JModuleHelper::getLayoutPath('mod_form2contentsearch', $params->get('template', 'default'));
?>