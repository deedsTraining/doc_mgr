<?php
/**
 * @file dm_lookup.php a model for the dm_* lookup tables
 */
class Dm_lookup extends CI_Model {
  public function __construct() {
    parent::__construct();
  }

  public function get_dm_charter_status() { // 1 to many
    $query = $this->db->query("SELECT status_code, status FROM deeds_db.dm_charter_status order by status_code");
    $result = $query->result();
    return $result;
  }

  public function get_dm_charter_language() {
    $this->db->order_by('language', 'asc');
    $query = $this->db->get('dm_charter_language');
    return $query->result();
  }

  public function get_dm_charter_origin() {
    $this->db->order_by('origin', 'asc');
    $query = $this->db->get('dm_charter_origin');
    return $query->result();
  }

  public function get_dm_charter_type() {
    $query = $this->db->get('dm_charter_type');
    return $query->result();
  }

  public function get_dm_names() {
    $this->db->order_by('nid', 'asc');
    $query = $this->db->get('dm_names');
    return $query->result();
  }

  public function get_dm_diplomatics() {
    $this->db->order_by('layer', 'asc');
    $query = $this->db->get('dm_diplomatics');
    return $query->result();
  }

  public function get_dm_markup_types() {
    $names = $this->get_dm_names();
    $diplomatics = $this->get_dm_diplomatics();

    return array('names' => $names, 'diplomatics' => $diplomatics);
  }

  public function get_dm_charter_note() {
    $query = $this->db->get('dm_charter_note');
    return $query->result();
  }

  public function get_dm_charter_source() {
    $query = $this->db->get('dm_charter_source');
    return $query->result();
  }

  public function get_dm_inst_type() {
    $query = $this->db->query('select distinct(inst_type) from deeds_db.dm_institution_type order by inst_type');
    $inst_types['']='Please Select';
    foreach ($query->result_array() as $row) {
      $inst_types[$row['inst_type']] = $row['inst_type'];
    }
    return $inst_types;
  }

  public function get_dm_inst_rank() {

    $this->db->select('inst_type, inst_rank');
    $this->db->order_by('inst_rank', 'asc');
    $query = $this->db->get('deeds_db.dm_institution_type');
    $inst_ranks['']='Please Select';

    if($query->result()){
        foreach ($query->result() as $row) {
            $inst_ranks[$row->inst_type][$row->inst_rank] = $row->inst_rank;
        }
        return $inst_ranks;
    } else {
        return FALSE;
    }

  }

}