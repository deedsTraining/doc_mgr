<?php
class Cartulary_model extends CI_Model {

  function __construct() {
    parent::__construct();
  }

  function get_all_cartularies() {
    $query = $this->db->order_by('short', 'asc')->get_where('cartulary');
    $result = $query->result();
    return $result;
  }

  function get_info($cartnum) {
    $sql = 'SELECT * FROM deeds_db.cartulary WHERE deeds_db.cartulary.cartnum = ?';
    $query = $this->db->query($sql, array($cartnum));
    $cartulary = $query->row_array();
    return $cartulary;
  }

  function save_info($cartnum, $data) {

    $cartnum = $this->db->escape($data['cartnum']);
    $short = $this->db->escape($data['short']);
    $title = $this->db->escape($data['title']);
    $multi = $this->db->escape($data['multi']);
    $order_name = $this->db->escape($data['order_name']);
    $cart_type = $this->db->escape($data['cart_type']);
    $series = $this->db->escape($data['series']);
    $private = $this->db->escape($data['private']);
    $diplomatics = $this->db->escape($data['diplomatics']);
    $names = $this->db->escape($data['names']);

    $query = $this->db->query("INSERT INTO deeds_db.cartulary
        (cartnum, short, title, multi, order_name, cart_type, series, private, diplomatics, names)
        VALUES ($cartnum, $short, $title, $multi, $order_name, $cart_type, $series, $private, $diplomatics, $names)
        ON DUPLICATE KEY UPDATE
          short = $short,
          title = $title,
          multi = $multi,
          order_name = $order_name,
          cart_type = $cart_type,
          series = $series,
          private = $private,
          diplomatics = $diplomatics,
          names = $names");

    return $query;
  }

  /* getters */

  function get_resource($cartnum) { // 1 to 1
    $query = $this->db->query('
        SELECT * FROM deeds_db.cartulary_resource
        WHERE deeds_db.cartulary_resource.cartnum = ?', array($cartnum));
    $resource = $query->row_array();
    return $resource;
  }

  function get_institutions($cartnum) { // 1 to many
    $ins = array();
    $query = $this->db->query('
        SELECT * FROM deeds_db.cartulary_institution
        WHERE deeds_db.cartulary_institution.cartnum = ?', array($cartnum));
    $ins = $query->result_array();
    return $ins;
  }

  function get_biblio($cartnum) { // 1 to 1
    $query = $this->db->query('
        SELECT * FROM deeds_db.cartulary_biblio
        WHERE deeds_db.cartulary_biblio.cartnum = ?', array($cartnum));
    $biblio = $query->row_array();
    return $biblio;
  }

  function get_sources($cartnum) { // 1 to many
    $sources = array();

    $query = $this->db->query('SELECT * FROM deeds_db.cartulary_source WHERE cartnum = ?', array($cartnum));

    $sources = $query->result_array();
    return $sources;
  }

  function get_image($cartnum) { // 1 to 1
    $query = $this->db->query('
        SELECT * FROM deeds_db.cartulary_image img
        WHERE img.cartnum = ?', array($cartnum));
    $image = $query->row_array();
    return $image;
  }

  function get_locations($cartnum) { // 1 to 1
    $query = $this->db->query('
        SELECT * FROM deeds_db.cartulary_location loc
        WHERE loc.cartnum = ?
        ORDER BY instance ASC', array($cartnum));

    $locations = $query->result_array() ;
    return $locations;
  }

  function get_order_names() {
	$query = $this->db->query('
		select order_name from deeds_db.dm_institution_order order by order_name;');
	$names[''] = '';
	foreach ($query->result_array() as $row) {
		$names[$row['order_name']] = $row['order_name'];
	}
	return $names;
  }

  function get_series() {
	$query = $this->db->query('
		select series from deeds_db.dm_series order by series;');
	$names[''] = '';
	foreach ($query->result_array() as $row) {
		$names[$row['series']] = $row['series'];
	}
	return $names;
  }

  function get_cart_types() {
	$query = $this->db->query('
		select source from deeds_db.dm_charter_source order by source;');
	$cart_types[''] = 'Please Select';
	foreach ($query->result_array() as $row) {
		$cart_types[$row['source']] = $row['source'];
	}
    return $cart_types;
  }

  /* Setters */

  function save_image($cartnum, $data) {
    $cartnum = $this->db->escape($cartnum);
    $image = $this->db->escape($data['image']);
    $thumb = $this->db->escape($data['thumb']);
    $query = $this->db->query("
        INSERT INTO deeds_db.cartulary_image
        (cartnum, image, thumb)
        VALUES ($cartnum, $image, $thumb)
        ON DUPLICATE KEY UPDATE
          image = $image,
          thumb = $thumb");

    return $query;
  }

  function save_resource($cartnum, $data) {
    $cartnum = $this->db->escape($cartnum);
    $utl = $this->db->escape($data['utl']);
    $google = $this->db->escape($data['google']);
    $wiki = $this->db->escape($data['wiki']);
    $pdf = $this->db->escape($data['pdf']);
    $worldcat = $this->db->escape($data['worldcat']);

    $query = $this->db->query("
        INSERT INTO deeds_db.cartulary_resource
        (cartnum, utl, google, wiki, pdf, worldcat)
        VALUES ($cartnum, $utl, $google, $wiki, $pdf, $worldcat)
        ON DUPLICATE KEY UPDATE
          utl = $utl,
          google = $google,
          wiki = $wiki,
          pdf = $pdf,
          worldcat = $worldcat"
    );

    return $query;
  }

  function save_source($cartnum, $data) {
	$cartnum_e = $this->db->escape($data['cartnum']);
	$sourceid = $this->db->escape($data['sourceid']);
	$source_url = $this->db->escape($data['source_url']);
	$source_title = $this->db->escape($data['source_title']);
	$query = $this->db->query("INSERT INTO deeds_db.cartulary_source
		(cartnum, sourceid, source_url, source_title)
		VALUES ($cartnum_e, $sourceid, $source_url, $source_title)
		ON DUPLICATE KEY UPDATE
		  source_url = $source_url,
		  source_title = $source_title");

	return $query;
  }

  function save_biblio($cartnum, $data) {
    $cartnum = $this->db->escape($data['cartnum']);
    $biblio = $this->db->escape($data['biblio']);

    $query = $this->db->query("INSERT INTO deeds_db.cartulary_biblio
        (cartnum, biblio)
        VALUES ($cartnum, $biblio)
        ON DUPLICATE KEY UPDATE
          biblio = $biblio");

    return $query;
  }

  function save_location($cartnum, $data) {
	$cartnum_e = $this->db->escape($cartnum);
	$instance = $this->db->escape($data['instance']);
	$location = $this->db->escape($data['location']);
	$latlong = explode(',', $data['latlong']);
	$lat = '';
	$long = '';

	if (isset($latlong[0])) {
	  $lat = $this->db->escape($latlong[0]);
	}

	if (isset($latlong[1])) {
	  $long = $this->db->escape($latlong[1]);
	}

	$query = $this->db->query("
		INSERT INTO deeds_db.cartulary_location
		(cartnum, instance, location, lat, `long`)
		VALUES ($cartnum_e, $instance, $location, $lat, $long)
		ON DUPLICATE KEY UPDATE
		  instance = $instance,
		  location = $location,
		  lat = $lat,
		  `long` = $long"
		);

	return $query;
	}

  function save_institution($cartnum, $data) {
    $cartnum_e = $this->db->escape($cartnum);
	$instance = $this->db->escape($data['instance']);
	$institution = $this->db->escape($data['institution']);
	$query = $this->db->query("INSERT INTO deeds_db.cartulary_institution
		(cartnum, instance, institution)
		VALUES ($cartnum_e, $instance, $institution)
		ON DUPLICATE KEY UPDATE
		  instance = $instance,
		  institution = $institution");
    return $query;
  }
}
