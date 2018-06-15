jQuery(document).ready(function() { 

	get_inst_ranks_per_inst_type(); 

	$('#info_inst_type').change(function(){

		get_inst_ranks_per_inst_type(); 

	}); //end change 

	function get_inst_ranks_per_inst_type() { 

	    var inst_type = $('#info_inst_type').val();
	    if (inst_type === '') { 
	        $("#info_inst_rank").children().hide();
	    	$('#info_inst_rank').prop('disabled', true);
	    } else {

	    	// get the inst_type for the selected rank
	    	var inst_type_per_selected_rank = $('#info_inst_rank option:selected').closest('optgroup').prop('label');

	    	// if different institution type is selected
	    	if (inst_type !== inst_type_per_selected_rank ) { 
	    		// deselect the rank
				$('#info_inst_rank option:selected').prop("selected", false);
	    	}

	    	$('#info_inst_rank').prop('disabled', false);

			//get rank options that are not selected
			var arr1 = $('#info_inst_type option:not(:selected)');
			//get the ones that are selected
			var arr2 = $('#info_inst_type').find(":selected");

	        //hide optgroups
	        for (var j = 0; j < arr1.length; j++) {
	            $("#info_inst_rank").children("optgroup[label='" + arr1[j].value + "']").hide();
	        }

	        //show the rank options for the selected inst_type
	        for (var k = 0; k < arr2.length; k++) {
	            $("#info_inst_rank").children("optgroup[label='" + arr2[k].value + "']").show();
	        }
	    } //end if	

	}

	$("#info form").submit(function( event ) {

		var inst_name = $('#inst_name').val().trim();
		if (inst_name.length === 0 ) { 
		  	alert("Please enter Institution Name");
		  	event.preventDefault();
		} 

		var inst_type = $('#info_inst_type').val().trim();
		if (inst_type.length === 0 ) { 
		  	alert("Please select Institution Type");
		  	event.preventDefault();
		} 

		var inst_rank = $('#info_inst_rank').val().trim();
		if (inst_rank.length === 0 ) { 
		  	alert("Please select Institution Rank");
		  	event.preventDefault();
		} 

	});


}); 
