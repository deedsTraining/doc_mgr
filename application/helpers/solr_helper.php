<?php
/**
 * Indexes Charter and Cartulary into Solr
 * Modified from deeds_solr_index.php
 */
 
// echo "Clearing cartularies  ...\n";
// deeds_solr_helper_clear_index('cartulary');
// echo "Done.  Re-indexing cartularies ...\n";
// deeds_do_index_cartularies();
// echo "Done.\n";

// echo "Clearing charters  ...\n";
// deeds_solr_helper_clear_index('charter');
// echo "Done.  Re-indexing charters ...\n";
//deeds_do_index_charters();

// echo "Done\n";
//test_update();
//deeds_solr_helper_clear_index_by_unique_id('test123');

function deeds_do_index_cartularies($obj, $cartnum) {
	$record_type = 'Cartulary';

	$query = $obj->db->query("
      SELECT cart.*, bib.biblio, loc.location, res.utl, res.google, res.wiki, res.worldcat
      FROM
        deeds_db.cartulary cart
			LEFT JOIN deeds_db.cartulary_biblio bib ON cart.cartnum = bib.cartnum
			LEFT JOIN deeds_db.cartulary_location loc ON cart.cartnum = loc.cartnum
			LEFT JOIN deeds_db.cartulary_resource res ON cart.cartnum = res.cartnum
			where cart.cartnum = " . $cartnum );
	
	$count = 0;
	
	$obj->load->library("Apache/Solr/Apache_Solr_Service", array('host' => SOLR_HOST, 'port' =>SOLR_PORT, 'path' => SOLR_PATH), 'service');

	foreach ($query->result_array() as $row) {
		try {
			$cart_num = $row['cartnum'];
			$obj->load->library("Apache/Solr/Apache_Solr_Document", '', 'document');
			$obj->document->addField('unique_id', $record_type.'_'.$cart_num);
			$obj->document->addField('record_type', $record_type);
			$obj->document->addField('cart_num', $cart_num);

			if ($row['location'] != '' && $row['location'] != 'NULL') {
				$obj->document->addField('cart_location', $row['location']);
				$cart_loc = explode(', ', trim($row['location']));
				if (count($cart_loc) == 3) {
					$obj->document->addField('cart_country', $cart_loc[2]);
					$obj->document->addField('cart_county', $cart_loc[1]);
					$obj->document->addField('cart_place', $cart_loc[0]);
				}

				if (count($cart_loc) == 2) {
					$obj->document->addField('cart_country', $cart_loc[1]);
					$obj->document->addField('cart_county', $cart_loc[0]);
				}

				if (count($cart_loc) == 1) {
					$obj->document->addField('cart_country', $cart_loc[0]);
				}
			}

			$obj->document->addField('cart_title_short', $row['short']);
			$obj->document->addField('cart_title', $row['title']);
			$obj->document->addField('cart_bib_title', $row['biblio']);


			$cart_order_name = '';
			if ($row['order_name'] != '' && $row['order_name'] != 'NULL') {
				$obj->document->addField('cart_order_name', $row['order_name']);
			}

			$cart_institutions = deeds_solr_helper_get_cartulary_institutions($cart_num, $obj);
			foreach ($cart_institutions as $cart_inst) {
				$obj->document->addField('cart_inst_rank', $cart_inst['inst_rank']);
				$obj->document->addField('cart_inst_type', $cart_inst['inst_type']);
				$obj->document->addField('cart_inst', $cart_inst['inst_name']);
				$obj->document->addField('cart_inst_disp', $cart_inst['inst_name'].' (Rank:'.$cart_inst['inst_rank'].', Type: '.$cart_inst['inst_type'].')');
			}

			if ($row['series'] != '' && $row['series'] != 'NULL') {
				$obj->document->addField('cart_series', $row['series']);
			}

			if ($row['multi'] != '' && $row['multi'] != 'NULL') {
				$obj->document->addField('cart_vol', $row['multi']);
			}

			if ($row['utl'] != '' && $row['utl'] != 'NULL') {
				$obj->document->addField('cart_utl', $row['utl']);
			}

			if ($row['google'] != '' && $row['google'] != 'NULL') {
				$obj->document->addField('cart_google', $row['google']);
			}

			if ($row['wiki'] != '' && $row['wiki'] != 'NULL') {
				$obj->document->addField('cart_wiki', $row['wiki']);
			}

			if ($row['worldcat'] != '' && $row['worldcat'] != 'NULL') {
				$obj->document->addField('cart_worldcat', $row['worldcat']);
			}

			if ($row['diplomatics'] != '' && $row['diplomatics'] != 'NULL') {
				$obj->document->addField('cart_diplomatics_complete', 'true');
			}

			if ($row['names'] != '' && $row['names'] != 'NULL') {
				$obj->document->addField('cart_names_complete', 'true');
			}

			$count++;
			//echo "Cartulary add count: $count\n";

			$obj->service->addDocument($obj->document);
			$obj->service->commit();
		} catch (Exception $e) {
			echo "Error: ".$e->getMessage()."\n";
			echo "File: ".$e->getFile()."\n";
			echo "Line: ".$e->getLine()."\n";
			echo "Code: ".$e->getCode()."\n";
			//echo "Trace: ".print_r($e->getTrace(), TRUE)."\n";
		}
	}
}


function deeds_do_index_charters($obj, $docnum) {
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
	$query = $obj->db->query('SELECT
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
						charter_location.place

						FROM
						deeds_db.charter charter
						LEFT JOIN deeds_db.charter_doc txt ON charter.docnum = txt.docnum
						LEFT JOIN deeds_db.charter_date charter_date ON charter.docnum = charter_date.docnum
						LEFT JOIN deeds_db.charter_location ON charter.docnum = charter_location.docnum AND charter_location.location_type = \'issued\'
						LEFT JOIN deeds_db.cartulary ON cartulary.cartnum = substring(charter.docnum, 1, 4)
						LEFT JOIN deeds_db.cartulary_biblio ON cartulary_biblio.cartnum = substring(charter.docnum, 1, 4)
	    				where charter.docnum = \'' . $docnum . '\' LIMIT 100
						');
	$result = $query->result_array();
	
	$count = 0;

	$obj->load->library("Apache/Solr/Apache_Solr_Service", array('host' => SOLR_HOST, 'port' =>SOLR_PORT, 'path' => SOLR_PATH), 'service');

	foreach ($result as $row) {
		try {
			$dnum = $row['docnum'];
			$obj->load->library("Apache/Solr/Apache_Solr_Document", '', 'document');
			$obj->document->addField('unique_id', $record_type.'_'.$dnum);
			$obj->document->addField('record_type', $record_type);
			$obj->document->addField('dnum', $dnum);
			$obj->document->addField('content', $row['txt']);

			if ($row['location'] != '' && $row['location'] != 'NULL') {
				$obj->document->addField('location_issued', $row['location']);
			}

			if ($row['country'] != '' && $row['country'] != 'NULL') {
				$obj->document->addField('issued_country', $row['country']);
			}

			if ($row['county'] != '' && $row['county'] != 'NULL') {
				$obj->document->addField('issued_county', $row['county']);
			}

			if ($row['place'] != '' && $row['place'] != 'NULL') {
				$obj->document->addField('issued_place', $row['place']);
			}

			// indexing multi-valued property locations
			$property_locations = deeds_solr_helper_get_charter_property_locations($row['docnum'], $obj);
			foreach ($property_locations as $property_location) {
				$obj->document->addField('property_country', $property_location['country']);
				$obj->document->addField('property_county', $property_location['county']);
				$obj->document->addField('property_place', $property_location['place']);
			}

			if ($row['charter_type'] != '' && $row['charter_type'] != 'NULL') {
				$charter_types = explode(', ', $row['charter_type']);
				foreach ($charter_types as $charter_type) {
					$obj->document->addField('charter_type', $charter_type);
				}
			}

			$obj->document->addField('origin', $row['origin']);
			$obj->document->addField('language', $row['language']);

			$dated = deeds_core_formatyr($row['dated']);
			$lodate = deeds_core_formatyr($row['lodate']);
			$hidate = deeds_core_formatyr($row['hidate']);			

			if ($dated != '0000') {
				$obj->document->addField('docYear_display', $dated);
				$obj->document->addField('docYear_multi', $dated);
			} else if ($lodate != '0000' && $hidate != '0000') {
				$int_loyr = intval($lodate);
				$int_hiyr = intval($hidate);
				for ($i = $int_loyr; $i <= $int_hiyr; $i++) {
					$obj->document->addField('docYear_multi', $i);
				}
				$obj->document->addField('docYear_display', "{$lodate}-{$hidate}");
			} else if ($lodate != '0000') {
				$obj->document->addField('docYear_display', $lodate);
				$obj->document->addField('docYear_multi', $lodate);
			} else if ($hidate != '0000') {
				$obj->document->addField('docYear_display', $hidate);
				$obj->document->addField('docYear_multi', $hidate);
			}
			
			$date_precision = $row['date_precision'];
			$obj->document->addField('date_precision', $date_precision);

			$copySource = $row['charter_source'];
			$obj->document->addField('copySource', $copySource);

			$obj->document->addField('cart_title_short', $row['cart_title_short']);
 			$obj->document->addField('cart_title', $row['cart_title']);
 			$obj->document->addField('cart_bib_title', $row['cart_bib_title']);

			$count++;
			//echo "Charter add count: $count\n";

			$obj->service->addDocument($obj->document);
			$obj->service->commit();
		} catch (Exception $e) {
			echo $e->getMessage();
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

		$service->commit();
	}
}

function deeds_solr_helper_clear_index_by_unique_id($unique_id = '', $obj) {
	if ($record_type != '') {
		$obj->load->library("Apache/Solr/Apache_Solr_Service", array('host' => SOLR_HOST, 'port' =>SOLR_PORT, 'path' => SOLR_PATH), 'service');
        $obj->service->deleteByQuery('unique_id:"'.$unique_id.'"');
		$obj->service->commit();
	}
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