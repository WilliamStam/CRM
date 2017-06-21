<?php
namespace models;
use \timer as timer;

class search extends _ {


	private static $instance;
	function __construct() {
		parent::__construct();


	}
	public static function getInstance(){
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function searching($search,$keys=array()){
		$return = "";

		//$search = "type:phone      type:email  company:xyz   e   staff:test woof:hahaha";

		$search_string = $search;
		$sql = "1 ";

		foreach ($keys as $key=>$template){
			$matches_sql = array();
			preg_match_all('/'.$key.':([^-\s]*)/', $search, $matches);
			foreach ($matches[1] as $val){
				$matches_sql[] = str_replace("@@@@",$val,$template);
			}
			if (count($matches_sql)){
				foreach ($matches[0] as $str){
					$search_string = str_replace($str,"",$search_string);
				}
				$matches_sql = "(".implode(" OR ",$matches_sql).")";
				$sql = $sql . " AND ".$matches_sql;
			}
		}

		$search_string = trim(str_replace(array("  ","  "), " ", $search_string));
		if($search_string){
			$return = $search_string;
		}
		return array(
			"search"=>$return,
			"sql"=>$sql
		);
	}

	function interactions($search){
		$return = "";
		$keys = array(
			"type"=>"interaction_types.label LIKE '%@@@@%'",
			"company"=>"company = '@@@@'",
			"contact"=>"contact LIKE '@@@@'",
			"staff"=>"staff LIKE '@@@@'",
		);

		$searching = $this->searching($search,$keys);

		$return = $searching['sql'] ;

		if ($searching['search']){
			$return = $return . " AND interactions.label LIKE '%{$searching['search']}%'";
		}







		$return = " data LIKE '%{$search}%'";
		return $return;
	}
	function companies($search){
		$return = "";
		$keys = array(
			"type"=>"interaction_types.label LIKE '%@@@@%'",
			"name"=>"name = '@@@@'",
			"contact"=>"contact LIKE '@@@@'",
			"staff"=>"staff LIKE '@@@@'",
		);

		$searching = $this->searching($search,$keys);

		$return = $searching['sql'] ;

		if ($searching['search']){
			$return = $return . " AND interactions.label LIKE '%{$searching['search']}%'";
		}






		$return = " data LIKE '%{$search}%'";

		return $return;
	}
	function individuals($search){
		$return = "";
		/*
		$keys = array(
			"type"=>"interaction_types.label LIKE '%@@@@%'",
			"name"=>"name = '@@@@'",
			"contact"=>"contact LIKE '@@@@'",
			"staff"=>"staff LIKE '@@@@'",
		);

		$searching = $this->searching($search,$keys);

		$return = $searching['sql'] ;

		if ($searching['search']){
			$return = $return . " AND interactions.label LIKE '%{$searching['search']}%'";
		}

*/


		$return = " data LIKE '%{$search}%'";







		return $return;
	}
	function tasks($search){
		$return = "";
		$keys = array(
			"type"=>"interaction_types.label LIKE '%@@@@%'",
			"name"=>"name = '@@@@'",
			"contact"=>"contact LIKE '@@@@'",
			"staff"=>"staff LIKE '@@@@'",
		);

		$searching = $this->searching($search,$keys);

		$return = $searching['sql'] ;

		if ($searching['search']){
			$return = $return . " AND interactions.label LIKE '%{$searching['search']}%'";
		}

		$return = " data LIKE '%{$search}%'";
		return $return;
	}



}
