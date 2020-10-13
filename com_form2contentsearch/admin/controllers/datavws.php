<?php
// No direct access.
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controlleradmin');

class Form2ContentSearchControllerDatavws extends JControllerAdmin
{
	protected $default_view = 'datavws';

	public function __construct($config = array())
	{
		// Access check.
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			return JError::raiseError(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		parent::__construct($config);
	}

	public function &getModel($name = 'Datavw', $prefix = 'Form2ContentSearchModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
?>