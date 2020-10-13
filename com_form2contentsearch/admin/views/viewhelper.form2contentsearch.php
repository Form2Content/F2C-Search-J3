<?php
defined('JPATH_PLATFORM') or die('Restricted acccess');

function DisplayCredits()
{
	if($data = JApplicationHelper::parseXMLInstallFile(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'manifest.xml')) 
	{
		$version = $data['version'];
	}
	else
	{
		$version = 'undefined';
	}
	?>
	<table width="100%" border="0">
	<tr>
	  <td width="99%" align="right" valign="top">
		<br/>
		<div style="text-align: center">
			<span class="smallgrey"><?php echo JText::_('COM_FORM2CONTENTSEARCH_FORM2CONTENTSEARCH') . ' ' . JText::_('COM_FORM2CONTENTSEARCH_VERSION') . ' ' . $version; ?> (<a href="http://www.form2content.com/changelog/search-joomla3" target="_blank"><?php echo JText::_('COM_FORM2CONTENTSEARCH_CHECK_VERSION'); ?></a>), &copy; 2008 - Copyright by <a href="http://www.opensourcedesign.nl" target="_blank">Open Source Design</a> - e-mail: <a href="mailto:support@opensourcedesign.nl">support@opensourcedesign.nl</a></span>
		</div>
	  </td>
	  </tr>
	</table>
	<?php		
}
?>