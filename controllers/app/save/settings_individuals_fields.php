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

	function resource() {
		$return = array();
		$ID = isset($_REQUEST['______ID______'])?$_REQUEST['______ID______']:"";



		$values = array(
			"name" => $this->post("name",true),
			"description" => $this->post("description",true),
		);

		$exclude = array("name","description","______ID______","______table______");
		foreach ($_POST as $key=>$value){
			if (!in_array($key,$exclude)){
				$values[$key] = $value;
			}
		}


		$table = isset($_POST['______table______'])?$_POST['______table______']:"individuals";


		if (!isset($values['isGroup']))$values['isGroup']=0;
		if (!isset($values['isLookup']))$values['isLookup']=0;



		//test_array($values);

		if (count($this->errors)==0){

			$ID = models\fields::getInstance($table)->_save($ID,$values);
		}
		$return = array(
			"ID" => $ID,
			"errors" => $this->errors
		);

		return $GLOBALS["output"]['data'] = $return;
	}




}
