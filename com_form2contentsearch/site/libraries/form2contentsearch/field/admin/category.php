<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminCategory extends F2csearchFieldAdminBase
{
	public function DisplayFormField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('helptext'); ?></div>
			<div class="controls"><?php echo $form->getInput('helptext'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('emptytext'); ?></div>
			<div class="controls"><?php echo $form->getInput('emptytext'); ?></div>
		</div>		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('attribs'); ?></div>
			<div class="controls"><?php echo $form->getInput('attribs'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('displaymode'); ?></div>
			<div class="controls"><?php echo $form->getInput('displaymode'); ?></div>
		</div>
		
		<h2><?php echo JText::_('COM_FORM2CONTENTSEARCH_ADDITIONAL_SETTINGS'); ?></h2>
		
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('cat_display', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('cat_display', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('cat_filter', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('cat_filter', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('cat_search_below', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('cat_search_below', 'settings'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('cat_ids', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('cat_ids', 'settings'); ?></div>
		</div>
		<?php echo $form->getInput('fieldid');	
	}
	
	public function DisplayDataviewField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('fld_multiselect'); ?></div>
			<div class="controls"><?php echo $form->getInput('fld_multiselect'); ?></div>
		</div>
		<?php 
	}
	
	public function prepareFormField($form, $item)
	{
		// Add some styling to set the height of the category control
		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration("#jform_settings_cat_ids { height: 250px; }");
	}
	
	public function prepareFormDataView($form, $item)
	{
		$regOptions 	= new JRegistry();	
		$categoryList 	= array();
		$db				= JFactory::getDbo();
		$field 			= $this->loadSearchFormField($item->search_form_fieldid);
		$query 			= $db->getQuery(true);
		
		$items 			= self::getCategoryList($field->settings);
		$minIndent		= 1000;
		
		if(count($items))
		{
			if((int)$field->settings->get('cat_display') == 0)
			{
				// determine the lowest indention level for the tree
				foreach ($items as &$item)
				{
					if($item->level < $minIndent)
					{
						$minIndent = $item->level;
					}
				}
			}
			
			$minIndent--;
			
			foreach ($items as &$item) 
			{
				if((int)$field->settings->get('cat_display') == 0)
				{
					// indented tree view
					$repeat = ( $item->level - 1 >= 0 ) ? $item->level - 1 : 0;
					$item->title = str_repeat('- ', $repeat - $minIndent).$item->title;
				}
				else 
				{
					$item->title = $item->title;
				}
				
				$categoryList[$item->id] = $item->title;
			}
		}
				
		$regOptions = new JRegistry();				
		$regOptions->set('options', $categoryList);
		$form->setFieldAttribute('fld_multiselect', 'options', $regOptions->toString());
	}
	
	public function saveDataView(&$data)
	{
		$registry = new JRegistry();
		$registry->loadArray($data['fld_multiselect']);
		$data['value'] = $registry->toString();
	}

	public function getItemDataViewField(&$item)
	{
			$regValues = new JRegistry($item->value);
			$item->fld_multiselect = $regValues->toArray();
	}
	
	private function getCategoryList($fieldSettings)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('a.id, a.title, a.level');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');
		$query->where('extension = \'com_content\'');
		$query->where('a.published = 1');
				
		$catIds = (array)$fieldSettings->get('cat_ids');
		
		switch((int)$fieldSettings->get('cat_filter'))
		{
			case 1:
				// exclude selected categories
				$query->where('a.id NOT IN ('.implode(',', $catIds).')');
				break;
			case 2:
				// limit to selected categories
				$query->where('a.id IN ('.implode(',', $catIds).')');
				break;
		}
		
		switch((int)$fieldSettings->get('cat_display'))
		{
			case 0: // tree view
				$query->order('a.lft');
				break;
			case 1: // alphabetically sorted list
				$query->order('a.title');
				break;
		}

		$db->setQuery($query);
		return $db->loadObjectList();		
	}
}
?>
