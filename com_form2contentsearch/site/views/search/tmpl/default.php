<?php
// no direct access
defined('JPATH_PLATFORM') or die;

$formAction = JURI::current().'?'.$_SERVER['QUERY_STRING'];

$pageClassSuffix 	= htmlspecialchars($this->params->get('pageclass_sfx'));
$showPageHeading 	= $this->params->get('show_page_heading', 1);
$pageHeader 		= $this->escape($this->params->get('page_heading'));
$description 		= null;

require __DIR__ . '/../../common/default.php';