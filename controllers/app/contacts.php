<?php
namespace controllers\app;
use \timer as timer;
use \models as models;
class contacts extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		
		$tmpl = new \template("template.twig","ui/app");
		$tmpl->page = array(
			"section"    => "contacts",
			"sub_section"=> "list",
			"template"   => "contacts",
			"meta"       => array(
				"title"=> "CRM | Contacts",
			),
		);
		$tmpl->output();
	}
	
	
	
}
