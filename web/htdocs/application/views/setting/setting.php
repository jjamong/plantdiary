<?php include "application/views/include/header.php" ?>

			<!-- //#container.container -->
			<div id="container" class="container common setting">
				<div class="contents-section">
					<div class="contents">
						<div class="list">
							<ul>
								<li class="login-section">
									<div class="login">로그인</div>
								</li>
								<li class="email-section">
									<div class="title">이메일</div>
									<div class="desc"></div>
								</li>
								<li class="pw-change">
									<div class="title">비밀번호 변경하기</div>
								</li>
								<li class="logout-section">
									<div class="logout">로그아웃</div>
								</li>
								<li class="pw-withdrawal">
									<div class="title">회원탈퇴</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<!-- //#container.container -->

			<!-- 비밀번호 변경 팝업(confirm) -->
			<div class="password-change-layer common-layer" id="password_change_layer">
				<div class="title-section">
					<div class="title">비밀번호 변경</div>
				</div>
				<div class="content-section">
					<form name="passwordChangeForm" id="passwordChangeForm" method="post">
						<div class="item pw-section">
							<label for="current_user_password">기존 비밀번호</label>
							<div class="input user_password"><input type="password" name="current_user_password" id="current_user_password" placeholder="기존 비밀번호를 입력해 주세요."/></div>
							<div class="msg"></div>
						</div>
						<div class="item user_password">
							<label for="current_user_password">신규 비밀번호</label>
							<div class="input user-password"><input type="password" name="user_password" id="user_password" placeholder="신규 비밀번호를 입력해주세요."/></div>
							<div class="msg"></div>
						</div>
						<div class="item user_password_chk">
							<label for="current_user_password">신규 비밀번호 확인</label>
							<div class="input user-password-chk"><input type="password" name="user_password_chk" id="user_password_chk" placeholder="비밀번호를 재입력해주세요."/></div>
							<div class="msg"></div>
						</div>
					</form>
				</div>
				<div class="btn-section btn-2">
					<div class="btn cancel layer-close">취소</div>
					<div class="btn ok layer-close">확인</div>
				</div>
			</div>

			<!-- 회원탈퇴 팝업(confirm) -->
			<div class="withdrawal-layer common-layer" id="withdrawal_layer">
				<div class="title-section">
					<div class="title">회원탈퇴</div>
				</div>
				<div class="content-section">
					<form name="withdrawalForm" id="withdrawalForm" method="post">
						<div class="item pw-section">
							<div class="input user_password"><input type="password" name="user_password" id="user_password" placeholder="비밀번호를 입력해 주세요."/></div>
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

					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {

						// 웹뷰 로드 완료
						if (key === 'webViewLoad') {
							// 회원정보 설정
							app.userData = data.userData;
							
							if (app.userData) {
								$('.email-section .desc').text(app.userData.user_id)
								$('.contents-section').addClass('login')
								$('.login-section').css('display', 'none');

								$('.email-section').css('display', 'block');
								$('.logout-section').css('display', 'block');
								$('.pw-change').css('display', 'block');
								$('.pw-withdrawal').css('display', 'block');
							} else {
								$('.email-section').css('display', 'none');
								$('.logout-section').css('display', 'none');
								$('.pw-change').css('display', 'none');
								$('.pw-withdrawal').css('display', 'none');
								
								$('.login-section').css('display', 'block');
							}
							
							// 웹뷰 준비 완료
							app.webViewReady();
						}
					}
					
					// 웹 상태일 경우
					if (app.webMode) {
						if (app.userData) {
							$('.email-section .desc').text(app.userData.user_id)
							$('.contents-section').addClass('login')
							$('.login-section').css('display', 'none');

							$('.email-section').css('display', 'block');
							$('.logout-section').css('display', 'block');
							$('.pw-change').css('display', 'block');
							$('.pw-withdrawal').css('display', 'block');
						} else {
							$('.email-section').css('display', 'none');
							$('.logout-section').css('display', 'none');
							$('.pw-change').css('display', 'none');
							$('.pw-withdrawal').css('display', 'none');
							
							$('.login-section').css('display', 'block');
						}
					}
					
					// 로그인 화면 이동
					$('.setting .login').on('click', function() {
						let message = {
							key : 'moveLogin',
							data : {}
						}
						app.reactNativePostMessage(message);
					});

					// 비밀번호 변경 선택 시
					$('.pw-change').on('click', function() {
						layer.showLayer('password_change_layer', 'password-change');
					});
					// 비밀번호 변경 팝업 확인/취소 선택 시
					$(document).on('click', '.password-change .layer-close', function() {
						
						if ($(this).hasClass('ok')) {
							let form;
							form = new $Form('passwordChangeForm');
							form.require('current_user_password', '비밀번호', {msgType: 'msg'});
							form.userPw('user_password', '비밀번호', {msgType: 'msg'});

							if (form.validate()) {
								$.ajax({
									url: '/api/login/passwordCheck',
									type: 'post',
									data: {
										user_seq: app.userData.user_seq,
										user_password: $('#password_change_layer #current_user_password').val(),
									},
									success: function(response) {
										response = JSON.parse(response);
										let key = response.key;
										let data = response.data;
										
										if (key == 'success') {
											layer.showLayer('confirm_layer', 'password-change-confirm', '비밀번호를 변경 하시겠습니까?');

										} else if (key == 'failure') {
											layer.showLayer('alert_layer', '', '비밀번호가 맞지 않습니다.');
										}
									}
								});
							}
							
						} else {
							layer.hideLayer('password_change_layer', 'password-change');
						}
					});
					$(document).on('click', '.password-change-confirm .layer-close', function() {

						if ($(this).hasClass('ok')) {

							$.ajax({
								url: '/api/login/passwordChange',
								type: 'post',
								data: {
									user_seq: app.userData.user_seq,
									user_password: $('#password_change_layer #user_password').val(),
								},
								success: function(response) {
									response = JSON.parse(response);
									let key = response.key;
									let data = response.data;
									
									if (key == 'success') {
										layer.hideLayer('confirm_layer', 'password-change-confirm');
										layer.hideLayer('password_change_layer', 'password-change');
										layer.showLayer('alert_layer', '', '비밀번호가 변경 되었습니다.');
									}
								}
							});
						} else {
							layer.hideLayer('confirm_layer', 'password-change-confirm');
						}
					});

					// 로그아웃
					$('.setting .logout').on('click', function() {
						let logoutMessage = {
							key : 'settingLogout',
							data : {}
						}
						app.reactNativePostMessage(logoutMessage);
					});

					// 회원탈퇴 선택 시
					$('.pw-withdrawal').on('click', function() {
						layer.showLayer('withdrawal_layer', 'withdrawal-pw');
					});
					// 회원탈퇴 팝업 확인/취소 선택 시
					$(document).on('click', '.withdrawal-pw .layer-close', function() {
						
						if ($(this).hasClass('ok')) {
							let form;
							form = new $Form('withdrawalForm');
							form.require('user_password', '비밀번호', {msgType: 'msg'});

							if (form.validate()) {
								$.ajax({
									url: '/api/login/passwordCheck',
									type: 'post',
									data: {
										user_seq: app.userData.user_seq,
										user_password: $('#withdrawal_layer #user_password').val(),
									},
									success: function(response) {
										response = JSON.parse(response);
										let key = response.key;
										let data = response.data;
										
										if (key == 'success') {
											layer.showLayer('confirm_layer', 'withdrawal-confirm', '탈퇴 하시겠습니까?');

										} else if (key == 'failure') {
											layer.showLayer('alert_layer', '', '비밀번호가 맞지 않습니다.');
										}
									}
								});
							}
							
						} else {
							layer.hideLayer('withdrawal_layer', 'withdrawal-pw');
						}
					});
					$(document).on('click', '.withdrawal-confirm .layer-close', function() {

						if ($(this).hasClass('ok')) {

							$.ajax({
								url: '/api/login/withdrawal',
								type: 'post',
								data: {
									user_seq: app.userData.user_seq,
								},
								success: function(response) {
									response = JSON.parse(response);
									let key = response.key;
									let data = response.data;
									
									if (key == 'success') {
										layer.hideLayer('withdrawal_layer', 'withdrawal-pw');
										layer.hideLayer('confirm_layer', 'withdrawal-confirm');
														
										let message = {
											key : 'withdrawalSuccess',
											data : {}
										}
										app.reactNativePostMessage(message);
									}
								}
							});
						} else {
							layer.hideLayer('confirm_layer', 'withdrawal-pw');
						}
					});
				});
			</script>

<?php include "application/views/include/footer.php" ?>