<?php
class Charter extends CI_Controller {

  public function __construct() {
    parent::__construct();

    $this->load->helper(array('form', 'url', 'solr'));
    $this->load->library('form_validation');
    $this->load->model('Charter_model');
    $this->load->model('Dm_lookup');

    $this->type = $this->uri->segments[1];
    $this->action = 'edit';
    $this->docnum = (isset($this->uri->segments[3])?$this->uri->segments[3]:'');
  }


  public function list_records($startsWith = '0001') {

    if (!$this->session->userdata('logged_in'))  {
        redirect('login?type='.$this->type.'&action=list_records');
    }

    $master_charter_list = $this->Charter_model->get_all_charters($startsWith);
    $this->load->view('charter_landing_page', array('charter_list' => $master_charter_list, 'starts_with' => $startsWith));
  }

  public function edit($docnum = '') {

    if (!$this->session->userdata('logged_in'))  {
      if (isset($_GET['cartnum']) && ($this->docnum == '')) { //add a new charter under existing cartulary
        redirect('login?type='.$this->type.'&action='.$this->action.'&id_num='.$this->docnum.'&cartnum='.$_GET['cartnum']);
      } else {
        redirect('login?type='.$this->type.'&action='.$this->action.'&id_num='.$this->docnum);
      }
    }

    $this->docnum = $docnum;

    $dm_charter_language_lookup = $this->Dm_lookup->get_dm_charter_language();
    $dm_charter_status_lookup = $this->Dm_lookup->get_dm_charter_status();
    $dm_charter_type_lookup = $this->Dm_lookup->get_dm_charter_type();
    $dm_markup_types_lookup = $this->Dm_lookup->get_dm_markup_types();
    $dm_charter_note_lookup = $this->Dm_lookup->get_dm_charter_note();
    $dm_charter_source_lookup = $this->Dm_lookup->get_dm_charter_source();
    $note_types = array(''=>'Please Select');

    foreach ($dm_charter_note_lookup as $note) {
      $type = $note->note_type;
      $note_types[$type] =  $type;
    }

    if ($docnum != '' && !isset($_POST['save_mode'])) {

	  // Call the getters to retrieve from the DB
      $charter = $this->Charter_model->get_charter($docnum);
      $charter_status = $this->Charter_model->get_charter_status($docnum);
      $charter_ref = $this->Charter_model->get_charter_ref($docnum);
      $charter_type = $this->Charter_model->get_charter_type($docnum);
      $charter_source = $this->Charter_model->get_charter_source($docnum);
      $doc = $this->Charter_model->get_doc($docnum);
      $date = $this->Charter_model->get_date($docnum);
      $date_type_selected = explode(", ",$date->date_type);

      $names_titles = $this->Charter_model->get_names_titles($docnum);
      $names = $this->Charter_model->get_names($docnum);
      $diplomatics = $this->Charter_model->get_diplomatics($docnum);
      $image = $this->Charter_model->get_image($docnum);
      $notes = $this->Charter_model->get_note($docnum);
      $resources = $this->Charter_model->get_resources($docnum);
      $parties = $this->Charter_model->get_parties($docnum);
      $locations = $this->Charter_model->get_locations($docnum);
      $parties_type = $this->Charter_model->get_parties_type($docnum);
      $parties_person_types = $this->Charter_model->get_parties_person_types($docnum);
      $parties_name_types = $this->Charter_model->get_parties_name_types($docnum);
      $parties_name_roles = $this->Charter_model->get_parties_name_role($docnum);
      $inst_names = $this->Charter_model->get_inst_names($docnum);
      $definitive_name_types = $this->Charter_model->get_definitive_name_types($docnum);
      $charter_origin = $this->Charter_model->get_charter_origin($docnum);
      $name_title_name_roles = $this->Charter_model->get_name_title_name_roles($docnum);
      $name_natures = $this->Charter_model->get_name_natures($docnum);
      $title_txt = $this->Charter_model->get_title_txt($docnum);
      $date_scope = $this->Charter_model->get_date_scope($docnum);

  	} else { // saving new or existing record
      $save_mode = isset($_POST['save_mode']) ? $_POST['save_mode']:'';

      if ($docnum != '') { // editing an existing record (save mode with given docnum)
        $charter = $this->Charter_model->get_charter($docnum);
        $charter_status = $this->Charter_model->get_charter_status($docnum);
        $charter_ref = $this->Charter_model->get_charter_ref($docnum);
        $charter_type = $this->Charter_model->get_charter_type($docnum);
        $charter_source = $this->Charter_model->get_charter_source($docnum);
        $doc = $this->Charter_model->get_doc($docnum);
        $date = $this->Charter_model->get_date($docnum);
        $date_type_selected = explode(", ",$date->date_type);
        $names_titles = $this->Charter_model->get_names_titles($docnum);
        $names = $this->Charter_model->get_names($docnum);
        $diplomatics = $this->Charter_model->get_diplomatics($docnum);
        $image = $this->Charter_model->get_image($docnum);
        $notes = $this->Charter_model->get_note($docnum);
        $resources = $this->Charter_model->get_resources($docnum);
        $parties = $this->Charter_model->get_parties($docnum);
        $locations = $this->Charter_model->get_locations($docnum);
    		$parties_type = $this->Charter_model->get_parties_type($docnum);
    		$parties_person_types = $this->Charter_model->get_parties_person_types($docnum);
    		$parties_name_types = $this->Charter_model->get_parties_name_types($docnum);
    		$parties_name_roles = $this->Charter_model->get_parties_name_role($docnum);
    		$inst_names = $this->Charter_model->get_inst_names($docnum);
    		$definitive_name_types = $this->Charter_model->get_definitive_name_types($docnum);
    		$charter_origin = $this->Charter_model->get_charter_origin($docnum);
    		$name_title_name_roles = $this->Charter_model->get_name_title_name_roles($docnum);
    		$name_natures = $this->Charter_model->get_name_natures($docnum);
    		$title_txt = $this->Charter_model->get_title_txt($docnum);
    		$date_scope = $this->Charter_model->get_date_scope($docnum);

      } else {
        $charter = new stdClass();
        $charter_status = array();
        $charter_ref = '';
        $charter_type = array();
        $doc = array();
        $date = array();
        $date_type_selected = array();
        $notes = array();
        $names = array();
        $diplomatics = array();
        $names_titles = array();
        $image = array();
        $resources = array();
        $parties =  array();
        $locations = array();
    		$parties_type = array();
    		$parties_person_types = array();
    		$parties_name_types = array();
    		$parties_name_roles = array();
    		$definitive_name_types = array();
    		$charter_origin = array();
    		$name_title_name_roles = array();
    		$name_natures = array();
    		$title_txt = array();
          	$charter->docnum = isset($_GET['cartnum']) ? $_GET['cartnum'] : '';
    		$inst_names = array();
      }

      switch ($save_mode) {
        case 'charter_info':
          if ($docnum == '') {
            $this->form_validation->set_rules('docnum', 'Document Number', 'trim|required|callback_docnum_available');
          }
          $this->form_validation->set_rules('language', 'Language', 'trim|required');
          $this->form_validation->set_rules('charter_status', 'Charter Status', 'required');
          if($charter_status == 'Embedded') {
            $this->form_validation->set_rules('embedded-in', 'Embedded In', 'trim|callback_valid_ref_docnum');
          };           
          if ($this->form_validation->run()) {
            $this->Charter_model->save_charter($_POST);
      			$this->update_solr($docnum);
            redirect('charter/edit/'.$_POST['docnum'].'#info');
          } else { // form validation failed
            $charter = new stdClass();
            $charter->docnum = $_POST['docnum'];
            $charter->language = $_POST['language'];
            $charter->origin = $_POST['origin'];
            $charter->charter_type = $_POST['charter_type'];
            $charter->charter_source = $_POST['charter_source'];
            if (!isset($_POST['charter_status'])) {
              $charter_status = array();
            } else {
              $charter_status = $_POST['charter_status'];
            }
          }
          break;

        // Saving the document text
        case 'charter_doc':
          $this->form_validation->set_rules('txt', 'txt', 'trim|required');
          if ($this->form_validation->run()) {
            $this->Charter_model->save_doc($docnum, $_POST);
			$this->update_solr($docnum);
            redirect('charter/edit/'.$docnum.'#document-text');
          } else { // form validation failed
            $doc = array('txt' => $_POST['txt']);
          }
          break;

        case 'charter_date':
          $this->form_validation->set_rules('dated', 'Dated', 'trim|callback_check_date_dated');
          $this->form_validation->set_rules('hidate', 'High Date', 'trim|callback_check_date_hidate');
          $this->form_validation->set_rules('lodate', 'Low Date', 'trim|callback_check_date_lodate');
          if ($this->form_validation->run()) {
            if ($this->Charter_model->save_date($docnum, $_POST)) {
			  $this->update_solr($docnum);
              redirect('charter/edit/'.$docnum.'#date');
            }
          }
          break;

		case 'charter_image':
			$this->form_validation->set_rules('image', 'Image', 'trim|required');
			$this->form_validation->set_rules('thumb', 'Thumb', 'trim');
			if ($this->form_validation->run()) {
				$this->Charter_model->save_image($docnum, $_POST);
				$this->update_solr($docnum);
				redirect('charter/edit/' . $docnum.'#image');
			}
			break;

		case 'charter_resource':
			$this->form_validation->set_rules('resourceid', 'Resourceid', 'trim');
			$this->form_validation->set_rules('resource_url', 'Resource_url', 'trim|required');
			$this->form_validation->set_rules('url_title', 'Url_title', 'trim');
			if ($this->form_validation->run()) {
				$this->Charter_model->save_resource($docnum, $_POST);
				$this->update_solr($docnum);
				redirect('charter/edit/' . $docnum.'#resource');
			}
			break;

		case 'charter_names_title':
			$this->Charter_model->save_names_title($_POST);
			$this->update_solr($docnum);
			redirect('charter/edit/'.$docnum.'#names-title');
			break;

		case 'charter_markup':
		  $markup_type = $_POST['markup_type'];
		  $do_save_name = false;
		  foreach ($dm_markup_types_lookup['names'] as $name_lookup) {
		    if ($markup_type == $name_lookup->names) {
		      $do_save_name = true;
		      break;
		    }
		  }

          if ($do_save_name) {
            if (!isset($_POST['markup_item_val']) || $_POST['markup_item_val'] == '') {
              // item always = 1 for new insert
              $_POST['markup_item_val'] = 1;
            }

            if (!isset($_POST['markup_instance_val']) || $_POST['markup_instance_val'] == '') {
              $_POST['markup_instance_val'] = count($names) + 1;
            }

            $this->Charter_model->save_markup_name($docnum, $_POST);
			$this->update_solr($docnum);
			redirect('charter/edit/'.$docnum.'#markups');
          } else {
            // save_diplomatics
            if (!isset($_POST['markup_item_val']) || $_POST['markup_item_val'] == '') {
              // item always = 1 for new insert
              $_POST['markup_item_val'] = 1;
            }

            if (!isset($_POST['markup_instance_val']) || $_POST['markup_instance_val'] == '') {
              $_POST['markup_instance_val'] = count($diplomatics) + 1;
            }
            $this->Charter_model->save_markup_diplomatic($docnum, $_POST);
			$this->update_solr($docnum);
			redirect('charter/edit/'.$docnum.'#markups');
          }
		  break;

    case 'charter_notes':
      $this->form_validation->set_rules('note_type', 'Note Type', 'trim|required');
      $this->form_validation->set_rules('note_text', 'Note Text', 'trim|required');
      if ($this->form_validation->run()) {
        $this->Charter_model->save_note($_POST);
		$this->update_solr($docnum);
        redirect('charter/edit/'.$docnum.'#notes');
      }

      break;

	case 'charter_parties':
		$this->Charter_model->save_parties($docnum, $_POST);
		$this->update_solr($docnum);
		redirect('charter/edit/' . $docnum.'#parties');
		break;

	case 'charter_locations':
	  $this->Charter_model->save_locations($docnum, $_POST);
	  $this->update_solr($docnum);
	  redirect('charter/edit/' . $docnum.'#location');
	  break;
     }

    }

	// make available the retrieved data in the page
    $this->load->view('charter_form',array(
      'docnum' => $docnum,
      'charter' => $charter,
      'charter_status' => $charter_status,
      'charter_ref' => $charter_ref,
      'charter_type' => $charter_type,
      'doc' => $doc,
      'date' => $date,
      'date_type_selected' => $date_type_selected,
      'names' => $names,
      'diplomatics' => $diplomatics,
      'names_titles' => $names_titles,
      'dm_charter_language_lookup' => $dm_charter_language_lookup,
      'dm_charter_origin_lookup' => $this->Dm_lookup->get_dm_charter_origin(),
      'dm_charter_status_lookup' => $dm_charter_status_lookup,
      'dm_charter_type_lookup' => $dm_charter_type_lookup,
      'dm_markup_types_lookup' => $dm_markup_types_lookup,
      'dm_charter_source_lookup' => $dm_charter_source_lookup,
      'note_types' => $note_types,
      'image' => $image,
      'resources' => $resources,
      'notes' => $notes,
      'parties' => $parties,
      'locations' => $locations,
      'parties_type' => $parties_type,
      'parties_person_types' => $parties_person_types,
      'parties_name_types' => $parties_name_types,
      'parties_name_roles' => $parties_name_roles,
      'inst_names' => $inst_names,
      'definitive_name_types' => $definitive_name_types,
      'charter_origin' => $charter_origin,
      'name_title_name_roles' => $name_title_name_roles,
      'name_natures' => $name_natures,
      'title_txt' => $title_txt,
      'date_scope' => $date_scope
    ));
  }

  public function check_date_dated($date) {
    if (!preg_match('/\d\d\d\d\-\d\d\-\d\d/', $date)) {
      $this->form_validation->set_message('check_date_dated', "Invalid date format for Dated");
      return false;
    }
    return true;
  }

  public function check_date_lodate($date) {

    if (!preg_match('/\d\d\d\d\-\d\d\-\d\d/', $date)) {
      $this->form_validation->set_message('check_date_lodate', "Invalid date format for Low Date");
      return false;
    }
    return true;
  }

  public function check_date_hidate($date) {

    if (!preg_match('/\d\d\d\d\-\d\d\-\d\d/', $date)) {
      $this->form_validation->set_message('check_date_hidate', "Invalid date format for High Date");
      return false;
    }
    return true;
  }

  function validateDate($date, $format = 'Y-m-d H:i:s')
  {
    $d = DateTime::createFromFormat($format, $date);

    return $d && $d->format($format) == $date;
  }

  public function docnum_available($docnum) {
  	$result = $this->Charter_model->get_charter($docnum);
  	if (count($result) > 0) {
  		$this->form_validation->set_message('docnum_available', "Document Number taken");
  		return false;
  	} else {
  		return true;
  	}
  }

  public function valid_ref_docnum($docnum) {
  	$result = $this->Charter_model->get_charter($docnum);
  	if (count($result) < 1) {
  		$this->form_validation->set_message('valid_ref_docnum', "Embedded in must be a valid document number");
  		return false;
  	} else {
  		return true;
  	}
  }

  public function logout() {
    $userdata = $this->session->userdata('logged_in');
    // $docnum_accessed =$userdata['id_num'];
    // $cartularies_num = $userdata['new_charter_cartnum'];
    $this->session->unset_userdata('logged_in');

    $host_url = '';
    if ($_SERVER['HTTP_HOST'] != '') {
      $host_url = '';
    } else {
      $host_url = 'http://'.$_SERVER['HTTP_HOST'];
    }

    // if ($docnum_accessed != '') {
    //   redirect($host_url.'/charters/'.$docnum_accessed);
    // } else if ($cartularies_num != '') {
    //   redirect($host_url.'/cartularies/'.$cartularies_num);
    // } else {
      redirect($host_url.'/doc_mgr/index.php/login'); 
    // }
  }

  public function update_solr($docnum) {
	//deeds_do_index_charters($this, $docnum);
  }
}
