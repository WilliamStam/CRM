<?php
namespace resources\inputs\text;

class item extends \resources\inputs\items {
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
			"type" => "text",
			"label" => "Text Input",
			"resource"=>"inputs",
			"ordering"=>40
		);
	}
	
	static function _list($search="") {
		$list = array();
		
		$list[] = array(
			"ID" => "inputs|text|fullname",
			"description" => "Text input 'Full Name'",
			"name" => "fullname",
			"value_type"=>"alpha",
			"data" => array(
				
				"label" => "Full Name",
				"placeholder" => "Full Name",
				"style" => "2",
				"custom_use_style" => "0",
			)
		
		);
		
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
				"placeholder" => "",
				"style" => "2",
				"label" => "",
				"custom_use_style" => "0",
			// form-horizontal, form-inline
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
		//test_array($vals);
		return \resources\_::_templ($data,$value_field,"details.twig",self::_def(),$vals);

	}
	static function _settings($data){
		return parent::merge_data($data,self::default_data());
	}
	
	
}
