<?php

function deeds_do_index_cartularies($cartnum) {

	$record_type = 'Cartulary';
	$sql = 'SELECT cart.*, bib.biblio, loc.location, res.utl, res.google, res.wiki, res.worldcat
			FROM deeds_db.cartulary cart
			LEFT JOIN deeds_db.cartulary_biblio bib ON cart.cartnum = bib.cartnum
			LEFT JOIN deeds_db.cartulary_location loc ON cart.cartnum = loc.cartnum
			LEFT JOIN deeds_db.cartulary_resource res ON cart.cartnum = res.cartnum 
			WHERE cart.cartnum =\''.$cartnum.'\'
			';

	$ci=& get_instance();
	$ci->load->database(); 

	$unique_id = $record_type.'_'.$cartnum; 

	$query = $ci->db->query($sql);
	$result = $query->result_array();

	$count = 0;
	$row = $result[0];

	$ci->load->library("Apache/Solr/Apache_Solr_Service", array('host' => SOLR_HOST, 'port' =>SOLR_PORT, 'path' => SOLR_PATH), 'service');
	$ci->load->library("Apache/Solr/Apache_Solr_Document", '', 'doc');


	foreach ($result as $row) {
		try {

			$cart_num = $row['cartnum'];

			$ci->doc->addField('unique_id', $unique_id);
			$ci->doc->addField('record_type', $record_type);
			$ci->doc->addField('cart_num', $cart_num);

			if ($row['location'] != '' && $row['location'] != 'NULL') {
				$ci->doc->addField('cart_location', $row['location']);
				$cart_loc = explode(', ', trim($row['location']));
				if (count($cart_loc) == 3) {
					$ci->doc->addField('cart_country', $cart_loc[2]);
					$ci->doc->addField('cart_county', $cart_loc[1]);
					$ci->doc->addField('cart_place', $cart_loc[0]);
				}

				if (count($cart_loc) == 2) {
					$ci->doc->addField('cart_country', $cart_loc[1]);
					$ci->doc->addField('cart_county', $cart_loc[0]);
				}

				if (count($cart_loc) == 1) {
					$ci->doc->addField('cart_country', $cart_loc[0]);
				}
			}

			$ci->doc->addField('cart_title_short', $row['short']);
			$ci->doc->addField('cart_title', $row['title']);
			$ci->doc->addField('cart_bib_title', $row['biblio']);

			$cart_order_name = '';
			if ($row['order_name'] != '' && $row['order_name'] != 'NULL') {
				$ci->doc->addField('cart_order_name', $row['order_name']);
			}

			$cart_institutions = deeds_solr_helper_get_cartulary_institutions($cart_num, $ci);


			foreach ($cart_institutions as $cart_inst) {
				$ci->doc->addField('cart_inst_rank', $cart_inst['inst_rank']);
				$ci->doc->addField('cart_inst_type', $cart_inst['inst_type']);
				$ci->doc->addField('cart_inst', $cart_inst['inst_name']);
				$ci->doc->addField('cart_inst_disp', $cart_inst['inst_name'].' (Rank:'.$cart_inst['inst_rank'].', Type: '.$cart_inst['inst_type'].')');
			}

			if ($row['series'] != '' && $row['series'] != 'NULL') {
				$ci->doc->addField('cart_series', $row['series']);
			}

			if ($row['multi'] != '' && $row['multi'] != 'NULL') {
				$ci->doc->addField('cart_vol', $row['multi']);
			}

			if ($row['utl'] != '' && $row['utl'] != 'NULL') {
				$ci->doc->addField('cart_utl', $row['utl']);
			}

			if ($row['google'] != '' && $row['google'] != 'NULL') {
				$ci->doc->addField('cart_google', $row['google']);
			}

			if ($row['wiki'] != '' && $row['wiki'] != 'NULL') {
				$ci->doc->addField('cart_wiki', $row['wiki']);
			}

			if ($row['worldcat'] != '' && $row['worldcat'] != 'NULL') {
				$ci->doc->addField('cart_worldcat', $row['worldcat']);
			}

			if ($row['diplomatics'] != '' && $row['diplomatics'] != 'NULL') {
				$ci->doc->addField('cart_diplomatics_complete', 'true');
			}

			if ($row['names'] != '' && $row['names'] != 'NULL') {
				$ci->doc->addField('cart_names_complete', 'true');
			}

			//echo "<pre>".print_r($ci->doc, 1)."</pre>";

			$ci->service->addDocument($ci->doc);
			$ci->service->commit();
			$ci->doc->clear();
			$count++;

		} catch (Exception $e) {
			echo "Error: ".$e->getMessage()."\n";
			echo "File: ".$e->getFile()."\n";
			echo "Line: ".$e->getLine()."\n";
			echo "Code: ".$e->getCode()."\n";
			//echo "Trace: ".print_r($e->getTrace(), TRUE)."\n";
		}
	}
}

function deeds_do_index_charters($docnum) {

	/**
	 * Fields:
	 * - unique_id
	 * - record_type
	 * - country
	 * - county
	 * - place
	 * - cart_title
	 * - cart_bib_title
	 * - dnum
	 * - content
	 * - transactionType
	 * - origin
	 * - language
	 * - docYear_display
	 * - docYear_multi
	 * - copySource
	 */

	$record_type = 'Charter';

	$sql = 'SELECT
				cartulary.short as cart_title_short,
				cartulary.title as cart_title,
				cartulary_biblio.biblio as cart_bib_title,
				charter.*,
				txt.txt,
				charter_date.dated,
				charter_date.lodate,
				charter_date.hidate,
				charter_date.date_precision,
				charter_location.location,
				charter_location.country,
				charter_location.county,
				charter_location.place,
				charter_place.country as origin_country,
				charter_place.county as origin_county,
				charter_place.place as origin_place
			FROM
				deeds_db.charter charter
			LEFT JOIN deeds_db.charter_doc txt ON charter.docnum = txt.docnum
			LEFT JOIN deeds_db.charter_date charter_date ON charter.docnum = charter_date.docnum
			LEFT JOIN deeds_db.charter_location ON charter.docnum = charter_location.docnum AND charter_location.location_type = \'issued\' 
			LEFT JOIN deeds_db.charter_place ON charter.docnum = charter_place.docnum  
			LEFT JOIN deeds_db.cartulary ON cartulary.cartnum = substring(charter.docnum, 1, 4)
			LEFT JOIN deeds_db.cartulary_biblio ON cartulary_biblio.cartnum = substring(charter.docnum, 1, 4) 
			WHERE charter.docnum = \'' . $docnum . '\' LIMIT 100
	'; 
	$ci=& get_instance();
	$ci->load->database(); 
	$query = $ci->db->query($sql);
	$result = $query->result_array();
	$ci->db->flush_cache();

	$count = 0;

	$ci->load->library("Apache/Solr/Apache_Solr_Service", array('host' => SOLR_HOST, 'port' =>SOLR_PORT, 'path' => SOLR_PATH), 'service');
	$ci->load->library("Apache/Solr/Apache_Solr_Document", '', 'doc');


	foreach ($result as $row) {
		try {

			$dnum = $row['docnum'];
			$unique_id = $record_type.'_'.$dnum; 

			$ci->doc->addField('unique_id', $record_type.'_'.$dnum);
			$ci->doc->addField('record_type', $record_type);
			$ci->doc->addField('dnum', $dnum);
			$ci->doc->addField('content', $row['txt']);

			if ($row['location'] != '' && $row['location'] != 'NULL') {
				$ci->doc->addField('location_issued', $row['location']);
			}

			if ($row['country'] != '' && $row['country'] != 'NULL') {
				$ci->doc->addField('issued_country', $row['country']);
			}

			if ($row['county'] != '' && $row['county'] != 'NULL') {
				$ci->doc->addField('issued_county', $row['county']);
			}

			if ($row['place'] != '' && $row['place'] != 'NULL') {
				$ci->doc->addField('issued_place', $row['place']);
			}

			// indexing multi-valued property locations
			$property_locations = deeds_solr_helper_get_charter_property_locations($row['docnum'], $ci);

			foreach ($property_locations as $property_location) {
				$ci->doc->addField('property_country', $property_location['country']);
				$ci->doc->addField('property_county', $property_location['county']);
				$ci->doc->addField('property_place', $property_location['place']);
			}

			if ($row['origin_country'] != '' && $row['origin_country'] != 'NULL') {
				$ci->doc->addField('origin_country', $row['origin_country']);
			}

			if ($row['origin_county'] != '' && $row['origin_county'] != 'NULL') {
				$ci->doc->addField('origin_county', $row['origin_county']);
			}

			if ($row['origin_place'] != '' && $row['origin_place'] != 'NULL') {
				$ci->doc->addField('origin_place', $row['origin_place']);
			}

			if ($row['charter_type'] != '' && $row['charter_type'] != 'NULL') {
				$charter_types = explode(', ', $row['charter_type']);
				foreach ($charter_types as $charter_type) {
					$ci->doc->addField('charter_type', $charter_type);
				}
			}

			$ci->doc->addField('origin', $row['origin']);
			$ci->doc->addField('language', $row['language']);

			$dated = deeds_core_formatyr($row['dated']);
			$lodate = deeds_core_formatyr($row['lodate']);
			$hidate = deeds_core_formatyr($row['hidate']);

			$date_precision = $row['date_precision'];

			if ($dated != '0000') {
				$ci->doc->addField('docYear_display', $dated);
				$ci->doc->addField('docYear_multi', $dated);
			} else if ($lodate != '0000' && $hidate != '0000') {
				$int_loyr = intval($lodate);
				$int_hiyr = intval($hidate);
				for ($i = $int_loyr; $i <= $int_hiyr; $i++) {
					$ci->doc->addField('docYear_multi', $i);
				} 
				if($int_loyr === $int_hiyr) { 
					$ci->doc->addField('docYear_display', $lodate);
				} else { 
					$ci->doc->addField('docYear_display', "{$lodate}-{$hidate}");
				}
			} else if ($lodate != '0000') {
				$ci->doc->addField('docYear_display', $lodate);
				$ci->doc->addField('docYear_multi', $lodate);
			} else if ($hidate != '0000') {
				$ci->doc->addField('docYear_display', $hidate);
				$ci->doc->addField('docYear_multi', $hidate);
			}
			$ci->doc->addField('date_precision', $date_precision);

			$copySource = $row['charter_source'];
			$ci->doc->addField('copySource', $copySource);

			$ci->doc->addField('cart_title_short', $row['cart_title_short']);
 			$ci->doc->addField('cart_title', $row['cart_title']);
 			$ci->doc->addField('cart_bib_title', $row['cart_bib_title']);

			$count++;

			$ci->service->addDocument($ci->doc);
			$ci->service->commit();
			$ci->doc->clear();

		} catch (Exception $e) {
			echo "Error: ".$e->getMessage()."\n";
			echo "File: ".$e->getFile()."\n";
			echo "Line: ".$e->getLine()."\n";
			echo "Code: ".$e->getCode()."\n";
			//echo "Trace: ".print_r($e->getTrace(), TRUE)."\n";
		}
	}
}




function deeds_solr_helper_get_cartulary_institutions($cartnum = '', $obj) {
	$rtval = array();
	if ($cartnum != '') {
		$query = $obj->db->query(
				"SELECT
					cart_inst.cartnum,
					inst.inst_name,
					inst.inst_rank,
					inst.inst_type
					FROM deeds_db.cartulary_institution cart_inst
					LEFT JOIN deeds_db.institution inst ON cart_inst.institution = inst.inst_name
					WHERE cart_inst.cartnum = " . $cartnum);

		foreach ($query->result_array() as $row) {
			$rtval[] = $row;
		}
	}

	return $rtval;
}

function deeds_solr_helper_clear_index($record_type = '', $obj) {
	if ($record_type != '') {
		$obj->load->library("Apache/Solr/Apache_Solr_Service", array('host' => SOLR_HOST, 'port' =>SOLR_PORT, 'path' => SOLR_PATH), 'service');

		switch ($record_type) {
			case 'cartulary':
				$obj->service->deleteByQuery('record_type:Cartulary');
				break;

			case 'charter':
				$obj->service->deleteByQuery('record_type:Charter');
				break;

			case 'all':
				$obj->service->deleteByQuery('*:*');
				break;
		}

		$obj->service->commit();
	}
}

function deeds_solr_helper_clear_index_by_unique_id($unique_id = '', $obj) {
	if ($unique_id != '') {
		$obj->load->library("Apache/Solr/Apache_Solr_Service", array('host' => SOLR_HOST, 'port' =>SOLR_PORT, 'path' => SOLR_PATH), 'service');
        $obj->service->deleteByQuery('unique_id:"'.$unique_id.'"');
		$obj->service->commit();
	}
}

function deeds_solr_helper_check_valid_charter($dnum = '') {
	if ($dnum != '') {

		$ci=& get_instance();
		$ci->load->database(); 
		$query = $ci->db->query('SELECT docnum FROM deeds_db.charter WHERE docnum = \''.$dnum.'\'');
		foreach ($query->result_array() as $row) {
			$docnums[] = $row;
		}
	}

	return $docnums;
}



function deeds_solr_helper_check_valid_cartularies($cartnum = '') {
	if ($cartnum != '') {

		$ci=& get_instance();
		$ci->load->database(); 
		$query = $ci->db->query('SELECT cartnum FROM deeds_db.cartulary WHERE cartnum = \''.$cartnum.'\'');
		foreach ($query->result_array() as $row) {
			$cartnums[] = $row;
		}
	}

	return $cartnums;
}


function deeds_solr_helper_get_charter_property_locations($dnum = '', $obj) {
	$locations = array();
	if ($dnum != '') {
		$query = $obj->db->query('SELECT location, country, county, place FROM deeds_db.charter_location WHERE location_type = \'property\' AND docnum = ' . $dnum . ' ORDER BY instance ASC');
		foreach ($query->result_array() as $row) {
			$locations[] = $row;
		}
	}

	return $locations;
}

function deeds_core_formatyr($year_string) {
  if (strlen($year_string) >= 4) {
    return substr($year_string, 0, 4);
  } else {
    return $year_string;
  }
}
