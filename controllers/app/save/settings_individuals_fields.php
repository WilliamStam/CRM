<?php

namespace controllers\app\save;

use \models as models;
use \resources as resources;

class settings_individuals_fields extends _ {
	function __construct() {
		parent::__construct();

	}


	function form() {
		$return = array();
		$ID = isset($_REQUEST['ID'])?$_REQUEST['ID']:"";



		$values = array(
			"renderer" => $this->post("______renderer______",true),
		);

		$content = $this->post("______content______");
		if ($values['renderer']){
			$values["individuals_".$values['renderer']] = $content;
		}

		if ($content==""){
			$this->errors['content'] = "Content required";
		}

		//test_array($values);

		if (count($this->errors)==0){

			$ID = models\system_companies::_save($this->user['company']['ID'],$values);
		}
		$return = array(
			"ID" => $ID,
			"errors" => $this->errors
		);

		return $GLOBALS["output"]['data'] = $return;
	}




}
