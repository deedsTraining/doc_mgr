/** Custom jQuery plugin written by Ken Yang **/
(function($) {
	$.fn.highlighter = function(options) {
		var settings = $.extend(
			{
				bgcolor			:			'#ffff00',
				color			:			'#ff0000',
				items			:			[] // assuming array index starts from 1 for deeds markup start, end
			},
			options
		);
		
		// a helper function to help sorting our custom item array
		function item_sort_handler(a, b) {
			if (a.start != undefined && a.start != null && b.start != undefined && b.start != null) {
				aStart = parseInt(a.start);
				bStart = parseInt(b.start);
				if (aStart == bStart) {
			        return 0;
			     } else if (aStart > bStart) {
			        return 1;
			     } else {
			        return -1;
			     }
			} else {
				throw 'item sort error: start attribute is bad!';
			}
		 }
		
		// sort items according to the start attribute
		settings.items.sort(item_sort_handler);
		
		var old_text = charter_content_raw;
		var new_text = '';
		var highlight_content = [];
		var start_pos = 0;
		
		var id = 0;
		
		for (k = 0; k < settings.items.length; k++) {
			if (settings.items[k].item_type != 'numeral') {
				//numeral diplomatics are not included, confirmed with currend deeds implmenetation
				//also the numeral markup start, end sometime overlaps with other diplomatics
//			var item_fcolor = settings.color;
//			var item_bcolor = settings.bgcolor;
			item_fcolor = '#00f';
			item_bcolor = '#fff';
			item_type_machine_name = settings.items[k].item_type_machine_name;
			var prefix = old_text.substring(start_pos, settings.items[k].start - 1);
			var highlight = old_text.substring(settings.items[k].start - 1 , settings.items[k].end - 1);

			if (settings.items[k].item_type == 'name') { // includes the qtip_item class
				highlight = '<span style="cursor:pointer; font-weight:bold; color:' + item_fcolor + '; background:' + item_bcolor + ';" class="qtip_item text-highlighted '+item_type_machine_name+'" id="' + id + '">'+highlight+'</span>';
			} else { // do not apply qtip, do not apply mouse pointer
				highlight = '<span style="font-weight:bold; color:' + item_fcolor + '; background:' + item_bcolor + ';" class="'+item_type_machine_name+'" id="' + id + '">'+highlight+'</span>';
			}
			id = id + 1;
			highlight_content.push({title: settings.items[k].title, content:settings.items[k].content});
			start_pos = settings.items[k].end - 1;
			
			new_text = new_text + prefix + highlight;
			if (k == settings.items.length - 1) {
				new_text = new_text + old_text.substring(start_pos, old_text.length);
			}
			
			//console.log('new_text: '+new_text);
			}
		}
		
		this.html(new_text);
	};
})(jQuery);