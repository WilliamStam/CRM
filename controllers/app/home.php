<?php
namespace controllers\app;
use \timer as timer;
use \models as models;
class home extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		
		$tmpl = new \template("template.twig","ui/app");
		$tmpl->page = array(
			"section"    => "home",
			"sub_section"=> "list",
			"template"   => "home",
			"meta"       => array(
				"title"=> "CRM | Dashboard",
			),
		);
		$tmpl->output();
	}
	
	
	
}
