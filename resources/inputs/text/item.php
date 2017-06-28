<?php
namespace resources\inputs\text;

class item extends \resources\inputs\items {
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
			"type" => "text",
			"label" => "Text Input",
			"resource"=>"inputs",
			"ordering"=>40
		);
	}
	
	static function _list($search="") {
		$list = array();
		
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
				"select" => "",
				"join" => "",
				"form" => "",
				"details" => "",
		);
		
		
		return $settings;
	}




	
	
}
