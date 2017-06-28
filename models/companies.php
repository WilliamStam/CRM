<?php
namespace models;
use \timer as timer;

class companies extends _ {

	
	private static $instance;
	function __construct() {
		parent::__construct();




		$this->table = "companies";
		$this->fieldssO =  new fields($this->table);
		$this->fields = $this->fieldssO->getAll("cID = '{$this->user['company']['ID']}'");


	}
	public static function getInstance(){
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	function fields(){
		return $this->fields;
	}

	function get($ID,$options=array()) {
		$timer = new timer();
		$where = "({$this->table}.ID = '$ID' OR MD5({$this->table}.ID) = '$ID')";
		
		
		$result = $this->getData($where,"","0,1",$options);
		

		if (count($result)) {
			$return = $result[0];
			
		} else {
			$return = parent::dbStructure($this->table);
		}

		$raw = $return;
		if ($options['format']){
			$return = $this->format($return,$options);
		}

		if ($options['render']){
			$raw['data'] = (array) json_decode($raw['data'],true);

			$return = $this->format($return,$options);
			$data = array();
			$fields = array();
			switch ($options['render']){
				case "form":
					foreach ($this->fields as $field){
						$data[$field['key']] = isset($raw['data'][$field['key']])?$raw['data'][$field['key']]:"";
					}
					break;
				default:
					foreach ($this->fields as $field){
						$data[$field['key']] = isset($return[$field['key']])?$return[$field['key']]:"";
					}
					break;
			}









			//test_array($fields);
			$template = renderer::getInstance()->render( $this->user['company']['companies_'.$options['render']], $options['render'], $this->fields,$data);
			$return['template'] = $template;
			//test_string($template);
			//test_array($this->fields);
		}
		
		//test_array($return);
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}
	public function getAll($where = "", $orderby = "", $limit = "", $options = array()) {
		$result = $this->getData($where,$orderby,$limit,$options);
		$result = $this->format($result,$options);
		return $result;
		
	}

	public function getData($where = "", $orderby = "", $limit = "", $options = array()) {
		$timer = new timer();
		$f3 = \Base::instance();

		//test_array($options);



		$sql = $this->fieldssO->DataSQL($where, $orderby, $limit, $options);


		if (isset($_GET['sql'])&&isLocal()){
			test_string($sql);
		}

		//
		$result = $f3->get("DB")->exec($sql, $args, $ttl);

		//test_array($result);
		$return = $result;
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}




	public function _save($ID, $values = array()) {
		$timer = new timer();
		$f3 = $this->f3;
		$return = array();


		//test_array($values);

		//if (isset($values['data']))$values['data'] = json_encode($values['data']);
		$data = $values["data"];
		unset($values['data']);

		$s = array();
		foreach ($data as $key => $value) {
			$s[] = "'$.$key', '$value'";
		}


		$a = new \DB\SQL\Mapper($f3->get("DB"), $this->table);
		$a->load("ID='$ID'");


		$updateFields = true;

		//test_array($values);
		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$a->$key = $value;
			}
		}


		if ($a->data==""){
			$a->data = json_encode($data);
			$updateFields = false;
		}


		$a->save();
		$ID = ($a->ID) ? $a->ID : $a->_id;



		if (count($s) && $updateFields){
			$d = json_encode(array("d"=>date("Y-m-d H:i:s")));
			$s = implode(",",$s);
			$sql = "UPDATE {$this->table} SET `data` = JSON_SET(`data`, $s) WHERE ID = '$ID'";
			$f3->get("DB")->exec($sql);
		}

		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $ID;
	}






	public function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"companies");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return "done";

	}

	
	
	static function format($data,$options) {
		$timer = new timer();
		$single = false;
		$f3 = \Base::instance();
		$cfg = $f3->get('cfg');
		//	test_array($items); 
		if (isset($data['ID'])) {
			$single = true;
			$data = array($data);
		}
		//test_array($items);

		$fields =
		
		
		$recordIDs = array();
		
		$i = 1;
		$n = array();
		foreach ($data as $item) {
			$recordIDs[] = $item['ID'];
			if (isset($item['data'])) $item['data'] = json_decode($item['data'],true);


			if (isset($options['group_heading'])) $item['group_heading'] = $item[$options['group_heading']];
			$item = fields::format_record(self::getInstance()->fields(),$item,$cfg);

			$n[] = $item;
		}



		
	//	test_array($n);
		
		
		
		
		
		if ($single) $n = $n[0];
		
		
		$records = $n;
		
		
		
	//	test_array($records);
		
		
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $records;
	}

	function _columns($select=false){
		$return = array();
		$return["ID"] =array(
				"c"=>"ID", 	// column
				"l"=>"ID",				// label
				"d"=>"Record ID",		// description
				"w"=>40,				// width
				"m"=>30,				// minimum width
				"o"=>"numeric"			// order icon type
			);
		$return["name"] =array(
				"c"=>"name", 	// column
				"l"=>"name",				// label
				"d"=>"Company Name",		// description
				"o"=>"alpha"			// order icon type
			);
		$return["date_in"] =array(
				"c"=>"date_in", 	// column
				"l"=>"date_in",				// label
				"d"=>"Date Captured",		// description
				"w"=>40,				// width
				"m"=>30,				// minimum width
				"o"=>"numeric"			// order icon type
			);

		foreach($this->fields as $field){
			$return[$field['key']] = array(
				"c"=>"`".$field['key']."`",
				"l"=>$field['name'],
				"d"=>$field['description'],
				"o"=>$field['value_type'],
				"lookup"=>$field['isLookup']
			);
		}


	//	test_array($this->user);

	//


		if ($select){
			$r = array();
			foreach((array)$select as $key){
				$r[$key] = $return[$key];
			}
			$return = $r;
		}
		//test_array($return);


		return $return;
	}
	function _groupby(){
		$columns = self::_columns();
		$return = array(
			"none"=>array(
				"l"=>"None",
				"d"=>"None",
				"c"=>"'None'",
			),



		);

		foreach($this->fields as $field){
			if ($field['isGroup']){
				$return[$field['key']] = array(
					"c"=>"`".$field['key']."`",
					"l"=>$field['name'],
					"d"=>$field['description']
				);
			}

		}

		//test_array($return);

		return $return;
	}
	
	
	
}
