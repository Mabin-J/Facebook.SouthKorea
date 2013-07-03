이미 설정된 도메인이 존재합니다.<br>
<br>
원본 주소 : <a href="<?=$userProfile['link'];?>" target="_blank"><?=$userProfile['link'];?></a></br>
한글 주소 : <a href="http://<?=$korean_url;?>.얼굴책.한국" target="_blank">http://<?=$korean_url;?>.얼굴책.한국</a><br>
<br>
<a href="#" onClick="clickShareUsing('<?=$korean_url?>', '<?=$puricode?>');">공유하기</a> 버튼을 눌러서 한글 주소 사용을 페이스북 친구들에게 알려보세요!<br>
<br>
<div class="modify_box">
	<div class="button_deco" id="modify_btn" onclick="clickModify();">
		<div class="button">수정</div>
	</div>
	<div id="modify_box">
		<div class="modify_input_box">
			수정할 주소 : <input type="text" name="keyword" id="keyword"></input>.얼굴책.한국<br>
		</div>	
		<div class="button_deco" onclick="clickModifyConfirm(document.getElementById('keyword').value);">
			<div class="button">수정</div>
		</div>
		<div class="button_deco" onclick="clickModifyClose();">
			<div class="button">닫기</div>
		</div>
	</div>
</div>
<div class="button_deco" onclick="clickDelete();">
	<div class="button">삭제</div>
</div>

