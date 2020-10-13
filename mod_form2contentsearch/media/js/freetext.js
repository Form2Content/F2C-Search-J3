/*
 * Initialization code for FreeText field
 */
jQuery(document).ready(function()
{
	jQuery(".F2cSearchFreetextField").bind('keyup', function(event)
	{
		if(typeof(document.global_filter_trigger) != 'undefined') clearTimeout(document.global_filter_trigger);
		document.global_filter_trigger = setTimeout(function () { F2CSearchFreeTextTrigger(event.target, searchMinChar); }, searchDelay); 							
	});
	jQuery(".F2cSearchFreetextField").keypress(function(event)
	{
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == 13)
		{
			event.preventDefault();
			F2CSearchFreeTextTrigger(event.target, searchMinChar);
		}				 
	});
});

function F2CElementInfo(id)
{
	arrElements = id.split("_");
	this.moduleId = arrElements[1]; 
	this.searchFieldId = arrElements[2];
	this.formId = arrElements[3]; 
}

function F2CSearchFreeTextTrigger(element, searchMinChar)
{
	elmInfo = new F2CElementInfo(element.id);
	
	// do nothing when less than $searchMinChar characters are entered
	if(element.value.length > 0 && element.value.length < searchMinChar) return false;
	// do nothing when the value of the field hasn't changed
	var elementHistory = jQuery('#hid_'+ element.id);
	if(elementHistory.value == element.value) return;
	// back-up the new value
	elementHistory.value = element.value;
	eval('F2CSearchGetHits'+elmInfo.moduleId+'();');
}
