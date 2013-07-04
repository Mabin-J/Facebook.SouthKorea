<?

$main_domain = "xn--bf0bl47bpte.xn--3e0b707e";

// Bot 판별
if(is_bot()){
	header ('Content-type: text/html; charset=utf-8');
	readfile("./pages/for_bot.htm");
	exit;
}

// 얼굴책.한국 연결
if($_SERVER['SERVER_NAME'] == $main_domain
		|| $_SERVER['SERVER_NAME'] == "www." . $main_domain){
	showRedirectPage("http://facebook.com" . $_SERVER['REQUEST_URI']);
	exit();
}

// 2차도메인 판별
$second_domain = str_replace(".".$main_domain, "", $_SERVER['SERVER_NAME']);

// 앱.얼굴책.한국
if($second_domain == "xn--rf5b"){
	require_once("./app.php");
	if(!session_id()){
		session_start();
	}

	if(isset($_SESSION['recent_time'])){
		if(microtime(true) - $_SESSION['recent_time'] > 3600){
			$_SESSION['recent_time'] = microtime(true);
			$oApp = new App();
			$_SESSION['oApp'] = $oApp;
		} else {
			$oApp = $_SESSION['oApp'];
		}
	} else {
		$_SESSION['recent_time'] = microtime(true);
		$oApp = new App();
		$_SESSION['oApp'] = $oApp;
	}

	$oApp->run();

	exit();
}

// 기타 2차도메인 판별
require("./config.php");
$oDB = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);

if($oDB->connect_errno){
	echo "Cannot Access DB (" . $oDB->connect_errno . ")";
}
$oDB->set_charset("UTF-8");



$result = $oDB->query("SELECT link FROM fb_korean_url WHERE `url` = \"" . $second_domain . "\";");
$result_arr = $result->fetch_array();
$result_url = $result_arr[0];


if($result_url){
	$result = $result_url.$_SERVER['REQUEST_URI'];
	showRedirectPage($result);
	exit;
} else{
	require_once("./pages/notexistdomain.php");
	exit();
}

function is_bot(){
	if(strpos($_SERVER['HTTP_USER_AGENT'], "Googlebot"))
		return true;

	if(strpos($_SERVER['HTTP_USER_AGENT'], "facebookexternalhit"))
		return true;

	if(strpos($_SERVER["REMOTE_ADDR"], "74.125.126"))
		return true;

	if(strpos($_SERVER["HTTP_USER_AGENT"], "+https://developers.google.com/+/web/snippet/"))
		return true;

	return false;
}

function showRedirectPage($target){
	header("Location: ".$target);
	require_once("./pages/redirect.php");
}

?>

