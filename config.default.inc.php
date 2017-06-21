<?php
$cfg['DB']['host'] = "localhost";
$cfg['DB']['username'] = "";
$cfg['DB']['password'] = "";
$cfg['DB']['database'] = "crm_db";

$cfg['git'] = array(
	'username'=>"",
	"password"=>"",
	"path"=>"github.com/WilliamStam/CRM",
	"branch"=>"master"
);

$cfg['media'] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR. "media" . DIRECTORY_SEPARATOR;
$cfg['backup'] = $cfg['media'] . "backups" . DIRECTORY_SEPARATOR;


$cfg['field_lookup_separator'] = array(
	"list"=>", ",
	"idvalue"=>"|",
	"records"=>"||"
);
$cfg['ttl'] = 0;

