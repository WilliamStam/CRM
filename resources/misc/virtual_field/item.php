<?php
namespace resources\misc\virtual_field;

class item extends \resources\misc\items {
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
			"type" => "virtual_field",
			"label" => "Virtual Fields",
			"resource"=>"misc",
			"ordering"=>50
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
