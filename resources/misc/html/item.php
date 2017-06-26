<?php
namespace resources\misc\html;

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
			"type" => "html",
			"label" => "HTML block",
			"resource"=>"misc",
			"ordering"=>50
		);
	}
	
	static function _list($search="") {
		$list[] = array(
			"ID" => self::_def()['resource']."|". self::_def()['type']."|hr",
			"name" => "hr",
			"description" => "Horizontal Rule",

			"value_type"=>"alpha",
			"data" => array(
				"html" => "<hr>",
			)
		);
		$list[] = array(
			"ID" => self::_def()['resource']."|". self::_def()['type']."|padding10",
			"name" => "padding10",
			"description" => "Padding 10",

			"value_type"=>"alpha",
			"data" => array(
				"html" => "<div class='padding:10px;'></div>",
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
				"html" => "",
		);
		
		
		return $settings;
	}

	
}
