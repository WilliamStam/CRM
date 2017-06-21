<?php

namespace resources;

use \timer as timer;
use \models as models;

class _ {

	function __construct() {
		$this->f3 = \Base::instance();
		$this->user = $this->f3->get("user");
		$this->cfg = $this->f3->get("cfg");

		$this->f3->set("NOTIMERS", TRUE);

	}

	static function searching($array, $search = "", $fields = array()) {
		$searchKeys = array(
			"ID",
			"name",
			"description",
			"type",
		);
		$return = array();
		foreach ( $array as $item ) {
			$inc = TRUE;
			if ( $search ) {
				$inc = FALSE;

				foreach ( $searchKeys as $k ) {
					if ( isset($item[$k]) ) {
						//test_array(strpos($item[$k],$search));
						if ( strpos(strtolower($item[$k]), strtolower($search)) !== FALSE ) {
							$inc = TRUE;
						}
					}
				}

			}

			if ( $inc ) {
				$return[] = $item;
			}
		}


		return $return;
	}

	static function getList() {
		$return = array();




			foreach ( glob("./resources/*/*/item.php") as $input ) {
				$t = explode("/", $input);

				//test_array($input);
				$inputO = "\\resources\\{$t[2]}\\{$t[3]}\\item";

				if ( class_exists($inputO) ) {
					$defO = $inputO . "::_def";

					$def = $defO();
					$def['class'] = $inputO;
					$return[] = $def;


				}
			}


		usort($return, function($a, $b) {
			return $a['ordering'] <=> $b['ordering'];
		});


	//	test_array($return);

		return $return;

	}
	static function _templ($data,$value_field,$file,$def=array(),$vals){

		$vals["value"] = $data[$value_field];

		//test_array($vals);

		$tmpl = new \template($file, "resources/{$def['resource']}/".$def['type']);
		foreach ($vals as $k=>$v){
			$tmpl->$k = $v;
		}


		return $tmpl->render_template();
	}

}
