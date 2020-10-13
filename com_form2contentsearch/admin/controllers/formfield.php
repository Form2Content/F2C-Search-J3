<?php
defined('_JEXEC') or die('Restricted acccess');

jimport('joomla.application.component.controllerform');

class Form2ContentSearchControllerFormfield extends JControllerForm
{
	public function __construct($config = array())
	{
		// Access check.
		if (!JFactory::getUser()->authorise('core.admin')) 
		{
			return JError::raiseError(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}
		
		parent::__construct($config);
	}
	
	protected function getRedirectToListAppend()
	{
		$input		= JFactory::getApplication()->input;
		$tmpl		= $input->getString('tmpl');
		$formId		= $input->getInt('formid');
		$append		= '';

		if(empty($formId))
		{
			$jform 	= $input->get('jform', array(), 'array');
			$formId	= (int)$jform['formid'];
		}
		
		// Setup redirect info.
		if ($tmpl) 
		{
			$append .= '&tmpl='.$tmpl;
		}

		$append .= '&formid=' . $formId;
		
		return $append;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$redirect = parent::getRedirectToItemAppend($recordId, $urlVar);
	
		if($formId = JFactory::getApplication()->input->getInt('formid'))
		{
			$redirect .= '&formid=' . $formId;
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
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item.$this->getRedirectToItemAppend().'&fieldtypeid='.$app->input->getString('fieldtypeid'), false));

		return true;
	}
	
	function fieldselect()
	{
		$view = $this->getView('formfieldselect', 'html');
		$view->setModel( $this->getModel('formfield'), true);
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
					$this->redirect = 'index.php?option=com_form2contentsearch&view=formfield&task=formfield.fieldselect&formid='.(int)$jform['formid'];
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