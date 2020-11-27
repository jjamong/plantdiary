<?php include "application/views/include/header.php" ?>

			<!-- #container.container -->
			<div id="container" class="container common myplant-detail">
				<div class="contens-section">
					<div class="web header-title-section">
						<div class="text modify">수정</div>
					</div>
					<div class="contents">
						<div class="image-section">
							<div class="img"></div>
						</div>
						<div class="content-section">
							<div class="detail-section">
								<div class="title-section">
									<h2 class="title"></h2>
									<div class="day"></div>
									<!-- <div class="control-section">
										<div class="btn water">물주기</div>
										<div class="btn procrastina">미루기</div>
									</div> -->
								</div>
								<ul>
									<li class="first-grow-date">
										<div class="desc-title">키우기 시작한 날</div>
										<div class="desc"></div>
									</li>
									<li class="water-interval">
										<div class="desc-title">물주기 간격일</div>
										<div class="desc"></div>
									</li>
									<li class="water-day">
										<div class="desc-title">다음 물 줄 날</div>
										<div class="desc"></div>
									</li>
									<li class="last-watering-date">
										<div class="desc-title">마지막 물 준날</div>
										<div class="desc"></div>
									</li>
								</ul>
							</div>
							<div class="diary-section">
								<div class="diary-title-section">
									<div class="diary-title">다이어리</div>
									<div class="diary-all">전체 보기 ></div>
								</div>
								<ul></ul>
							</div>
							<div class="btn-section">
								<div class="modify">수정</div>
								<div class="delete">삭제</div>
							</div>
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

					let myplantSeq;
					let sysMyplantImg;

					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {
						
						// 웹뷰 로드 완료
						if (key === 'webViewLoad') {
							// 회원정보 설정
							app.userData = data.userData;
							myplantSeq = data.myplantSeq;

							// 식물 상세 가져오기
							getPlantDetail();

						// 물주기 컨펌 확인 시
						} else if (key === 'confirmWatering') {
							// 물주기
							watering();

						// 식물 삭제 컨펌 확인 시
						} else if (key === 'confirmMyPlantDelete') {
							del();
						}
					}
					
					// 웹 상태일 경우
					if (app.webMode) {
						// 식물 상세 가져오기
						myplantSeq = 163;
						getPlantDetail();
					}

					// 식물 상세 가져오기
					function getPlantDetail() {
						if (app.userData) {
							$.ajax({
								url: '/api/myplant/select',
								type: 'get',
								data: {
									user_seq: app.userData.user_seq,
									myplant_seq: myplantSeq
								},
								success: function(response) {
									response = JSON.parse(response);
									let key = response.key;
									let mplantRow = response.data.mplantRow;
									let diaryResult = response.data.diaryResult;
									
									if (key == 'success') {
										
										// 내식물 설정
										let myplant_img;
										if (mplantRow.sys_myplant_img) {
											myplant_img = '<?=SITE_URL?>uploads/user_' + mplantRow.user_seq + '/myplant_' + mplantRow.myplant_seq + '/' + mplantRow.sys_myplant_img;
										} else {
											myplant_img = '/resource/images/plant_default_img.jpg';
										}
										let water_day = util.formatDate(mplantRow.water_day, 'noDivision');
										let diary_date = util.formatDate(mplantRow.diary_date, 'noDivision');
										
										$('.image-section .img').html('<img src="' + myplant_img + '">');
										$('.content-section .title').html(mplantRow.myplant_name);
										$('.content-section .first-grow-date .desc').html(util.formatDate(mplantRow.first_grow_date, 'noDivision'));
										$('.content-section .water-interval .desc').html(mplantRow.water_interval + '일');
										$('.content-section .water-day .desc').html(water_day + ' ' + util.dayWeek(water_day));
										$('.content-section .last-watering-date .desc').html(diary_date + ' ' + util.dayWeek(diary_date));

										// 다이어리 최근 데이터 설정
										let diaryhtml = "";
										let diaryCount = diaryResult.length;
										if (diaryCount > 0) {
											for (let i=0; i< diaryCount; i++) {
												console.log(diaryResult[i])

												let water_yn = (diaryResult[i].water_yn == 'Y') ? '물 줌' : '물 안줌';
												let soil_condition_yn;
												if (diaryResult[i].soil_condition_yn == '1') {
													soil_condition_yn = '건조함';
												} else if (diaryResult[i].soil_condition_yn == '2') {
													soil_condition_yn = '보통';
												} else if (diaryResult[i].soil_condition_yn == '3') {
													soil_condition_yn = '과습';
												}

												diaryhtml += '<li data-myplantdiaryseq="' + diaryResult[i].myplant_diary_seq + '">';
												diaryhtml += '	<div class="diary-date-section">';
												diaryhtml += '		<div class="diary-date">' + util.formatDate(diaryResult[i].diary_date, 'noDivision') + '</div>';
												diaryhtml += '	</div>';
												diaryhtml += '	<div class="diary-detail-section">';
												diaryhtml += '		<div class="left">';
												diaryhtml += '			<div class="desc-title">물주기:</div>';
												diaryhtml += '			<div class="desc">' + water_yn + '</div>';
												diaryhtml += '		</div>';
												diaryhtml += '		<div class="right">';
												diaryhtml += '			<div class="desc-title">흙상태:</div>';
												diaryhtml += '			<div class="desc">' + soil_condition_yn + '</div>';
												diaryhtml += '		</div>';
												diaryhtml += '	</div>';
												diaryhtml += '</li>';
											};

											$('.diary-section ul').html(diaryhtml);
										}
									}
						
									// 웹뷰 준비 완료
									app.webViewReady();
								}
							});
						}
					}

					// 물주기 선택 시
					let selectMyplantSeq = 0;
					let selectMyplantState;
					$('.myplant-detail .water').on('click', function() {
						let msg = '';

						// 물주는 날에 물을 줄 경우
						if ($(this).parents('.title-section').find('.waterday').length > 0) {
							msg = '물을 주시겠습니까?';
							selectMyplantState = 'waterday';

						// 물주는 날 전에 미리 물을 줄 경우
						} else if ($(this).parents('.title-section').find('.inadvance').length > 0) {
							msg = '미리 물을 주시겠습니까?\n이후 물주기가 변경됩니다.';
							selectMyplantState = 'inadvance';
						
						// 물주는 날 후에 늦게 물을 줄 경우
						} else if ($(this).parents('.title-section').find('.warning').length > 0) {
							msg = '물을 주시겠습니까?';
							selectMyplantState = 'warning';
						}

						// 웹 상태일 경우
						if (app.webMode) {
							if (confirm(msg)) {
								watering();
							}
						}
						
						let message = {
							key : 'confirmWatering',
							data : {
								message : msg
							}
						}
						app.reactNativePostMessage(message);
					});

					// 물주기
					function watering() {
						$.ajax({
							url: '/api/calendar/myplantWatering',
							type: 'get',
							data: {
								myplant_seq: myplantSeq,
								user_seq: app.userData.user_seq,
								state: selectMyplantState
							},
							success: function(response) {
								
								// 식물 상세 가져오기
								getPlantDetail();
							}
						});
					}

					// 미루기 선택 시
					$('.myplant-detail .procrastina').on('click', function() {
						let message = {
							key : 'showProcrastina',
							data : {
								myplantSeq : myplantSeq
							}
						}
						app.reactNativePostMessage(message);
					});

					// 수정
					$('.control-section .modify').on('click', function() {
						let message = {
							key : 'moveMyplantForm',
							data : {
								myplantSeq : myplantSeq
							}
						}
						app.reactNativePostMessage(message);
					});

					// 삭제
					$('.control-section .delete').on('click', function() {
						let msg = '삭제 하시겠습니까?';

						// 웹 상태일 경우
						if (app.webMode) {
							if (confirm(msg)) {
								del();
							}
						}
						
						let message = {
							key : 'confirmMyPlantDelete',
							data : {
								message : msg
							}
						}
						app.reactNativePostMessage(message);
					});

					// 삭제
					function del() {
						$.ajax({
							url: '/api/myplant/delete',
							type: 'post',
							data: {
								user_seq: app.userData.user_seq,
								sys_myplant_img: sysMyplantImg,
								myplant_seq: myplantSeq
							},
							success: function(response) {

								let message = {
									key : 'moveMyplantList',
									data : {}
								}
								app.reactNativePostMessage(message);
							}
						});
					}
				});
			</script> 

<?php include "application/views/include/footer.php" ?>