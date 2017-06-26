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
				if ( $item['ID'] == $search ) {
					$inc = TRUE;
					return $item;
				} else {
					foreach ( $searchKeys as $k ) {
						if ( isset($item[$k]) ) {
							//test_array(strpos($item[$k],$search));
							if ( strpos(strtolower($item[$k]), strtolower($search)) !== FALSE ) {
								$inc = TRUE;
							}
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


		return $return;

	}


	function render($data, $template = "details") {
		$settings = array();

		$field = array_merge(static::_def(),static::_settings($this->resource));


		//test_array([$field,$this->resource]);

		$template_ = $this->template($template);

		$tmpl = new \template($template_);
		$tmpl->data = $data;
		$tmpl->field = $field;
		$tmpl->value = $data[$field['key']];
		return $tmpl->render_string("resources");
	}

	function template($template = "details") {
		$settings = array();

		$field = static::_settings($this->resource);
		$settings = $field['data'];


		//test_array([$settings,$this->resource]);


		$template_ = isset($settings[$template]) ? $settings[$template] : "";
		if ( $template_ == "" ) {
			$default_file = "./resources/{$field['resource']}/{$field['type']}/{$template}.twig";
			if ( file_exists($default_file) ) {
				$template_ = file_get_contents($default_file);
			}
		}

		return $template_;
	}

	static function merge_data($data, $default) {
		if ( isset($data['data']) && is_array($data['data']) ) {
			$settings = array_merge($default, (array) $data['data']);
		} else {
			$settings = $default;
		}
		$data['data'] = $settings;

		return $data;
	}

	static function _settings($data) {
		return self::merge_data($data, static::default_data());
	}

}
