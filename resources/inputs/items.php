<?php
namespace resources\inputs;
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
	
	function render($data,$adminRender){
		$return = "";
		
		//test_array($data); 
		
		if ($adminRender){
			$tmpl = new \template("admin_template.twig","resources/inputs");
			$tmpl->settings = $data['data'];
			$tmpl->data = $data;
			$return = $tmpl->render_template(); ;
		} else {
			$module = $data['module'];
			$inputO = \resources\inputs\items::getInputClass($module);
			if (class_exists($inputO)){
				$return = $inputO::getInstance()->front($data);
			}
			
			
		}
		
		return $return;
		
	}
	
	static function merge_data($data,$default){
		if (isset($data['data'])&&is_array($data['data'])){
			$settings = array_merge($default,(array)$data['data']);
		} else {
			$settings = $default;
		}
		$data['data'] = $settings;
		return $data;
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
