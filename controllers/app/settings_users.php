<?php
namespace controllers\app;
use \timer as timer;
use \models as models;
class settings_users extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");
		
		
		$tmpl = new \template("template.twig","ui/app");
		$tmpl->page = array(
			"section"    => "settings",
			"sub_section"=> "users",
			"template"   => "users",
			"meta"       => array(
				"title"=> "CRM | Settings | Users",
			),
		);
		$tmpl->output();
	}
	
	
	
}
