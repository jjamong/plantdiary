<?php include "application/views/include/header.php" ?>

			<!-- #container.container -->
			<div id="container" class="container common calendar">
				<div id="calendar"></div>
				<div class="setting">
					<div class="img"><img src="<?=SITE_URL?>resource/images/setting_icon_off.png" /></div>
				</div>
				<div class="plant-list">
					<div class="arrow">
						<div class="img bottom"><img src="<?=SITE_URL?>resource/images/calendar/plantlist_bottom_icon.png"></div>
					</div>
					<div class="contens-section">
						<div class="date-section">
							11.6 (금)
						</div>
						<ul class="list"></ul>
					</div>
				</div>
			</div>
			<!-- //#container.container -->

			<link rel="stylesheet" href="<?=SITE_URL?>resource/css/vendor/calendar.css">
			<script src="<?=SITE_URL?>resource/js/vendor/fullcalendar.min.js"></script>
			<script src="<?=SITE_URL?>resource/js/vendor/hammer.min.js"></script>

			<script>
				
				$(function() {
					let app = $App;
					let util = $Util;
					app.init();
					app.webViewMessage(webViewMessage);

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

						// 날짜선택기 선택 후 캘린더 날짜 설정
						} else if (key === 'dateSelect') {
							calendar.gotoDate(data.date);
						
						// 물주기 컨펌 확인 시
						} else if (key === 'confirmWatering') {
							// 물주기
							watering();
						}
					}

					// 웹 상태일 경우
					if (app.webMode) {
						// 캘린더 데이터 가져오기
						getCalendar();
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
					
					// 캘린더 데이터 가져오기
					function getCalendar() {
						if (app.userData) {
							$.ajax({
								url: '/api/calendar/myplantAllList',
								type: 'get',
								data: {
									user_seq: app.userData.user_seq
								},
								success:function(response){
									calendarData = JSON.parse(response)

									// 캘린더 이벤트 렌더링
									eventRender();

									// 웹뷰 준비 완료
									app.webViewReady();
								}
							});
						} else {
							// 웹뷰 준비 완료
							app.webViewReady();
						}
					}
					
					// 식물 리스트 날짜 초기 설정
					let toDay = new Date();
					dayPlantList(getFormatDate(toDay));

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
						
						// 선택한 날짜 선택효과
						//selectedDayEffect();
					});

					// 날짜선택기 설정
					$('.fc-toolbar-chunk:nth-child(2)').append('<div class="datepicker-select"><img src="<?=SITE_URL?>resource/images/datepicker_select_icon.png"?></div>');
					$('.fc-toolbar-chunk').on('click', function() {
						let yearMonth = $('.fc-toolbar-title').text().replace('.', '-');
						let message = {
							key : 'datePicker',
							data : {
								date : yearMonth
							}
						}
						app.reactNativePostMessage(message);
					});
					
					// 캘린더 이벤트 렌더링
					function eventRender () {
						let dataCount = calendarData.length;

						$('#calendar .fc-daygrid-day').each(function(index, item) {
							$(this).find('.fc-daygrid-day-events').html('');
							
							let dayPlantCount = 0;

							for (let i=0; i< dataCount; i++) {
								if ($(this).data('date') == util.formatDate(calendarData[i].water_day, 'noDivision')) {
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

					// 날짜 변환
					function getFormatDate(date){
						var year = date.getFullYear();
						var month = (1 + date.getMonth());
						month = month >= 10 ? month : '0' + month;
						var day = date.getDate();
						day = day >= 10 ? day : '0' + day;
						return year + '-' + month + '-' + day;
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
									date: selectedDay.replace(/-/gi, ''),
									user_seq: app.userData.user_seq
								},
								success: function(response) {
									let dayPlantListData = JSON.parse(response)

									// 식물 리스트 날짜 설정
									$('.plant-list .date-section').html(selectedDay);

									// 식물 리스트 설정
									let itemhtml = "";
									let dataCount = dayPlantListData.length;
									let day = util.dDay(selectedDay);

									// 내식물 데이터가 있을 경우
									if (dataCount > 0) {
										for (let i=0; i< dataCount; i++) {
											itemhtml += '<li data-myplantseq="' + dayPlantListData[i].myplant_seq + '">';
											itemhtml += '	<div class="item">';
											itemhtml += '		<div class="plant-img">';
											itemhtml += '			<div class="img"><img src="<?=SITE_URL?>uploads/myplant/' + app.userData.user_seq + '/' + dayPlantListData[i].sys_myplant_img + '" /></div>';
											itemhtml += '		</div>';
											itemhtml += '		<div class="content-section">';
											itemhtml += '			<div class="title">' + dayPlantListData[i].myplant_name + '</div>';
											itemhtml += '			<div class="day">' + util.dDayHtml(day) + '</div>';
											itemhtml += '		</div>';
											itemhtml += '	</div>';
											itemhtml += '	<div class="control-section">';
											itemhtml += '		<div class="btn water">물주기</div>';
											itemhtml += '		<div class="btn procrastina">미루기</div>';
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
							});

						} else {

							let itemhtml = "";
							itemhtml += '<li>';
							itemhtml += '	<div class="login">로그인 하러 가기</div>';
							itemhtml += '</li>';
							$('.plant-list .list').html(itemhtml);
						}
					};
					
					// 상세 화면 이동
					$(document).on('click', '.plant-list .list .item', function() {
						let message = {
							key : 'moveMyplantDetail',
							data : {
								myplantSeq : $(this).parent('li').data('myplantseq')
							}
						}
						app.reactNativePostMessage(message);
					});

					// 물주기 선택 시
					let selectMyplantSeq = 0;
					let selectMyplantState;
					$(document).on('click', '.plant-list .list .water', function() {
						let msg = '';
						selectMyplantSeq = $(this).parents('li').data('myplantseq');

						// 물주는 날에 물을 줄 경우
						if ($(this).parents('li').find('.waterday').length > 0) {
							msg = '물을 주시겠습니까?';
							selectMyplantState = 'waterday';

						// 물주는 날 전에 미리 물을 줄 경우
						} else if ($(this).parents('li').find('.inadvance').length > 0) {
							msg = '미리 물을 주시겠습니까?\n이후 물주기가 변경됩니다.';
							selectMyplantState = 'inadvance';
						
						// 물주는 날 후에 늦게 물을 줄 경우
						} else if ($(this).parents('li').find('.warning').length > 0) {
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
								myplant_seq: selectMyplantSeq,
								user_seq: app.userData.user_seq,
								state: selectMyplantState
							},
							success: function(response) {
								
								// 캘린더 데이터 가져오기
								getCalendar();

								//식물 리스트 날짜 초기 설정
								dayPlantList(selectDay);
							}
						});
					}

					// 미루기 선택 시
					$(document).on('click', '.plant-list .list .procrastina', function() {
						selectMyplantSeq = $(this).parents('li').data('myplantseq');
						let message = {
							key : 'showProcrastina',
							data : {
								myplantSeq : selectMyplantSeq
							}
						}
						app.reactNativePostMessage(message);
					});

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