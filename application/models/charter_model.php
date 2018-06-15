<?php
class Charter_model extends CI_Model {

  function __construct() {
    parent::__construct();
    $this->load->model('Dm_lookup');
  }

  function get_all_charters($startsWith = '0001') {
    $query = $this->db->like('docnum', $startsWith, 'after')->order_by('docnum', 'asc')->get_where('charter');
    $result = $query->result();
    return $result;
  }

  /**** Getters ****/

  // 1 to 1
  function get_charter($docnum) {
  	$query = $this->db->get_where('charter', array('docnum' => $docnum));
  	$result = $query->result();
  	return empty($result)?$result: $result[0]; //since there will be only one record per docid
  }

  function get_charter_source($docnum) { // 1 to 1
    $query = $this->db->query("SELECT charter_source FROM deeds_db.charter WHERE docnum =".$docnum);
    $result = $query->result();
    return $result;
  }

  function get_charter_status($docnum) { // 1 to 1
    $query = $this->db->query("SELECT charter_status FROM deeds_db.charter WHERE docnum =".$docnum);
    $result = $query->result();
    return $result;
  }

  // Returns the docnum referenced from a document
  function get_charter_ref($docnum = '') {
    if ($docnum != '') {
      $query = $this->db->get_where('charter_status', array('docnum' => $docnum, 'charter_status' => 'Embedded'));

      $row = $query->row();

      if (isset($row->ref)) {
        return $row->ref;
      }
    }
    return '';
  }

  function get_charter_type($docnum) { // 1 to many
    $query = $this->db->get_where('charter_type', array('docnum' => $docnum));
    $type = array();
    foreach ($query->result() as $row) {
      $type[] = $row->charter_type;
    }
    return $type;
  }

  // 1 to many
  function get_doc($docnum) {
    $query = $this->db->get_where('charter_doc',array('docnum' => $docnum));
    $doc = $query->row_array();
    return $doc;
  }

  // 1 to 1
  function get_date($docnum) {
    $query = $this->db->get_where('charter_date', array('docnum' => $docnum));
    $result = $query->result();
    return empty($result)?$result: $result[0]; //since there will be only one record per docid
  }

  // 1 to many
  function get_names_titles($docnum) {
	$query = $this->db->get_where('charter_names_title', array('docnum' => $docnum));
	return $query->result_array();
  }

  function get_note($docnum) {
    $query = $this->db->get_where('charter_note',array('docnum' => $docnum));

    return $query->result_array();
  }

  // 1 to 1
  function get_image($docnum) {
    $query = $this->db->get_where('charter_image', array('docnum' => $docnum));
    $result = $query->row();
    return $result;
  }

    // 1 to many
  function get_resources($docnum) {
    $query = $this->db->get_where('charter_resource', array('docnum' => $docnum));
    return $query->result_array();
  }

  // 1 to many
  function get_parties($docnum) {
	$query = $this->db->get_where('charter_parties', array('docnum' => $docnum));
	return $query->result_array();
  }

   // 1 to many
  function get_locations($docnum) {
	$query = $this->db->get_where('charter_location', array('docnum' => $docnum));
	return $query->result_array();
  }

  // Get the corresponding cartulary's title - used for Solr Indexing
  function get_cart_title($docnum) {
	$query = $this->db->get_where('cartulary', array('cartnum' => substr($docnum, 0, 4)));
  	return $query->row()->title;
  }

  // Get the corresponding cartulary's Biblio title - used for Solr Indexing
  function get_cart_bib_title($docnum) {
	$query = $this->db->get_where('cartulary_biblio', array('cartnum' => substr($docnum, 0, 4)));
  	return $query->row()->biblio;
  }

  // 1 to many
  function get_names($docnum) {
    $this->db->order_by('instance', 'asc');
    $this->db->order_by('item', 'asc');
	$query = $this->db->get_where('charter_names', array('docnum' => $docnum));
	$result = $query->result_array();
	return $result;
  }

  function get_diplomatics($docnum) {
    $this->db->order_by('instance', 'asc');
    $this->db->order_by('item', 'asc');
	$query = $this->db->get_where('charter_diplomatics', array('docnum' => $docnum));
	$result = $query->result_array();
	return $result;
  }

  function get_parties_type($docnum) {
	$query = $this->db->query('
		select parties_type from deeds_db.dm_parties_type order by parties_type;');
	foreach ($query->result_array() as $row) {
		$parties_type[$row['parties_type']] = $row['parties_type'];
	}
    return $parties_type;
  }

  function get_parties_person_types($docnum) {
	$query = $this->db->query('SELECT distinct person_type FROM deeds_db.charter_parties;');
	foreach ($query->result_array() as $row) {
		$parties_person_types[$row['person_type']] = $row['person_type'];
	}
    return $parties_person_types;
  }

  function get_parties_name_types($docnum) {
	$query = $this->db->query('SELECT name_type FROM deeds_db.dm_parties_name_type;');
	foreach ($query->result_array() as $row) {
		$parties_name_types[$row['name_type']] = $row['name_type'];
	}
    return $parties_name_types;
  }

  function get_parties_name_role($docnum) {
	$query = $this->db->query('SELECT name_role FROM deeds_db.dm_parties_name_role;');
	foreach ($query->result_array() as $row) {
		$name_roles[$row['name_role']] = $row['name_role'];
	}
    return $name_roles;
  }

  function get_inst_names($docnum) {
	$query = $this->db->query('SELECT inst_name FROM deeds_db.institution;');
	$inst_names[''] = ''; // We need a null choice in the dropdown
	foreach ($query->result_array() as $row) {
		$inst_names[$row['inst_name']] = $row['inst_name'];
	}
    return $inst_names;
  }

  function get_definitive_name_types($docnum) {
	$query = $this->db->query('SELECT distinct definitive_name_type FROM deeds_db.charter_names_title where definitive_name_type is not null order by definitive_name_type;');
	foreach ($query->result_array() as $row) {
		$definitive_name_types[$row['definitive_name_type']] = $row['definitive_name_type'];
	}
    return $definitive_name_types;
  }

  function get_charter_origin($docnum) {
	$query = $this->db->query("SELECT origin FROM deeds_db.dm_charter_origin order by origin;");
	$origins[''] = '';
	foreach ($query->result_array() as $row) {
		$origins[$row['origin']] = $row['origin'];
	}
    return $origins;
  }

  function get_name_title_name_roles($docnum) {
	$query = $this->db->query('SELECT distinct name_role FROM deeds_db.charter_names_title where name_role is not null order by name_role;');
	foreach ($query->result_array() as $row) {
		$name_roles[$row['name_role']] = $row['name_role'];
	}
    return $name_roles;
  }

  function get_name_natures($docnum) {
	$query = $this->db->query('SELECT nature_type FROM deeds_db.dm_charter_names_nature order by nature_type;');
	$nature_types[''] = '';
	foreach ($query->result_array() as $row) {
		$nature_types[$row['nature_type']] = $row['nature_type'];
	}
    return $nature_types;
  }

  function get_title_txt($docnum) {
	$query = $this->db->query('SELECT distinct title_txt FROM deeds_db.charter_names_title where title_txt is not null order by title_txt;');
	$nature_types[''] = '';
	foreach ($query->result_array() as $row) {
		$title_txts[$row['title_txt']] = $row['title_txt'];
	}
    return $title_txts;
  }

  function get_date_scope($docnum) {
	$query = $this->db->query('SELECT date_scope FROM deeds_db.dm_date_scope;');
	$date_scope[''] = '';
	foreach ($query->result_array() as $row) {
		$date_scope[$row['date_scope']] = $row['date_scope'];
	}
    return $date_scope;
  }

  //   /**** Setters ****/
  // function save_charter_status($docnum, $charter_status, $embedded_in = '') {

  //   $charter_status = $this->db->escape($charter_status);

  //   $insert_to_charter_date = $this->db->query("INSERT INTO deeds_db.charter_date (docnum, dated, lodate, hidate, circa, date_precision, scope, date_type)
  //     VALUES ('$docnum', $dated_db, $lodate, $hidate, $circa, $date_precision, $scope, $date_type_string)
  //     ON DUPLICATE KEY UPDATE dated = $dated_db, lodate = $lodate, hidate = $hidate, date_precision = $date_precision, circa = $circa, scope = $scope, date_type = $date_type_string");


  //   // Clear previous status entries in charter_status table
  //   //$this->db->delete('charter_status', array('docnum' => $docnum));

  //   // $status_code_lookup = $this->Dm_lookup->get_dm_charter_status();
  //   // $status_code = array();
  //   // foreach ($status_code_lookup as $status_lookup) {
  //   //   $status_code[$status_lookup->status] = $status_lookup->status_code;
  //   // }

  //   // // Insert new entries
  //   // foreach ($data as $i => $status_str) {
  //   //   $this->db->insert('charter_status', array(
  //   //     'docnum' => $docnum,
  //   //     'instance' => $i+1,
  //   //     'status_code' => $status_code[$status_str],
  //   //     'ref' => $status_str != 'Embedded' ? '':$embedded_in,
  //   //     'charter_status' => $status_str
  //   //   ));
  //   // }

  // }

  function save_charter_type($docnum, $data) {
    // Clear previous entries in charter_type table
    $this->db->delete('charter_type', array('docnum' => $docnum));

    // Insert new entries
    foreach ($data as $i => $type_str) {
      $this->db->insert('charter_type', array(
        'docnum' => $docnum,
        'instance' => $i+1,
        'charter_type' => $type_str
      ));
    }

    // Update the main charter table
    $this->db->update(
        'charter', // the table
        array('charter_type' => implode(', ', $data)), // the data
        array('docnum' => $docnum) // WHERE clause
    );
  }

  function save_doc($docnum, $data) {
    $docnum = $this->db->escape($docnum);
    $txt = $this->db->escape($data['txt']);

    // remove punctuations and convert to lower case
    $punctuations = array(
        '~',
        '`',
        '!',
        '@',
        '#',
        '$',
        '%',
        '^',
        '&',
        '*',
        '(',
        ')',
        '-',
        '+',
        '=',
        '{',
        '[',
        '}',
        ']',
        '|',
        '\\',
        ':',
        ';',
        '"',
        '\'',
        '<',
        ',',
        '>',
        '.',
        '?',
        '/'
    );
    $ctx = strtolower(trim(str_replace($punctuations, '', $data['txt'])));
    $ctxt = $this->db->escape($ctx);


    $query = $this->db->query("INSERT INTO deeds_db.charter_doc
        (docnum, txt, ctxt)
        VALUES ($docnum, $txt, $ctxt)
        ON DUPLICATE KEY UPDATE
          txt = $txt,
          ctxt = $ctxt");

    return $query;
  }

  function save_date($docnum, $data) {
    $dated_db = $this->db->escape($data['dated']);
    $lodate = $this->db->escape($data['lodate']);
    $hidate = $this->db->escape($data['hidate']);
    $circa = $this->db->escape($data['circa']);
    $scope = $this->db->escape($data['scope']);
    $date_type_array = $data['date_type'];

    $date_type_string = $this->db->escape(implode(", ",$date_type_array));

    $lodate_obj = new DateTime($data['lodate']);
    $hidate_obj = new DateTime($data['hidate']);

    $interval_obj = $hidate_obj->diff($lodate_obj);

    $interval_val = (int) $interval_obj->format('%a');

    if ($data['dated'] != '0000-00-00') {
      $date_precision = 0;
    } else {

      if($interval_val > 0 && $interval_val <= 365 ) {
        $date_precision = 1;
      }

      if ($interval_val > 365 && $interval_val <= 730) {
        $date_precision = 2;
      }

      if ($interval_val> 730 || $circa == null) {
        $date_precision = null;
      }
    }

    $date_precision = $this->db->escape($date_precision);

    $insert_to_charter_date = $this->db->query("INSERT INTO deeds_db.charter_date (docnum, dated, lodate, hidate, circa, date_precision, scope, date_type)
      VALUES ('$docnum', $dated_db, $lodate, $hidate, $circa, $date_precision, $scope, $date_type_string)
      ON DUPLICATE KEY UPDATE dated = $dated_db, lodate = $lodate, hidate = $hidate, date_precision = $date_precision, circa = $circa, scope = $scope, date_type = $date_type_string");

    if ($insert_to_charter_date) {
      //delete what's on charter_date_detail first

      $this->db->delete('charter_date_detail', array('docnum' => $docnum));

      $instance = 1;

      foreach ($date_type_array as $date_type) {
        $date_type_escape = $this->db->escape($date_type);
        $insert_data = array('docnum' => $docnum, 'instance' => $instance, 'details_type' => $date_type_escape);
        $this->db->insert('charter_date_detail', $insert_data);

        $instance ++;
      }

      return true;


    } else {
      return false;
    }
  }

  function get_last_instance($docnum) {
    $this->db->select_max('instance');
    $query = $this->db->get_where('charter_note', array('docnum' => $docnum));
    $result = $query->result();
    return (int)$result[0]->instance;
  }

  function save_note($data) {
	$docid = $this->db->escape($data['docid']);
	$docnum = $this->db->escape($data['docnum']);
	$note_type = $this->db->escape($data['note_type']);
	$note_text = $this->db->escape($data['note_text']);
	$instance = $this->db->escape($data['instance']);
	
	$query = $this->db->query("INSERT INTO deeds_db.charter_note
        (docid, docnum, instance, note_type, note_text)
        VALUES ($docid, $docnum, $instance, $note_type, $note_text)
        ON DUPLICATE KEY UPDATE		
		  instance = $instance,
          note_type = $note_type,
		  note_text = $note_text");
    return $query;
  }

  function save_charter($data) {

    $docnum = $this->db->escape($data['docnum']);
    $language = $this->db->escape($data['language']);
    $origin = $this->db->escape($data['origin']);

    $charter_type = '';
    $charter_type_data = array();
    if (isset($data['charter_type']) && is_array($data['charter_type'])) {
      if (!empty($data['charter_type'])) {
        $charter_type = $this->db->escape(implode(', ', $data['charter_type']));
      }
      $charter_type_data = $data['charter_type'];
    }
    if ($charter_type == '') { $charter_type = "''"; }

    $charter_source = $this->db->escape($data['charter_source']);

    if (isset($data['charter_status']) && !empty($data['charter_status'])) {
        $charter_status = $this->db->escape($data['charter_status']);
    }

    $this->db->query("INSERT INTO deeds_db.charter
        (docnum, language, origin, charter_type, charter_source, charter_status)
        VALUES ($docnum, $language, $origin, $charter_type, $charter_source, $charter_status)
        ON DUPLICATE KEY UPDATE
          language = $language,
          origin = $origin,
          charter_type = $charter_type,
          charter_source = $charter_source,
          charter_status = $charter_status");

    $this->save_charter_type($data['docnum'], $charter_type_data);
    //$this->save_charter_status($data['docnum'], $charter_status_data, $data['embedded-in']);
  }

  function save_names_title($data) {
	$docnum = $this->db->escape($data['docnum']);
	$layer_instance = $this->db->escape($data['layer_instance']);
	$item_instance = $this->db->escape($data['item_instance']);
	$title_instance = $this->db->escape($data['title_instance']);
	$first_name = $this->db->escape($data['first_name']);
	$definitive_name = $this->db->escape($data['definitive_name']);
	$standard_name = $this->db->escape($data['standard_name']);
	$definitive_name_type = $this->db->escape($data['definitive_name_type']);
	$name_type = $this->db->escape($data['name_type']);
	$name_role = $this->db->escape($data['name_role']);
	$nature = $this->db->escape($data['nature']);
	$title_txt = $this->db->escape($data['title_txt']);
	$inst_name = $this->db->escape($data['inst_name']);	

	$query = "INSERT INTO deeds_db.charter_names_title
		(docnum, layer_instance, item_instance, title_instance,
		first_name, definitive_name, standard_name, definitive_name_type, name_type,
		name_role, nature, title_txt, inst_name)
		VALUES ($docnum, $layer_instance, $item_instance, $title_instance,
		$first_name, $definitive_name, $standard_name, $definitive_name_type, $name_type,
		$name_role, $nature, $title_txt, $inst_name)
		ON DUPLICATE KEY UPDATE
			first_name = $first_name,
			definitive_name = $definitive_name,
			standard_name = $standard_name,
			definitive_name_type = $definitive_name_type,
			name_type = $name_type,
			name_role = $name_role,
			nature = $nature,
			title_txt = $title_txt,
			inst_name = $inst_name";	
	
	return $query;
  }

  function save_names($data) {
	for ($i = 0; $i < count($data['instance']); $i++) {
		if ($data['docnum'][$i] != '') {
			$nid = $this->db->escape($data['nid'][$i]);
			$docnum = $this->db->escape($data['docnum'][$i]);
			$instance = $this->db->escape($data['instance'][$i]);
			$item = $this->db->escape($data['item'][$i]);
			$names = $this->db->escape($data['names'][$i]);
			$start = $this->db->escape($data['start'][$i]);
			$end = $this->db->escape($data['end'][$i]);
			$txt = $this->db->escape($data['txt'][$i]);

			$query = "INSERT INTO deeds_db.charter_names
			(nid, docnum, instance, item, names, start, end, txt)
			VALUES ($nid, $docnum, $instance, $item, $names, $start, $end, $txt)
			ON DUPLICATE KEY UPDATE
				nid = $nid,
				names = $names,
				start = $start,
				end = $end,
				txt = $txt";

			if (!$this->db->query($query)) {
				return false;
			}
		}
	}
	return true;
  }

  function save_markup_name($docnum, $data) {
    $instance = $this->db->escape($data['markup_instance_val']);
    $item = $this->db->escape($data['markup_item_val']);
    $names = $this->db->escape($data['markup_type']);
    $start = $this->db->escape($data['markup_start_val']);
    $end = $this->db->escape($data['markup_end_val']);
    $txt = $this->db->escape($data['markup_text_val']);

    return $this->db->query("INSERT INTO deeds_db.charter_names
        (docnum, instance, item, names, start, end, txt)
        VALUES ('$docnum', $instance, $item, $names, $start, $end, $txt)
        ON DUPLICATE KEY UPDATE
          names = $names,
          start = $start,
          end = $end,
          txt = $txt");
  }

  function save_markup_diplomatic($docnum, $data) {
    $instance = $this->db->escape($data['markup_instance_val']);
    $item = $this->db->escape($data['markup_item_val']);
    $layer = $this->db->escape($data['markup_type']);
    $start = $this->db->escape($data['markup_start_val']);
    $end = $this->db->escape($data['markup_end_val']);
    $txt = $this->db->escape($data['markup_text_val']);

    return $this->db->query("INSERT INTO deeds_db.charter_diplomatics
        (docnum, instance, layer, start, end, item, txt)
        VALUES ('$docnum', $instance, $layer, $start, $end, $item, $txt)
        ON DUPLICATE KEY UPDATE
          layer = $layer,
          start = $start,
          end = $end, 
          txt = $txt");
  }

  function save_image($docnum, $data) {
	$docnum = $this->db->escape($docnum);
	$image = $this->db->escape($data['image']);
	$thumb = $this->db->escape($data['thumb']);

	$query = $this->db->query("INSERT INTO deeds_db.charter_image
        (docnum, image, thumb)
        VALUES ($docnum, $image, $thumb)
        ON DUPLICATE KEY UPDATE
          image = $image,
		  thumb = $thumb");

    return $query;
  }

  function save_resource($docnum, $data) {
	$docnum = $this->db->escape($docnum);
	$resource_url = $this->db->escape($data['resource_url']);
	$url_title = $this->db->escape($data['url_title']);

	$query = $this->db->query("INSERT INTO deeds_db.charter_resource
        (docnum, resource_url, url_title)
        VALUES ($docnum, $resource_url, $url_title)
        ON DUPLICATE KEY UPDATE
          resource_url = $resource_url,
		  url_title = $url_title");
    return $query;
  }

  function save_parties($docnum, $data) {
	$docnum = $this->db->escape($data['docnum']);
	for ($i = 0; $i < count($data['party_instance']); $i++) {
		$party_instance = $this->db->escape($data['party_instance'][$i]);
		$title_instance = $this->db->escape($data['title_instance'][$i]);
		$person_instance = $this->db->escape($data['person_instance'][$i]);
		$parties_type = $this->db->escape($data['parties_type'][$i]);
		$person_type = $this->db->escape($data['person_type'][$i]);
		$name_type = $this->db->escape($data['name_type'][$i]);
		$name_role = $this->db->escape($data['name_role'][$i]);
		$name_link = $this->db->escape($data['name_link'][$i]);
		$name_txt = $this->db->escape($data['name_txt'][$i]);
		$title_inst = $this->db->escape($data['title_inst'][$i]);
		$title_txt = $this->db->escape($data['title_txt'][$i]);

		$query = "INSERT INTO deeds_db.charter_parties
			(docnum, party_instance, title_instance, person_instance,
			parties_type, person_type, name_type, name_role,
			name_link, name_txt, title_inst, title_txt)
			VALUES ($docnum, $party_instance, $title_instance, $person_instance,
			$parties_type, $person_type, $name_type, $name_role,
			$name_link, $name_txt, $title_inst, $title_txt)
			ON DUPLICATE KEY UPDATE
			parties_type = $parties_type,
			person_type = $person_type,
			name_type = $name_type,
			name_role = $name_role,
			name_link = $name_link,
			name_txt = $name_txt,
			title_inst = $title_inst,
			title_txt = $title_txt";

		if (!$this->db->query($query)) {
			return false;
		}
	}
    return true;
  }

  function save_locations($docnum, $data) {

	if ($data['instance'] != '') {
		$docnum = $this->db->escape($data['docnum']);
		$instance = $this->db->escape($data['instance']);
		$location_type = $this->db->escape($data['location_type']);
		$property_type = $this->db->escape($data['property_type']);
		$latlong = explode(',', $data['latlong']);
		$lat = '';
		$long = '';

		if (isset($latlong[0])) {
		  $lat = $this->db->escape($latlong[0]);
		}

		if (isset($latlong[1])) {
		  $long = $this->db->escape($latlong[1]);
		}

		$country = $this->db->escape($data['country']);
		$county = $this->db->escape($data['county']);
		$place = $this->db->escape($data['place']);
		$location = $this->db->escape("{$data['place']}, {$data['county']}, {$data['country']}");

		$query = "INSERT INTO deeds_db.charter_location
			(docnum, instance, location_type, property_type,
			lat, `long`, location, country, county, place)
			VALUES ($docnum, $instance, $location_type, $property_type,
			$lat, $long, $location, $country, $county, $place)
			ON DUPLICATE KEY UPDATE
			location_type = $location_type,
			property_type = $property_type,
			lat = $lat,
			`long` = $long,
			location = $location,
			country = $country,
			county = $county,
			place = $place";

		if (!$this->db->query($query)) {
			return false;
		}
	}

    return true;
  }
}
