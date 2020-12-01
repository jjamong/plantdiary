<?php include "application/views/include/header.php" ?>

			<script src="/resource/js/vendor/slick.min.js"></script>

			<!-- #container.container -->
			<div id="container" class="container common diary-detail">
				<div class="contens-section">
					<div class="web header-title-section">
						<div class="text modify">수정</div>
					</div>
					<div class="contents">
						<div class="image-section">
							<div class="img"><img src="/resource/images/plant_default_img.jpg"></div>
						</div>
						<div class="content-section">
							<div class="detail-section">
								<div class="title-section">
									<h2 class="title"></h2>
								</div>
								<ul>
									<li class="water-yn">
										<div class="desc-title">물주기</div>
										<div class="desc"></div>
									</li>
									<li class="soil-condition-yn">
										<div class="desc-title">흙상태</div>
										<div class="desc"></div>
									</li>
									<li class="medicine-yn">
										<div class="desc-title">약주기</div>
										<div class="desc"></div>
									</li>
									<li class="pot-replace-yn">
										<div class="desc-title">분갈이</div>
										<div class="desc"></div>
									</li>
								</ul>
								<div class="diary-content">
									<div class="desc-title">메모</div>
									<div class="desc"></div>
								</div>
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

					let myplantDiarySeq;

					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {
						
						// 웹뷰 로드 완료
						if (key === 'webViewLoad') {
							// 회원정보 설정
							app.userData = data.userData;
							myplantDiarySeq = data.myplantDiarySeq;

							// 다이어리 상세 가져오기
							getDiaryDetail();

						// 식물 삭제 컨펌 확인 시
						} else if (key === 'confirmMyPlantDelete') {
							del();
						}
					}
					
					// 웹 상태일 경우
					if (app.webMode) {
						// 다이어리 상세 가져오기
						myplantDiarySeq = 184;
						getDiaryDetail();
					}

					// 식물 상세 가져오기
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
										
										console.log(diaryRow)
										console.log(diaryImageResult)

										// 다이어리 최근 데이터 설정
										let title = util.dateFormat('noDivision', diaryRow.diary_date) + ' ' + util.dateFormat('dayWeek', util.dateFormat('noDivision', diaryRow.diary_date));
										let diaryImagesCount = diaryImageResult.length;
										if (diaryImagesCount > 0) {
											$('.image-section').html('');
											for (let i=0; i<diaryImagesCount; i++) {
												let diaryImg = '/uploads/user_' + diaryRow.user_seq + '/myplant_' + diaryRow.myplant_seq + '/diary_' + diaryRow.myplant_diary_seq + '/' + diaryImageResult[i].sys_diary_img;
												let diaryImgHtml = '<div class="img"><img src="' + diaryImg +'" /></div>';
												$('.image-section').append(diaryImgHtml);
											}
										}

										let water_yn = (diaryRow.water_yn == 'Y') ? '물 줌' : '물 안줌';
										let soil_condition_yn;
										if (diaryRow.soil_condition_yn == '1') {
											soil_condition_yn = '건조함';
										} else if (diaryRow.soil_condition_yn == '2') {
											soil_condition_yn = '보통';
										} else if (diaryRow.soil_condition_yn == '3') {
											soil_condition_yn = '과습';
										}
										let medicine_yn = (diaryRow.medicine_yn == 'Y') ? '약 줌' : '약 안줌';
										let pot_replace_yn = (diaryRow.pot_replace_yn == 'Y') ? '분갈이 함' : '분갈이 안함';

										$('.title-section .title').html(title);
										$('.water-yn .desc').html(water_yn);
										$('.soil-condition-yn .desc').html(soil_condition_yn);
										$('.medicine-yn .desc').html(soil_condition_yn);
										$('.pot-replace-yn .desc').html(soil_condition_yn);
										$('.diary-content .desc').html(diaryRow.diary_content);
										
										$('.image-section').slick({
											dots: true,
											infinite: false,
											prevArrow: false,
											nextArrow: false
										});

										// 웹뷰 준비 완료
										app.webViewReady();
									}
								}
							});
						}
					}

					// 다이어리 전체 보기 선택 시
					$('.diary-section .diary-all').on('click', function() {
						let message = {
							key : 'moveMyplantDiaryList',
							data : {
								myplantSeq : myplantSeq
							}
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
							url: '/api/diary/delete',
							type: 'post',
							data: {
								user_seq: app.userData.user_seq,
								myplant_diary_seq: myplantDiarySeq
							},
							success: function(response) {
								response = JSON.parse(response);
								let key = response.key;
								let data = response.data;
								
								if (key == 'success') {
									let message = {
										key : 'moveMyplantDiaryList',
										data : {
											myplantSeq : data.myplantSeq
										}
									}
									app.reactNativePostMessage(message);
								}
							}
						});
					}
				});
			</script> 

<?php include "application/views/include/footer.php" ?>