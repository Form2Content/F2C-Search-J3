<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

jimport('joomla.application.component.controllerform');

class Form2ContentSearchControllerDatavwField extends JControllerForm
{
	public function __construct($config = array())
	{
		// Access check.
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		parent::__construct($config);
	}
	
	protected function getRedirectToListAppend()
	{
		$input		= JFactory::getApplication()->input;
		$tmpl		= $input->getString('tmpl');
		$dataViewId	= $input->getInt('datavwid');
		$append		= '';

		if(empty($dataViewId))
		{
			$jform 		= $input->get('jform', array(), 'array');
			$dataViewId	= (int)$jform['datavwid'];
		}
		
		// Setup redirect info.
		if ($tmpl) 
		{
			$append .= '&tmpl='.$tmpl;
		}

		$append .= '&datavwid=' . $dataViewId;
		
		return $append;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$redirect = parent::getRedirectToItemAppend($recordId, $urlVar);
	
		if($dataViewId = JFactory::getApplication()->input->getInt('datavwid'))
		{
			$redirect .= '&datavwid=' . $dataViewId;
		}
		
		return $redirect;
	}
	
	function add()
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$context	= "$this->option.edit.$this->context";

		// Access check.
		if (!$this->allowAdd()) 
		{
			// Set the internal error and also the redirect error.
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list.$this->getRedirectToListAppend(), false));

			return false;
		}

		// Clear the record edit information from the session.
		$app->setUserState($context.'.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend().'&search_form_fieldid='.$app->input->getInt('search_form_fieldid'), false));

		return true;
	}
	
	function fieldselect()
	{
		$view = $this->getView('datavwfieldselect', 'html');
		$view->setModel( $this->getModel('datavwfield'), true);
		$view->display();
	}
	
	public function save($key = null, $urlVar = null)
	{
		if(parent::save($key, $urlVar))
		{

			switch($this->getTask())
			{
				case 'save2new':
					$jform = JFactory::getApplication()->input->get('jform', array(), 'array');
					$this->redirect = 'index.php?option=com_form2contentsearch&view=formfield&task=datavwfield.fieldselect&datavwid='.(int)$jform['datavwid'];
					return true;
					break;
				default:
					break;
			}
			
			return true;
		}
		else 
		{
			return false;
		}
	}
}
?>