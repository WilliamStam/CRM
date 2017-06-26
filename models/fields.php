<?php
namespace models;
use \timer as timer;

class fields extends _ {

	
	private static $instance;
	function __construct($table) {
		parent::__construct();

		$this->table = $table;
		$this->fields = array();

	}
	public static function getInstance($table=""){
	//	if ( is_null( self::$instance ) ) {
			self::$instance = new self($table);
	//	}
		return self::$instance;
	}

	function get($ID,$options=array()) {
		$timer = new timer();
		$where = "(ID = '$ID' OR MD5(ID) = '$ID')";
		
		
		$result = $this->getData($where,"","0,1",$options);
		

		if (count($result)) {
			$return = $result[0];
			
		} else {
			$return = parent::dbStructure("fields_".$this->table);
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

		$this->fields = $result;
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




		//test_array($this->table);



		$sql = "
			SELECT *
			FROM fields_{$this->table}
			$where
			$groupby
			$orderby
			$limit;
		";

		if (isset($_GET['sql'])&&isLocal()){
		//	test_string($sql);
		}

		//
		$result = $f3->get("DB")->exec($sql, $args, $ttl);



		$return = $result;
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $return;
	}

	
	
	function _save($ID,$values = array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();


		//test_array($values);

		if (isset($values['data']))$values['data'] = json_encode($values['data']);


		$a = new \DB\SQL\Mapper($f3->get("DB"), "fields_".$this->table);
		if (!is_numeric($ID) && $ID!=""){
			$a->load("name='{$values['name']}'");
		} else {
			$a->load("ID='{$ID}'");
		}

		//if (isset($values['data'])) $values['data'] = json_encode($values['data']);



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






	function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"),"fields_".$this->table);
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return "done";

	}
	
	
	function format($data,$options) {
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
			$item['key'] = "f".$item['ID'];




			$n[] = $item;
		}


		$got_records = false;
		if (count($recordIDs)){
			$recordIDs = implode(",",$recordIDs);
			$got_records = true;
		}


		
	//	test_array($n);
		
		
		
		
		
		if ($single) $n = $n[0];
		
		
		$records = $n;
		
		
		
	//	test_array($records);
		
		
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());
		return $records;
	}

	function DataSQL($where = "", $orderby = "", $limit = "", $options = array()){


		if ($where) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}
		$cfg= $this->cfg;

		//test_array($cfg);

		$select_ = array();
		$select_[] = "{$this->table}.*";



		$select = isset($options['select'])?$options['select']:array();

		foreach ($select as $item){
			$select_[] = $item;
		}
//test_array($select_);
		$groupby = "GROUP BY {$this->table}.ID";
		if (isset($options['groupby'])) {
			$groupby = "GROUP BY " . $options['groupby'];
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

		//JSON_UNQUOTE(JSON_EXTRACT(a.json_field, "$.json_value"))

		$joins = "";





		foreach ($this->fields as $item){
			$selectStr = "`{$item['key']}`";

			if ($item['isLookup']=='1'){
				$selectStr = "GROUP_CONCAT(DISTINCT CONCAT( table_join_{$item['key']}.ID,'{$cfg['field_lookup_separator']['idvalue']}',table_join_{$item['key']}.value) SEPARATOR '{$cfg['field_lookup_separator']['records']}')";

				if ($item['data']['join']){
					$joins .= " LEFT JOIN ({$item['data']['join']}) table_join_{$item['key']}  ON FIND_IN_SET(table_join_{$item['key']}.`ID`, `{$item['key']}`) ";
				} else {
					$joins .= " LEFT JOIN `fields_{$this->table}_data` table_join_{$item['key']} ON FIND_IN_SET(table_join_{$item['key']}.`ID`, `{$item['key']}`) ";
				}
				$select_ = str_replace("`{$item['key']}`", $selectStr, $select_);
				$orderby = str_replace("`{$item['key']}`", $selectStr, $orderby);

			}
				if ($item['data']['select']){
					$selectStr = "(".$item['data']['select'].")" ;
				}




			$select_[] = $selectStr. " AS {$item['key']}";

		}

	//	test_array($orderby);



		$select = "SELECT ". implode(", ",$select_);
		$sql = "

			$select
			FROM {$this->table}
			$joins
			$where
			$groupby
			$orderby
			$limit
			
		";
		//test_string($sql);

		foreach ($this->fields as $item){

				if ($item['data']['select']){
					$lookupcol = "(".$item['data']['select'].")" ;
					$sql = str_replace("`{$item['key']}`", $lookupcol, $sql);
				}
			if ($item['isLookup']=='1'){
				$selectStr = "(table_join_{$item['key']}.value)";
				//$sql = str_replace("`{$item['name']}`", $selectStr, $sql);

			}


		}





		foreach ($this->fields as $item){
			$lookupcol = $lookupcolID = "JSON_UNQUOTE(JSON_EXTRACT(`{$this->table}`.`data`, '$.{$item['key']}'))";
			$sql = str_replace("`{$item['key']}`", $lookupcol, $sql);
		}

		//test_string($sql);

		return $sql;
	}
	static function format_record($fields,$item,$cfg){

		foreach ($fields as $field){
			if ($field['isLookup']){
				$str = $item[$field['key']];
				$rec = explode($cfg['field_lookup_separator']['records'],$str);
				$item[$field['key']] = array();
				foreach ($rec as $rec_item){
					$rec_item = explode($cfg['field_lookup_separator']['idvalue'],$rec_item);
					$item[$field['key']][] = array(
						"ID"=>$rec_item[0],
						"value"=>$rec_item[1]
					);
				}


			}
		}

		return $item;

	}


	
	
}
