<?php

namespace controllers\app\data;

use \models as models;

class interactions extends _ {
	function __construct() {
		parent::__construct();

	}


	function data() {
		$return = array();
		$groupby = models\interactions::getInstance()->_groupby();
		$columns = models\interactions::getInstance()->_columns();

		$default_settings = models\system_users::default_settings("interactions");

		$settings = $this->_settings("interactions", array("groupby","groupby_dir","order","order_dir","order_dir","columns","search","mine","num_records"),array("groupby"=>$groupby,"columns"=>$columns));



		//test_array($settings);



		$return['options'] = $settings;
		$return['setup'] = array(
			"groupby"=>$groupby,
			"columns"=>$columns
		);



		$order = str_replace("`", "", $settings['order']);
		$group = str_replace("`", "", $settings['groupby']);

		if ($columns[$order]['c'] && $groupby[$group]['c'] && $settings['groupby_dir']){
			$orderby =  "".$columns[$order]['c']. " " . $settings['order_dir'];
			$orderby = $groupby[$group]['c'] . " " . $settings['groupby_dir']. ", ". $orderby;

		} else {
			$orderby = $default_settings['order']." ".$default_settings['order_dir'];
		}




		//test_array($settings['groupby']);

		$options = array(
			"select"=>array("".$groupby[$settings['groupby']]['c'] ." AS group_heading")
		);


		$where_sql = "_deleted='0'";

		if ($settings['search']){
			$search_str = models\search::getInstance()->interactions($settings['search']);
			$where_sql = $where_sql . " AND " . $search_str;
		}

		if ($settings['mine']=='1'){
			$where_sql = $where_sql . " AND userID ='{$this->user['ID']}'";
		}


		/*

		*/

	//	test_array($default_settings);
	//	$settings['filter']['daterange'] = "2017-01-01 to 2017-02-10";

		if ($settings['daterange']) {

			$daterange = models\daterange::getInstance()->values($settings['daterange'], $default_settings);

			$where_sql = $where_sql . " AND (date_in BETWEEN '{$daterange['start']}' AND '{$daterange['end']}')";
			//test_array($daterange);

		}




		//$search_str =

	//	test_array(array($options,$where_sql));
		$records = models\interactions::getInstance()->getAll($where_sql,$orderby,"",$options);

		$r = array();
		foreach($records as $item){
			if (!isset($r[$item['group_heading']])){
				$r[$item['group_heading']] = array(
					"heading"=>$item['group_heading'],
					"records"=>array()
				);
			}
			$v = $item;
			unset($v['group_heading']);
			$r[$item['group_heading']]['records'][] = $v;

		}

		$records = $r;




		$return['records'] = $records;


		return $GLOBALS["output"]['data'] = $return;
	}



}
