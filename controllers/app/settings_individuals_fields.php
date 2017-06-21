<?php
namespace controllers\app;
use \timer as timer;
use \models as models;
class settings_individuals_fields extends _ {
	function __construct(){
		parent::__construct();
	}
	function page(){
		//if ($this->user['ID']=="")$this->f3->reroute("/login");
		$resources_ = \resources\_::getList();
		$_settings = models\system_users::settings();
		$settings = $_settings["settings_individuals_fields"];


		$resources = array();
		foreach ($resources_ as $item){
			if (!isset($resources[$item['resource']])){
				$resources[$item['resource']] = array(
					"resource"=>ucfirst($item['resource']),
					"records"=>array()
				);
			}
			$resources[$item['resource']]['records'][] = $item;
		}


	//	test_array($resources);

		$tmpl = new \template("template.twig","ui/app");
		$tmpl->page = array(
			"section"    => "settings",
			"sub_section"=> "individuals_fields",
			"template"   => "settings_individuals_fields",
			"meta"       => array(
				"title"=> "CRM | Settings | Individuals | Fields",
			),
		);
		$tmpl->settings = $settings;
		$tmpl->resources = $resources;
		$tmpl->output();
	}
	
	
	
}
