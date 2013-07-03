<?
require_once("./facebook_sdk/facebook.php");
require_once("./puricode-conv/idna_convert.class.php");

class App{
	private $facebook;
	private $facebookConfig;
	private $facebookUid;
	private $facebookProfile;

	private $db;
	private $dbConfig;

	private $idnaConverter;

	private $loaded = false;

	public function __construct(){
		require_once("./config.php");

		$this->dbConfig = $dbConfig;
		$this->facebookConfig = $facebookConfig;

		$this->dbConnect();
		$this->loadFacebook();

	}

	private function dbConnect(){
		$this->db = new mysqli($this->dbConfig['host'], $this->dbConfig['username'], $this->dbConfig['password'], $this->dbConfig['database']);

		if($this->db->connect_errno){
			echo "Cannot Access DB (" . $this->db->connect_errno . ")";
		}
		$this->db->set_charset("UTF-8");
	}

	private function loadFacebook(){
		require_once("./facebook_sdk/facebook.php");
		$this->facebook = new Facebook($this->facebookConfig);
		$this->facebookUid = $this->facebook->getUser();

		try{
			if($this->facebookUid)
				$this->facebookProfile = $this->facebook->api('/me?fields=id,name,picture,link','GET');
		} catch(Exception $e) {
			$this->facebookUid = 0;
		}
	}

	public function run(){
		$this->dbConnect();
		$this->idnaConverter = new idna_convert();
		$this->loadFacebook();

		$reqUri = $_SERVER['REQUEST_URI'];

		if($reqUri != "/"){
			if($reqUri == '/channel.html'){
				$cache_expire = 60*60*24*365;
				header("Pragma: public");
				header("Cache-Control: max-age=".$cache_expire);
				header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');

				readfile("./root/channel.html");
				exit();
			} else if(file_exists("./root" . $reqUri)){
				if(strpos($_SERVER['REQUEST_URI'], ".js"))
					header("Content-Type: text/javascript");
				else if(strpos($_SERVER['REQUEST_URI'], ".css"))
					header("Content-Type: text/css");
				else if(strpos($_SERVER['REQUEST_URI'], ".ico"))
					header("Content-Type: image/ico");
				else if(strpos($_SERVER['REQUEST_URI'], ".png"))
					header("Content-Type: image/png");
				
				if(strpos($_SERVER['REQUEST_URI'], ".htm")
						|| strpos($_SERVER['REQUEST_URI'], ".html"))
					require_once("./root" . $_SERVER['REQUEST_URI']);
				else 
					readfile("./root" . $_SERVER['REQUEST_URI']);

				exit();
			}
		} 
		
		if($reqUri == "/api"){
			header ('Content-type: text/plain; charset=utf-8');

			if($_SERVER['REQUEST_METHOD'] != "POST"){
				$this->showDenienedMsg();
				exit();
			}

			if(!$_POST['uid']){
				$this->showDenienedMsg();
				$this->facebookUid = 0;
				exit();
			}

			if($this->facebookUid == 0){
				$this->showDenienedMsg();
				exit();
			}

			if($_POST['mode'] == "query"){
				$this->apiQuery();
			} else if ($_POST['mode'] == "add"){
				$this->apiAdd();
			} else if ($_POST['mode'] == "modify"){
				$this->apiModify();
			} else if ($_POST['mode'] == "delete"){
				$this->apiDelete();
			} else {

			}
		} else if($reqUri == "/"){
			header ('Content-type: text/html; charset=utf-8');

			if(!$this->loaded){
				$this->loaded = true;
				$this->showLoadingPage();
			} else {
				$this->showMainPage();
			}
		} else if($reqUri == "/about"
				|| $reqUri == "/privacy"
				|| $reqUri == "/term"){
			$this->showNormalPage($reqUri);
		} else {
			$this->showDenienedMsg();
			exit();
		}
	}

	private function showLoadingPage(){
		readfile("./pages/loading.htm");
	}

	private function showMainPage(){
		if(($uid = $this->facebookUid) != 0){
			$userProfile = $this->facebookProfile;
		}

		require_once("./pages/layout_head.php");

		if($uid){
			require_once("./pages/main_authed.php");
		} else {
			require_once("./pages/main_noauthed.php");
		}

		require_once("./pages/layout_tail.php");
	}

	private function showNormalPage($reqUri){
		header ('Content-type: text/html; charset=utf-8');

		if(($uid = $this->facebookUid) != 0){
			$userProfile = $this->facebookProfile;
		}

		require_once("./pages/layout_head.php");
		require_once("./pages/" . $reqUri . ".php");
		require_once("./pages/layout_tail.php");
	}

	private function blockInjection($target){
		$target = trim($target); // 공백제거
		$target = @str_replace("\\","",$target);
		$target = @str_replace("'","",$target);
		$target = @str_replace("\”",””,$target);
		$target = addslashes($target);

		return $target;
	}

	private function apiQuery(){
		$dbresult = $this->db->query("SELECT * FROM fb_korean_url WHERE uid = " . $this->facebookUid . ";");
		if($dbresult){
			$userProfile = $this->facebookProfile;

			if($dbresult->num_rows > 0){
				$result_arr = $dbresult->fetch_array();
				$puricode = $result_arr[2];
				$korean_url = $this->idnaConverter->decode($puricode);
				require_once("./pages/query_exist.php");
			} else {
				require_once("./pages/query_notexist.php");
			}
		} else {
			$this->showErrorMsg();
			exit();
		}
	}

	private function apiAdd(){
		if(!$_POST['keyword']){
			$this->showErrorMsg();
			exit();
		}

		$userProfile = $this->facebookProfile;

		$korean_url = $this->blockInjection($_POST['keyword']);

		$puricode = $this->idnaConverter->encode($korean_url);

		$dbResult = $this->db->query("INSERT INTO fb_korean_url (`uid`, `link`, `url`) VALUES ("
				. $userProfile['id'] . ", "
				. "\"" . $userProfile['link'] . "\", "
				. "\"" . $puricode . "\");");

		if($dbResult){
			require_once("./pages/query_added.php");
		} else {
			require_once("./pages/query_added_error.php");
		}
	}

	private function apiModify(){
		if(!$_POST['keyword']){
			$this->showErrorMsg();
			exit();
		}

		$dbResult = $this->db->query("SELECT url FROM fb_korean_url WHERE `uid` = " . $this->facebookUid . ";");
		$result_arr = $dbResult->fetch_array();
		$before_url = $result_arr[0];
		$korean_url = $_POST['keyword'];
		$puricode = $this->idnaConverter->encode($korean_url);

		$dbResult = $this->db->query("UPDATE fb_korean_url SET `URL` = \"" . $puricode .
				"\" WHERE `uid` = " . $this->facebookUid . ";");

		if($dbResult){
			$before_url = $this->idnaConverter->decode($before_url);
			require_once("./pages/query_modified.php");
		} else {
			require_once("./pages/query_modified_error.php");
		}
	}

	private function apiDelete(){
		$dbResult = $this->db->query("SELECT url FROM fb_korean_url WHERE `uid` = " . $this->facebookUid . ";");
		$result_arr = $dbResult->fetch_array();
	        $before_url = $result_arr[0];
		
		$dbResult = $this->db->query("DELETE FROM fb_korean_url WHERE `uid` = " . $this->facebookUid .";");

		if($dbResult){
			$before_url = $this->idnaConverter->decode($before_url);

			require_once("./pages/query_deleted.php");	
		} else {
			require_once("./pages/query_deleted_error.php");	
		}
	}

	private function showErrorMsg(){
		echo "오류가 발생했습니다.";
		echo "<br><div class='button_deco' onclick=\"location.href='/'\"><div class='button'>돌아가기</div></div>";
	}

	private function showDenienedMsg(){
		echo "잘못된 접근입니다.";
		echo "<br><div class='button_deco' onclick=\"location.href='/'\"><div class='button'>돌아가기</div></div>";
	}
}

?>
