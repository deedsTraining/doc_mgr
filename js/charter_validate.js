function validate_charter_location_form(instance) {
	var location_type_field_id = '#location_type';
	var property_type_field_id = '#property_type';
	var latlong_field_id = '#latlong';
	var country_field_id = '#country';
	var county_field_id = '#county';
	var place_field_id = '#place';
	
	if (instance > -1) {
		location_type_field_id += '_'+instance;
		property_type_field_id += '_'+instance;
		latlong_field_id += '_'+instance;
		country_field_id += '_'+instance;
		county_field_id += '_'+instance;
		place_field_id += '_'+instance;
	}
	
	if (jQuery(location_type_field_id).val() == '') {
		alert('Location Type cannot be empty!');
		jQuery(location_type_field_id).focus();
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
	if (jQuery(country_field_id).val() == '') {
		alert('Country cannot be empty!');
		jQuery(country_field_id).focus();
		return false
	}
	
	if (jQuery(county_field_id).val() == '') {
		alert('County cannot be empty!');
		jQuery(county_field_id).focus();
		return false
	}
	
	if (jQuery(place_field_id).val() == '') {
		alert('Place cannot be empty!');
		jQuery(place_field_id).focus();
		return false
	}
	return true; 
}

function validate_charter_image_form() {
	if (jQuery('#image').val() == '') {
		alert('image cannot be empty');
		jQuery('#image').focus();
		return false;
	} else if (jQuery('#thumb').val() == '') {
		alert('image cannot be empty');
		jQuery('#thumb').focus();
		return false;
	}
	return true;
}

function validate_charter_resource_form(instance) {
	if (instance != -1) {
		var url_field = jQuery('#resource_url_' + instance);
		var title_field = jQuery('#url_title_' + instance);
	} else {
		var url_field = jQuery('#resource_url');
		var title_field = jQuery('#url_title');
	}

	if (url_field.val() == '') {
		alert('resource url cannot be empty');
		url_field.focus();
		return false;
	} else if (title_field.val() == '') {
		alert('resource title cannot be empty');
		title_field.focus();
		return false;
	}
	return true;
}

function validate_charter_notes_form(instance) {
	if (instance != -1) {
		var note_type_field = jQuery('#note_type_' + instance);
		var note_text_field = jQuery('#note_text_' + instance);
	} else {
		var note_type_field = jQuery('#note_type');
		var note_text_field = jQuery('#note_text');
	}

	if (!note_type_field.val()) {
		alert('note type must be selected');
		note_type_field.focus();
		return false;
	} else if (note_text_field.val() == '') {
		alert('note text cannot be empty');
		note_text_field.focus();
		return false;
	}
	return true;
}