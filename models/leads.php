<?php
namespace models;
use \timer as timer;

class leads extends _ {

	
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

	function get($ID,$options=array()) {
		$timer = new timer();
		$where = "(ID = '$ID' OR MD5(ID) = '$ID')";
		
		
		$result = $this->getData($where,"","0,1",$options);
		

		if (count($result)) {
			$return = $result[0];
			
		} else {
			$return = parent::dbStructure("interactions");
		}
		
		if ($options['format']){
			$return = $this->format($return,$options);
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

		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		$groupby = "";
		if (isset($options['groupby'])) {
			$groupby = " GROUP BY " . $options['groupby'];
		}
		if ($orderby) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ($limit) {
			$limit = " LIMIT " . $limit;
		}

		$args = "";
		if (isset($options['args'])) $args = $options['args'];

		$ttl = "";
		if (isset($options['ttl'])) $ttl = $options['ttl'];


		$select = isset($options['select'])?$options['select']:array();


		$columns = self::_columns();
		foreach($columns as $key=>$item){
			$select[] = $item['c']." AS ".$key;
		}

		$select = "SELECT ". implode(", ",$select);




		$sql = "
			$select
			FROM interactions JOIN interaction_types ON interaction_types.ID = interactions.typeID
			$where
			$groupby
			$orderby
			$limit;
		";

		//if (isset($_GET['sql'])&&isLocal()){
		//	test_string($sql);
		//}

		//
		$result = $f3->get("DB")->exec($sql, $args, $ttl);

		$return = $result;
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}

	
	
	public static function _save($ID, $values = array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();


		//test_array($values);

		if (isset($values['data']))$values['data'] = json_encode($values['data']);


		$a = new \DB\SQL\Mapper($f3->get("DB"), "interactions");
		$a->load("ID='$ID'");



		//test_array($values);
		foreach ($values as $key => $value) {
			if (isset($a->$key)) {
				$a->$key = $value;
			}
		}

		$a->save();
		$ID = ($a->ID) ? $a->ID : $a->_id;


		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $ID;
	}






	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"interactions");
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
		//	test_array($items); 
		if (isset($data['ID'])) {
			$single = true;
			$data = array($data);
		}
		//test_array($items);
		
		
		
		$recordIDs = array();
		
		$i = 1;
		$n = array();
		foreach ($data as $item) {
			$recordIDs[] = $item['ID'];
			if (isset($item['data'])) $item['data'] = json_decode($item['data'],true);




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
		$return = array(
			"ID"=>array(
				"c"=>"interactions.ID",
				"l"=>"ID",
				"d"=>"Record ID",
				"w"=>40,
				"m"=>30,
				"o"=>"numeric"
			),
			"label"=>array(
				"c"=>"interactions.label",
				"l"=>"Heading",
				"d"=>"Heading",
				"o"=>"alpha"
			),
			"date_in"=>array(
				"c"=>"interactions.date_in",
				"l"=>"Created",
				"d"=>"Created Date time",
				"w"=>80,
				"m"=>70,
				"o"=>"amount"
			),
			"date_changed"=>array(
				"c"=>"interactions.date_changed",
				"l"=>"Changed",
				"d"=>"Changed Date time",
				"w"=>80,
				"m"=>70,
				"o"=>"amount"
			),
			"type"=>array(
				"c"=>"interaction_types.label",
				"l"=>"Type",
				"d"=>"Interaction Type",

				"o"=>"alpha"
			),
			"user"=>array(
				"c"=>"(SELECT fullname FROM system_users WHERE system_users.ID = interactions.userID)",
				"l"=>"Staff",
				"d"=>"Staff member who added the record",

				"o"=>"alpha"
			),

		);

		if ($select){
			$r = array();
			foreach((array)$select as $key){
				$r[$key] = $return[$key];
			}
			$return = $r;
		}

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
			"type"=>$columns['type'],
			"outcomes"=>array(
				"l"=>"Outcomes",
				"d"=>"Outcomes",
				"c"=>"",

			),
			"contacts"=>array(
				"l"=>"Contacts",
				"d"=>"Contacts"
			),
			"user"=>$columns['user']

		);




		return $return;
	}
	
	
	
}
