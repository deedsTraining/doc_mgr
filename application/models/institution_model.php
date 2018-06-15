<?php
class Institution_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }

  function get_all_institution() {
    $query = $this->db->get_where('institution');
    $result = $query->result();   
    return $result;
  }


  function get_institution_info ($instid) {
    $query = $this->db->get_where('institution',array('instid' => $instid));
    $result = $query->result();
    return empty($result)?$result: $result[0];

  }

  function get_mother_houses() {
    $query = $this->db->query('
      select inst_name from deeds_db.institution order by inst_name asc;');
    $mother_houses[''] = '';
    foreach ($query->result_array() as $row) {
      $mother_houses[$row['inst_name']] = $row['inst_name'];
    }
    return $mother_houses;
  }

  function get_order_names() {
    $query = $this->db->query('
      select order_name from deeds_db.dm_institution_order order by order_name asc;');
    $order_names[''] = '';
    foreach ($query->result_array() as $row) {
      $order_names[$row['order_name']] = $row['order_name'];
    }
    return $order_names;
  }

  function get_institution_loc($name) {
    $query = $this->db->get_where('institution_location',array('inst_name' => $name));
    $result = $query->result();
    return empty($result)?$result: $result[0]; 	
  }

  function get_institution_resource($instid) {
    $query = $this->db->get_where('institution_resource',array('instid' => $instid));
    $result = $query->result();
    return empty($result)?$result: $result[0];   	
  }

  function get_institution_date($name) {
     $query = $this->db->get_where('institution_dates',array('inst_name' => $name));
     $result = $query->result();

    return empty($result)?$result: $result[0];    	
  }

  function get_new_inst_id() {
    $this->db->select_max('instid', 'max_inst_id');
    $query = $this->db->get('institution');

    $result_arr = $query->result();

    $max_inst_id = (int)($result_arr[0]->max_inst_id) ;

    return $max_inst_id+1;
  }

  function get_new_location_id() {
    $this->db->select_max('instid', 'max_inst_loc_id');
    $query = $this->db->get('institution_location');

    $result_arr = $query->result();

    $max_inst_loc_id = (int)($result_arr[0]->max_inst_loc_id);

    return $max_inst_loc_id+1;   
  }

  function get_new_date_id() {
    $this->db->select_max('did', 'max_inst_did');
    $query = $this->db->get('institution_dates');

    $result_arr = $query->result();

    $max_inst_did = (int)($result_arr[0]->max_inst_did);

    return $max_inst_did+1;       
  }

  function save_location($data) {
    if ($data['instid'] != '') {
      $instid = $data['instid'];
    } else {
      $instid = $this->get_new_location_id();
    }
    $inst_name = $this->db->escape($data['inst_name']);
    $lat = $this->db->escape($data['lat']);
    $long = $this->db->escape($data['long']);
    $location_name = $this->db->escape($data['loc']);

    return $this->db->query("INSERT INTO deeds_db.institution_location (instid, inst_name, lat,`long`,location) 
        values ($instid,$inst_name,$lat, $long, $location_name)
        ON DUPLICATE KEY UPDATE 
          instid = $instid,
          lat = $lat,
          `long` = $long,
          location = $location_name");

  }

  function save_date($data) {
    if ($data['dateid']) {
      $did = $data['dateid'];
    } else {
      $did = $this->get_new_date_id();
    }
    $inst_name = $this->db->escape($data['inst_name']);
    $first_date = $this->db->escape($data['first-date']);
    $last_date = $this->db->escape($data['last-date']);

    if ($data['circa'] == 'Yes') {
      $circa = $this->db->escape('Circa');
    } else {
      $circa = $this->db->escape(NULL);
    }

    return $this->db->query("INSERT INTO deeds_db.institution_dates (did, inst_name, first_date,last_date,circa) 
        values ($did,$inst_name,$first_date, $last_date, $circa)
        ON DUPLICATE KEY UPDATE 
          inst_name = $inst_name,
          first_date = $first_date,
          last_date = $last_date,
          circa = $circa");

  }

  function check_inst_name($inst_name) {
    $query = $this->db->get_where('institution',array('inst_name' => $inst_name));
    $result = $query->result();
    return $result;
  }

  function save_info($instid, $data) {
  		$inst_name = $this->db->escape($data['inst_name']);
  		$inst_rank = $this->db->escape($data['inst_rank']);
  		$inst_type = $this->db->escape($data['inst_type']);
  		$old_name = $this->db->escape($data['old_name']);
  		$mother_house = $this->db->escape($data['mother_house']);

      if ($data['order_name'] == '') { 
        $order_name = $this->db->escape(NULL);
      } else { 
        $order_name = $this->db->escape($data['order_name']);
      }

      if ($data['mother_house'] == '') { 
        $mother_house = $this->db->escape(NULL);
      } else { 
        $mother_house = $this->db->escape($data['mother_house']);
      }

      if ($data['alien_house'] == 'Yes') {
        $alien_house = $this->db->escape('Alien');
      } else {
        $alien_house = $this->db->escape(NULL);
      }

      if ($data['convent'] == 'Yes') {
        $convent = $this->db->escape('Yes');
      } else {
        $convent = $this->db->escape(NULL);
      }

		return $this->db->query("INSERT INTO deeds_db.institution (instid, inst_name, inst_rank, inst_type, old_name, order_name, mother_house, alien_house,convent) 
  			values ($instid,$inst_name,$inst_rank,$inst_type,$old_name,$order_name,$mother_house,$alien_house,$convent)
  			ON DUPLICATE KEY UPDATE 
  				instid = $instid,
  				inst_rank = $inst_rank,
  				inst_type = $inst_type,
  				old_name = $old_name,
  				order_name = $order_name,
  				mother_house = $mother_house,
  				alien_house = $alien_house,
  				convent = $convent");
  	}

    function save_resource($data) {
        $instid = $this->db->escape($data['instid']);
        $inst_name = $this->db->escape($data['inst_name']);
        $wiki = $this->db->escape($data['wiki']);
     return $this->db->query("INSERT INTO deeds_db.institution_resource (instid, inst_name, wiki) 
        values ($instid,$inst_name,$wiki)
        ON DUPLICATE KEY UPDATE 
          inst_name = $inst_name,
          wiki = $wiki");       
    }

}

?>