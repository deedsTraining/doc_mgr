var charter_content_raw = '';
var markup_mode = 'names';

jQuery(document).ready(function() {
		jQuery('#markup-guide').text('To add new markup, highlight part of, or all of the text in the area below.');
		charter_content_raw = jQuery('#markup_canvas').val();
		jQuery('#markup_canvas').select(text_selected_handler);
		jQuery('#markup_listing_names li, #markup_listing_diplomatics li').click(load_markup_to_edit_form);
});

function set_markup_mode(mmode) {
	cancel_markup();
	markup_mode = mmode;
	jQuery('#markup_listing_names').hide();
	jQuery('#markup_listing_diplomatics').hide();
	
	jQuery('#names_markup_type').hide();
	jQuery('#diplomatics_markup_type').hide();
	
	switch (markup_mode) {
		case 'names':
			jQuery('#markup-tab-title').text('Name Markups');
			jQuery('#markup_listing_names').show();
			jQuery('#names_markup_type').show();
			break;
		
		case 'diplomatics':
			jQuery('#markup-tab-title').text('Diplomatic Markups');
			jQuery('#markup_listing_diplomatics').show();
			jQuery('#diplomatics_markup_type').show();
			break;
	}
}

function text_selected_handler() {
	jQuery('#markup-guide').text("Good! Now pick a 'Diplomatic Type' then click the 'Add current selection to diplomatics' button below.");
	var markup_canvas = jQuery(this);
	var highlight = markup_canvas.textrange();
	jQuery('#markup_start').text(highlight.start+1);
	jQuery('#markup_start_val').val(highlight.start+1);
	
	jQuery('#markup_end').text(highlight.end+1);
	jQuery('#markup_end_val').val(highlight.end+1);
	
	jQuery('#markup_text').text(highlight.text);
	jQuery('#markup_text_val').val(highlight.text);
}

function load_markup_to_edit_form() {
	jQuery('#markup-guide').text("You have selected an existing diplomatics.  Click 'Edit selected markup' if you with to make a change.");
	jQuery('#markup_listing_names li, #markup_listing_diplomatics li').removeClass('active');
	jQuery(this).addClass('active');
	var instance = jQuery(this).children('.markup_instance').html();
	var item = jQuery(this).children('.markup_item').html();
	var markup_type = jQuery(this).children('.markup_type').html();
	var start = jQuery(this).children('.markup_start').html();
	var end = jQuery(this).children('.markup_end').html();
	var text = jQuery(this).children('.markup_text').html();
	
	console.log(markup_type);
	
	// To let the editing form know which li to "reset" when pressing Cancel
	jQuery('#loaded_from').val(jQuery(this).attr('id'));
	
	switch (markup_mode) {
	case 'names':
		jQuery('#names_markup_type').val(markup_type);
		break;
	
	case 'diplomatics':
		jQuery('#diplomatics_markup_type').val(markup_type);
		break;
	}
	
	jQuery('#markup_instance_val').val(instance);
	jQuery('#markup_item_val').val(item);
	
	jQuery('#markup_start').text(start);
	jQuery('#markup_start_val').val(start);
	
	jQuery('#markup_end').text(end);
	jQuery('#markup_end_val').val(end);
	
	jQuery('#markup_text').text(text);
	jQuery('#markup_text_val').val(text);
	
	
	var highlightData = [{
		item_type:markup_type,
		start:start,
		end:end,
		title:text
	}];
	
	jQuery('#markup_canvas_display').highlighter({items:highlightData, bgcolor:'#ffffff', color:'#000000'});
	jQuery('#markup_canvas_display').show();
	jQuery('#markup_canvas').hide();
	jQuery('#markup_edit_button').show();
	jQuery('#markup_save_button').hide();
}

// Start editing an existing markup
function markup_start_editing() {
	jQuery('#markup_canvas_display').hide();
	jQuery('#markup_canvas').show();
	jQuery('#markup_edit_button').hide();
	jQuery('#markup_save_button').val('Update selected markup');
	jQuery('#markup-guide').text("Please make your update and click 'Update selected markup' when you are done.");
	jQuery('#markup_save_button').show();
}

function cancel_markup() {
	jQuery('#markup-guide').text('To add new markup, highlight part of, or all of the text in the area below.');
	var whichOne = jQuery('#loaded_from').val();
	jQuery('#'+whichOne).removeClass('active');
	
	jQuery('#loaded_from').val('');
	
	jQuery('#markup_type').val('');
	
	jQuery('#markup_instance_val').val('');
	jQuery('#markup_item_val').val('');
	
	jQuery('#markup_start').text('');
	jQuery('#markup_start_val').val('');
	
	jQuery('#markup_end').text('');
	jQuery('#markup_end_val').val('');
	
	jQuery('#markup_text').text('');
	jQuery('#markup_text_val').val('');
	
	jQuery('#markup_canvas_display').hide();
	jQuery('#markup_canvas').show();
	jQuery('#markup_edit_button').hide();
	jQuery('#markup_save_button').val('Add new markup');
	jQuery('#markup_save_button').show();
}


function validate_markup_form() {
	switch (markup_mode) {
		case 'names':
			jQuery('#markup_type').val(jQuery('#names_markup_type').val());
			break;
		
		case 'diplomatics':
			jQuery('#markup_type').val(jQuery('#diplomatics_markup_type').val());
			break;
	}
	
	if (jQuery('#markup_type').val() == '') {
		alert('Please pick a markup type.');
		return false;
	}
	
	if (jQuery('#markup_start_val').val() == '' | jQuery('#markup_end_val').val() == '') {
		alert('Please specify the start and end position by dragging your mouse over the text area on the left.');
		return false;
	}
	
	if (jQuery('#markup_text_val').val() == '') {
		alert('The text you selected is blank.');
		return false;
	}
	
	return true;
}