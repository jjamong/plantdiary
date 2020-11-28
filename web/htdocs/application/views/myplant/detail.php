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
									<div class="care">돌보기</div>
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
					let layer = $Layer;
					app.init();
					app.webViewMessage(webViewMessage);

					let myplantSeq;

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
						myplantSeq = 167;
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
									let myplantRow = response.data.myplantRow;
									let diaryResult = response.data.diaryResult;
									
									if (key == 'success') {
										
										// 내식물 설정
										let myplant_img;
										if (myplantRow.sys_myplant_img) {
											myplant_img = '/uploads/user_' + myplantRow.user_seq + '/myplant_' + myplantRow.myplant_seq + '/' + myplantRow.sys_myplant_img;
										} else {
											myplant_img = '/resource/images/plant_default_img.jpg';
										}
										let water_day = util.dateFormat('noDivision', myplantRow.water_day);
										let diary_date = util.dateFormat('noDivision', myplantRow.diary_date);
										let day = util.dateCalculate('today', water_day);
										let thday = (util.dateCalculate('today', util.dateFormat('noDivision', myplantRow.first_grow_date)) * -1) + 1;
										
										$('.image-section .img').html('<img src="' + myplant_img + '">');
										$('.content-section .title').html(myplantRow.myplant_name);
										$('.content-section .first-grow-date .desc').html(util.dateFormat('noDivision', myplantRow.first_grow_date) + ' ' + '(' + thday + '일째)');
										$('.content-section .water-interval .desc').html(myplantRow.water_interval + '일');
										$('.content-section .water-day').addClass(util.waterDayCheck(day));
										$('.content-section .water-day .desc').html(water_day + ' ' + util.dateFormat('dayWeek', water_day) + ' (' + util.waterDayHtml(day) + ')');
										$('.content-section .last-watering-date .desc').html(diary_date + ' ' + util.dateFormat('dayWeek', diary_date));

										// 다이어리 최근 데이터 설정
										let diaryhtml = "";
										let diaryCount = diaryResult.length;
										if (diaryCount > 0) {
											for (let i=0; i< diaryCount; i++) {

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
												diaryhtml += '		<div class="diary-date">' + util.dateFormat('noDivision', diaryResult[i].diary_date) + '</div>';
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
												diaryhtml += '	<div class="diary-content-section">';
												diaryhtml += '		<div class="content">' + diaryResult[i].diary_content + '</div>';
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

					// 다이어리 전체 보기 선택 시
					$('.diary-section .diary-all').on('click', function() {
						let message = {
							key : 'moveDiaryList',
							data : {}
						}
						app.reactNativePostMessage(message);
					});

					// 내식물 삭제
					$('.btn-section .delete').on('click', function() {
						layer.showLayer('confirm_layer', 'myplant-delete-confirm', '삭제 하시겠습니까?');
					});
					// 내식물 삭제 레이어 팝업 확인/취소 선택 시
					$(document).on('click', '.myplant-delete-confirm .layer-close', function() {
						if ($(this).hasClass('ok')) {
							del();
						}
						
						layer.hideLayer('confirm_layer', 'myplant-delete-confirm');
					});
					// 삭제
					function del() {
						$.ajax({
							url: '/api/myplant/delete',
							type: 'post',
							data: {
								user_seq: app.userData.user_seq,
								myplant_seq: myplantSeq
							},
							success: function(response) {
								response = JSON.parse(response);
								let key = response.key;
								
								if (key == 'success') {
									let message = {
										key : 'moveMyplantList',
										data : {}
									}
									app.reactNativePostMessage(message);
								}
							}
						});
					}
				});
			</script> 

<?php include "application/views/include/footer.php" ?>