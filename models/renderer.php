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

	function render($template,$renderer,$fields,$data=array(),$mask=false){
		$content = $template;
		$timer = new timer();
		$content_arr = array();
		$content_arr_layout = array();
		foreach ($fields as $item){
			$classO = "resources\\".$item['resource']."\\".$item['type']."\\item";
			if (class_exists($classO)) {
				$item['class'] = $classO;
				$content_arr[$item['ID'] . "-" . $item['resource']."-" . $item['type']] = $item;
			}
		}
		$fields = fields::getInstance("layout")->getAll();
		foreach ($fields as $item){
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
					$d = $classO::_list($def_item);
					$d['class'] = $classO;
					$content_arr[$def_item."-".$type[0]."-".$type[1]] = $d;
				}

			}
		}

		foreach ($parsedDom as $item){
			$attr = $item->getAttributes();
			if (isset($attr['data-item']) && $attr['data-item']){
				$id = $attr['data-item'];
				$id = str_replace("item-", "", $id);
				if (isset($content_arr[$id])){
					$d = $content_arr[$id];
					$chan[] = $d;

					$classO = $d['class'];
					$classO = $classO::getInstance($d);
					$html = $classO->render($data,"template");

					//if ($id=="html|hr-html") test_array(d);

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
					$d = $classO::_list($def_item);
					$d['class'] = $classO;
					$content_arr[$def_item."-".$type[0]."-".$type[1]] = $d;
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
					$d = $content_arr[$id];
					$chan[] = $d;

				//	test_array($d);
					$classO = $d['class'];
					$classO = $classO::getInstance($d);
					$html = $classO->render($data,$renderer);
					//if ($id=="item-5-inputs-text") test_array(d);

					$html = $html . "<div class='clearfix'></div>";
					if ($mask)	{
						$html = $html . "<div class='mask' title='{$d['resource']}/{$d['type']}/{$d['name']}'><div class='mask-label'>{$d['resource']}/{$d['type']}</div></div>";
					}
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
