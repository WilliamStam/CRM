<?php
namespace resources\layout\rows;

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
			"type" => "rows",
			"label" => "Bootstrap Rows",
			"resource"=>"layout",
			"ordering"=>1
		);
	}
	
	static function _list($search="") {
		$list = array();
		
		$list[] = array(
			"ID" => "layout|rows|1column",
			"name" => "12 column",
			"description" => "1 row 12 columns",
			"data"=>array(
				"html"=>'<div class="row"><div class="col-sm-12 content-area"></div></div>'
			)

		);
		$list[] = array(
			"ID" => "layout|rows|2columns",
			"name" => "6 | 6 column",
			"description" => "1 row 2 columns 50% each",
			"data"=>array(
				"html"=>'<div class="row"><div class="col-sm-6 content-area"></div><div class="col-sm-6 content-area"></div></div>'
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


		);
		
		
		return $settings;
	}


	
	
}
