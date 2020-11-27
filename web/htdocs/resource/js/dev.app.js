/*
	React Native Communication
*/ 
(function($) {

	$App = {
		webMode : true,				// true 웹 상태, false 웹뷰 상태(디폴트), 
		userData : {},				// 회원 정보
		deviceType : undefined,		// 디바이스 타입(android, ios)
		messageObject : undefined,	// webview message 오브젝트

		// 초기화
		init : function () {
			// 디바이스 타입 체크 및 웹뷰 설정
			this.deviceType = this.deviceCheck();
			if (this.deviceType == 'android') {
				this.messageObject = document;
			} else {
				this.messageObject = window;
			}

			// 웹뷰 객체 체크
			if (this.reactNativeWebViewCheck()) {
				this.webMode = false;
				console.log('window.ReactNativeWebView 객체가 있습니다.[OK]')
			} else {
				console.log('window.ReactNativeWebView 객체가 없습니다.[NO]');
				$('#container').css('visibility', 'visible');
					
				// 웹 상태일 경우
				if (this.webMode) {
					$('.web').css('display', 'block');

					this.userData = {
						'user_seq': '1',
						'user_id': 'gudrb3001@naver.com'
					}
				}
			}
		},

		// 리액트 네이티브 웹뷰인지 체크
		reactNativeWebViewCheck : function () {
			let check = false;
			// 리액트 네이티브일 경우
			if (window.ReactNativeWebView) {
				check = true;
			} else {
				check = false;
			}
			return check;
		},
		
		// WEB에서 APP으로 데이터 통신
		reactNativePostMessage : function (message) {
			if (this.reactNativeWebViewCheck()) {
				window.ReactNativeWebView.postMessage(JSON.stringify(message));
			}
		},

		// APP에서 WEB으로 데이터 통신
		webViewMessage : function (callFunc) {
			this.messageObject.addEventListener('message', function(response) {
				if (response.data) {
					response = JSON.parse(response.data);
					let key = response.key;
					let data = response.data;

					callFunc(key, data);
				}
			});
		},

		// 웹뷰 준비완료 통신
		webViewReady : function () {
			if (this.reactNativeWebViewCheck()) {
				let webViewReady = {
					key : 'webViewReady',
					data : {}
				}
				this.reactNativePostMessage(webViewReady);
				$('#container').css('visibility', 'visible');
			}
		},

		// 디바이스 체크
		deviceCheck : function() {
			let userAgent = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
			let check;
			if ( userAgent.indexOf('android') > -1) {
				check = 'android';
			} else if ( userAgent.indexOf("iphone") > -1||userAgent.indexOf("ipad") > -1||userAgent.indexOf("ipod") > -1 ) {
				check = 'ios';
			} else {
				check = 'other';
			}
			return check;
		},
		
	};

})(jQuery);