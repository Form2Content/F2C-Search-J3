function f2cConvertDate(elm)
{
	var dateFormat= jQuery("#" + elm.id).datepicker( "option", "dateFormat" );
	
	try
	{
		var date = jQuery.datepicker.parseDate(dateFormat, elm.value);
		// When we're here, we're sure there's an empty or valid date selected
		jQuery("#" + elm.id).removeClass("invalid");
		
		if(date == null)
		{
			// update the value field
			jQuery("#" + elm.id + "_hidden").val('');
		}
		
		arrElements = elm.id.split("_");
		eval('F2CSearchGetHits'+arrElements[1]+'();');
	}
	catch(err)
	{
		alert(msgInvalidDate.replace('%s', dateFormat));
		// Show the datepicker again
		jQuery("#" + elm.id).addClass("invalid");
		jQuery("#" + elm.id).datepicker("show");
	}
}
