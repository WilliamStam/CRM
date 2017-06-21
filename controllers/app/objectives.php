<?php
namespace controllers\app;
use \timer as timer;
use \models as models;
class objectives extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		
		$tmpl = new \template("template.twig","ui/app");
		$tmpl->page = array(
			"section"    => "objectives",
			"sub_section"=> "list",
			"template"   => "objectives",
			"meta"       => array(
				"title"=> "CRM | Objectives",
			),
		);
		$tmpl->output();
	}
	
	
	
}
