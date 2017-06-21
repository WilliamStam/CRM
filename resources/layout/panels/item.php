<?php
namespace resources\layout\panels;

class item extends \resources\layout\items {
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
	
	function form($data,$value_field="value") {
		$settings = array();
		
		$data = self::_settings($data);
		$settings = $data['data'];

		$vals = array(
			"settings"=>$settings,
			"data"=>$data,
			"value_field"=>$value_field,
		);

		return \resources\_::_templ($data,$value_field,"template.twig",self::_def(),$vals);
		
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

		return \resources\_::_templ($data,$value_field,"template.twig",self::_def(),$vals);

	}
	
	function admin($data) {
		$settings = array();

		$data = self::_settings($data);
		$settings = $data['data'];

		$tmpl = new \template("admin_template.twig", "resources/layout/".self::_def()['type']);
		$tmpl->settings = $settings;
		$tmpl->data = $data;
		
		return $tmpl->render_template();
	}
	static function _settings($data){
		return parent::merge_data($data,self::default_data());
	}
	
	
}
