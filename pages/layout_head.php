<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
<?
if($uid){
?>
	<title><?=$userProfile['name'] ?> - 얼굴책.한국</title>
<?
} else {
?>
	<title>얼굴책.한국</title>
<?
}
?>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="./css/default.css" />
	<script language="javascript" src="./js/default.js"></script>
</head>
<body>
<div class="canvas" id="fb-root">
	<div class="head">
		<div class="title_bar">
			<a href="/"><div class="title title_item"></div></a>
<?
if($uid){
?>
			<a href="<?=$userProfile['link']?>" class="profile title_item">
						<div class="profile_name"><?=$userProfile['name'] ?></div>
						<img class="profile_img" src="<?=$userProfile['picture']['data']['url'] ?>">
			</a>
<?
} else {
?>
			<div class="login_box title_item">
				<div class="login" onClick="clickLogin();">
					Facebook Login
				</div>
			</div>
<?
}
?>
		</div>
	</div>
	<div class="fb_like_bar">
		<fb:like send="true" href="http://xn--rf5b.xn--bf0bl47bpte.xn--3e0b707e" width="960" show_faces="false" class="fb_like" action="recommend"/>
	</div>
	<div class="content">
		<div class="content_box fb_root" id="content_box">
