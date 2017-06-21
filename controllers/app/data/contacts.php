<?php

namespace controllers\app\data;

use \models as models;

class contacts extends _ {
	function __construct() {
		parent::__construct();

	}

	function data(){
		$return = array();

		$settings = $this->_settings("contacts", array("search","type"));

		$return['options'] = $settings;
		//test_array($settings);
		switch ($settings['type']){
			case "1":
				$title = "Individuals";
				$type = "individual";
				$return = array_merge($return,$this->_individuals($settings));
				break;

			default:
				$type = "company";
				$title = "Companies";
				$return = array_merge($return,$this->_companies($settings));
				break;
		}

		$return['title'] = $title;
		$return['type'] = $type;



		return $GLOBALS["output"]['data'] = $return;

	}


	function _companies($settings = array()) {
		$return = array();
		$groupby = models\companies::getInstance()->_groupby();
		$columns = models\companies::getInstance()->_columns();

		$default_settings = models\system_users::default_settings("companies");

		$settings = array_merge($settings,$this->_settings("companies", array("groupby","groupby_dir","order","order_dir","order_dir","columns"),array("groupby"=>$groupby,"columns"=>$columns)));
		


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



		//test_array($orderby);

		$options = array(
			"select"=>array("".$groupby[$settings['groupby']]['c'] ." AS group_heading")
		);


		$where_sql = "_deleted='0'";

		if ($settings['search']){
			$search_str = models\search::getInstance()->companies($settings['search']);
			$where_sql = $where_sql . " AND " . $search_str;
		}




		/*

		*/

		//test_array($default_settings);
		//	$settings['filter']['daterange'] = "2017-01-01 to 2017-02-10";

		if ($settings['daterange']) {

			$daterange = models\daterange::getInstance()->values($settings['daterange'], $default_settings);

			$where_sql = $where_sql . " AND (date_in BETWEEN '{$daterange['start']}' AND '{$daterange['end']}')";
			//test_array($daterange);

		}



	//	$where_sql .= " AND `new1`.ID IN (1,4,3)";
		//$search_str =

		//test_array(array($where_sql,$orderby,"",$options));
		$records = models\companies::getInstance()->getAll($where_sql,$orderby,"",$options);
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

		$a = array();
		foreach ($r as $item){
			$a[] = $item;
		}
		$records = $a;




		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function _individuals($settings = array()) {
		$return = array();
		$groupby = models\individuals::getInstance()->_groupby();
		$columns = models\individuals::getInstance()->_columns();



		$default_settings = models\system_users::default_settings("individuals");

		$settings = array_merge($settings,$this->_settings("individuals", array("groupby","groupby_dir","order","order_dir","order_dir","columns","num_records"),array("groupby"=>$groupby,"columns"=>$columns)));



		$return['options'] = $settings;
		$return['setup'] = array(
			"groupby"=>$groupby,
			"columns"=>$columns
		);
		//test_array($settings);


		$order = str_replace("`", "", $settings['order']);
		$group = str_replace("`", "", $settings['groupby']);

		if ($columns[$order]['c'] && $groupby[$group]['c'] && $settings['groupby_dir']){
			$orderby =  "".$columns[$order]['c']. " " . $settings['order_dir'];
			$orderby = $groupby[$group]['c'] . " " . $settings['groupby_dir']. ", ". $orderby;

		} else {
			$orderby = $default_settings['order']." ".$default_settings['order_dir'];
		}



		//test_array($settings);

		$options = array(
			"select"=>array("".$groupby[$settings['groupby']]['c'] ." AS group_heading")
		);


		$where_sql = "_deleted='0'";

		if ($settings['search']){
			$search_str = models\search::getInstance()->individuals($settings['search']);
			$where_sql = $where_sql . " AND " . $search_str;
		}




		/*

		*/

		//test_array($default_settings);
		//	$settings['filter']['daterange'] = "2017-01-01 to 2017-02-10";

		if ($settings['daterange']) {

			$daterange = models\daterange::getInstance()->values($settings['daterange'], $default_settings);

			$where_sql = $where_sql . " AND (date_in BETWEEN '{$daterange['start']}' AND '{$daterange['end']}')";
			//test_array($daterange);

		}



	//	$where_sql .= " AND `new1`.ID IN (1,4,3)";
		//$search_str =

		//test_array(array($where_sql,$orderby,"",$options));
		$records = models\individuals::getInstance()->getAll($where_sql,$orderby,"",$options);


		$recordCount = count($records);


		$limits = array();;
		$selectedpage = (isset($_REQUEST['page'])) ? $_REQUEST['page'] :"";
		if (!$selectedpage) $selectedpage = 1;
		$limit = $settings['num_records'];
		$pagination = new \pagination();
		$pagination = $pagination->calculate_pages($recordCount, $limit,$selectedpage, 19);

		//test_array(models\adverts::getInstance()->getCount($where));

		$limits = $pagination['limit'];

		$pagination['hide_fast_jump']=false;
		$return['pagination'] = $pagination;

		$limit = explode(",",$pagination['limit']);



		$records = array_slice($records, $limit[0], $limit[1]);




		$r = array();
		foreach($records as $item){
			if (!isset($r[$item['group_heading']])){
				$r[$item['group_heading']] = array(
					"heading"=>$item['group_heading'],
					"count"=>0,
					"records"=>array()
				);
			}
			$v = $item;
			unset($v['group_heading']);
			$r[$item['group_heading']]['records'][] = $v;
			$r[$item['group_heading']]['count']++;

		}

		$a = array();
		foreach ($r as $item){
			$a[] = $item;
		}
		$records = $a;




		$return['records'] = $records;

		return $GLOBALS["output"]['data'] = $return;
	}

	function individual(){
		$return = array();
		$ID = isset($_GET['ID'])?$_GET['ID']:"";
		$return = models\individuals::getInstance()->get($ID,array("render"=>"details"));





		return $GLOBALS["output"]['data'] = $return;
	}
	function company(){
		$return = array();
		$ID = isset($_GET['ID'])?$_GET['ID']:"";
		$return = models\companies::getInstance()->get($ID,array("render"=>"details"));





		return $GLOBALS["output"]['data'] = $return;
	}


}
