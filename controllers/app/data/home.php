<?php

namespace controllers\app\data;

use \models as models;

class home extends _ {
	function __construct() {
		parent::__construct();

	}


	function data() {
		$return = array();
		$return['options'] = array();
		$return['records'] = array();


		return $GLOBALS["output"]['data'] = $return;
	}



}
