<?php
namespace resources\misc;
use \timer as timer;
use \models as models;
class items extends \resources\_ {
	private static $instance;
	function __construct(){
		parent::__construct();
	}
	public static function getInstance(){
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	


	
	static function getList($inputtypes=false){
		$return = array();
		
		/* read all the input.php pages in the sub folder returning the definition method */
		if (is_array($inputtypes)){
			foreach ($inputtypes as $input){
				$inputO = self::getClass($input);
				// test_array($inputO); 
				if (class_exists($inputO)){
					$defO = $inputO . "::_def";
					$def = $defO();
					$def['class'] = $inputO;
					$return[] = $def;
				}
				
				
			}
		} else {
			foreach (glob("./resources/misc/*/item.php") as $item) {
				$t = explode("/",$item);
				
				$inputO = self::getClass($t[3]);
				// test_array($inputO); 
				if (class_exists($inputO)){
					$defO = $inputO . "::_def";
					
					$def = $defO();
					$def['class'] = $inputO;
					$return[] = $def;
					
					
				}
			}
		}

		usort($return, function($a, $b) {
			return $a['ordering'] <=> $b['ordering'];
		});
		return $return;
		
	}
	
	static function getClass($item){
		/* returns the full class name for the input */
		return "\\resources\\misc\\{$item}\\item";
		
	}
 }
