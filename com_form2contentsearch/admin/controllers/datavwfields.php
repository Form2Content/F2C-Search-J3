<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controlleradmin');

class Form2ContentSearchControllerDatavwFields extends JControllerAdmin
{
	protected $default_view = 'datavwfields';

	public function &getModel($name = 'DatavwField', $prefix = 'Form2ContentSearchModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
	
	function delete()
	{
		parent::delete();
		$this->redirect .= '&datavwid='.JFactory::getApplication()->input->getInt('datavwid');
	}
}
?>