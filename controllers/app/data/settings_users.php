<?php

namespace controllers\app\data;

use \models as models;

class settings_users extends _ {
	function __construct() {
		parent::__construct();

	}


	function form() {
		$return = array();
		$ID = isset($_GET['ID']) ? $_GET['ID'] : "";


		$return = models\users::getInstance()->get($ID);
		unset($return['password']);


		return $GLOBALS["output"]['data'] = $return;
	}

	function _list() {
		$return = array();
		$ID = isset($_GET['ID']) ? $_GET['ID'] : "";
		$search = isset($_GET['search']) ? $_GET['search'] : "";

		$where = "";
		if ($search) {
			$where = " AND label LIKE '%{$search}%'";
		}

		$return['search'] = $search;

		$return['list'] = models\users::getInstance()->getAll($where,"fullname ASC","",array("format"=>true));


		return $GLOBALS["output"]['data'] = $return;
	}


}
