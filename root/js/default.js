
function createXMLHttpRequest() {
	var xmlhttp;
	if (window.XMLHttpRequest){
		xmlhttp = new XMLHttpRequest();
	}else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	return xmlhttp;
}

function callbackfunc(){
	if(oXHR.readyState == 4) {
		if(oXHR.status == 200) {
			document.getElementById("content_box").innerHTML = oXHR.responseText;
		}
	}
}

function init(){
	if(typeof onPageLoad == 'function')
		onPageLoad();
}

function requestHTML(url, param){
//	var param = "";
//	for(name in param_arr){
//		param += name + "=" + param_arr[name] + "&";
//	}
//	param = param.substring(0, param.length - 1);

	oXHR = createXMLHttpRequest();
	oXHR.onreadystatechange = callbackfunc;
	oXHR.open("POST", url, false);
	oXHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	oXHR.setRequestHeader("Content-length", params.length);
	oXHR.send(param);
}

function clickMake(value){
	if(value == ""){
		alert("입력값이 잘못되었습니다.\n다시 입력해주세요.");
	} else {
//		var param = "keyword=" + document.getElementsByName("keyword").value;
		var param = "mode=add&keyword=" + value + "&uid=" + facebookUid;
		requestHTML("./api", param);
	}
}

function clickModify(){
	document.getElementById("modify_btn").style.display = "none";
	document.getElementById("modify_box").style.display = "block";
}

function clickModifyClose(){
	document.getElementById("modify_btn").style.display = "block";
	document.getElementById("modify_box").style.display = "none";
}

function clickModifyConfirm(value){
	if(value == ""){
		alert("입력값이 잘못되었습니다.\n다시 입력해주세요.");
	} else {
		var param = "mode=modify&keyword=" + value + "&uid=" + facebookUid;
		requestHTML("./api", param);
	}
}

function clickDelete(){
	var param = "mode=delete" + "&uid=" + facebookUid;
	requestHTML("./api", param);
}

function setCookie(name, value, expiredays){
	 var todayDate = new Date();
	  todayDate.setDate( todayDate.getDate() + expiredays );
	   document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
}

function clickLogin(){
	FB.login(function(response) {
		if (response.authResponse) {
			FB.api('/me', function(response) {
				var today = new Date();
				setCookie("ticket", "1", today + 300);
				location.href = "./";
			});
		}
	}, {scope: 'publish_stream'});
}

function clickShareUsing(kor_keyword, puricode){
	// calling the API ...
	var obj = {
		method: 'feed',
		link: 'http://' + puricode + '.xn--bf0bl47bpte.xn--3e0b707e',
		picture: 'http://xn--rf5b.xn--bf0bl47bpte.xn--3e0b707e/imgs/logo.png',
		name: kor_keyword + '.얼굴책.한국',
//		caption: 'http://' + kor_keyword + '.얼굴책.한국',
		caption: '주소입력란에 \'' + kor_keyword + '.얼굴책.한국\' 를 적어보세요~!'
	};

	function callback(response) {
	}

	FB.ui(obj, callback);
}

function clickShareModify(kor_keyword, puricode){
	// calling the API ...
	var obj = {
		method: 'feed',
		link: 'http://' + puricode + '.xn--bf0bl47bpte.xn--3e0b707e',
		picture: 'http://xn--rf5b.xn--bf0bl47bpte.xn--3e0b707e/imgs/logo.png',
		name: kor_keyword + '.얼굴책.한국',
//		caption: 'http://' + kor_keyword + '.얼굴책.한국',
		caption: 'http://' + kor_keyword + '.얼굴책.한국 으로 한글주소가 변경되었습니다.'
	};

	function callback(response) {
	}

	FB.ui(obj, callback);
}

function clickShareDelete(kor_keyword){
	// calling the API ...
	var obj = {
		method: 'feed',
		link: 'http://xn--rf5b.xn--bf0bl47bpte.xn--3e0b707e',
		picture: 'http://xn--rf5b.xn--bf0bl47bpte.xn--3e0b707e/imgs/logo.png',
		name: '앱.얼굴책.한국',
//		caption: 'http://' + kor_keyword + '.얼굴책.한국',
		caption: kor_keyword + '.얼굴책.한국 을 삭제하였습니다.'
	};

	function callback(response) {
	}

	FB.ui(obj, callback);
}






var facebookUid = 0;

window.fbAsyncInit = function() {
	FB.init({
		appId		: '139433632759813', // App ID
		channelUrl	: '//xn--rf5b.xn--bf0bl47bpte.xn--3e0b707e/channel.html', // Channel File
		status		: true, // check login status
		cookie		: true, // enable cookies to allow the server to access the session
		xfbml		: true  // parse XFBML

	});

	          // Additional initialization code here
	FB.getLoginStatus(function(response) {
		if (response.status === 'connected') {
			facebookUid = response.authResponse.userID;
		}

		init();
	});
};

  // Load the SDK Asynchronously
(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
}(document));
