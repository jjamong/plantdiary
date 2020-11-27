<?php include "application/views/include/header.php" ?>

			<!-- //#container.container -->
			<div id="container" class="container common login">
				<div class="contents-section">
					<div class="title-section text-c">
						<div class="text-section">
							<h2 class="title">PLANT</h2>
							<div class="sub-title">서브 타이틀</div>
						</div>
					</div>
					<form name="loginForm" id="loginForm" method="post" action="#NONE">
					<div class="detail-section">
						<div class="login-section">
							<div class="id-section">
								<!-- <label for="id">아이디</label> -->
								<div class="input login-id"><input type="text" name="userId" id="userId" placeholder="아이디를 입력해주세요."/></div>
								<div class="msg"></div>
							</div>
							<div class="pw-section">
								<!-- <label for="pw">비밀번호</label> -->
								<div class="input"><input type="password" name="userPassword" id="userPassword" placeholder="비밀번호를 입력해주세요."/></div>
								<div class="msg"></div>
							</div>
						</div>
						<div class="btn-section">
							<div class="login">로그인</div>
						</div>
						<div class="sub-section">
							<div class="txt">아이디 찾기</div>
							<div class="txt">비밀번호 찾기</div>
							<div class="txt join">회원가입</div>
						</div>
					</div>
					</form>
				</div>
			</div>
			<!-- //#container.container -->

			<script>

				$(function() {
					let app = $App;
					app.init();
					app.webViewMessage(webViewMessage);

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
						form.require("userId", "아이디", {msgType: 'msg'});
						form.require("userPassword", "비밀번호", {msgType: 'msg'});
						
						var userId = $("#userId");
						var userPassword = $("#userPassword");

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
									let result = response.data.result;
									
									// 로그인 성공
									if (key == 'loginSuccess') {
										let message = {
											key : 'loginOK',
											data : result
										}
										app.reactNativePostMessage(message);
									// 로그인 실패
									} else if (key == 'loginFail') {
										$('.pw-section .msg').html('아이디와 비밀번호를 확인해주세요.');
									}
								},
								error: function(result, error, status) {
									console.log('err____' + error + '___status__' + status);
								}
							});
						};
					});

					// 회원가입 선택 시
					$('.sub-section .join').on('click', function() {
						// let message = {
						// 	key : 'moveJoin',
						// 	data : {}
						// }
						// app.reactNativePostMessage(message);
					});
				});
				
			</script>

<?php include "application/views/include/footer.php" ?>