<?php
class Institution extends CI_Controller {
  function __construct() {
    parent::__construct();
    $this->load->model('Institution_model');
    $this->load->model('Dm_lookup');
    $this->load->helper(array('form','url'));
    $this->load->library('form_validation');

    $this->type = $this->uri->segments[1];
    $this->action = 'edit';
    $this->instid = (isset($this->uri->segments[3])?$this->uri->segments[3]:'');
  }

  function index() {

    	if (!$this->session->userdata('logged_in'))  {
        	redirect('login?type='.$this->type.'&action=index');
    	}
  	$master_institution_list = $this->Institution_model->get_all_institution();
  	$this->load->view('institution_landing_page', array('institution_list' => $master_institution_list));
  }


  function edit($instid = '') {

    if (!$this->session->userdata('logged_in'))  {
      if (isset($_GET['instid']) && ($this->instid == '')) { 
      	//add a new institution
        redirect('login?type='.$this->type.'&action='.$this->action.'&id_num='.$this->instid.'&instid='.$_GET['instid']);
      } else {
        redirect('login?type='.$this->type.'&action='.$this->action.'&id_num='.$this->instid);
      }
    }


  	if ($instid != '' && !isset($_POST['save_mode'])) {

  		if ($instid != '') {
		    $institution = $this->Institution_model->get_institution_info($instid);
			$dm_inst_type_lookup = $this->Dm_lookup->get_dm_inst_type();
			$dm_inst_rank_lookup = $this->Dm_lookup->get_dm_inst_rank();
		    $mother_houses = $this->Institution_model->get_mother_houses(); 
		    $order_names = $this->Institution_model->get_order_names(); 
		    $name = $institution->inst_name;
		    $institution_loc = $this->Institution_model->get_institution_loc($name);
		    $institution_resource = $this->Institution_model->get_institution_resource($instid);
		    $institution_date = $this->Institution_model->get_institution_date($name);
		    $this->load->view('institution_form', array(
		    		'error_location' => '', 
		    		'instid' =>$instid, 
		    		'institution' => $institution, 
		    		'dm_inst_type_lookup' => $dm_inst_type_lookup, 
		    		'dm_inst_rank_lookup' => $dm_inst_rank_lookup, 
		    		'mother_houses' => $mother_houses, 
		    		'order_names' => $order_names, 
		    		'location' => $institution_loc, 
		    		'date' => $institution_date, 
		    		'resource' => $institution_resource));
		}
	} else {
		$save_mode = isset($_POST['save_mode']) ? $_POST['save_mode']:'';

		$validation_passed = true;

		$error_location = '';

		switch ($save_mode) {
			case "institution_info" :
				$this->form_validation->set_rules('instid', 'Institution ID', 'trim|required');
				if ($instid == '') {
					$instid = $_POST['instid'];
					$this->form_validation->set_rules('inst_name', 'Institution Name', 'trim|required|callback_inst_name_available');
				} else {
					$this->form_validation->set_rules('inst_name', 'Institution Name', 'trim|required');
				}				
				$this->form_validation->set_rules('inst_type', 'Institution Type', 'trim|required');
				$this->form_validation->set_rules('inst_rank', 'Institution Rank', 'trim|required');
				$this->form_validation->set_rules('old_name', 'Old Name','trim');
				$this->form_validation->set_rules('order_name', 'Order Name','trim');
				$this->form_validation->set_rules('mother_house', 'Mother House','trim');
				if ($this->form_validation->run() == FALSE) {
					$validation_passed = false;
				} else {
					$this->Institution_model->save_info($instid, $_POST);
					redirect('institution/edit/'.$instid.'#info');
				}
				break;
			case "institution_location" :
				$this->form_validation->set_rules('lat', 'Latitude', 'trim|required');
				$this->form_validation->set_rules('long', 'Longitude', 'trim|required');
				$this->form_validation->set_rules('loc', 'Location', 'trim|required');				
				if ($this->form_validation->run() == FALSE) {
					$validation_passed = false;
				} else {
					$this->Institution_model->save_location($_POST);
					redirect('institution/edit/'.$instid.'#location');
				}
				break;
			case "institution_date" :
				$this->form_validation->set_rules('first-date', 'First Date', 'trim|required');
				$this->form_validation->set_rules('last-date', 'Last Date', 'trim|required');	
				if ($this->form_validation->run() == FALSE) {
					$validation_passed = false;
				} else {
					$this->Institution_model->save_date($_POST);
					redirect('institution/edit/'.$instid.'#date');

				}		
				break;
			case "institution_resource" :
				$this->Institution_model->save_resource($_POST);
				redirect('institution/edit/'.$instid.'#resource');
				break;
		}

		if (!$validation_passed) {
			$error_location = $save_mode;	
			$institution = $this->Institution_model->get_institution_info($instid);
		    
		}  
		if ($save_mode == '') {
			$new_inst_id = $this->Institution_model->get_new_inst_id();
			$new_inst_location_id = $this->Institution_model->get_new_location_id();

			$this->load->view('institution_form', array('error_location'=>$error_location, 'new_instid' => $new_inst_id,'new_loc_id' => $new_inst_location_id));
		} else {
			$institution = $this->Institution_model->get_institution_info($instid);

			if (empty($institution)) {
				$institution_loc = array();
			    $institution_date = array();
			    $institution_resource = array();
			} else {
			    $name = $institution->inst_name;

			    $institution_loc = $this->Institution_model->get_institution_loc($name);
			    $institution_date = $this->Institution_model->get_institution_date($name);
			    $institution_resource = $this->Institution_model->get_institution_resource($instid);
			}
			$this->load->view('institution_form', array('error_location'=>$error_location, 'institution' => $institution,  'location' => $institution_loc, 'date' => $institution_date, 'resource' => $institution_resource));
		}				
	}

  }

  function inst_name_available($inst_name) {
  	$msg = "Institution name ".$inst_name. " taken";

  	$result = $this->Institution_model->check_inst_name($inst_name);
  	if (count($result) > 0) {
 	 	$this->form_validation->set_message('inst_name_available', $msg);
  		return false;  		
  	} else {
  		return true;
  	}


  }

}
