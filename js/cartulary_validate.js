function validate_cartulary_source_form(sourceid) {
	var source_url_field_id = '#source_url';
	var source_title_field_id = '#source_title';
	
	if (sourceid > -1) {
		source_url_field_id += '_'+sourceid;
		source_title_field_id += '_'+sourceid;
	}
	
	if (jQuery(source_url_field_id).val() == '') {
		alert('Source URL cannot be empty!');
		jQuery(source_url_field_id).focus();
		return false
	}
	
	if (jQuery(source_title_field_id).val() == '') {
		alert('Source name cannot be empty!');
		jQuery(source_title_field_id).focus();
		return false
	}
	return true;
}

function validate_cartulary_location_form(instance) {
	
	var location_field_id = '#location_field';
	var latlong_field_id = '#latlong';
	
	if (instance > -1) {		
		location_field_id += '_'+instance;
		latlong_field_id += '_'+instance;
	}
	
	var location = jQuery(location_field_id).val();
	var latlong = jQuery(latlong_field_id).val();
	
	if (location == '') {
		alert('Location field cannot be empty!');
		jQuery(location_field_id).focus();
		return false
	}
	
	if (jQuery(latlong_field_id).val() == '') {
		alert('Lat Long cannot be empty!');
		jQuery(latlong_field_id).focus();
		return false
	} else {
		// Validate lat long format
		var latlngVal = /^-?([0-8]?[0-9]|90)\.[0-9]{1,6},-?((1?[0-7]?|[0-9]?)[0-9]|180)\.[0-9]{1,6}$/;
		if (!latlngVal.test(jQuery(latlong_field_id).val())) {
			alert('Invalid Lat, Long format!');
			jQuery(latlong_field_id).focus();
			return false;
		}
	}	
	
	return true;
}

function validate_cartulary_institution_form(instance) {
	var institution_field_id = '#institution_field';	

	if (instance > -1) {
		institution_field_id += '_' + instance;		
	}
	
	var institution = jQuery(institution_field_id).val();	
	
	if (institution == '') {
		alert('institution cannot be empty!');
		jQuery(institution_field_id).focus();
		return false;
	}

	return true;
}