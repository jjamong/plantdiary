<?php include "application/views/include/header.php" ?>

			<!-- //#container.container -->
			<div id="container" class="container common login">
				<div class="contents-section">
					<div class="title-section">
						<h2 class="title"><img src="/resource/images/logo.jpg"></h2>
						<div class="sub-title"></div>
					</div>
					<form name="loginForm" id="loginForm" method="post" action="#NONE">
					<div class="detail-section">
						<div class="login-section">
							<div class="id-section">
								<!-- <label for="id">아이디</label> -->
								<div class="input login-id"><input type="text" name="user_id" id="user_id" placeholder="아이디를 입력해주세요."/></div>
								<div class="msg"></div>
							</div>
							<div class="pw-section">
								<!-- <label for="pw">비밀번호</label> -->
								<div class="input"><input type="password" name="user_password" id="user_password" placeholder="비밀번호를 입력해주세요."/></div>
								<div class="msg"></div>
							</div>
						</div>
						<div class="btn-section">
							<div class="login">로그인</div>
						</div>
						<div class="sub-section">
							<div class="txt password-search">비밀번호 찾기</div>
							<div class="txt join">회원가입</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<!-- //#container.container -->

			<!-- 비밀번호 찾기 팝업(confirm) -->
			<div class="password-search-layer common-layer" id="password_search_layer">
				<div class="title-section">
					<div class="title">비밀번호 찾기</div>
				</div>
				<div class="content-section">
					<form name="passwordSearchForm" id="passwordSearchForm" method="post">
						<div class="item pw-section">
							<div class="input user_id"><input type="text" name="user_id" id="user_id" placeholder="아이디를 입력해 주세요."/></div>
							<div class="msg"></div>
						</div>
					</form>
				</div>
				<div class="btn-section btn-2">
					<div class="btn cancel layer-close">취소</div>
					<div class="btn ok layer-close">확인</div>
				</div>
			</div>

			<script>

				$(function() {
					let app = $App;
					let util = $Util;
					let layer = $Layer;
					app.init();
					app.webViewMessage(webViewMessage);
					layer.init();

					let userInfo;
					
					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {
					}

					// 웹뷰 준비 완료
					app.webViewReady();

					// 로그인 선택 시
					$('.btn-section .login').on('click', function() {
						let form;
						form = new $Form("loginForm");
						form.require("user_id", "아이디", {msgType: 'msg'});
						form.require("user_password", "비밀번호", {msgType: 'msg'});
						
						var userId = $('.detail-section #user_id');
						var userPassword = $('.detail-section #user_password');

						if (form.validate()) {
							$.ajax({
								type: 'post',
								url: '<?=SITE_URL?>api/login/login',
								data: {
									user_id			:	userId.val(),
									user_password	:	userPassword.val(),
								},
								success: function(response) {
									response = JSON.parse(response);
									let key = response.key;
									let userRow = response.data.userRow;
									let notificationData = response.data.notificationData;
									
									// 로그인 성공
									if (key == 'loginSuccess') {
										app.reactNativePostMessage(response);
										
									// 로그인 실패
									} else if (key == 'loginFailure') {
										$('.pw-section .msg').html('아이디와 비밀번호를 확인해주세요.');
									}
								},
								error: function(result, error, status) {
									console.log('err____' + error + '___status__' + status);
								}
							});
						};
					});

					// 비밀번호 찾기 선택 시
					$('.sub-section .password-search').on('click', function() {
						layer.showLayer('password_search_layer', 'password-search');
					});
					// 비밀번호 찾기 확인/취소 선택 시
					$(document).on('click', '.password-search .layer-close', function() {
						
						if ($(this).hasClass('ok')) {
							let form;
							form = new $Form('passwordSearchForm');
							form.require('user_id', '아이디', {msgType: 'msg'});

							if (form.validate()) {
								$.ajax({
									url: '/api/login/duplicationCheck',
									type: 'post',
									data: {
										user_id: $('#password_search_layer #user_id').val(),
									},
									success: function(response) {
										response = JSON.parse(response);
										let key = response.key;
										let data = response.data;
										
										if (key == 'success') {
											emailSend($('#password_search_layer #user_id').val());
										} else {
											layer.showLayer('alert_layer', '', '등록된 아이디가 없습니다.');
										}
									}
								});
							}
						} else {
							layer.hideLayer('password_search_layer', 'password-search');
						}
					});

					// 메일 발송
					function emailSend(userId) {
						if (user_id) {
							$.ajax({
								url: '/api/login/mailSend',
								type: 'post',
								data: {
									user_id: userId,
								},
								success: function(response) {
									response = JSON.parse(response);
									let key = response.key;
									let data = response.data;
									
									if (key == 'success') {
										layer.showLayer('alert_layer', '', '메일이 발송 되었습니다.');

									} else if (key == 'mailFailure') {
										layer.showLayer('alert_layer', '', '메일 발송이 실패하였습니다.');
									}
									
									layer.hideLayer('password_search_layer', 'password-search');
								}
							});
						}
					}

					// 회원가입 선택 시
					$('.sub-section .join').on('click', function() {
						let message = {
							key : 'moveJoin',
							data : {}
						}
						app.reactNativePostMessage(message);
					});
				});
				
			</script>

<?php include "application/views/include/footer.php" ?>