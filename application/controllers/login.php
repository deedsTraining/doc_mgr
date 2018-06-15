<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {

  	function __construct() {
    	parent::__construct();
    	$this->load->helper(array('form'));

  		$this->load->library('form_validation');
	}


	function index() {
//		$id_num = $_GET['id_num'];
//		

		if(isset($_GET['id_num'])) { 
			$id_num = $_GET['id_num'];
		} else { 
			$id_num = '';
		}

		if(isset($_GET['type'])) { 
			$type = $_GET['type'];
		} else { 
			$type = 'cartulary';
		}

		if(isset($_GET['action'])) { 
			$action = $_GET['action'];
		} else { 
			$action = 'index';
		}

		if (isset($_GET['cartnum'])) {
			$new_charter_cartnum = $_GET['cartnum'];
			$this->load->view('login_form', array('type'=>$type, 'action' => $action, 'id_num' => $id_num, 'new_charter_cartnum' => $new_charter_cartnum));
		} else {
			$this->load->view('login_form', array('type'=>$type, 'action' => $action, 'id_num' => $id_num));
		}
		
	}

	function verify() {

		$this->form_validation->set_rules('username','Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password','Password', 'trim|required|xss_clean|callback_verify_login');

		$this->form_validation->set_rules('type','Type', 'trim');
		$this->form_validation->set_rules('action','Action', 'trim');
		$this->form_validation->set_rules('id_num','DocNum', 'trim');
		$this->form_validation->set_rules('new_charter_cartnum','new_charter_cartnum', 'trim');

		if ($this->form_validation->run()) {
		    if ($_POST['type'] != '') {
    			if ($_POST['type'] == 'charter' && $_POST['id_num'] == '' && isset($_POST['new_charter_cartnum'])) {
    				redirect($_POST['type'].'/'.$_POST['action'].'?cartnum='.$_POST['new_charter_cartnum']);
    			} else {
    				redirect($_POST['type'].'/'.$_POST['action'].'/'.$_POST['id_num']);
    			}
		    } else {
		      redirect('cartulary');
		    }
		} else {
			$this->load->view('login_form');
		}
	}


	function verify_login() {
		$username = $_POST['username'];
		$password = $_POST['password'];
		$id_num = $_POST['id_num'];
		$type = $_POST['type'];
		$action = $_POST['action'];
		$new_charter_cartnum = (isset($_POST['new_charter_cartnum'])? $_POST['new_charter_cartnum']:'');

		if ($username == '' && $password =='') {
			$sess_array = array('username' => $username,'type'=>$type, 'id_num' => $id_num, 'new_charter_cartnum' => $new_charter_cartnum);
			$this->session->set_userdata('logged_in', $sess_array);
			return TRUE;
		} else {
			$this->form_validation->set_message('verify_login', 'Invalid username or password');
			return FALSE;
		}


	}
}
