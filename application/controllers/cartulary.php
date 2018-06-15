<?php
class Cartulary extends CI_Controller {

  function __construct() {
    parent::__construct();

    $this->load->helper(array('form', 'url', 'solr'));
  	$this->load->library('form_validation');
    $this->load->model('Cartulary_model');

    $this->type = $this->uri->segments[1];
    $this->action = 'edit';
    $this->cartnum= (isset($this->uri->segments[3])?$this->uri->segments[3]:'');
  }

  function index() {

    	if (!$this->session->userdata('logged_in'))  {
      		redirect('login?type='.$this->type.'&action=index');
    	}
  	$master_cartulary_list = $this->Cartulary_model->get_all_cartularies();
  	$this->load->view('cartulary_landing_page', array('cartulary_list' => $master_cartulary_list));
  }

  /*
  * The method invoked when someone visits "http://deeds/doc_mgr/index.php/cartulary/edit/"
  * @param string $cartnum
  *		The number at the end of the URL, used to identified a cartulary.
  *		This cartnum is used to take user to the "edit" view; it does not exist when making a new cartulary.
  *		Another cartnum exists in $_POST['cartnum'] and is used for making a new cartulary.
  */
  function edit($cartnum = '') {

    if (!$this->session->userdata('logged_in'))  {
      redirect('login?type='.$this->type.'&action='.$this->action.'&id_num='.$this->cartnum);
    }

    if ($cartnum != '' && !isset($_POST['save_mode'])) {
		/* we are viewing an existing record */
		$cartulary = $this->Cartulary_model->get_info($cartnum);
		$resource = $this->Cartulary_model->get_resource($cartnum);
		$image = $this->Cartulary_model->get_image($cartnum);
		$sources = $this->Cartulary_model->get_sources($cartnum);
		$institutions = $this->Cartulary_model->get_institutions($cartnum);
		$locations = $this->Cartulary_model->get_locations($cartnum);
		$biblio = $this->Cartulary_model->get_biblio($cartnum);
		$order_names = $this->Cartulary_model->get_order_names();
		$series = $this->Cartulary_model->get_series();
		$cart_types = $this->Cartulary_model->get_cart_types();

    } else {
		$save_mode = isset($_POST['save_mode']) ? $_POST['save_mode']:'';
		if ($cartnum != ''){
			/* we are updating an existing record */
			$cartulary = $this->Cartulary_model->get_info($cartnum);
			$order_names = $this->Cartulary_model->get_order_names();
			$series = $this->Cartulary_model->get_series();
			$cart_types = $this->Cartulary_model->get_cart_types();
			$resource = $this->Cartulary_model->get_resource($cartnum);
			$image = $this->Cartulary_model->get_image($cartnum);
			$sources = $this->Cartulary_model->get_sources($cartnum);
			$institutions = $this->Cartulary_model->get_institutions($cartnum);
			$locations = $this->Cartulary_model->get_locations($cartnum);
			$biblio = $this->Cartulary_model->get_biblio($cartnum);
		} else {
			/* we are making a new record */
			$cart_types = $this->Cartulary_model->get_cart_types();
			$order_names = $this->Cartulary_model->get_order_names();
			$series = $this->Cartulary_model->get_series();
		}

		switch ($save_mode) {
			case 'cartulary_info':
			  if (isset($_POST['cartnum']) && empty($cartnum)) {
				$cartnum = $_POST['cartnum'];
				$this->form_validation->set_rules('cartnum', 'Cart Num', 'trim|required|callback_cartnum_available');
				$this->form_validation->set_message('cartnum_available', "Cartulary Number taken");
			  }
			  $this->form_validation->set_rules('short', 'Short Title', 'trim|required');
			  $this->form_validation->set_rules('title', 'Cartulary Title', 'trim|required');
			  $this->form_validation->set_rules('multi', 'Multi', 'trim');
			  $this->form_validation->set_rules('order_name', 'Order Name', 'trim');
			  $this->form_validation->set_rules('cart_type', 'Cart Type', 'trim|required');
			  $this->form_validation->set_rules('series', 'Series', 'trim');
			  $this->form_validation->set_rules('private', 'Private / Public', 'trim|required');
			  $this->form_validation->set_rules('diplomatics', 'Diplomatics', 'trim');
			  $this->form_validation->set_rules('names', 'Names', 'trim');
			  $this->form_validation->set_rules('cartid', 'Cart ID', 'trim');
			  if ($this->form_validation->run()) {
				$this->Cartulary_model->save_info($cartnum, $_POST);
				$this->update_solr($cartnum);
				redirect('cartulary/edit/'.$_POST['cartnum'].'#info');
				return;
			  }
			  break;
			case 'cartulary_resource':
			  $this->form_validation->set_rules('utl', 'UTL', 'trim');
			  $this->form_validation->set_rules('google', 'Google', 'trim');
			  $this->form_validation->set_rules('wiki', 'Wiki', 'trim');
			  $this->form_validation->set_rules('pdf', 'PDF', 'trim');
			  $this->form_validation->set_rules('worldcat', 'Worldcat', 'trim');
			 if ($this->form_validation->run()) {
				$this->Cartulary_model->save_resource($cartnum, $_POST);
				$this->update_solr($cartnum);
				redirect('cartulary/edit/'.$cartnum.'#resource');
				return;
			  }
			  break;
			case 'cartulary_image':
			  $this->form_validation->set_rules('image', 'Image', 'trim');
			  $this->form_validation->set_rules('thumb', 'Image Thumbnail', 'trim');
			 if ($this->form_validation->run()) {
				$this->Cartulary_model->save_image($cartnum, $_POST);
				$this->update_solr($cartnum);
				redirect('cartulary/edit/'.$cartnum.'#image');
				return;
			  }
			  break;
			case 'cartulary_source':
				$this->form_validation->set_rules('source_url', 'Source URL', 'trim|required');
				$this->form_validation->set_rules('source_title', 'Source Title', 'trim|required');
				if ($this->form_validation->run()) {
					$result = $this->Cartulary_model->save_source($cartnum, $_POST);
					$this->update_solr($cartnum);
					redirect('cartulary/edit/'.$cartnum.'#source');
					return;
				}
			  break;
			case 'cartulary_biblio':
			  $this->form_validation->set_rules('biblio', 'Biblio', 'trim|required');
			 if ($this->form_validation->run()) {
				$this->Cartulary_model->save_biblio($cartnum, $_POST);
				$this->update_solr($cartnum);
				redirect('cartulary/edit/'.$cartnum.'#biblio');
				return;
			  }
			  break;
			case 'cartulary_location':
				$this->Cartulary_model->save_location($cartnum, $_POST);
				$this->update_solr($cartnum);
				redirect('cartulary/edit/'.$cartnum.'#location');
				return;
				break;
			case 'cartulary_institution':
				$this->Cartulary_model->save_institution($cartnum, $_POST);
				$this->update_solr($cartnum);
				redirect('cartulary/edit/'.$cartnum.'#institution');
				return;
				break;
		}
	}

	$this->load->view('cartulary_form', array(
		'cartnum' => $cartnum,
		'cartulary' => $cartulary,
		'order_names' => $order_names,
		'series' => $series,
		'cart_types' => $cart_types,
		'resource' => $resource,
		'sources' => $sources,
		'image' => $image,
		'institutions' => $institutions,
		'locations' => $locations,
		'biblio' => $biblio,
		'new_source' => $new_source
	));
  }

  /**
   * Custom form validation function
   */
  function cartnum_available($cartnum) {
    $query = $this->db->query("
        SELECT * FROM deeds_db.cartulary
        WHERE cartnum = ?", array($cartnum));
    return $query->num_rows() == 0;
  }


  function update_solr($cartnum) {
	//deeds_do_index_cartularies($this, $cartnum);
  }
}
