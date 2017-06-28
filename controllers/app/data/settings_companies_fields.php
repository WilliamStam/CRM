<?php

namespace controllers\app\data;

use \models as models;
use \resources as resources;

class settings_companies_fields extends _ {
	function __construct() {
		parent::__construct();

	}


	function data() {
		$return = array();
		$settings = $this->_settings("settings_companies_fields", array());

		$return['section'] = $settings['section'];
		
		


		$content = $this->_render();

		$list = $this->_list();

		//test_array($content);
		$return = array_merge_recursive($return,$list,$content);



		return $GLOBALS["output"]['data'] = $return;
	}

	function _list() {
		$return = array();
		$default_settings = models\system_users::default_settings("settings_companies_fields");
		$settings = $this->_settings("settings_companies_fields", array("search","type"));

		$resources_ = \resources\_::getList();
		$res = array();
		foreach ($resources_ as $item){
			$res[] = $item['resource']."-".$item['type'];
		}

		if (!in_array($settings['type'],$res)){
			$settings['type'] = $default_settings['type'];
		}

		//test_array($type);

		$t = explode("-",$settings['type']);

		$table = "companies";
		$where = "1";

		if ($t[0]=="layout"){
			$table = "layout";
		}

		$class = "\\resources\\".$t[0]."\\".$t[1]."\\item";
		$where = "resource='{$t[0]}' AND type = '{$t[1]}'";


		if ($settings['search']) {
			$where .= " AND (name LIKE '%{$settings['search']}%' OR description LIKE '%{$settings['search']}%')";
		}



		$return['list'] = models\fields::getInstance($table)->getAll($where);
		$return['default_list'] = $class::_list($settings['search']);


		$return['type'] = $settings['type'];
		$return['search'] = $settings['search'];





		return $GLOBALS["output"]['data'] = $return;
	}
	function _render(){
		$timer = new \timer();

		$settings = $this->_settings("settings_companies_fields", array("renderer"));

		$content = $this->user['company']['companies_'.$settings['renderer']];


		$template = isset($_REQUEST['content'])?$_REQUEST['content']:$content;
		$renderer = $settings['renderer']?$settings['renderer']:"details";


		$fields = models\fields::getInstance("companies")->getAll();


		$data = array();
		foreach ($fields as $item){
			$data[$item['key']] = $item['name'];
		}


		$default_resources = resources\_::getList();


		foreach ($default_resources as $resource_item){
			$items = $resource_item['class']::_list();
			foreach ($items as $item){
				$data[$item['ID']] = $item['name'];
			}
		}

		//test_array($fields);

		$content = models\renderer::getInstance()->render($template, $renderer, $fields,$data,true);


		$return = array();
		$return['content'] = $content;
		$return['renderer'] = $renderer;




		$timer->stop("Renderer");
		return $GLOBALS["output"]['data'] = $return;
	}
	function resource(){
		$return = array();
		$ID = isset($_GET['ID'])?$_GET['ID']:"";
		if ($ID){
			$parts = explode("-",$ID);
			$ID = $parts[1];
			$resource = $parts[2];
			$type = $parts[3];

			$class = "resources\\{$resource}\\{$type}\\item";
			$return['def'] = $class::_def();
			$table = "companies";
			if (is_numeric($ID)){
				$return['record'] = models\fields::getInstance($table)->get($ID,array("format"=>true));
			} else {
				$return['record'] = $class::_list($ID);
			}





			$return['template'] =  $class::getInstance($return['record'])->render(array(),"admin");


			$return['resource'] = $return['record']['resource']?$return['record']['resource']:$resource;
			$return['type'] = $return['record']['type']?$return['record']['type']:$type;
			if ($return['record']['resource']=="layout"){
				$table = "layout";
			}

			$return['table'] = $table;
			//$return['record']['value'] = "woof";

			$return['templates'] = array(
				"form"=>$class::getInstance($return['record'])->template("form"),
				"details"=>$class::getInstance($return['record'])->template("details"),
			);

		}







		return $GLOBALS["output"]['data'] = $return;
	}

	function testing(){

		$class = "resources\\inputs\\text\\item";




	}

}
