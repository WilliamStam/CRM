<?php
namespace controllers\app;
use \timer as timer;
use \models as models;
class page_test extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");



		
		$tmpl = new \template("template.twig","ui/app");
		$tmpl->page = array(
			"section"    => "test",
			"sub_section"=> "test",
			"template"   => "test",
			"meta"       => array(
				"title"=> "CRM | TEST",
			),
		);
		$tmpl->output();
	}
	function data(){
		$this->f3->set("__runJSON", TRUE);


		$data = array();
		$data['type'] = array(
			"records"=>models\interaction_types::getInstance()->getAll(),
			"field"=>"toAscii",
			"template"=>"<div>@@label@@</div>"
		);
		$data['company'] = array(
			"records"=>models\system_companies::getInstance()->getAll(),
			"field"=>"toAscii",
			"template"=>"<div>@@company@@</div>"
		);


		return $GLOBALS["output"]['data'] = $data;
	}
	
	
	
}
