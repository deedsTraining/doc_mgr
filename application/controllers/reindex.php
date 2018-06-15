<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');


class Reindex extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url', 'solr_reindex_helper'));

		$this->type = $this->uri->segments[1];
		$this->action = 'reindex';
	}

	//if index is loaded
	public function index() {

		if (!$this->session->userdata('logged_in'))  {
			redirect('login?type='.$this->type.'&action='.$this->action);
		}

		//load the helper library
		$this->load->helper('form');
		$this->load->helper('url');
		//Set the message for the first time
		$data = array(
			'msg' => '',
			'upload_data' => ''
		);
    
		//load the view/reindex.php with $data
		$this->load->view('reindex', $data);		
	}


	function run_reindex() {

		// file upload button is clicked
	    if (isset($_POST['upload'])) {

			//load the helper
			$this->load->helper('form');

			//folder permission should be 770
			$path = FCPATH.'uploads/tmp/'; 

			if (!file_exists($path)) {
				$data = array('msg' => "The file upload path ($path) doesn't exist.");
			} else { 

				//Configure
				//set the path where the file uploaded will be copied. 
				$config['upload_path'] = $path; 

				// set the filter image types: only .txt is allowed
				$config['allowed_types'] = 'txt';

				// escape special characters in the file name
				$config['encrypt_name'] = TRUE;


				//load the upload library
				$this->load->library('upload', $config);

				$this->upload->initialize($config);

				$this->upload->set_allowed_types('*');

				$data['upload_data'] = '';

		    
				//if not successful, set the error message
				if (!$this->upload->do_upload('userfile')) {

					$data = array('msg' => $this->upload->display_errors());

				} else { //else, set the success message

					$data = array('msg' => "File upload successful! Click the re-index button.");
					$data['upload_data'] = $this->upload->data();
				}

			}
			
			//load the view/reindex.php
			$this->load->view('reindex', $data);
	    }

	    // reindex button is clicked
	    elseif (isset($_POST['reindex'])) {

	    	$result = '';

	    	if(isset($_POST['file_path'])) { 

				try {

					// get the max line number 
					$file = new SplFileObject($_POST['file_path'], 'r');
					$file->seek(PHP_INT_MAX);
					$line_count = $file->key() + 1;
					$file = null;

					if($line_count > 1000) { 
						$result .= "ERROR! Maximum 1000 records can be indexed per a time. Please edit the text file and re-upload it.<br />";
					} else {


						$default = ini_get('max_execution_time');
						set_time_limit(0);

						$count = 1;

						$file = new SplFileObject($_POST['file_path']);

						// Loop until we reach the end of the file.
						while (!$file->eof()) {


							$line = trim($file->fgets());
							$line_string_length = strlen($line); 


							switch ($line_string_length) {

								// Charter
							    case 8:
							    	if(deeds_solr_helper_check_valid_charter($line)) { 
							    		deeds_do_index_charters($line); 
							    		$result .= "$count - docnum $line indexed<br />";
							    	} else {
							    		$result .= "$count - docnum $line doesn't exist<br />"; 
							    	}
							        break;

							    // Cartulary
							    case 4:
							    	if(deeds_solr_helper_check_valid_cartularies($line)) {
							    		deeds_do_index_cartularies($line); 
							    		$result .= "$count - cartnum $line indexed<br />";
							    	} else {
							    		$result .= "$count - cartnum $line  doesn't exist<br />"; 
							    	}
							        break;
							    default:
							    	$result .= "$count - $line is not a valid record number<br />";
							    	
							}

							$count++;
						}

						set_time_limit($default);

					}

					// delete the file
					unlink($_POST['file_path']);

					// Unset the file to call __destruct(), closing the file handle.
					$file = null;

				} catch (Exception $e) {
				    //$results .= 'Cannot read the file. Caught exception: '.$e->getMessage()."\n";
				    $result .= 'Cannot find or read the txt file. Please upload a txt file. <br />';
				}

				$data = array(
					'result' =>  $result
				);

	    	} else {

				$data = array('msg' => "Please upload a txt file that has docnumber or catnum per a line. Maximum 1000 records can be indexed per a time.");

	    	}


			//load the view/reindex.php
			$this->load->view('reindex', $data);
	    }

	}
}
