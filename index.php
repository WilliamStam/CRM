<?php
date_default_timezone_set('Africa/Johannesburg');
setlocale(LC_ALL, 'en_ZA.UTF8');
$errorFolder = "." . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "php";
if (!file_exists($errorFolder)) {
	@mkdir($errorFolder, 01777, TRUE);
}
$errorFile = $errorFolder . DIRECTORY_SEPARATOR . date("Y-m") . ".log";
ini_set("error_log", $errorFile);

if (session_id() == "") {
	$SID = @session_start();
} else $SID = session_id();
if (!$SID) {
	session_start();
	$SID = session_id();
}




function currency($str){
	if ($str){
		$fmt = new NumberFormatter( 'en_ZA', NumberFormatter::CURRENCY );
		$fmt->setPattern(str_replace('¤#',"¤\xC2\xA0#", $fmt->getPattern()));
		$str = $fmt->formatCurrency($str, "ZAR");
	}

	return $str;
}


$GLOBALS["output"] = array();
$GLOBALS["models"] = array();
$GLOBALS["css"] = array();
$GLOBALS["javascript"] = array();


require_once('vendor/autoload.php');

$f3 = \base::instance();
require('inc/timer.php');
require('inc/template.php');
require('inc/functions.php');
require('inc/pagination.php');
$GLOBALS['page_execute_timer'] = new timer(TRUE);
$cfg = array();
require_once('config.default.inc.php');
if (file_exists("config.inc.php")) {
	require_once('config.inc.php');
}

$f3->set('AUTOLOAD', './|lib/|controllers/|inc/|/modules/|/app/controllers/|/resources/**/*');
$f3->set('PLUGINS', 'vendor/bcosca/fatfree/lib/');
$f3->set('CACHE', TRUE);

$f3->set('DB', new DB\SQL('mysql:host=' . $cfg['DB']['host'] . ';dbname=' . $cfg['DB']['database'] . '', $cfg['DB']['username'], $cfg['DB']['password']));


//test_array("woof"); 


$f3->set('cfg', $cfg);
$f3->set('DEBUG', 3);


$f3->set('UI', 'ui/|media/|resources/');
$f3->set('MEDIA', './media/|' . $cfg['media']);
$f3->set('TZ', 'Africa/Johannesburg');

$f3->set('TAGS', 'p,br,b,strong,i,italics,em,h1,h2,h3,h4,h5,h6,div,span,blockquote,pre,cite,ol,li,ul');


//$f3->set('ERRORFILE', $errorFile);
//$f3->set('ONERROR', 'Error::handler');
$f3->set('ONERRORd',
		function ($f3) {
			// recursively clear existing output buffers:
			while (ob_get_level())
				ob_end_clean();
			// your fresh page here:
			echo $f3->get('ERROR.text');
			print_r($f3->get('ERROR.stack'));
		}
);

$version = date("YmdH");
if (file_exists("./.git/refs/heads/" . $cfg['git']['branch'])) {
	$version = file_get_contents("./.git/refs/heads/" . $cfg['git']['branch']);
	$version = substr(base_convert(md5($version), 16, 10), -10);
}

$minVersion = preg_replace("/[^0-9]/", "", $version);
$f3->set('_version', $version);
$f3->set('_v', $minVersion);


$uID = isset($_SESSION['uID']) ? base64_decode($_SESSION['uID']) : "";

$userO = new \models\system_users();
$user = $userO->get($uID,array("format"=>true,"companies"=>true));
if (isset($_GET['auID']) && $user['su'] == '1') {
	$_SESSION['uID'] = $_GET['auID'];
	$user = $userO->get($_GET['auID'],array("format"=>true,"companies"=>true));
}

if ($user['lastCompanyID']==""){
	$user['lastCompanyID'] = isset($user['companies'][0]['ID'])?$user['companies'][0]['ID']:null;
}

$companyID  = isset($_GET['cID'])?$_GET['cID']:false;

if($user['ID']){

	if (($companyID) && ($companyID != $user['lastCompanyID'])){
		models\system_users::_save($user['ID'],array("lastCompanyID"=>$companyID));
	}

}

$companyID = $companyID?$companyID:$user['lastCompanyID'];

$company = models\system_companies::getInstance()->get($companyID,array("format"=>true));
$user['company'] = $company;
$settings = $company['settings'];

//test_array($settings);

$f3->set('settings', $settings);




$f3->set('user', $user);
//test_array($user);


if ($user['ID']) {
	models\system_users::lastActivity($user);
}



$f3->set('session', $SID);

//test_array($f3->get("types"));

$f3->route('GET|POST /login', 'controllers\front\login->page');




$f3->route('GET|POST /', 'controllers\front\login->page');

$f3->route('GET|POST /app', 'controllers\app\home->page');
$f3->route('GET|POST /app/interactions', 'controllers\app\interactions->page');
$f3->route('GET|POST /app/tasks', 'controllers\app\tasks->page');
$f3->route('GET|POST /app/objectives', 'controllers\app\objectives->page');
$f3->route('GET|POST /app/contacts', 'controllers\app\contacts->page');
$f3->route('GET|POST /app/leads', 'controllers\app\leads->page');


$f3->route('GET|POST /app/settings/users', 'controllers\app\settings_users->page');
$f3->route('GET|POST /app/settings/individuals/fields', 'controllers\app\settings_individuals_fields->page');
$f3->route('GET|POST /app/settings/companies/fields', 'controllers\app\settings_companies_fields->page');


$f3->route('GET|POST /app/test', 'controllers\app\page_test->page');
$f3->route('GET|POST /app/test/data', 'controllers\app\page_test->data');





$f3->route('GET /import', function ($app) {
	$csv = array_map('str_getcsv', file('_crap/MOCK_DATA.csv'));

	$mapping = array(
		"id"=>"",
		"first_name"=>"f1",
		"last_name"=>"f2",
		"email"=>"f6",
		"address1"=>"f7",
		"address2"=>"f8",
		"address3"=>"f9",
		"city"=>"f5",

	);

	$rec = 0;
	$c = array();
	foreach ($csv as $row){

		$r = array();
		$i = 0;
		foreach ($mapping as $column){
			if ($column){
				$r[$column] = $row[$i];
			}

			$i++;
		}
		//test_array($row);
		$data = json_encode(json_encode($r));
		$c[] = "(1, 1, 0, {$data})";

		//if ($rec>2) break;
		$rec++;

	}

	$sql_ = implode(",",$c);
	$sql = "INSERT INTO individuals (cID,userID, _deleted, data) VALUES {$sql_}";
	//test_string($sql);

	$app->get("DB")->exec($sql);




});



$f3->route('GET|POST /logout', function ($f3, $params) use ($user) {
	session_start();
	session_unset();
	session_destroy();
	session_write_close();
	setcookie(session_name(), '', 0, '/');
	$f3->reroute("/login");
});


$f3->route('GET /php', function () {
	phpinfo();
	exit();
});





$f3->route("GET|POST /app/save/@function", function ($app, $params) {
	$app->call("controllers\\app\\save\\save->" . $params['function']);
});
$f3->route("GET|POST /app/save/@class/@function", function ($app, $params) {
	$app->call("controllers\\app\\save\\" . $params['class'] . "->" . $params['function']);
});
$f3->route("GET|POST /app/save/@folder/@class/@function", function ($app, $params) {
	$app->call("controllers\\app\\save\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
});
$f3->route("GET|POST /app/data/@function", function ($app, $params) {
	$app->call("controllers\\app\\data\\data->" . $params['function']);
});
$f3->route("GET|POST /app/data/@class/@function", function ($app, $params) {
	$app->call("controllers\\app\\data\\" . $params['class'] . "->" . $params['function']);
});
$f3->route("GET|POST /app/data/@folder/@class/@function", function ($app, $params) {
	$app->call("controllers\\app\\data\\" . $params['folder'] . "\\" . $params['class'] . "->" . $params['function']);
});


$f3->run();


$models = $GLOBALS['models'];

///test_array($models); 
$t = array();
foreach ($models as $model) {
	$c = array();
	foreach ($model['m'] as $method) {
		$c[] = $method;
	}
	$model['m'] = $c;
	$t[] = $model;
}

//test_array($t); 

$models = $t;
$pageTime = timer::shortenTimer($GLOBALS['page_execute_timer']->stop("Page Execute"));

$GLOBALS["output"]['timer'] = $GLOBALS['timer'];

$GLOBALS["output"]['models'] = $models;


$GLOBALS["output"]['page'] = array(
		"page" => $_SERVER['REQUEST_URI'],
		"time" => $pageTime
);

//test_array($tt); 

if ($f3->get("ERROR")) {
	exit();
}

if (($f3->get("AJAX") && ($f3->get("__runTemplate") == FALSE) || $f3->get("__runJSON"))) {
	header("Content-Type: application/json");
	echo json_encode($GLOBALS["output"]);
} else {
	
	//if (strpos())
	if ($f3->get("NOTIMERS")) {
		exit();
	}
	
	
	echo '
		<script type="text/javascript">
			updatetimerlist(' . json_encode($GLOBALS["output"]) . ');
		</script>
		</body>
</html>';
	
}



?>
