<?php
namespace resources\layout\panels;

class item extends \resources\layout\items {
	private static $instance;

	function __construct($resource) {
		parent::__construct();
		$this->resource = $resource;
	}

	public static function getInstance($resource=array()) {
		self::$instance = new self($resource);
		return self::$instance;
	}
	
	static function _def() {
		return array(
			"type" => "panels",
			"label" => "Bootstrap Panels",
			"resource"=>"layout",
			"ordering"=>2
		);
	}
	
	static function _list($search="") {
		$list = array();
		
		$list[] = array(
			"ID" => "layout|panels|plain",
			"name" => "Blank Panel",
			"description" => "Plain BS panel",
			"data"=>array(
				"color" => "",
				"parts"=>array(
					"heading" => "1",
					"body" => "1",
					"footer" => "1",
				)
			)
		);
		$list[] = array(
			"ID" => "layout|panels|red",
			"name" => "Red Panel",
			"description" => "Red BS panel",
			"data"=>array(
				"color" => "red",
				"parts"=>array(
					"heading" => "1",
					"body" => "1",
					"footer" => "1",
				)
			)
		);
		$list[] = array(
			"ID" => "layout|panels|blue",
			"name" => "Blue Panel",
			"description" => "Blue BS panel",
			"data"=>array(
				"color" => "blue",
				"parts"=>array(
					"heading" => "1",
					"body" => "1",
					"footer" => "1",
				)
			)
		);
		
		$l = array();
		foreach ($list as $item) {
			$item['resource'] =  self::_def()['resource'];
			$item['type'] = self::_def()['type'];
			$item['key'] = $item['ID'];
			$l[$item['ID']] = $item;
		}
		$list = self::searching($l,$search);
		return $list;
	}
	
	static function default_data() {
		$settings = array(
			"color" => "",
			"parts"=>array(
				"heading" => "1",
				"body" => "1",
				"footer" => "1",
			)

		);
		
		
		return $settings;
	}

	
}
