<?php include "application/views/include/header.php" ?>

			<!-- #container.container -->
			<div id="container" class="container common join">
				<div class="contents-section">
					<div class="title-section">
						<h2 class="title"><img src="/resource/images/logo.jpg"></h2>
						<div class="sub-title"></div>
					</div>
					<form name="loginForm" id="loginForm" method="post" action="#NONE">
					<div class="detail-section">
						<div class="join-back-section">
							<div class="join-section">
								<div class="id-section">
									<div class="input user-id"><input type="text" name="user_id" id="user_id" placeholder="이메일을 입력해주세요."/></div>
									<div class="msg"></div>
								</div>
								<div class="pw-section">
									<div class="input user-password"><input type="password" name="user_password" id="user_password" placeholder="비밀번호를 입력해주세요."/></div>
									<div class="msg"></div>
								</div>
								<div class="user-password-chk">
									<div class="input user-password-chk"><input type="password" name="user_password_chk" id="user_password_chk" placeholder="비밀번호를 재입력해주세요."/></div>
									<div class="msg"></div>
								</div>
							</div>
						</div>

						<div class="agree-section">
							<div class="item user">
								<h3 class="tit"><label for="user_yn">플랜트 회원 약관(필수)</label></h3>
								<div class="agree">
									<input type="checkbox" name="user_yn" id="user_yn" value="Y" />
								</div>
								<div class="view">보기</div>
								<div class="msg"></div>
							</div>
							<div class="item personal">
								<h3 class="tit"><label for="privacy_yn">개인정보 수집/이용(필수)</label></h3>
								<div class="agree">
									<input type="checkbox" name="privacy_yn" id="privacy_yn" value="Y" />
								</div>
								<div class="view">보기</div>
								<div class="msg"></div>
							</div>
							<div class="item service">
								<h3 class="tit"><label for="service_yn">플랜트 서비스 약관(필수)</label></h3>
								<div class="agree">
									<input type="checkbox" name="service_yn" id="service_yn" value="Y" />
								</div>
								<div class="view">보기</div>
								<div class="msg"></div>
							</div>
							<div class="item transaction">
								<h3 class="tit"><label for="transaction_yn">전자금융 거래 이용 약관(필수)</label></h3>
								<div class="agree">
									<input type="checkbox" name="transaction_yn" id="transaction_yn" value="Y" />
								</div>
								<div class="view">보기</div>
								<div class="msg"></div>
							</div>
							<div class="item promotion">
								<h3 class="tit"><label for="promotion_yn">홍보성 정보 수신(선택)</label></h3>
								<div class="agree">
									<input type="checkbox" name="promotion_yn" id="promotion_yn" value="Y" />
								</div>
								<div class="msg"></div>
							</div>
						</div>

						<div class="btn-section">
							<div class="login">회원가입</div>
						</div>
					</div>
					</form>
				</div>
			</div>

			<!-- //#container.container -->

			<script>
				
				$(function() {
					let app = $App;
					let util = $Util;
					let layer = $Layer;
					app.init();
					app.webViewMessage(webViewMessage);
					layer.init();

					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {

						// 회원정보 설정
						if (key === 'userInfo') {
							app.userInfo = data.userInfo;
						}
					}

					// 웹뷰 준비 완료
					app.webViewReady();

					// 회원가입 선택 시
					$('.btn-section .login').on('click', function() {
						formCheck()
					});

					// 회원가입 폼 체크
					function formCheck() {
						let form;
						form = new $Form('loginForm');
						form.require('user_id', '이메일', {msgType: 'msg'});
						form.email('user_id', '이메일', {msgType: 'msg'});
						form.userPw('user_password', '비밀번호', {msgType: 'msg'});

						form.require('user_yn', '플랜트 회원 약관(필수)', {msgType: 'msg'});
						form.require('privacy_yn', '개인정보 수집/이용(필수)', {msgType: 'msg'});
						form.require('service_yn', '플랜트 서비스 약관(필수)', {msgType: 'msg'});
						form.require('transaction_yn', '전자금융 거래 이용 약관(필수)', {msgType: 'msg'});

						if (form.validate()) {
							layer.showLayer('confirm_layer', 'join-confirm', '가입 하시겠습니까?');
						}
					}

					$(document).on('click', '.join-confirm .layer-close', function() {
						if ($(this).hasClass('ok')) {
							join();
						}
						layer.hideLayer('confirm_layer', 'join-confirm');
					});

					// 회원가입
					function join() {
						$.ajax({
							type: 'post',
							url: '<?=SITE_URL?>api/login/join',
							data: {
								user_id : $('#user_id').val(),
								user_password : $('#user_password').val(),
							},
							success: function(response) {
								response = JSON.parse(response);
								let key = response.key;
								let result = response.data.result;
								
								// 아이디 중복 확인
								if (key == 'duplicationCheckFail') {
									$('.id-section .msg').html('이미 등록되어 있는 아이디 입니다.');

								// 회원가입 실패
								} else if (key == 'joinFail') {
									let message = {
										key : 'joinFail',
										data : {}
									}
									app.reactNativePostMessage(message);
								
								// 회원가입 성공
								} else if (key == 'joinSuccess') {
									let message = {
										key : 'joinSuccess',
										data : {}
									}
									app.reactNativePostMessage(message);
								}
							},
							error: function(result, error, status) {
								console.log('err____' + error + '___status__' + status);
							}
						});
					}

					// 플랜트 회원 약관(필수) 보기 선택 시
					$('.agree-section .user .view').on('click', function() {
						$.ajax({
							type: 'post',
							url: '/login/user',
							data: {},
							success: function(response) {
								// 플랜트 회원 약관(필수) 레이어 팝업 노출
								$('.join-layer .title').html($('.agree-section .user h3').text());
								$('.join-layer .term').html(response);
								layer.showLayer('join_layer');
							},
							error: function(result, error, status) {
								console.log('err____' + error + '___status__' + status);
							}
						});
					});

					// 개인정보 수집/이용(필수) 보기 선택 시
					$('.agree-section .personal .view').on('click', function() {
						$.ajax({
							type: 'post',
							url: '/login/personal',
							data: {},
							success: function(response) {
								// 개인정보 수집/이용(필수) 레이어 팝업 노출
								$('.join-layer .title').html($('.agree-section .personal h3').text());
								$('.join-layer .term').html(response);
								layer.showLayer('join_layer');
							},
							error: function(result, error, status) {
								console.log('err____' + error + '___status__' + status);
							}
						});
					});

					// 플랜트 서비스 약관(필수)
					$('.agree-section .service .view').on('click', function() {
						$.ajax({
							type: 'post',
							url: '/login/service',
							data: {},
							success: function(response) {
								// 플랜트 서비스 약관(필수) 레이어 팝업 노출
								$('.join-layer .title').html($('.agree-section .service h3').text());
								$('.join-layer .term').html(response);
								layer.showLayer('join_layer');
							},
							error: function(result, error, status) {
								console.log('err____' + error + '___status__' + status);
							}
						});
					});
					
					// 전자금융 거래 이용 약관(필수)
					$('.agree-section .transaction .view').on('click', function() {
						$.ajax({
							type: 'post',
							url: '/login/transaction',
							data: {},
							success: function(response) {
								// 전자금융 거래 이용 약관(필수) 레이어 팝업 노출
								$('.join-layer .title').html($('.agree-section .transaction h3').text());
								$('.join-layer .term').html(response);
								layer.showLayer('join_layer');
								
							},
							error: function(result, error, status) {
								console.log('err____' + error + '___status__' + status);
							}
						});
					});

					// 약관 레이어 팝업 확인 선택 시
					$(document).on('click', '#join_layer .ok', function() {
						$Layer.hideLayer('join_layer', '');
					});
				});
			</script>

<?php include "application/views/include/footer.php" ?>