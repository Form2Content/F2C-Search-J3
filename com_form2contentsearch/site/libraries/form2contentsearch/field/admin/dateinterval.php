<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

class F2csearchFieldAdminDateinterval extends F2csearchFieldAdminBase
{
	public function DisplayFormField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('field', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('field', 'settings'); ?></div>
		</div>
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
			<div class="control-label"><?php echo $form->getLabel('script', 'settings'); ?></div>
			<div class="controls"><?php echo $form->getInput('script', 'settings'); ?><?php echo JText::_('COM_FORM2CONTENTSEARCH_INTERVAL_SCRIPT_FORMFIELD_HELP'); ?></div>
		</div>
		
		<script type="text/javascript">
		jQuery(document).ready()
		{
			jQuery("#jform_settings_script").width("400px");
			jQuery("#jform_settings_script").css("float", "left");
			jQuery("#jform_settings_script").css("margin-right", "10px");
		}
		</script>
		<?php		
	}
	
	public function DisplayDataviewField($form)
	{
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $form->getLabel('script'); ?></div>
			
			<div class="controls"><?php echo $form->getInput('script'); ?><?php echo JText::_('COM_FORM2CONTENTSEARCH_INTERVAL_SCRIPT_HELP'); ?></div>
		</div>
		<script type="text/javascript">
		jQuery(document).ready()
		{
			jQuery("#jform_script").width("400px");
			jQuery("#jform_script").css("float", "left");
			jQuery("#jform_script").css("margin-right", "10px");
		}
		</script>
		<?php 
	}
	
	public function prepareFormField($form, $item)
	{
		$form->setFieldAttribute('field', 'query', $this->getContentTypeFieldListQuery($item->formid, array('Datepicker')), 'settings');
	}
	
	public function prepareFormDataView($form, $item)
	{
	}
	
	public function saveDataView(&$data)
	{
		$registry = new JRegistry();
		$registry->set('startdate', $data['startdate']);
		$registry->set('enddate', $data['enddate']);
		$registry->set('script', $data['script']);
		$data['value'] = $registry->toString();
	}
	
	public function getItemDataViewField(&$item)
	{
		$regOptions = new JRegistry($item->value);	
		$item->startdate = $regOptions->get('startdate');
		$item->enddate = $regOptions->get('enddate');
		$item->script = $regOptions->get('script');
	}
}
?>