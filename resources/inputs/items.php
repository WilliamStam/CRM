<?php
namespace resources\inputs;
use \timer as timer;
use \models as models;
class items extends \resources\_ {
	private static $instance;
	function __construct(){
		parent::__construct();
	}

	

	
	static function getList($inputtypes=false){
		$return = array();
		
		/* read all the input.php pages in the sub folder returning the definition method */
		if (is_array($inputtypes)){
			foreach ($inputtypes as $input){
				$inputO = self::getInputClass($input);
				// test_array($inputO); 
				if (class_exists($inputO)){
					$defO = $inputO . "::_def";
					$def = $defO();
					$def['class'] = $inputO;
					$return[] = $def;
				}
				
				
			}
		} else {
			foreach (glob("./resources/inputs/*/item.php") as $input) {
				$t = explode("/",$input);
				
				$inputO = self::getInputClass($t[3]);
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
	
	static function getInputClass($input){
		/* returns the full class name for the input */
		return "\\resources\\inputs\\{$input}\\item";
		
	}
 }
