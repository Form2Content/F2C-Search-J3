<?php
// no direct access
defined('JPATH_PLATFORM') or die;

$formAction = JRoute::_($this->formAction);

$pageClassSuffix 	= htmlspecialchars($this->menuParms->get('pageclass_sfx', $this->params->get('pageclass_sfx')));
$showPageHeading 	= $this->menuParms->get('show_page_heading', 1);
$pageHeader 		= $this->escape($this->menuParms->get('page_heading', $this->params->get('page_heading')));
$description 		= $this->model->dataView->description;

require __DIR__ . '/../../common/default.php';