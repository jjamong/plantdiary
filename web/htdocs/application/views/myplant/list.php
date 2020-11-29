<?php include "application/views/include/header.php" ?>

			<!-- //#container.container -->
			<div id="container" class="container myplant-list">
				<div class="header-title-section">
					<div class="title-section" data-target="#alert_layer">
						<h2 class="title">내 식물</h2>
						<div class="setting">
							<div class="img"><img src="/resource/images/setting_icon_off.png" /></div>
						</div>
					</div>
				</div>
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
									response = JSON.parse(response);
									let key = response.key;
									let plantListData = response.data;
									
									if (key == 'success') {
										
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
										
										$('.myplant-list .list ul').html(itemhtml);
									}
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

					// 식물 리스트 선택 시
					$(document).on('click', '.myplant-list .list .li-section', function() {
						let message = {
							key : 'moveMyplantDetail',
							data : {
								myplantSeq : $(this).parent('li').data('myplant_seq')
							}
						}
						app.reactNativePostMessage(message);
					});

					// 돌보기 선택 시
					$(document).on('click', '.myplant-list .list .care', function() {
						
						// 돌보기(다이어리) 데이터 가져오기
						let myplantSeq = $(this).parents('li').data('myplant_seq');
						let diaryDate = util.dateFormat('', new Date()).replace(/-/gi, '');
						getDiary(myplantSeq, diaryDate);
						
						// 돌보기 레이어 팝업 노출
						layer.showLayer('plant_care_layer', 'plant-care');
						$('#plant_care_layer').data('myplant_seq', myplantSeq);
					});
					// 돌보기 레이어 팝업 확인/취소 선택 시
					$(document).on('click', '.plant-care .layer-close', function() {
						
						if ($(this).hasClass('ok')) {
							layer.showLayer('confirm_layer', 'plant-care-confirm', '다이어리를 저장 하시겠습니까?');
						} else {
							// 돌보기 팝업 닫기
							$('#plant_care_layer').data('myplant_seq', '');
							$('#plant_care_layer').data('myplant_diary_seq', '');
							layer.hideLayer('plant_care_layer', 'plant-care');
						}
					});
					// 돌보기 다이어리 날짜 선택 시
					$(document).on('click', '.plant-care-layer .diary-date', function() {
						
						let date = ($('.plant-care-layer .diary-date').text()) ? new Date($('.plant-care-layer .diary-date').text()) : new Date();
						layer.showDetePicker('datepicker_layer', 'plant-care-date-picker', date);
					});
					// 날짜 선택기 확인/취소 선택 시
					$(document).on('click', '.plant-care-date-picker .layer-close', function() {

						layer.hideLayer('datepicker_layer', 'plant-care-date-picker');
						
						if ($(this).hasClass('ok')) {
							let date = layer.selectDetePicker();
							let dateDiff = util.dateCalculate('today', util.dateFormat('noDivision', date));

							// 오늘 날짜보다 큰 날짜일 경우
							if (dateDiff > 0) {
								layer.showLayer('alert_layer', '', '오늘 날짜 보다 클 수 없습니다.');
							} else {
								$('.plant-care-layer .diary-date').text(util.dateFormat('noDivision', date));

								let myplantSeq = $('.plant-care-layer').data('myplant_seq');

								// 돌보기(다이어리) 데이터 가져오기
								getDiary(myplantSeq, date);
							}
						}
					});
					// 돌보기 등록/수정 컨펌창 선택 시
					$(document).on('click', '.plant-care-confirm .layer-close', function() {
						
						layer.hideLayer('confirm_layer', 'plant-care-confirm');
						
						if ($(this).hasClass('ok')) {
							
							let myplantSeq =  $('#plant_care_layer').data('myplant_seq');
							let myplantDiarySeq =  $('#plant_care_layer').data('myplant_diary_seq');
							// 다이어리 등록 시
							if (myplantDiarySeq == '') {
								diaryInsert(myplantSeq, myplantDiarySeq);

							// 다이어리 수정 시
							} else {
								diaryUpdate(myplantSeq, myplantDiarySeq);
							}
						}
					});

					// 이미지 파일 찾기 시 미리보기
					util.imagePreview($('#sys_diary_img1'));
					util.imagePreview($('#sys_diary_img2'));
					util.imagePreview($('#sys_diary_img3'));
					let sysDiaryImg1;
					let sysDiaryImg1DelYN = 'N';
					let sysDiaryImg2;
					let sysDiaryImg2DelYN = 'N';
					let sysDiaryImg3;
					let sysDiaryImg3DelYN = 'N';

					// 이미지 삭제 버튼 선택 시
					$('.image-section .image1 .cancel').on('click', function() {
						$('.image-section .image1 .img img').attr('src', '/resource/images/plant_default_img.jpg');
						$('#sys_diary_img1').val('');
						sysDiaryImg1DelYN = 'Y';
					});
					$('.image-section .image2 .cancel').on('click', function() {
						$('.image-section .image2 .img img').attr('src', '/resource/images/plant_default_img.jpg');
						$('#sys_diary_img2').val('');
						sysDiaryImg2DelYN = 'Y';
					});
					$('.image-section .image3 .cancel').on('click', function() {
						$('.image-section .image3 .img img').attr('src', '/resource/images/plant_default_img.jpg');
						$('#sys_diary_img3').val('');
						sysDiaryImg3DelYN = 'Y';
					});

					// 돌보기(다이어리) 데이터 가져오기
					function getDiary(myplantSeq, diaryDate) {
						
						// 오늘 날짜 다이어트 날짜 설정
						$.ajax({
							url: '/api/diary/select',
							type: 'get',
							data: {
								myplant_seq: myplantSeq,
								diary_date: diaryDate
							},
							success: function(response) {
								response = JSON.parse(response);
								let key = response.key;
								let data = response.data;
								
								if (key == 'success') {
									let diaryRow = data.diaryRow;
									let diaryImages = data.diaryImages;

									// 다이어리가 있을 경우
									if (data.diaryCount > 0) {
										$('#plant_care_layer').data('myplant_diary_seq', diaryRow.myplant_diary_seq);

										$('#plant_care_layer .diary-date').text(util.dateFormat('noDivision', diaryRow.diary_date))
										if (diaryRow.water_yn == 'Y') {
											$('#water_yn').prop('checked', true);
										} else {
											$('#water_yn').prop('checked', false);
										}

										if (diaryRow.medicine_yn == 'Y') {
											$('#medicine_yn').prop('checked', true);
										} else {
											$('#medicine_yn').prop('checked', false);
										}
										
										if (diaryRow.pot_replace_yn == 'Y') {
											$('#pot_replace_yn').prop('checked', true);
										} else {
											$('#pot_replace_yn').prop('checked', false);
										}

										if (diaryRow.soil_condition_yn == '1') {
											$('#soil_condition_yn_1').prop('checked', true);
										} else if (diaryRow.soil_condition_yn == '2') {
											$('#soil_condition_yn_2').prop('checked', true);
										} else if (diaryRow.soil_condition_yn == '3') {
											$('#soil_condition_yn_3').prop('checked', true);
										}
										
										let diaryImagesCount = diaryImages.length;
										for (let i=0; i<3; i++) {
											let diaryImg = '/resource/images/plant_default_img.jpg';
											$('#sys_diary_img' + (i+1)).val('');
											$('.diary-img-section label[for="sys_diary_img' + (i+1) + '"] img').attr('src', diaryImg);
										}
										for (let i=0; i<diaryImagesCount; i++) {
											let diaryImg = '/uploads/user_' + diaryRow.user_seq + '/myplant_' + diaryRow.myplant_seq + '/diary_' + diaryRow.myplant_diary_seq + '/' + diaryImages[i].sys_diary_img;
										
											$('.diary-img-section .image' + (i+1)).data('myplant_diary_img_seq', diaryImages[i].myplant_diary_img_seq);
											$('.diary-img-section label[for="sys_diary_img' + (i+1) + '"] img').attr('src', diaryImg);
											
											if (i==0) { 
												sysDiaryImg1 = diaryImg;
												sysDiaryImg1DelYN = 'N';
											}
											if (i==1) { 
												sysDiaryImg2 = diaryImg;
												sysDiaryImg2DelYN = 'N';
											}
											if (i==2) { 
												sysDiaryImg3 = diaryImg;
												sysDiaryImg3DelYN = 'N';
											}
										}

										$('#diary_content').val(diaryRow.diary_content);
										
									// 다이어리가 없을 경우
									} else {
										$('#plant_care_layer').data('myplant_diary_seq', '');

										$('#water_yn').prop('checked', false);
										$('#medicine_yn').prop('checked', false);
										$('#pot_replace_yn').prop('checked', false);
										$('#soil_condition_yn_2').prop('checked', true);

										for (let i=0; i<3; i++) {
											$('#sys_diary_img' + (i+1)).val('');
											let diaryImg = '/resource/images/plant_default_img.jpg';
											$('.diary-img-section label[for="sys_diary_img' + (i+1) + '"] img').attr('src', diaryImg);
										}
										$('#diary_content').val('');
									}
								}
							}
						});
					}

					// 다이어리 등록
					function diaryInsert(myplantSeq, myplantDiarySeq) {
						let formData;
						formData = new FormData($('#plantCareForm')[0]);
						formData.append('user_seq', app.userData.user_seq);
						formData.append('myplant_seq', myplantSeq);
						formData.append('diary_date', $('.plant-care-layer .diary-date').text().replace(/-/gi, ''));
						formData.append('water_yn', $('#water_yn').is(':checked') ? 'Y' : 'N');
						formData.append('soil_condition_yn', $(":input[name=soil_condition_yn]:checked").val());
						formData.append('medicine_yn', $('#medicine_yn').is(':checked') ? 'Y' : 'N');
						formData.append('pot_replace_yn', $('#pot_replace_yn').is(':checked') ? 'Y' : 'N');
						formData.append('diary_content', $('#diary_content').val());
						
						$.ajax({
							url: '/api/diary/insert',
							type: 'post',
							data: formData,
							processData: false,
							contentType: false,
							success: function(response) {
								response = JSON.parse(response);
								let key = response.key;
								let data = response.data;
								
								if (key == 'success') {
									// 식물 리스트 가져오기
									getPlantList();
									
									// 돌보기 팝업 닫기
									$('#plant_care_layer').data('myplant_seq', '');
									$('#plant_care_layer').data('myplant_diary_seq', '');
									layer.hideLayer('plant_care_layer', 'plant-care');
								}
							}
						});
					};
					
					// 다이어리 수정
					function diaryUpdate(myplantSeq, myplantDiarySeq) {
						let formData;
						formData = new FormData($('#plantCareForm')[0]);
						formData.append('user_seq', app.userData.user_seq);
						formData.append('myplant_seq', myplantSeq);
						formData.append('myplant_diary_seq', myplantDiarySeq);
						formData.append('diary_date', $('.plant-care-layer .diary-date').text().replace(/-/gi, ''));
						formData.append('water_yn', $('#water_yn').is(':checked') ? 'Y' : 'N');
						formData.append('soil_condition_yn', $(":input[name=soil_condition_yn]:checked").val());
						formData.append('medicine_yn', $('#medicine_yn').is(':checked') ? 'Y' : 'N');
						formData.append('pot_replace_yn', $('#pot_replace_yn').is(':checked') ? 'Y' : 'N');
						formData.append('diary_content', $('#diary_content').val());

						formData.append('sys_diary_img1_delyn', sysDiaryImg1DelYN);
						formData.append('sys_diary_img1_seq', $('.diary-img-section .image1').data('myplant_diary_img_seq'));
						formData.append('sys_diary_img2_delyn', sysDiaryImg2DelYN);
						formData.append('sys_diary_img2_seq', $('.diary-img-section .image2').data('myplant_diary_img_seq'));
						formData.append('sys_diary_img3_delyn', sysDiaryImg3DelYN);
						formData.append('sys_diary_img3_seq', $('.diary-img-section .image3').data('myplant_diary_img_seq'));
						
						$.ajax({
							url: '/api/diary/update',
							type: 'post',
							data: formData,
							processData: false,
							contentType: false,
							success: function(response) {
								response = JSON.parse(response);
								let key = response.key;
								let data = response.data;
								
								if (key == 'success') {
									// 식물 리스트 가져오기
									getPlantList();

									// 돌보기 팝업 닫기
									$('#plant_care_layer').data('myplant_seq', '');
									$('#plant_care_layer').data('myplant_diary_seq', '');
									layer.hideLayer('plant_care_layer', 'plant-care');

								} else if (key == 'waterCountFailure') {
									// 돌보기 레이어 팝업 노출 
									layer.showLayer('alert_layer', '', '최소 1개의 다이어리에는 물주기가 ON 되어 있어야 합니다.');
								}
							}
						});
					}

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