<?php

namespace controllers\app\data;

use \models as models;
use \resources as resources;

class settings_individuals_fields extends _ {
	function __construct() {
		parent::__construct();

	}


	function data() {
		$return = array();
		$settings = $this->_settings("settings_individuals_fields", array());

		$return['section'] = $settings['section'];
		
		


		$content = $this->_render();

		$list = $this->_list();

		//test_array($content);
		$return = array_merge_recursive($return,$list,$content);



		return $GLOBALS["output"]['data'] = $return;
	}

	function _list() {
		$return = array();
		$default_settings = models\system_users::default_settings("settings_individuals_fields");
		$settings = $this->_settings("settings_individuals_fields", array("search","type"));

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

		$table = "individuals";
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
		$return['renderType'] = $settings['renderType'];
		$return['search'] = $settings['search'];





		return $GLOBALS["output"]['data'] = $return;
	}
	function _render(){
		$timer = new \timer();

		$settings = $this->_settings("settings_individuals_fields", array("renderer"));

		$content = $this->user['company']['individuals_'.$settings['renderer']];


		$content = isset($_REQUEST['content'])?$_REQUEST['content']:$content;
		$renderer = $settings['renderer'];


		$records = models\fields::getInstance("individuals")->getAll();








		$content = models\renderer::getInstance()->render($content, $renderer, $records,"name");


		$return = array();
		$return['content'] = $content;
		$return['renderer'] = $renderer;




		$timer->stop("Renderer");
		return $GLOBALS["output"]['data'] = $return;
	}


}
