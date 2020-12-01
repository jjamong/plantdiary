<?php include "application/views/include/header.php" ?>

			<!-- //#container.container -->
			<div id="container" class="container diary-list">
				<div class="contens-section">
					<div class="contents">
						<div class="list">
							<ul></ul>
						</div>
						<div class="btn-section">
							<div class="plus"><img src="/resource/images/plus_icon.png"/></div>
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

					layer.init();
					
					let myplantSeq;

					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {
						
						// 웹뷰 로드 완료
						if (key === 'webViewLoad') {
							// 회원정보 설정
							app.userData = data.userData;
							myplantSeq = data.myplantSeq;

							// 추가 버튼 보이기
							if (app.userData) $('.diary-list .btn-section').css('display', 'block');

							// 다이어리 리스트 가져오기
							getDiaryList();
						}
					}
					
					// 웹 상태일 경우
					if (app.webMode) {
						myplantSeq = 169;

						// 추가 버튼 보이기
						if (app.userData) $('.diary-list .btn-section').css('display', 'block');

						// 다이어리 리스트 가져오기
						getDiaryList();
					}

					// 다이어리 리스트 가져오기
					function getDiaryList() {

						if (app.userData) {
							$.ajax({
								url: '/api/diary/list',
								type: 'get',
								data: {
									user_seq: app.userData.user_seq,
									myplant_seq: myplantSeq
								},
								success: function(response) {
									response = JSON.parse(response);
									let key = response.key;
									let diaryResult = response.data.diaryResult;
									
									if (key == 'success') {

										console.log(diaryResult)

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

												diaryhtml += '<li data-myplant_diary_seq="' + diaryResult[i].myplant_diary_seq + '">';
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
											
										} else {

											diaryhtml += '<li>';
											diaryhtml += '	<div class="nodata">식물이 없습니다.</div>';
											diaryhtml += '</li>';
										}

										$('.diary-list ul').html(diaryhtml);

										/*
										// 식물 리스트 설정
										let itemhtml = "";
										let dataCount = plantListData.length;

										// 내식물 데이터가 있을 경우
										if (dataCount > 0) {
											for (let i=0; i< dataCount; i++) {

												let diary_date = util.dateFormat('noDivision', plantListData[i].diary_date);
												let myplant_img;
												if (plantListData[i].sys_myplant_img) {
													myplant_img = '/uploads/user_' + plantListData[i].user_seq + '/myplant_' + plantListData[i].myplant_seq + '/' + plantListData[i].sys_myplant_img;
												} else {
													myplant_img = '/resource/images/plant_default_img.jpg';
												}

												let day = util.dateCalculate('today', util.dateFormat('noDivision', plantListData[i].water_day));

												itemhtml += '<li class="li" data-myplant_seq="' + plantListData[i].myplant_seq + '">';
												itemhtml +=	'	<div class="li-section">';
												itemhtml += '		<div class="content-section">';
												itemhtml += '			<div class="item title">';
												itemhtml += '				<div class="tit">' + plantListData[i].myplant_name + '</div>';
												itemhtml += '			</div>';
												itemhtml += '			<div class="item water-day ' + util.waterDayCheck(day) + '">';
												itemhtml += '				<div class="tit">물 주는 날</div>';
												itemhtml += '				<div class="txt">' + util.waterDayHtml(day) + ' (' + util.dateFormat('noDivision', plantListData[i].water_day) + ')</div>';
												itemhtml += '			</div>';
												itemhtml += '			<div class="item water-interval">';
												itemhtml += '				<div class="tit">물 주는 간격</div>';
												itemhtml += '				<div class="txt">' + plantListData[i].water_interval + '일 간격</div>';
												itemhtml += '			</div>';
												itemhtml += '			<div class="item last-water-day">';
												itemhtml += '				<div class="tit">마지막 물 준날</div>';
												itemhtml += '				<div class="txt">' + diary_date + ' ' + util.dateFormat('dayWeek', diary_date) + '</div>';
												itemhtml += '			</div>';
												itemhtml += '		</div>';
												itemhtml += '		<div class="image-section">';
												itemhtml += '			<div class="plant-img">';
												itemhtml += '				<div class="img"><img src="' + myplant_img + '"></div>';
												itemhtml += '			</div>';
												itemhtml += '		</div>';
												itemhtml +=	' 	</div>';
												itemhtml += '	<div class="control-section">';
												itemhtml += '		<div class="btn care">돌보기</div>';
												itemhtml += '		<div class="btn diary">다이어리</div>';
												itemhtml += '	</div>';
												itemhtml += '</li>';
											};

										} else {

											itemhtml += '<li>';
											itemhtml += '	<div class="nodata">식물이 없습니다.</div>';
											itemhtml += '</li>';
										}
										
										$('.diary-list .list ul').html(itemhtml);
										*/
									}
								}
							});

						} else {

							let itemhtml = "";
							itemhtml += '<li>';
							itemhtml += '	<div class="login">로그인 하러 가기</div>';
							itemhtml += '</li>';
							$('.diary-list .list ul').html(itemhtml);
						}
						// 웹뷰 준비 완료
						app.webViewReady();
					}

					// 추가 버튼 선택 시
					// $('.myplant-list .btn-section').on('click', function() {
					// 	let myplantFormMoveMessage = {
					// 		key : 'myplantForm',
					// 		data : {}
					// 	}
					// 	app.reactNativePostMessage(myplantFormMoveMessage);
					// });

					// 다이어리 리스트 선택 시
					$(document).on('click', '.diary-list .list li', function() {
						let message = {
							key : 'moveMyplantDiaryDetail',
							data : {
								myplantDiarySeq : $(this).data('myplant_diary_seq')
							}
						}
						app.reactNativePostMessage(message);
					});
				});

				
			</script>

<?php include "application/views/include/footer.php" ?>