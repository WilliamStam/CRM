<?php
namespace models;
use \timer as timer;

class daterange extends _ {


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


	function values($value,$default_settings){
		$return = array();

		$date_from = false;
		$date_to = false;
		$label = "";

		if (strpos($value, " to ")){
			$parts = explode(" to ",$value);
			if ($parts[0] && date("Y-m-d",strtotime($parts[0]))==$parts[0]){
				$date_from = $parts[0]." 00:00:00";
			}
			if ($parts[1] && date("Y-m-d",strtotime($parts[1]))==$parts[1]){
				$date_to = $parts[1] ." 23:59:59";
			}
		}
		$label = $value;
		SWITCH($value){
			CASE 'Today':
				$date_from = date("Y-m-d 00:00:00");
				$date_to = date("Y-m-d 23:59:59");

				break;
			CASE 'Yesterday':
				$date_from = date("Y-m-d 00:00:00",strtotime("yesterday"));
				$date_to = date("Y-m-d 23:59:59",strtotime("yesterday"));

				break;
			CASE 'Last 7 Days':
				$date_from = date("Y-m-d 00:00:00",strtotime("-7 days"));
				$date_to = date("Y-m-d 23:59:59");

				break;
			CASE 'Last 30 Days':
				$date_from = date("Y-m-d 00:00:00",strtotime("-30 days"));
				$date_to = date("Y-m-d 23:59:59");

				break;
			CASE 'This Month':
				$date_from = date("Y-m-01 00:00:00");
				$date_to = date("Y-m-t 23:59:59");

				break;
			CASE 'Last Month':
				$date_from = date("Y-m-01 00:00:00",strtotime("-1 month"));
				$date_to = date("Y-m-t 23:59:59",strtotime($date_from));
				break;
			DEFAULT:

				$label = "Custom Range";


				break;
		}


		$return = array(
			"label"=>$label,
			"start" =>  $date_from,
			"end" =>  $date_to
		);
	//	test_array($return);
		if (!$return['start'] || !$return['end']){

			$return = $this->values($default_settings['filter']['daterange'],$default_settings);
		}

		return $return;
	}

}
