<?php

namespace controllers\app\save;

use \models as models;
use \resources as resources;

class contacts extends _ {
	function __construct() {
		parent::__construct();

	}




	function company() {
		$return = array();
		$ID = isset($_GET['ID'])?$_GET['ID']:"";

		$values = array(
			"cID"=>$this->user['company']['ID'],
			"uID" =>$this->user['ID'],
			//"datein" => "",
			//"date_created" => "",
			//"_deleted" => 0,
			"data" => array(),
		);

		$items = models\renderer::getInstance()->getItems($this->user['company']['companies_form']);
		
		//test_array($items);

		$data = array();
		foreach (\models\companies::getInstance()->fields() as $item){
			if (in_array($item['ID'],$items)){
				$data[$item['key']] =  $this->post($item['name']);
			}
		}
		$values['data'] = $data;


		//test_array($values);

		if (count($this->errors)==0){
			$ID = models\companies::getInstance()->_save($ID,$values);
		}
		$return = array(
			"ID" => $ID,
			"errors" => $this->errors
		);

		return $GLOBALS["output"]['data'] = $return;
	}
	function individual() {
		$return = array();
		$ID = isset($_GET['ID'])?$_GET['ID']:"";

		$values = array(
			"cID"=>$this->user['company']['ID'],
			"uID" =>$this->user['ID'],
			//"datein" => "",
			//"date_created" => "",
			//"_deleted" => 0,
			"data" => array(),
		);
		$items = models\renderer::getInstance()->getItems($this->user['company']['individuals_form']);
		$data = array();
		foreach (\models\individuals::getInstance()->fields() as $item){
			if (in_array($item['ID'],$items)){
				$data[$item['key']] =  $this->post($item['name']);
			}
		}
		$values['data'] = $data;


		//test_array($values);

		if (count($this->errors)==0){
			$ID = models\individuals::getInstance()->_save($ID,$values);
		}
		$return = array(
			"ID" => $ID,
			"errors" => $this->errors
		);

		return $GLOBALS["output"]['data'] = $return;
	}




}
