<?php

namespace models;

use \timer as timer;

class system_users extends _ {
	private static $instance;

	function __construct() {
		parent::__construct();


	}

	public static function getInstance() {
		if ( is_null(self::$instance) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	function get($ID, $options = array()) {
		$timer = new timer();
		$where = "system_users.ID = '$ID'";
		if ( !is_numeric($ID) ) {
			$where = "username = '$ID'";
		}


		$result = $this->getData($where, "", "0,1", $options);


		if ( count($result) ) {
			$return = $result[0];

		} else {
			$return = parent::dbStructure("system_users");
		}

		if ( $options['format'] ) {
			$return = $this->format($return, $options);
		}
		//test_array($return);
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());

		return $return;
	}

	public function getAll($where = "", $orderby = "", $limit = "", $options = array()) {
		$result = $this->getData($where, $orderby, $limit, $options);
		$result = $this->format($result, $options);

		return $result;

	}

	public function getData($where = "", $orderby = "", $limit = "", $options = array()) {
		$timer = new timer();
		$f3 = \Base::instance();

		if ( $where ) {
			$where = "WHERE " . $where . "";
		} else {
			$where = " ";
		}

		if ( $orderby ) {
			$orderby = " ORDER BY " . $orderby;
		}
		if ( $limit ) {
			$limit = " LIMIT " . $limit;
		}

		$args = "";
		if ( isset($options['args']) ) {
			$args = $options['args'];
		}

		$ttl = "";
		if ( isset($options['ttl']) ) {
			$ttl = $options['ttl'];
		}


		$result = $f3->get("DB")->exec("
			 SELECT DISTINCT system_users.*, system_users_companies.settings, system_users_companies.permissions
			FROM system_users LEFT JOIN system_users_companies ON system_users.ID = system_users_companies.userID
			$where
			$orderby
			$limit;
		", $args, $ttl);

		$return = $result;
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());

		return $return;
	}

	public static function login($username, $password) {
		$f3 = \Base::instance();
		$timer = new timer();

		$ID = "";


		setcookie("username", $username, time() + 31536000, "/");


		$password_hash = md5(md5('salt') . $password);


		$result = $f3->get("DB")->exec("
			SELECT ID, username FROM system_users WHERE `username` ='$username' AND `password` = '$password_hash'
		");


		if ( count($result) ) {
			$result = $result[0];
			$ID = $result['ID'];
			$f3->get("DB")->exec("UPDATE system_users SET lastlogin = now() WHERE ID = '$ID';");


			$_SESSION['uID'] = base64_encode($ID);
			if ( isset($_COOKIE['username']) ) {
				$_COOKIE['username'] = $result['username'];
			} else {
				setcookie("username", $result['username'], time() + 31536000, "/");
			}


		}
		//test_array($_SESSION);

		$return = $ID;
		$timer->stop(array(
			"Models" => array(
				"Class" => __CLASS__,
				"Method" => __FUNCTION__,
			),
		), func_get_args());

		return $return;
	}

	public static function _save($ID, $values = array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();
		$domain = $f3->get("domain");
		$domainID = $domain['ID'];

		if ( isset($values['ID']) ) {
			unset($values['ID']);
		}
		if ( isset($values['password']) ) {
			if ( $values['password'] != "" ) {
				$values['password'] = md5(md5('salt') . $values['password']);
			} else {
				unset($values['password']);
			}

		}

		if ( isset($values['settings']) ) {
			$values['settings'] = json_encode($values['settings']);
		}

		//test_array($values);

		$a = new \DB\SQL\Mapper($f3->get("DB"), "system_users");
		$a->load("ID='$ID'");

		foreach ( $values as $key => $value ) {
			if ( isset($a->$key) ) {
				$a->$key = $value;
			}

		}

		$a->save();
		$ID = ($a->ID) ? $a->ID : $a->_id;


		if ( $values['companyID'] ) {
			$b = new \DB\SQL\Mapper($f3->get("DB"), "system_users_companies");
			$b->load("userID='$ID' AND companyID = '{$values['companyID']}'");

			$b->userID = $ID;
			$b->companyID = $values['companyID'];
			$b->settings = $values['settings'];
			$b->save();

		}


		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());

		return $ID;
	}

	public static function _settings($userID, $companyID, $values = array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$return = array();

		//test_array($values);

		$values = json_encode($values);


		$b = new \DB\SQL\Mapper($f3->get("DB"), "system_users_companies");
		$b->load("userID='$userID' AND companyID = '{$companyID}'");

		$b->userID = $userID;
		$b->companyID = $companyID;
		$b->settings = $values;
		$b->save();


		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());

		return "done";
	}


	public static function _delete($ID) {
		$timer = new timer();
		$f3 = \Base::instance();
		$user = $f3->get("user");


		$a = new \DB\SQL\Mapper($f3->get("DB"), "system_users");
		$a->load("ID='$ID'");

		$a->erase();

		$a->save();


		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());

		return "done";

	}

	public static function companies($data, $options = array()) {
		$timer = new timer();
		$f3 = \Base::instance();
		$single = FALSE;
		//	test_array($items);
		if ( isset($data['ID']) ) {
			$single = TRUE;
			$data = array($data);
		}


		$ids = array();
		foreach ( $data as $item ) {
			if ( $item['ID'] ) {
				$ids[] = $item['ID'];
			}

		}

		//test_array($data);
		$companyList = array();
		if ( count($ids) ) {
			$ids = implode(",", $ids);
			$companies = $f3->get("DB")->exec("SELECT * FROM system_users_companies WHERE userID in ($ids);");

			$companyIDs = array();
			$companyListArray = array();
			foreach ( $companies as $item ) {
				$companyIDs[] = $item['companyID'];
			}

			if ( count($companyIDs) ) {
				$companyIDs = implode(",", $companyIDs);
				$companiesArray = \models\system_companies::getInstance()->getAll("ID in ($companyIDs)", "company ASC", "", array("format"));

				foreach ( $companiesArray as $item ) {
					$companyListArray[$item['ID']] = $item;
				}
			}


			//test_array($companies);


			foreach ( $companies as $item ) {
				if ( isset($companyListArray[$item['companyID']]) ) {

					$companyList[$item['userID']][] = $companyListArray[$item['companyID']];
				}
			}


			//test_array($companyList);


		}

		//test_array($companyList);

		$i = 1;
		$n = array();
		foreach ( $data as $item ) {

			$item['companies'] = isset($companyList[$item['ID']]) ? $companyList[$item['ID']] : array();
			$n[] = $item;
		}

		if ( $single ) {
			$n = $n[0];
		}


		$records = $n;


		//test_array($n);
		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());

		return $records;
	}

	static function format($data, $options = array()) {
		$timer = new timer();
		$single = FALSE;
		//	test_array($items);
		if ( isset($data['ID']) ) {
			$single = TRUE;
			$data = array($data);
		}


		if ( isset($options['companies']) && $options['companies'] ) {
			$data = self::companies($data, $options);
		}
		//	test_array($data);
		//test_array($data);

		$i = 1;
		$n = array();
		foreach ( $data as $item ) {
			$item['timeago']['lastlogin'] = timesince($item['lastlogin']);
			$item['timeago']['lastActivity'] = timesince($item['lastActivity']);
			$item['raw_settings'] = (json_decode($item['settings'], TRUE));
			$item['settings'] = self::settings($item['raw_settings']);

			$n[] = $item;
		}

		if ( $single ) {
			$n = $n[0];
		}


		$records = $n;


		//test_array($n); 


		$timer->_stop(__NAMESPACE__, __CLASS__, __FUNCTION__, func_get_args());

		return $n;
	}


	static function lastActivity($user) {
		if ( $user['ID'] ) {
			self::_save($user['ID'], array("lastActivity" => date("Y-m-d H:i:s")));
		}


		//test_array($settings); 
	}


	static function default_settings($section = FALSE) {


		$settings_defaults = array(
			"interactions" => array(
				"daterange" => "Last 30 Days",
				"search" => "",
				"mine" => "1",
				"groupby" => "none",
				"groupby_dir" => "ASC",
				"order" => "date_in",
				"order_dir" => "DESC",
				"num_records" => 20,
				"columns" => array(
					"ID",
					"label",
					"date_in",
					"user",

				),
			),
			"contacts" => array(

				"search" => "",
				"type" => "1",

			),
			"companies" => array(
				"groupby" => "none",
				"groupby_dir" => "ASC",
				"order" => "`date_in`",
				"order_dir" => "DESC",
				"num_records" => 20,
				"columns" => array(
					"ID",
					"name",
					"date_in",
					"date_created",
					"_deleted",

				),
			),
			"individuals" => array(
				"groupby" => "none",
				"groupby_dir" => "ASC",
				"order" => "`date_in`",
				"order_dir" => "DESC",
				"num_records" => 20,
				"columns" => array(
					"ID",
					"name",
					"date_in",
					"date_created",
					"_deleted",

				),
			),

			"tasks" => array(
				"filter" => array(
					"search" => "",
				),
				"groupby" => "none",
				"groupby_dir" => "ASC",
				"order" => "date_in",
				"order_dir" => "DESC",
				"num_records" => 20,
				"columns" => array(
					"ID",
					"date_in",

				),
			),
			"leads" => array(
				"filter" => array(
					"search" => "",
				),
				"groupby" => "none",
				"groupby_dir" => "ASC",
				"order" => "date_in",
				"order_dir" => "DESC",
				"num_records" => 20,
				"columns" => array(
					"ID",
					"date_in",

				),
			),
			"objectives" => array(
				"filter" => array(
					"search" => "",
				),
				"groupby" => "none",
				"groupby_dir" => "ASC",
				"order" => "date_in",
				"order_dir" => "DESC",
				"num_records" => 20,
				"columns" => array(
					"ID",
					"date_in",

				),
			),
			"settings_individuals_fields" => array(
				"search" => "",
				"type" => "layout-rows",
				"renderer" => "details",
			),
			"settings_companies_fields" => array(
				"search" => "",
				"type" => "layout-rows",
				"renderer" => "details",
			),
		);


		if ( $section ) {
			$settings_defaults = $settings_defaults[$section];
		}


		//test_array(array("settings"=>$settings,"user"=>$user['settings'],"defaults"=>$settings_defaults));


		return $settings_defaults;

	}

	static function settings($settings = FALSE) {
		$f3 = \Base::instance();
		if ( !$settings ) {
			$user = $f3->get("user");
			$settings = $user['settings'];
			if ( !is_array($settings) ) {
				$settings = json_decode($user['settings'], TRUE);
			}
		}

		//test_array($settings);


		$settings_defaults = self::default_settings();


		$settings = array_replace_recursive($settings_defaults, (array) $settings);


		//test_array(array("settings"=>$settings,"user"=>$user['settings'],"defaults"=>$settings_defaults));


		return $settings;

	}


}
