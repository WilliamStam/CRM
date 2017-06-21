<?php
namespace models;
use \timer as timer;

class renderer extends _ {

	
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

	function render($template,$renderer,$records,$value_field="value"){
		$content = $template;
		$timer = new timer();
		$content_arr = array();
		$content_arr_layout = array();
		foreach ($records as $item){
			$classO = "resources\\".$item['resource']."\\".$item['type']."\\item";
			if (class_exists($classO)) {
				$item['class'] = $classO;
				$content_arr[$item['ID'] . "-" . $item['resource']."-" . $item['type']] = $item;
			}
		}
		$records = fields::getInstance("layout")->getAll();
		foreach ($records as $item){
			$classO = "resources\\".$item['resource']."\\".$item['type']."\\item";
			if (class_exists($classO)) {
				$item['class'] = $classO;
				$content_arr_layout[$item['ID'] . "-" . $item['resource']."-" . $item['type']] = $item;
			}
		}








		$dom = new \IvoPetkov\HTML5DOMDocument();
		$dom->loadHTML($content);

		$ids = array();
		$default_ids = array();
		$parsedDom = $dom->querySelectorAll('.layout-item');
		foreach ($parsedDom as $item){
			$attr = $item->getAttributes();
			$items[] = $item->outerHTML;
			if (isset($attr['data-item']) && $attr['data-item']){
				$id = $attr['data-item'];
				$id = str_replace("item-", "", $id);
				$id = explode("-",$id);
				if (!in_array($id[0],$ids)){
					if (is_numeric($id[0])){
						$ids[] = $id[0];
					} else {
						$default_ids[] = $id[0];
					}

				}
			}

		}
		if (count($default_ids)){
			foreach($default_ids as $def_item){
				$type = explode("|",$def_item);
				$classO = "resources\\".$type[0]."\\".$type[1]."\\item";
				if (class_exists($classO)){
					$data = $classO::_list($def_item);
					$data = $data[0];
					$data['class'] = $classO;
					$content_arr[$def_item."-".$type[0]."-".$type[1]] = $data;
				}

			}
		}

		foreach ($parsedDom as $item){
			$attr = $item->getAttributes();
			if (isset($attr['data-item']) && $attr['data-item']){
				$id = $attr['data-item'];
				$id = str_replace("item-", "", $id);
				if (isset($content_arr[$id])){
					$data = $content_arr[$id];
					$chan[] = $data;

					$classO = $data['class'];
					$classO = $classO::getInstance();

					switch ($renderer){
						case "form":
							$html = $classO->form($data,$value_field);
							break;
						default:
							$renderer = "details";
							$html = $classO->details($data,$value_field);
							break;

					}
					//if ($id=="html|hr-html") test_array($data);

				//	$html = $html . "<div class='clearfix'></div><div class='mask'></div>";
					$item->outerHTML = $html;
				}
			}
		}



		



		$parsedDom = $dom->querySelectorAll('.item');
		$ids = array();
		$default_ids = array();
		$items = array();
		foreach ($parsedDom as $item){
			$attr = $item->getAttributes();
			$items[] = $item->outerHTML;
			if (isset($attr['data-item']) && $attr['data-item']){
				$id = $attr['data-item'];
				$id = str_replace("item-", "", $id);
				$id = explode("-",$id);
				if (!in_array($id[0],$ids)){
					if (is_numeric($id[0])){
						$ids[] = $id[0];
					} else {
						$default_ids[] = $id[0];
					}

				}
			}
		}
		if (count($default_ids)){
			foreach($default_ids as $def_item){
				$type = explode("|",$def_item);
				$classO = "resources\\".$type[0]."\\".$type[1]."\\item";
				if (class_exists($classO)){
					$data = $classO::_list($def_item);
					$data = $data[0];
					$data['class'] = $classO;
					$content_arr[$def_item."-".$type[0]."-".$type[1]] = $data;
				}

			}
		}

		//test_array($content_arr);


		$chan = array();
		foreach ($parsedDom as $item){
			$attr = $item->getAttributes();
			if (isset($attr['data-item']) && $attr['data-item']){
				$id = $attr['data-item'];
				$id = str_replace("item-", "", $id);
				if (isset($content_arr[$id])){
					$data = $content_arr[$id];
					$chan[] = $data;

					$classO = $data['class'];
					$classO = $classO::getInstance();

					switch ($renderer){
						case "form":
							$html = $classO->form($data,$value_field);
							break;
						default:
							$renderer = "details";
							$html = $classO->details($data,$value_field);
							break;

					}
					//if ($id=="html|hr-html") test_array($data);

					$html = $html . "<div class='clearfix'></div><div class='mask'></div>";
					$item->innerHTML = $html;
				}
			}
		}

		$dom->saveHTML();
		$content = $dom->querySelector('body')->innerHTML;

		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $content;

	}
	
	
	
}
