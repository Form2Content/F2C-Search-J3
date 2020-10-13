// Declare the Form2ContentSearch namespace
var Form2ContentSearch = {
		Fields: {		
			Multiselectlist: {}
		}
};

Form2ContentSearch.Fields.Multiselectlist =
{
	ResetControl: function (fieldName)
	{
		var field = jQuery('#'+fieldName);
		
		// Check if we have a dropdown or checkboxes
		if(field.is("select")) 
		{ 
			field.prop('selectedIndex', (field.prop('type') == 'select-one') ? 0 : -1);
		} 
		else 
		{ 
			jQuery.each(jQuery('[id^='+fieldName+']'), function(key, checkbox) { checkbox.checked = false; });
		}
	}		
}
