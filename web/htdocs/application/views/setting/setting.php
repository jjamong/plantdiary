<?php include "application/views/include/header.php" ?>

			<!-- //#container.container -->
			<div id="container" class="container common setting">
				<div class="contents-section">
					<div class="contents">
						<div class="list">
							<ul>
								<li class="login-section">
									<div class="login">로그인</div>
									<div class="logout">로그아웃</div>
								</li>
								<li class="login-section">
									<div class="user-info">회원정보</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<!-- //#container.container -->

			<script>

				$(function() {
					let app = $App;
					let util = $Util;
					app.init();
					app.webViewMessage(webViewMessage);


					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {

						// 웹뷰 로드 완료
						if (key === 'webViewLoad') {
							// 회원정보 설정
							app.userData = data.userData;
						
							if (app.userData) {
								$('.login-section .login').css('display', 'none');
								$('.login-section .logout').css('display', 'block');
								$('.login-section .user-info').css('display', 'block');
							} else {
								$('.login-section .login').css('display', 'block');
								$('.login-section .logout').css('display', 'none');
								$('.login-section .user-info').css('display', 'none');
							}
							
							// 웹뷰 준비 완료
							app.webViewReady();
						}
					}

					
					// 웹 상태일 경우
					if (app.webMode) {
						
						if (app.userData) {
							$('.login-section .login').css('display', 'none');
							$('.login-section .logout').css('display', 'block');
							$('.login-section .user-info').css('display', 'block');
						} else {
							$('.login-section .login').css('display', 'block');
							$('.login-section .logout').css('display', 'none');
							$('.login-section .user-info').css('display', 'none');
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

					// 로그아웃
					$('.setting .logout').on('click', function() {
						let logoutMessage = {
							key : 'settingLogout',
							data : {}
						}
						app.reactNativePostMessage(logoutMessage);
					});
				});
			</script>

<?php include "application/views/include/footer.php" ?>