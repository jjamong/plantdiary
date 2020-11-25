<?php include "application/views/include/header.php" ?>

			<!-- //#container.container -->
			<div id="container" class="container myplant-list">
				<div class="header-title-section">
					<div class="title-section" data-target="#alert_layer">
						<h2 class="title">내 식물</h2>
						<div class="setting">
							<div class="img"><img src="<?=SITE_URL?>resource/images/setting_icon_off.png" /></div>
						</div>
					</div>
				</div>
				<div class="contens-section">
					<div class="contents">
						<div class="list">
							<ul></ul>
						</div>
						<div class="btn-section">
							<div class="plus"><img src="<?=SITE_URL?>resource/images/plus_icon.png"/></div>
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

							// 추가 버튼 보이기
							if (app.userData) $('.myplant-list .btn-section').css('display', 'block');

							// 식물 리스트 가져오기
							getPlantList();

						// 물주기 컨펌 확인 시
						} else if (key === 'confirmWatering') {
							// 물주기
							watering();
						
						// 웹뷰 리로드
						} else if (key === 'webViewReload') {
							// 식물 리스트 가져오기
							getPlantList();
						}
					}
					
					// 웹 상태일 경우
					if (app.webMode) {

						// 추가 버튼 보이기
						if (app.userData) $('.myplant-list .btn-section').css('display', 'block');

						// 식물 리스트 가져오기
						getPlantList();
					}

					// 식물 리스트 가져오기
					function getPlantList() {

						if (app.userData) {
							$.ajax({
								url: '/api/myplant/list',
								type: 'get',
								data: {
									user_seq: app.userData.user_seq
								},
								success: function(response) {
									let plantListData = JSON.parse(response)

									// 식물 리스트 설정
									let itemhtml = "";
									let dataCount = plantListData.length;

									// 내식물 데이터가 있을 경우
									if (dataCount > 0) {
										for (let i=0; i< dataCount; i++) {

											let diary_date = util.formatDate(plantListData[i].diary_date, 'noDivision');
											let myplant_img;
											if (plantListData[i].sys_myplant_img) {
												myplant_img = '<?=SITE_URL?>uploads/user_' + plantListData[i].user_seq + '/myplant_' + plantListData[i].myplant_seq + '/' + plantListData[i].sys_myplant_img;
											} else {
												myplant_img = '/resource/images/plant_default_img.jpg';
											}

											let day = util.dDay(util.formatDate(plantListData[i].water_day, 'noDivision'));

											itemhtml += '<li data-myplantseq="' + plantListData[i].myplant_seq + '">';
											itemhtml += '	<div class="content-section">';
											itemhtml += '		<div class="item">';
											itemhtml += '			<div class="tit">식물 이름</div>';
											itemhtml += '			<div class="txt title">' + plantListData[i].myplant_name + '</div>';
											itemhtml += '		</div>';
											itemhtml += '		<div class="item">';
											itemhtml += '			<div class="tit">물 주는 날</div>';
											itemhtml += '			<div class="txt water_day">' + util.dDayHtml(day) + '</div>';
											itemhtml += '		</div>';
											itemhtml += '		<div class="item">';
											itemhtml += '			<div class="tit">물 주는 간격</div>';
											itemhtml += '			<div class="txt water_interval">' + plantListData[i].water_interval + '일 간격</div>';
											itemhtml += '		</div>';
											itemhtml += '		<div class="item">';
											itemhtml += '			<div class="tit">마지막 물 준날</div>';
											itemhtml += '			<div class="txt last_water_day">' + diary_date + ' <br>' + util.dayWeek(diary_date) + '</div>';
											itemhtml += '		</div>';
											itemhtml += '	</div>';
											itemhtml += '	<div class="image-section">';
											itemhtml += '		<div class="plant-img">';
											itemhtml += '			<div class="img"><img src="' + myplant_img + '"></div>';
											itemhtml += '		</div>';
											itemhtml += '		<div class="control-section">';
											itemhtml += '			<div class="btn water">물주기</div>';
											itemhtml += '			<div class="btn diary">다이어리</div>';
											itemhtml += '		</div>';
											itemhtml += '	</div>';
											itemhtml += '</li>';
										};

									} else {

										itemhtml += '<li>';
										itemhtml += '	<div>식물이 없습니다.</div>';
										itemhtml += '</li>';
									}
									
									$('.myplant-list .list ul').html(itemhtml);
									
									// alert 팝업 등록
									$('.myplant-list .list .water').layerPop();
								}
							});

						} else {

							let itemhtml = "";
							itemhtml += '<li>';
							itemhtml += '	<div class="login">로그인 하러 가기</div>';
							itemhtml += '</li>';
							$('.myplant-list .list ul').html(itemhtml);
						}
						// 웹뷰 준비 완료
						app.webViewReady();
					}

					// 설정 화면 이동
					$('.myplant-list .setting').on('click', function() {
						let message = {
							key : 'moveSetting',
							data : {}
						}
						app.reactNativePostMessage(message);
					});

					// 추가 버튼 선택 시
					$('.myplant-list .btn-section').on('click', function() {
						let myplantFormMoveMessage = {
							key : 'myplantForm',
							data : {}
						}
						app.reactNativePostMessage(myplantFormMoveMessage);
					});

					// 상세 화면 이동
					$(document).on('click', '.myplant-list .list .item', function() {
						let message = {
							key : 'moveMyplantDetail',
							data : {
								myplantSeq : $(this).parent('li').data('myplantseq')
							}
						}
						app.reactNativePostMessage(message);
					});
					
					setTimeout(function() {
						// 레이어팝업 등록
						$('.title-section').layerPop();
						
					}, 2000)

					// 물주기 선택 시
					let selectMyplantSeq = 0;
					let selectMyplantState;
					// $(document).on('click', '.myplant-list .list .water', function() {
					// 	let msg = '';
					// 	selectMyplantSeq = $(this).parents('li').data('myplantseq');

					// 	// 물주는 날에 물을 줄 경우
					// 	if ($(this).parents('li').find('.waterday').length > 0) {
					// 		msg = '물을 주시겠습니까?';
					// 		selectMyplantState = 'waterday';

					// 	// 물주는 날 전에 미리 물을 줄 경우
					// 	} else if ($(this).parents('li').find('.inadvance').length > 0) {
					// 		msg = '미리 물을 주시겠습니까?\n이후 물주기가 변경됩니다.';
					// 		selectMyplantState = 'inadvance';
						
					// 	// 물주는 날 후에 늦게 물을 줄 경우
					// 	} else if ($(this).parents('li').find('.warning').length > 0) {
					// 		msg = '물을 주시겠습니까?';
					// 		selectMyplantState = 'warning';
					// 	}

					// 	// 웹 상태일 경우
					// 	if (app.webMode) {
					// 		if (confirm(msg)) {
					// 			watering();
					// 		}
					// 	}
						
					// 	let message = {
					// 		key : 'confirmWatering',
					// 		data : {
					// 			message : msg
					// 		}
					// 	}
					// 	app.reactNativePostMessage(message);
					// });

					// 물주기
					function watering() {
						$.ajax({
							url: '/api/calendar/myplantWatering',
							type: 'get',
							data: {
								myplant_seq: selectMyplantSeq,
								user_seq: app.userData.user_seq,
								state: selectMyplantState
							},
							success: function(response) {
								
								// 식물 리스트 가져오기
								getPlantList();
							}
						});
					}

					// 미루기 선택 시
					$(document).on('click', '.myplant-list .list .procrastina', function() {
						selectMyplantSeq = $(this).parents('li').data('myplantseq');
						let message = {
							key : 'showProcrastina',
							data : {
								myplantSeq : selectMyplantSeq
							}
						}
						app.reactNativePostMessage(message);
					});

					// 로그인 화면 이동
					$(document).on('click', '.myplant-list .login', function() {
						let message = {
							key : 'moveLogin',
							data : {}
						}
						app.reactNativePostMessage(message);
					});
				});

				
			</script>

<?php include "application/views/include/footer.php" ?>