<?php include "application/views/include/header.php" ?>

			<!-- #container.container -->
			<div id="container" class="container common diary-form">
				<div class="contens-section">
					<div class="web header-title-section">
						<div class="text insert">완료(등록)</div>
						<div class="text update">완료(수정)</div>
					</div>
					<div class="contents">
						<form name="myplantDiaryForm" id="myplantDiaryForm" method="post" enctype="multipart/form-data" action="/api/myplant/insert">
							
							<div class="form-section">
								<div class="diary-form-section">
									<div class="item diary-date-section">
										<div class="diary-date"><?= date("Y-m-d") ?></div>
									</div>
									<div class="item water-yn-section">
										<label for="water_yn" class="label">물주기</label>
										<div class="toggle toggle-water">
											<input type="checkbox" name="water_yn" id="water_yn" class="toggle-checkbox" value='Y'>
											<label class="toggle-btn" for="water_yn"><span class="toggle-feature"></span></label>
										</div>
									</div>
									<div class="item soil-condition-yn-section">
										<label for="soil_condition_yn" class="label">흙상태</label>
										<div class="radio-section">
											<div class="input-radio">
												<input type="radio" name="soil_condition_yn" id="soil_condition_yn_1" value="1">
												<label for="soil_condition_yn_1">건조함</label>
											</div>
											<div class="input-radio">
												<input type="radio" name="soil_condition_yn" id="soil_condition_yn_2" checked value="2">
												<label for="soil_condition_yn_2">보통</label>
											</div>
											<div class="input-radio">
												<input type="radio" name="soil_condition_yn" id="soil_condition_yn_3" value="3">
												<label for="soil_condition_yn_3">과습</label>
											</div>
										</div>
									</div>
									<div class="item medicine-yn-section">
										<label for="medicine_yn" class="label">약주기</label>
										<div class="toggle toggle-water">
											<input type="checkbox" name="medicine_yn" id="medicine_yn" class="toggle-checkbox" value='Y'>
											<label class="toggle-btn" for="medicine_yn"><span class="toggle-feature"></span></label>
										</div>
									</div>
									<div class="item pot-replace-yn-section">
										<label for="pot_replace_yn" class="label">분갈이</label>
										<div class="toggle toggle-water">
											<input type="checkbox" name="pot_replace_yn" id="pot_replace_yn" class="toggle-checkbox" value='Y'>
											<label class="toggle-btn" for="pot_replace_yn"><span class="toggle-feature"></span></label>
										</div>
									</div>
									<div class="item diary-img-section">
										<div class="label">이미지</div>
										<div class="image-section">
											<div class="image image1" data-myplant_diary_img_seq="">
												<label for="sys_diary_img1">
													<div class="img"><img src="/resource/images/plant_default_img.jpg" /></div>
												</label>
												<div class="cancel"><img src="/resource/images/cancel_icon.png"></div>
												<div class="input sys_diary_img1"><input type="file" name="sys_diary_img-1" id="sys_diary_img1" placeholder="대표 이미지를 입력해주세요." /></div>
												<div class="msg"></div>
											</div>
											<div class="image image2" data-myplant_diary_img_seq="">
												<label for="sys_diary_img2">
													<div class="img"><img src="/resource/images/plant_default_img.jpg" /></div>
												</label>
												<div class="cancel"><img src="/resource/images/cancel_icon.png"></div>
												<div class="input sys_diary_img2"><input type="file" name="sys_diary_img-2" id="sys_diary_img2" placeholder="대표 이미지를 입력해주세요." /></div>
												<div class="msg"></div>
											</div>
											<div class="image image3" data-myplant_diary_img_seq="">
												<label for="sys_diary_img3">
													<div class="img"><img src="/resource/images/plant_default_img.jpg" /></div>
												</label>					
												<div class="cancel"><img src="/resource/images/cancel_icon.png"></div>				
												<div class="input sys_diary_img3"><input type="file" name="sys_diary_img-3" id="sys_diary_img3" placeholder="대표 이미지를 입력해주세요." /></div>
												<div class="msg"></div>
											</div>
										</div>
									</div>
									<div class="item diary-content-section">
										<label for="diary_content" class="label">메모</label>
										<div class="textarea textarea-diary-content">
											<textarea name="diary_content" id="diary_content" placeholder="메모를 입력해주세요."></textarea>
										</div>
										<div class="msg"></div>
									</div>
								</div>
							</div>
						</form>
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
					let myplantDiarySeq;

					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {
						// 웹뷰 로드 완료
						if (key === 'webViewLoad') {
							// 회원정보 설정
							app.userData = data.userData;
							myplantSeq = data.myplantSeq;
							myplantDiarySeq = data.myplantDiarySeq;

							// 등록일 경우
							if (!myplantDiarySeq) {

								// 웹뷰 준비 완료
								app.webViewReady();
								
							// 수정일 경우
							} else {
								$('.diary-form-section .diary-date').css('background', 'none');
								// 다이어리 상세 가져오기
								getDiaryDetail();
							}

						// 식물 완료(등록) 선택 시
						} else if (key === 'insert') {
							formCheckInsert();

						// 식물 완료(수정) 선택 시
						} else if (key === 'update') {
							formCheckUpdate();

						}
					}

					// 웹 상태일 경우
					if (app.webMode) {
						myplantSeq = 172;
						//myplantDiarySeq = 207;

						// 등록일 경우
						if (!myplantDiarySeq) {
							$('.header-title-section .update').css('display', 'none');

						// 수정일 경우
						} else {
							$('.diary-form-section .diary-date').css('background', 'none');
							$('.header-title-section .insert').css('display', 'none');
							// 다이어리 상세 가져오기
							getDiaryDetail();
						}
					}

					// 다이어리 상세 가져오기
					function getDiaryDetail() {
						if (app.userData) {
							$.ajax({
								url: '/api/diary/select',
								type: 'get',
								data: {
									user_seq: app.userData.user_seq,
									myplant_diary_seq: myplantDiarySeq
								},
								success: function(response) {
									response = JSON.parse(response);
									let key = response.key;
									let diaryRow = response.data.diaryRow;
									let diaryImageResult = response.data.diaryImageResult;
									
									if (key == 'success') {
										$('.diary-form-section .diary-date').text(util.dateFormat('noDivision', diaryRow.diary_date))

										if (diaryRow.water_yn == 'Y') {
											$('.diary-form-section #water_yn').prop('checked', true);
										} else {
											$('.diary-form-section #water_yn').prop('checked', false);
										}

										if (diaryRow.medicine_yn == 'Y') {
											$('.diary-form-section  #medicine_yn').prop('checked', true);
										} else {
											$('.diary-form-section #medicine_yn').prop('checked', false);
										}
										
										if (diaryRow.pot_replace_yn == 'Y') {
											$('.diary-form-section #pot_replace_yn').prop('checked', true);
										} else {
											$('.diary-form-section #pot_replace_yn').prop('checked', false);
										}

										if (diaryRow.soil_condition_yn == '1') {
											$('.diary-form-section #soil_condition_yn_1').prop('checked', true);
										} else if (diaryRow.soil_condition_yn == '2') {
											$('.diary-form-section #soil_condition_yn_2').prop('checked', true);
										} else if (diaryRow.soil_condition_yn == '3') {
											$('.diary-form-section #soil_condition_yn_3').prop('checked', true);
										}
										
										let diaryImagesCount = diaryImageResult.length;
										for (let i=0; i<3; i++) {
											let diaryImg = '/resource/images/plant_default_img.jpg';
											$('.diary-form-section #sys_diary_img' + (i+1)).val('');
											$('.diary-form-section .diary-img-section label[for="sys_diary_img' + (i+1) + '"] img').attr('src', diaryImg);
										}
										for (let i=0; i<diaryImagesCount; i++) {
											let diaryImg = '/uploads/user_' + diaryRow.user_seq + '/myplant_' + diaryRow.myplant_seq + '/diary_' + diaryRow.myplant_diary_seq + '/' + diaryImageResult[i].sys_diary_img;
											$('.diary-form-section .diary-img-section .image' + (i+1)).data('myplant_diary_img_seq', diaryImageResult[i].myplant_diary_img_seq);
											$('.diary-form-section .diary-img-section label[for="sys_diary_img' + (i+1) + '"] img').attr('src', diaryImg);
											
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

										$('.diary-form-section #diary_content').val(diaryRow.diary_content);
										
										// 웹뷰 준비 완료
										app.webViewReady();
									}
								}
							});
						}
					}

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
					$('.diary-form-section .image-section .image1 .cancel').on('click', function() {
						$('.image-section .image1 .img img').attr('src', '/resource/images/plant_default_img.jpg');
						$('#sys_diary_img1').val('');
						sysDiaryImg1DelYN = 'Y';
					});
					$('.diary-form-section .image-section .image2 .cancel').on('click', function() {
						$('.image-section .image2 .img img').attr('src', '/resource/images/plant_default_img.jpg');
						$('#sys_diary_img2').val('');
						sysDiaryImg2DelYN = 'Y';
					});
					$('.diary-form-section .image-section .image3 .cancel').on('click', function() {
						$('.image-section .image3 .img img').attr('src', '/resource/images/plant_default_img.jpg');
						$('#sys_diary_img3').val('');
						sysDiaryImg3DelYN = 'Y';
					});

					// 다이어리 날짜 선택 시
					$('.diary-form-section .diary-date').on('click', function() {
						// 등록일 경우
						if (!myplantDiarySeq) {
							let date = ($('.diary-form-section .diary-date').text()) ? new Date($('.diary-form-section .diary-date').text()) : new Date();
							layer.showDetePicker('datepicker_layer', 'diary-date', date);
						}
					});
					// 다이어리 날짜 레이어 팝업 선택 시
					$(document).on('click', '.diary-date .layer-close', function() {
						
						if ($(this).hasClass('ok')) {
							let date = layer.selectDetePicker();
							let dateDiff = util.dateCalculate('today', util.dateFormat('noDivision', date));

							// 오늘 날짜보다 큰 날짜일 경우
							if (dateDiff > 0) {
								layer.showLayer('alert_layer', '', '오늘 날짜 보다 클 수 없습니다.');
							} else {
								$('.diary-form-section .diary-date').text(util.dateFormat('noDivision', date));
								layer.hideLayer('datepicker_layer', 'diary-date');
							}
						} else {
							layer.hideLayer('datepicker_layer', 'diary-date');
						}
					});

					// 완료 선택 시 (WEB)
					$('.web .insert, .web .update').on('click', function() {
						if ($(this).hasClass('insert')) {
							formCheckInsert();
						} else {
							formCheckUpdate();
						}
					});
					
					// 등록 폼 체크
					function formCheckInsert() {
						$.ajax({
							url: '/api/diary/duplicationCheck',
							type: 'get',
							data: {
								user_seq: app.userData.user_seq,
								myplant_seq: myplantSeq,
								diary_date : $('.diary-form-section .diary-date').text().replace(/-/gi, '')
							},
							success: function(response) {
								response = JSON.parse(response);
								let key = response.key;
								let data = response.data;
								
								if (key == 'success') {
									layer.showLayer('confirm_layer', 'insert-confirm', '등록 하시겠습니까?');

								} else if (key == 'countFailure') {
									layer.showLayer('alert_layer', '', '이미 같은 날에 다이어리가 등록되어 있습니다.');
								}
							}
						});
					}

					// 등록 컨펌창 선택 시
					$(document).on('click', '.insert-confirm .layer-close', function() {
						
						if ($(this).hasClass('ok')) {
							insert();
						}
						layer.hideLayer('confirm_layer', 'insert-confirm');
					});
					
					// 등록
					function insert() {
						let formData;
						formData = new FormData($('#myplantDiaryForm')[0]);
						formData.append('user_seq', app.userData.user_seq);
						formData.append('myplant_seq', myplantSeq);
						formData.append('diary_date', $('.diary-form-section .diary-date').text().replace(/-/gi, ''));
						formData.append('water_yn', $('.diary-form-section #water_yn').is(':checked') ? 'Y' : 'N');
						formData.append('soil_condition_yn', $(".diary-form-section :input[name=soil_condition_yn]:checked").val());
						formData.append('medicine_yn', $('.diary-form-section #medicine_yn').is(':checked') ? 'Y' : 'N');
						formData.append('pot_replace_yn', $('.diary-form-section #pot_replace_yn').is(':checked') ? 'Y' : 'N');
						formData.append('diary_content', $('.diary-form-section #diary_content').val());
						
						$.ajax({
							url: '/api/diary/insert',
							type: 'post',
							data: formData,
							processData: false, // 필수
							contentType: false, // 필수
							success: function(response) {
								response = JSON.parse(response);
								let key = response.key;
								let data = response.data;

								if (key == 'success') {
									let message = {
										key : 'insertSuccess',
										data : {
											myplantSeq : myplantSeq,
											notificationData : data.notificationData
										}
									}

									// 물주기 Y로 선택하여 알림등록일 경우
									if (data.notificationData) {
										message.data.notificationData = {
											myplantSeq : data.notificationData.myplantSeq,
											myplantName : data.notificationData.myplantName,
											waterDay : util.dateFormat('noDivision', data.notificationData.waterDay),
										}
									}
									app.reactNativePostMessage(message);
								}
							}
						});
					};

					// 수정 폼 체크
					function formCheckUpdate() {
						layer.showLayer('confirm_layer', 'update-confirm', '수정 하시겠습니까?');
					}
					
					// 수정 컨펌창 선택 시
					$(document).on('click', '.update-confirm .layer-close', function() {
						
						if ($(this).hasClass('ok')) {
							update();
						}
						layer.hideLayer('confirm_layer', 'update-confirm');
					});

					// 수정
					function update() {
						let formData;
						formData = new FormData($('#myplantDiaryForm')[0]);
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

									// 알림 설정
									let message = {
										key : 'updateSuccess',
										data : {
											myplantSeq : myplantSeq,
											notificationData : {
												myplantSeq : data.notificationData.myplantSeq,
												myplantName : data.notificationData.myplantName,
												waterDay : util.dateFormat('noDivision', data.notificationData.waterDay),
											}
										}
									}
									app.reactNativePostMessage(message);

								} else if (key == 'waterCountFailure') {
									layer.showLayer('alert_layer', '', '최소 1개의 다이어리에는 물주기가 ON 되어 있어야 합니다.');
								}
							}
						});
					}
				});

			</script> 

<?php include "application/views/include/footer.php" ?>