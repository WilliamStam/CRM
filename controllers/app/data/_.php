<?php
namespace controllers\app\data;

use models as models;

class _ extends \controllers\_ {
	private static $instance;

	function __construct() {
		$this->f3 = \Base::instance();
		parent::__construct();
		$this->user = $this->f3->get("user");

		$this->f3->set("__runJSON", TRUE);




	}
	function _save_settings($section,$settings){
		$s_write = $this->user['raw_settings'];
		$s_write[$section] = array_replace_recursive((array)$s_write[$section],$settings);
		$this->user['raw_settings'] = $s_write;
		models\system_users::_settings($this->user['ID'], $this->user['company']['ID'], $s_write);

	}
	function _settings($section,$items=array(),$extra=array()){
		$default_settings = models\system_users::default_settings($section);
		$_settings = models\system_users::settings();
		$_settings = $_settings[$section];


		$settings = array();
		foreach ($items as $item){
			$settings[$item] = isset($_GET[$item])?$_GET[$item]:$_settings[$item];
		}
		//test_array($settings);
		//if ($section=="companies")	test_array($_settings);



		if (isset($extra['groupby'])&&(isset($items['groupby'])||in_array("groupby",$items))){
			if (!isset($extra['groupby'][$settings['groupby']]) && !isset($extra['groupby'][$settings['groupby']]['c'])){
				$settings['groupby'] = $default_settings['groupby'];
			}
		}



		if ((isset($items['groupby_dir'])||in_array("groupby_dir",$items))){
			if (!in_array($settings['groupby_dir'],array("ASC","DESC"))) $settings['groupby_dir'] = "ASC";
		}
		if (isset($items['order_dir'])||in_array("order_dir",$items)){
			if (!in_array($settings['order_dir'],array("ASC","DESC"))) $settings['order_dir'] = "ASC";
		}

		if ((in_array("order",$items) || isset($items['order']))&&isset($extra['columns'])){
			if (!isset($extra['columns'][$settings['order']]) || !isset($extra['columns'][$settings['order']]['o']) || !$extra['columns'][$settings['order']]['o']){
				$settings['order'] = $default_settings['order'];
			}
		}
		if (in_array("columns",$items) || isset($items['columns'])){
			if (!is_array($settings['columns'])){
				$settings['columns'] = explode(",",$settings['columns']);
			}
		//
		}


		$s = $settings;





		$this->_save_settings($section, $settings);




		$settings = array_replace_recursive((array)$this->user['settings'][$section],$settings);

		if (isset($extra['columns']) && $items['order']){
			if (!isset($extra['columns'][$settings['order']]) || !isset($extra['columns'][$settings['order']]['o']) || !$extra['columns'][$settings['order']]['o']){
				$settings['order'] = $default_settings['order'];
			}
		}
		if (isset($extra['groupby']) && $items['groupby']){
			if (!isset( $extra['groupby'][$settings['groupby']]) && !isset( $extra['groupby'][$settings['groupby']]['c'])){
				$settings['groupby'] = $default_settings['groupby'];
			}
		}

		if (isset($items['order_dir'])){
			if (!in_array($settings['order_dir'],array("ASC","DESC"))) $settings['order_dir'] = "ASC";
		}
		if (isset($items['groupby_dir'])){
			if (!in_array($settings['groupby_dir'],array("ASC","DESC"))) $settings['groupby_dir'] = "ASC";
		}




		if (isset($extra['columns'])&&isset($settings['columns'])){
			$c = array();
			$cols = $settings['columns'];
			if (!is_array($cols)){
				$cols = explode(",",$cols);
			}

			foreach($cols as $item){
				if (isset($extra['columns'][$item])) {
					$c[$item] = $extra['columns'][$item];
				}
			}
			$settings['columns'] = $c;
		}

		//if ($section=="individuals")	test_array(array($settings,$s,$settings['columns']));


		//test_array($settings);
		return $settings;

	}


}
