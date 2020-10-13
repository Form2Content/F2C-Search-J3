<?php
class Form2ContentSearchHelperAdmin
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	4.0.0
	 */
	public static function addSubmenu($vName)
	{
		$canDo	= self::getActions();
		
		if ($canDo->get('core.admin'))
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_FORM2CONTENTSEARCH_FORMSMANAGER'),
				'index.php?option=com_form2contentsearch&view=forms',
				$vName == 'forms'
			);

			JHtmlSidebar::addEntry(
				JText::_('COM_FORM2CONTENTSEARCH_DATAVIEWSMANAGER'),
				'index.php?option=com_form2contentsearch&view=datavws',
				$vName == 'datavws'
			);
		}
		
		JHtmlSidebar::addEntry(
			JText::_('COM_FORM2CONTENTSEARCH_ABOUT'),
			'index.php?option=com_form2contentsearch&view=about',
			$vName == 'about'
		);
	}
	
	public static function getActions($categoryId = 0, $formId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_form2contentsearch';

		$actions = array('core.admin');

		foreach ($actions as $action) 
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}	
}
?>
