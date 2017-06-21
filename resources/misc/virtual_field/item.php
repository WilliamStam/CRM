<?php
namespace resources\misc\virtual_field;

class item extends \resources\misc\items {
	private static $instance;
	
	function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		
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

	function form($data,$value_field="value") {
		$settings = array();

		$data = self::_settings($data);
		$settings = $data['data'];

		$vals = array(
			"settings"=>$settings,
			"data"=>$data,
			"value_field"=>$value_field,
		);

		return \resources\_::_templ($data,$value_field,"form.twig",self::_def(),$vals);

	}
	function details($data,$value_field="value") {
		$settings = array();

		$data = self::_settings($data);
		$settings = $data['data'];

		$vals = array(
			"settings"=>$settings,
			"data"=>$data,
			"value_field"=>$value_field,
		);

		return \resources\_::_templ($data,$value_field,"details.twig",self::_def(),$vals);

	}
	static function _settings($data){
		return parent::merge_data($data,self::default_data());
	}
	
	
}
