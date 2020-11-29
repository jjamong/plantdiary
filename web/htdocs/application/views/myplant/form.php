<?php include "application/views/include/header.php" ?>

			<!-- #container.container -->
			<div id="container" class="container common myplant-form">
				<div class="contens-section">
					<div class="web header-title-section">
						<div class="text insert">완료(등록)</div>
						<div class="text update">완료(수정)</div>
					</div>
					<div class="contents">
						<form name="myplantForm" id="myplantForm" method="post" enctype="multipart/form-data" action="/api/myplant/insert">
							<input type="hidden" name="user_seq" value="1" />
							
							<div class="form-section">

								<div class="plant-form-section">
									<div class="item myplant-img-section">
										<label class="image-view" for="sys_myplant_img">
											<div class="img default"><img src="/resource/images/plant_default_img.jpg" /></div>
											<div class="camera"><img src="/resource/images/camera_icon.png" /></div>
										</label>
										<div class="cancel"><img src="/resource/images/cancel_icon.png" /></div>
										<div class="input sys_myplant_img"><input type="file" name="sys_myplant_img" id="sys_myplant_img" /></div>
									</div>

									<div class="item name-section">
										<label for="myplant_name">이름</label>
										<div class="input name"><input type="text" name="myplant_name" id="myplant_name" placeholder="이름을 입력해주세요."/></div>
										<div class="msg"></div>
									</div>
									
									<div class="item first-grow-date-section">
										<label for="first_grow_date">키우기 시작한 날</label>
										<div class="input date first-grow-date"><input type="text" name="first_grow_date" id="first_grow_date" placeholder="키우기 시작한 날을 선택해주세요." readonly /></div>
										<div class="msg"></div>
									</div>

									<div class="item water-interval-section">
										<label for="water_interval">물주기 간격일</label> 
										<div class="input water-interval" data-target="#water_interval_layer"><input type="text" name="water_interval" id="water_interval" placeholder="물주기 간격을 선택해주세요." readonly/></div>
										<div class="msg"></div>
									</div>

									<div class="item water-day-section">
										<label for="water_day">다음 물 줄 날</label>
										<div class="input date water-day"><input type="text" name="water_day" id="water_day" placeholder="다음 물 줄 날을 선택해주세요." readonly /></div>
										<div class="msg"></div>
									</div>
								</div>

								<div class="diary-form-section">
									<div class="item diary-date-section">
										<div class="diary-date"><?= date("Y-m-d") ?></div>
									</div>
									<div class="item water-yn-section">
										<label for="water_yn" class="label">물주기</label>
										<div class="toggle toggle-water">
											<input type="checkbox" name="water_yn" id="water_yn" class="toggle-checkbox" value='Y' checked onclick="return false;">
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

					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {
						// 웹뷰 로드 완료
						if (key === 'webViewLoad') {
							// 회원정보 설정
							app.userData = data.userData;
							myplantSeq = data.myplantSeq;

							// 등록일 경우
							if (!myplantSeq) {

								// 웹뷰 준비 완료
								app.webViewReady();
								
							// 수정일 경우
							} else {
								// 식물 상세 가져오기
								getPlantDetail();
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
						myplantSeq = 169;

						// 등록일 경우
						if (!myplantSeq) {
							$('.header-title-section .update').css('display', 'none');

						// 수정일 경우
						} else {
							$('.header-title-section .insert').css('display', 'none');
							// 식물 상세 가져오기
							getPlantDetail();
						}
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
									
									if (key == 'success') {
										$('.diary-form-section').css('display', 'none');

										let myplant_img;
										if (myplantRow.sys_myplant_img) {
											myplant_img = '/uploads/user_' + myplantRow.user_seq + '/myplant_' + myplantRow.myplant_seq + '/' + myplantRow.sys_myplant_img;
											sysMyplantImgDelCheck = false;
										} else {
											myplant_img = '/resource/images/plant_default_img.jpg';
										}
										$('.image-view .img img').attr('src', myplant_img);
										$('#myplant_name').val(myplantRow.myplant_name);
										$('#first_grow_date').val(util.dateFormat('noDivision', myplantRow.first_grow_date));
										$('#water_interval').val(myplantRow.water_interval + '일');
										$('#water_day').val(util.dateFormat('noDivision', myplantRow.water_day));
										
										// 웹뷰 준비 완료
										app.webViewReady();
									}
								}
							});
						}
					}

					// 이미지 파일 찾기 시 미리보기
					util.imagePreview($('#sys_myplant_img'));
					util.imagePreview($('#sys_diary_img1'));
					util.imagePreview($('#sys_diary_img2'));
					util.imagePreview($('#sys_diary_img3'));
					let sysMyplantImg;
					let sysMyplantImgDelYN = 'N';
					let sysDiaryImg1;
					let sysDiaryImg1DelYN = 'N';
					let sysDiaryImg2;
					let sysDiaryImg2DelYN = 'N';
					let sysDiaryImg3;
					let sysDiaryImg3DelYN = 'N';
					
					// 이미지 삭제 버튼 선택 시
					$('.myplant-img-section .cancel').on('click', function() {
						$('.myplant-img-section .img img').attr('src', '/resource/images/plant_default_img.jpg');
						$('#sys_myplant_img').val('');
						sysMyplantImgDelYN = 'Y';
					});
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

					// 키우기 시작한 날 선택 시
					$('#first_grow_date').on('click', function() {
						let date = ($('#first_grow_date').val()) ? new Date($('#first_grow_date').val()) : new Date();
						layer.showDetePicker('datepicker_layer', 'first-grow-date', date);
					});
					// 키우기 시작한 날 선택 레이어 팝업 선택 시
					$(document).on('click', '.first-grow-date .layer-close', function() {

						if ($(this).hasClass('ok')) {
							let date = layer.selectDetePicker();
							let dateDiff = util.dateCalculate('today', util.dateFormat('noDivision', date));

							// 오늘 날짜보다 큰 날짜일 경우
							if (dateDiff > 0) {
								layer.showLayer('alert_layer', '', '오늘 날짜 보다 클 수 없습니다.');
							} else {
								$('#first_grow_date').val(util.dateFormat('noDivision', date));
								layer.hideLayer('datepicker_layer', 'first-grow-date');
							}
						} else {
							layer.hideLayer('datepicker_layer', 'first-grow-date');
						}
					});

					// 물주기 간격일 선택 시
					$('.water-interval-section .water-interval').on('click', function() {
						let html = '';
						for (let i=0; i<90; i++) {
							let item = (i+1) + '일';
							if ($('#water_interval').val() == item) {
								html += '<li data-value="' + (i+1) + '" class="selected">' + item + '</li>';
							} else {
								html += '<li data-value="' + (i+1) + '">' + item + '</li>';
							}
						}
						$('.selectbox-layer .list').html(html);
						
						layer.showLayer('selectbox_layer', 'water-interval')
					});
					// 물주기 간격일 레이어 팝업 선택 시
					$(document).on('click', '.water-interval .layer-close', function() {

						layer.hideLayer('selectbox_layer', 'water-interval');
						
						if ($(this).hasClass('ok')) {
							let select = layer.selectSelectBox();
							$('#water_interval').val(select);
						}
					});

					// 다음 물 줄 날 선택 시
					$('#water_day').on('click', function() {
						let date = ($('#water_day').val()) ? new Date($('#water_day').val()) : new Date();
						layer.showDetePicker('datepicker_layer', 'water-day', date);
					});
					// 다음 물 줄 날 레이어 팝업 선택 시
					$(document).on('click', '.water-day .layer-close', function() {
						
						if ($(this).hasClass('ok')) {
							let date = layer.selectDetePicker();
							let dateDiff = util.dateCalculate('today', util.dateFormat('noDivision', date));

							// 오늘 날짜보다 작은 날짜일 경우
							if (dateDiff < 0) {
								layer.showLayer('alert_layer', '', '오늘 날짜 보다 작을 수 없습니다.');
							} else {
								$('#water_day').val(util.dateFormat('noDivision', date));
								layer.hideLayer('datepicker_layer', 'water-day');
							}
						} else {
							layer.hideLayer('datepicker_layer', 'water-day');
						}
					});

					// 다이어리 날짜 선택 시
					$('.diary-form-section .diary-date').on('click', function() {
						let date = ($('.diary-form-section .diary-date').text()) ? new Date($('.diary-form-section .diary-date').text()) : new Date();
						layer.showDetePicker('datepicker_layer', 'diary-date', date);
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
						let form;
						form = new $Form('myplantForm');
						form.require('myplant_name', '이름', {msgType: 'msg'});
						form.require('first_grow_date', '키우기 시작한 날', {msgType: 'msg'});
						form.require('water_interval', '물주기 간격일', {msgType: 'msg'});
						form.require('water_day', '다음 물 줄 날', {msgType: 'msg'});

						if (form.validate()) {
							layer.showLayer('confirm_layer', 'insert-confirm', '등록 하시겠습니까?');
						}
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
						formData = new FormData($('#myplantForm')[0]);
						formData.append('user_seq', app.userData.user_seq);
						formData.append('myplant_name', $('#myplant_name').val());
						formData.append('first_grow_date', $('#first_grow_date').val().replace(/-/gi, ''));
						formData.append('water_interval', $('#water_interval').val().slice(0, -1));
						formData.append('water_day', $('#water_day').val().replace(/-/gi, ''));
						
						formData.append('diary_date', $('.diary-date').text().replace(/-/gi, ''));
						formData.append('water_yn', $('#water_yn').is(':checked') ? 'Y' : 'N');
						formData.append('soil_condition_yn', $(":input[name=soil_condition_yn]:checked").val());
						formData.append('medicine_yn', $('#medicine_yn').is(':checked') ? 'Y' : 'N');
						formData.append('pot_replace_yn', $('#pot_replace_yn').is(':checked') ? 'Y' : 'N');
						formData.append('diary_content', $('#diary_content').val());
						
						$.ajax({
							url: '/api/myplant/insert',
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
											myplantSeq : data.myplantSeq
										}
									}
									app.reactNativePostMessage(message);
								}
							}
						});
					};

					// 수정 폼 체크
					function formCheckUpdate() {

						let form;
						form = new $Form('myplantForm');
						form.require('myplant_name', '이름', {msgType: 'msg'});
						form.require('first_grow_date', '키우기 시작한 날', {msgType: 'msg'});
						form.require('water_interval', '물주기 간격일', {msgType: 'msg'});
						form.require('water_day', '다음 물 줄 날', {msgType: 'msg'});

						if (form.validate()) {
							layer.showLayer('confirm_layer', 'update-confirm', '수정 하시겠습니까?');
						}
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
						formData = new FormData($('#myplantForm')[0]);
						formData.append('user_seq', app.userData.user_seq);
						formData.append('myplant_seq', myplantSeq);
						formData.append('myplant_name', $('#myplant_name').val());
						formData.append('first_grow_date', $('#first_grow_date').val().replace(/-/gi, ''));
						formData.append('water_interval', $('#water_interval').val().slice(0, -1));
						formData.append('water_day', $('#water_day').val().replace(/-/gi, ''));
						
						formData.append('sys_myplant_img_delyn', sysMyplantImgDelYN);
						
						$.ajax({
							url: '/api/myplant/update',
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
										key : 'myPlantFormSuccess',
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