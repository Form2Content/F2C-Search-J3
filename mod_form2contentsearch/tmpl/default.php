<?php 
defined('JPATH_PLATFORM') or die; 

// Load framework in no conflict mode
JHtml::_('jquery.framework');
JHtml::_('jquery.ui');
JHtml::script('mod_form2contentsearch/f2c_search.js', array('relative' => true));
JHtml::stylesheet('mod_form2contentsearch/search.css', array(), array('relative' => true));
?>
<script type="text/javascript">
var searchMinChar = <?php echo $searchMinChar; ?>;
var searchDelay = <?php echo $searchDelay; ?>;
var F2CSearchResultCount<?php echo $moduleId; ?> = <?php echo $numResults; ?>;
<?php echo $jsSearchFields; ?>

function F2CSearchGetResults<?php echo $moduleId; ?>()
{
	var filterUrl = F2CSearchBuildFilterUrl(<?php echo $moduleId; ?>);
	if(filterUrl == false){ return false; }
	 
	location.href = '<?php echo JURI::base(); ?>index.php?option=com_form2contentsearch&task=search.display&pb=1&moduleid=<?php echo $moduleId; ?>&searchformid=<?php echo $searchFormId; ?>&results='+F2CSearchResultCount<?php echo $moduleId; ?>+'&'  + filterUrl<?php echo ($forcedItemId) ? ' + \'&Itemid=\' + ' . $forcedItemId : ''; ?>;
}

function F2CSearchGetHits<?php echo $moduleId; ?>()
{
	if(<?php echo $autoSearch ?>)
	{
		return F2CSearchGetResults<?php echo $moduleId; ?>();
	}

	var filterUrl = F2CSearchBuildFilterUrl(<?php echo $moduleId; ?>);
	if(filterUrl == false){ return false; }
	
	if(typeof(document.global_filter_trigger) != 'undefined') clearTimeout(document.global_filter_trigger);	
	var img = '<img src="<?php echo JURI::root(); ?>media/mod_form2contentsearch/images/loading.gif" title="<?php echo JText::_('MOD_FORM2CONTENTSEARCH_PLEASE_WAIT_LOADING'); ?>" alt="<?php echo JText::_('MOD_FORM2CONTENTSEARCH_PLEASE_WAIT_LOADING'); ?>" width="128" height="15" />';

	jQuery("#f2cs_elements_table_<?php echo $moduleId; ?>").find("select,button,input").attr("ditesabled", "disabled");
	jQuery('#f2cs_btn_show_<?php echo $moduleId; ?>').attr("disabled", "disabled");		
	jQuery('#f2cs_result_<?php echo $moduleId; ?>').css("display", "block");
	jQuery("#f2cs_result_<?php echo $moduleId; ?>").append(img);

	var url = '<?php echo JURI::base(); ?>index.php?option=com_form2contentsearch&task=search.gethits&format=raw&pb=1&moduleid=<?php echo $moduleId; ?>&searchformid=<?php echo $searchFormId; ?>&results=<?php echo urlencode($searchResult); ?>&' + filterUrl;
	
	jQuery.ajax({
      url: url,
      success: function(response) 
      {
		F2CSearchResultCount<?php echo $moduleId; ?> = response;
		jQuery('#f2cs_result_<?php echo $moduleId; ?>').html('<?php echo str_replace("'", "\'", $preResultText); ?>'+response+'<?php echo str_replace("'", "\'", $postResultText); ?>');
		jQuery("#f2cs_elements_table_<?php echo $moduleId; ?>").find("select,button,input").removeAttr("disabled");
		jQuery('#f2cs_btn_show_<?php echo $moduleId; ?>').removeAttr("disabled");
		<?php if(!$showResultCount) : ?>
		jQuery('#f2cs_result_<?php echo $moduleId; ?>').css("display", "none");
		<?php endif; ?>
        return true;
      },
      error: function(data) { alert('Error performing Form2Content Search.'); }
    });
}
</script>
<form action="index.php" method="post" name="formModF2CSearch<?php echo $searchFormId; ?>" id="formModF2CSearch<?php echo $moduleId.'_'.$searchFormId; ?>">
	<?php echo $helper->buildSearchFormClassic($moduleId, $searchForm, $searchFields); ?>
	<p style="height: 16px;"><span id="f2cs_result_<?php echo $moduleId; ?>" <?php if(!$showResultCount) {echo ' style="display:none;"';} ?>><?php if($showInitialResultCount) { echo $searchResult; } ?></span></p>
	<input type="hidden" name="option" value="com_form2contentsearch" />
	<input type="hidden" name="searchformid" value="<?php echo $searchFormId; ?>" />
	<?php if($searchForm->submit_pre_text) echo '<p>' . $helper->stringHTMLSafe($searchForm->submit_pre_text) . '</p>'; ?>
	<?php 
	if(!$autoSearch) 
	{ 
		$caption	= ($searchForm->submit_caption) ? $helper->stringHTMLSafe($searchForm->submit_caption) : JText::_('MOD_FORM2CONTENTSEARCH_SHOW_RESULTS');
		$class 		= ($searchForm->submit_class) ? $searchForm->submit_class : 'button';
		echo '<input type="button" id="f2cs_btn_show_'.$moduleId.'" value="' . $caption . '" onclick="F2CSearchGetResults'.$moduleId.'();" class="' . $class . '" />';
	}
	if($showReset)
	{
		$caption	= ($searchForm->attribs->get('reset_caption')) ? $helper->stringHTMLSafe($searchForm->attribs->get('reset_caption')) : JText::_('MOD_FORM2CONTENTSEARCH_RESET');
		$class 		= ($searchForm->attribs->get('reset_class')) ? $searchForm->attribs->get('reset_class') : 'button';
		echo '<input type="button" id="f2cs_btn_reset_'.$moduleId.'" value="' . $caption . '" onclick="F2CSearchResetControls('.$moduleId.','.$resetRefreshResults.');" class="' . $class . '" />';
	}
	?>
	<?php if($searchForm->submit_post_text) echo '<p>' . $helper->stringHTMLSafe($searchForm->submit_post_text) . '</p>'; ?>
</form>