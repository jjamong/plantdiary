<?php include "application/views/include/header.php" ?>

			<!-- #container.container -->
			<div id="container" class="container common calendar">
				<div class="header-title-section">
					<div class="title-section">
						<div class="setting">
							<div class="img"><img src="/resource/images/setting_icon_off.png" /></div>
						</div>
					</div>
				</div>
				<div id="calendar"></div>
				<div class="plant-list">
					<div class="arrow">
						<div class="img bottom"><img src="<?=SITE_URL?>resource/images/calendar/plantlist_bottom_icon.png"></div>
					</div>
					<div class="contens-section">
						<div class="date-section"></div>
						<ul class="list"></ul>
					</div>
				</div>
			</div>
			<!-- //#container.container -->
			
			<!-- 데이트 피커 팝업(month datepicker) -->
			<div class="datepicker-month-layer common-layer" id="datepicker_month_layer">
				<div class="datepicker-section">
					<div class="date">
						<span class="txt-year"></span>년
						<span class="txt-month"></span>월
					</div>
					<div class="select-section">
						<ul class="year"></ul>
						<ul class="month"></ul>
					</div>
				</div>
				<div class="btn-section btn-2">
					<div class="btn cancel layer-close">취소</div>
					<div class="btn ok layer-close">확인</div>
				</div>
			</div>
			

			<link rel="stylesheet" href="<?=SITE_URL?>resource/css/vendor/calendar.css">
			<script src="<?=SITE_URL?>resource/js/vendor/fullcalendar.min.js"></script>
			<script src="<?=SITE_URL?>resource/js/vendor/hammer.min.js"></script>

			<script>
				
				$(function() {
					let app = $App;
					let util = $Util;
					let layer = $Layer;
					app.init();
					app.webViewMessage(webViewMessage);
					layer.init();

					let selectDay = "";

					// APP에서 WEB으로 데이터 통신
					function webViewMessage(key, data) {
						
						// 웹뷰 로드 완료
						if (key === 'webViewLoad') {
							// 회원정보 설정
							app.userData = data.userData;

							// 캘린더 데이터 가져오기
							getCalendar();

							// 식물 리스트 날짜 설정
							dayPlantList(selectDay);
						}
					}
					
					// 캘린더 설정
					let calendarElement = $('#calendar')[0];
					let calendarTable = 200;
					let calendarData;
					let selectedDay;
					let calendar = new FullCalendar.Calendar(calendarElement, {
						initialView: 'dayGridMonth',
						dayMaxEvents: true,
						fixedWeekCount : false,
						headerToolbar: {
							left:   false,
							center: 'title',
							right:  false
						},
						titleFormat : function(date) {
							return date.date.year + "." + (date.date.month + 1); 
						},
						dateClick: function(info) {
							
							// 선택한 날짜 포커스
							selectedDayEffect(info.jsEvent.target);
							
							// 일별 식물 리스트 가져오기
							selectDay = info.dateStr;
							dayPlantList(info.dateStr);
									
							// 식물 리스트 올리기
							swipeState = "0";
							plantListUp();
						}
					});
					// 캘린더 랜더링
					calendar.render();
					
					// 캘린더 높이 값 설정
					let calendarTableHeight;
					setTimeout(function() {
						calendarTableHeight = $('#calendar .fc-scrollgrid-sync-table').outerHeight(true);
					}, 100);

					// 캘린더 요일 변경
					$('#calendar .fc-day-sun .fc-col-header-cell-cushion').html('일');
					$('#calendar .fc-day-mon .fc-col-header-cell-cushion').html('월');
					$('#calendar .fc-day-tue .fc-col-header-cell-cushion').html('화');
					$('#calendar .fc-day-wed .fc-col-header-cell-cushion').html('수');
					$('#calendar .fc-day-thu .fc-col-header-cell-cushion').html('목');
					$('#calendar .fc-day-fri .fc-col-header-cell-cushion').html('금');
					$('#calendar .fc-day-sat .fc-col-header-cell-cushion').html('토');

					let windowHeight = $(window).outerHeight(true);
					let headerHeight = $('.fc-header-toolbar').outerHeight(true);
					let plantArrowHeight = $('.plant-list .arrow').outerHeight(true);
					let plantDateHeight = $('.plant-list .date-section').outerHeight(true);
					let plantListHeight = windowHeight - headerHeight - plantArrowHeight - plantDateHeight - 16; // 1rem(16px) 
					let vh1 = windowHeight / 100;
					$('.plant-list .list').css('height', (plantListHeight / vh1) + 'vh');

					// 웹 상태일 경우
					if (app.webMode) {
						// 캘린더 데이터 가져오기
						getCalendar();
					}
					
					// 캘린더 데이터 가져오기
					function getCalendar() {
						if (app.userData) {
							$.ajax({
								url: '/api/calendar/myplantAllList',
								type: 'get',
								data: {
									user_seq: app.userData.user_seq
								},
								success:function(response) {
									response = JSON.parse(response);
									let key = response.key;
									calendarData = response.data.calendarResult;
									
									if (key == 'success') {
										// 캘린더 이벤트 렌더링
										eventRender();

										// 웹뷰 준비 완료
										app.webViewReady();
									}
								}
							});
						} else {
							$('#calendar .fc-daygrid-day').each(function(index, item) {
								$(this).find('.fc-daygrid-day-events').html('');
							});

							// 웹뷰 준비 완료
							app.webViewReady();
						}
					}
					
					// 식물 리스트 날짜 초기 설정
					let toDay = new Date();
					dayPlantList(util.dateFormat('', toDay));

					// 캘린더 스와이프
					let swipe = new Hammer(calendarElement);
					swipe.get('swipe').set({ direction: Hammer.DIRECTION_ALL });
					swipe.on("swipeleft", function (event) { 
						//console.log(event.type);
						calendar.next();

						// 캘린더 이벤트 렌더링
						eventRender();
						
						// 중간 상태일 경우
						if (swipeState == "1") {
							$('#calendar .fc-view-harness .fc-scrollgrid-section tr').css('border-bottom', 0);
						} else {
							$('#calendar .fc-scrollgrid-section tr').css('border-bottom', '0.01rem solid #ddd');
							$('#calendar .fc-scrollgrid-section tr:last-child()').css('border-bottom', '0');
						}
					});
					swipe.on("swiperight", function (event) {
						//console.log(event.type);
						calendar.prev();

						// 캘린더 이벤트 렌더링
						eventRender();

						// 중간 상태일 경우
						if (swipeState == "1") {
							$('#calendar .fc-view-harness .fc-scrollgrid-section tr').css('border-bottom', 0);
						} else {
							$('#calendar .fc-scrollgrid-section tr').css('border-bottom', '0.01rem solid #ddd');
							$('#calendar .fc-scrollgrid-section tr:last-child()').css('border-bottom', '0');
						}
					});

					// 날짜 선택기(월) 설정
					$('.fc-toolbar-chunk:nth-child(2)').append('<div class="datepicker-select"><img src="<?=SITE_URL?>resource/images/datepicker_select_icon.png"?></div>');
					$('.fc-toolbar-chunk').on('click', function() {
						let date = $(this).text().split('.');
						date = (date) ? new Date(date[0], (date[1]-1)) : new Date();
						layer.showDeteMonthPicker('datepicker_month_layer', 'calendar-date-picker', date);
					});
					
					// 날짜 선택기(월) 확인/취소 선택 시
					$(document).on('click', '.calendar-date-picker .layer-close', function() {
						if ($(this).hasClass('ok')) {
							let date = layer.selectDeteMonthPicker();
							calendar.gotoDate(date);
						}

						layer.hideLayer('datepicker_month_layer', 'calendar-date-picker');
					});
					
					// 캘린더 이벤트 렌더링
					function eventRender () {
						let dataCount = calendarData.length;
						$('#calendar .fc-daygrid-day').each(function(index, item) {
							$(this).find('.fc-daygrid-day-events').html('');
							
							let dayPlantCount = 0;

							for (let i=0; i< dataCount; i++) {
								if ($(this).data('date') == util.dateFormat('noDivision', calendarData[i].water_day)) {
									dayPlantCount++;
								}
							}

							if (dayPlantCount > 0) {
								$(this).find('.fc-daygrid-day-events').html('<div class="icon"><div class="count">' + dayPlantCount + '<div></div>');
							}
						});

						// 중간 상태일 경우
						if (swipeState == "1") {
							// 물방울 줄이기
							//$('#calendar .fc-view-harness').addClass('middle');
							$('#calendar .fc-view-harness .icon').css('width', '0.5rem').css('height', '0.5rem');
							$('#calendar .fc-view-harness .icon .count').css('opacity', '0');
						} else if (swipeState == "0") {
							
							$('#calendar .fc-view-harness .icon').css('width', '1rem').css('height', '1rem');
							$('#calendar .fc-view-harness .icon .count').css('opacity', '1');
						}
					};

					// 식물 리스트 노출을 위한 위아래 스와이프 설정
					let swipeState = "0"; // 0 : 아래, 1: 중간,2 위
					let documentSwipe = new Hammer($(document)[0]);
					documentSwipe.get('swipe').set({ direction: Hammer.DIRECTION_ALL });
					
					documentSwipe.on("swipeup", function (event) { 
						// 식물 리스트 올리기
						plantListUp();
					});

					documentSwipe.on("swipedown", function (event) { 
						// 식물 리스트 내리기
						plantListDown();
					});

					// 식물 리스트 화살표 버튼 선택 시
					$('.plant-list .arrow .img').on('click', function() {
						// 식물 리스트 내리기
						swipeState = "1";
						plantListDown();
					});

					// 식물 리스트 올리기
					function plantListUp () {

						// 아래 상태일 경우 
						if (swipeState == "0") {

							let headerHeight = $('.fc-header-toolbar').outerHeight(true);
							let calendarHeaderHeight = $('.fc-scrollgrid-section').outerHeight(true);
							let calendarHeight = headerHeight + calendarHeaderHeight + calendarTable + 8; // 0.5rem(8px)

							// 달력 영역 줄이기
							$('#calendar .fc-view-harness .fc-scrollgrid-section tr').css('border-bottom', 0);
							$('#calendar .fc-scrollgrid-sync-table').stop().animate({
								height : calendarTable
							}, 400);

							// 물방울 줄이기
							//$('#calendar .fc-view-harness').addClass('middle');
							$('#calendar .fc-view-harness .icon').stop().animate({
								width: '0.5rem',
								height: '0.5rem',
							}, 400);
							$('#calendar .fc-view-harness .icon .count').stop().animate({
								opacity: '0'
							}, 400);
							
							// 식물 리스트 영역 올리기
							swipeState = "1";
							$('.plant-list').stop().animate({
								top : calendarHeight,
							}, 400);

						// 중간 상태일 경우
						} else if (swipeState == "1") {

							let headerHeight = $('.fc-header-toolbar').outerHeight(true);
							let calendarHeaderHeight = $('.fc-scrollgrid-section').outerHeight(true);

							// 식물 리스트 영역 올리기
							swipeState = "2";
							$('.plant-list').stop().animate({
								top : headerHeight,
							}, 400, function() {
								$('.plant-list .list').css('overflow-y', 'scroll');
							});
						}
						
					};
					
					// 식물 리스트 내리기
					function plantListDown () {
						
						let scrollPosition = $('.plant-list').scrollTop();
						
						// 중간 상태일 경우
						if (swipeState == "1") {

							let headerHeight = $('.fc-header-toolbar').outerHeight(true);
							let calendarHeaderHeight = $('.fc-scrollgrid-section').outerHeight(true);

							// 달력 영역 늘리기
							$('#calendar .fc-scrollgrid-section tr').css('border-bottom', '0.01rem solid #ddd');
							$('#calendar .fc-scrollgrid-section tr:last-child()').css('border-bottom', '0');
							$('#calendar .fc-scrollgrid-sync-table').stop().animate({
								height : calendarTableHeight
							}, 400);
							
							// 물방울 늘리기
							$('#calendar .fc-view-harness .icon').stop().animate({
								width: '1rem',
								height: '1rem',
							}, 400);
							$('#calendar .fc-view-harness .icon .count').stop().animate({
								opacity: '1'
							}, 400);

							// 식물 리스트 영역 내리기
							swipeState = "0";
							$('.plant-list').stop().animate({
								top: windowHeight,
							}, 400);
							
						// 위 상태일 경우
						} else if (swipeState == "2" && scrollPosition == 0) {
							let headerHeight = $('.fc-header-toolbar').outerHeight(true);
							let calendarHeaderHeight = $('.fc-scrollgrid-section').outerHeight(true);
							let calendarBodyHeight = $('.fc-daygrid-body').outerHeight(true);
							let calendarHeight = headerHeight + calendarHeaderHeight + calendarBodyHeight + 8; // 0.5rem(8px)
							
							// 식물 리스트 영역 내리기
							swipeState = "1";
							$('.plant-list').stop().animate({
								top: calendarHeight,
							}, 400, function() {
								$('.plant-list .list').removeClass('top');
							});
						}

						$('.plant-list .list').css('overflow-y', 'visible');
					};

					// 선택한 날짜 선택효과
					function selectedDayEffect(selectElement) {
						$('.fc-view-harness .fc-day').each(function(index, item) {
							$(this).removeClass('fc-day-select');
						});
						$(selectElement).parents('.fc-day').addClass('fc-day-select');
					};

					// 일별 식물 리스트 가져오기
					function dayPlantList(selectedDay) {
						
						// 로그인 되어 있을 경우
						if (app.userData) {
							$.ajax({
								url: '/api/calendar/myplantDayList',
								type: 'get',
								data: {
									user_seq: app.userData.user_seq,
									date: selectedDay.replace(/-/gi, ''),
								},
								success: function(response) {
									response = JSON.parse(response);
									let key = response.key;
									let dayPlantListData = response.data.calendarResult;
									
									if (key == 'success') {
										console.log(dayPlantListData)
										
										// 식물 리스트 날짜 설정
										$('.plant-list .date-section').html(selectedDay);

										// 식물 리스트 설정
										let itemhtml = "";
										let dataCount = dayPlantListData.length;

										// 내식물 데이터가 있을 경우
										if (dataCount > 0) {
											for (let i=0; i< dataCount; i++) {


												let diary_date = util.dateFormat('noDivision', dayPlantListData[i].diary_date);
												let myplant_img;
												if (dayPlantListData[i].sys_myplant_img) {
													myplant_img = '/uploads/user_' + dayPlantListData[i].user_seq + '/myplant_' + dayPlantListData[i].myplant_seq + '/' + dayPlantListData[i].sys_myplant_img;
												} else {
													myplant_img = '/resource/images/plant_default_img.jpg';
												}

												let day = util.dateCalculate('today', util.dateFormat('noDivision', dayPlantListData[i].water_day));

												itemhtml += '<li class="li" data-myplant_seq="' + dayPlantListData[i].myplant_seq + '">';
												itemhtml +=	'	<div class="li-section">';
												itemhtml += '		<div class="content-section">';
												itemhtml += '			<div class="item title">';
												itemhtml += '				<div class="tit">' + dayPlantListData[i].myplant_name + '</div>';
												itemhtml += '			</div>';
												itemhtml += '			<div class="item water-day ' + util.waterDayCheck(day) + '">';
												itemhtml += '				<div class="tit">물 주는 날</div>';
												itemhtml += '				<div class="txt">' + util.waterDayHtml(day) + ' (' + util.dateFormat('noDivision', dayPlantListData[i].water_day) + ')</div>';
												itemhtml += '			</div>';
												itemhtml += '			<div class="item water-interval">';
												itemhtml += '				<div class="tit">물 주는 간격</div>';
												itemhtml += '				<div class="txt">' + dayPlantListData[i].water_interval + '일 간격</div>';
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
											
											$('.plant-list .list').html(itemhtml);

										} else {
											let itemhtml = '';
											itemhtml += '<li>';
											itemhtml += '	<div class="nodata">식물이 없습니다.</div>';
											itemhtml += '</li>';
											$('.plant-list .list').html(itemhtml);
										}

									}
								}
							});

						} else {

							let itemhtml = "";
							itemhtml += '<li>';
							itemhtml += '	<div class="login">로그인 하러 가기</div>';
							itemhtml += '</li>';
							$('.plant-list .list').html(itemhtml);
						}
					};
					
					// 식물 리스트 선택 시
					$(document).on('click', '.plant-list .list .li-section', function() {
						let message = {
							key : 'moveMyplantDetail',
							data : {
								myplantSeq : $(this).parent('li').data('myplant_seq')
							}
						}
						app.reactNativePostMessage(message);
					});

					// 다이어리 선택 시
					$(document).on('click', '.plant-list .list .diary', function() {
						let message = {
							key : 'moveMyplantDiaryList',
							data : {
								myplantSeq : $(this).parents('li').data('myplant_seq')
							}
						}
						app.reactNativePostMessage(message);
					});

					// 돌보기 선택 시
					$(document).on('click', '.plant-list .list .care', function() {
						
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
							url: '/api/diary/careSelect',
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
									// 캘린더 데이터 가져오기
									getCalendar();
									
									// 식물 리스트 날짜 설정
									dayPlantList(selectDay);
									
									// 돌보기 팝업 닫기
									$('#plant_care_layer').data('myplant_seq', '');
									$('#plant_care_layer').data('myplant_diary_seq', '');
									layer.hideLayer('plant_care_layer', 'plant-care');

									// 알림 설정
									let message = {
										key : 'setNotification',
										data : {
											notificationData : {
												myplantSeq : data.notificationData.myplantSeq,
												myplantName : data.notificationData.myplantName,
												waterDay : util.dateFormat('noDivision', data.notificationData.waterDay),
											}
										}
									}
									app.reactNativePostMessage(message);
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
									// 캘린더 데이터 가져오기
									getCalendar();
									
									// 식물 리스트 날짜 설정
									dayPlantList(selectDay);

									// 돌보기 팝업 닫기
									$('#plant_care_layer').data('myplant_seq', '');
									$('#plant_care_layer').data('myplant_diary_seq', '');
									layer.hideLayer('plant_care_layer', 'plant-care');

									// 알림 설정
									let message = {
										key : 'setNotification',
										data : {
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

					// 설정 화면 이동
					$('.calendar .setting').on('click', function() {
						let message = {
							key : 'moveSetting',
							data : {}
						}
						app.reactNativePostMessage(message);
					});

					// 로그인 화면 이동
					$(document).on('click', '.plant-list .login', function() {
						let message = {
							key : 'moveLogin',
							data : {}
						}
						app.reactNativePostMessage(message);
					});
				});
			</script>

<?php include "application/views/include/footer.php" ?>