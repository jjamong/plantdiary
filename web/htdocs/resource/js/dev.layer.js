/*
	jQurey Layer Class
*/ 
(function($) {
	
	$Layer = {
		
		showLayerCount : 0,	// 보여지고 있는 레이어 개수

		// 초기화
		init : function () {

			// 날짜 선택 레이어 팝업 선택 효과
			$(document).on('click touchend', '.datepicker-layer li', function(e) {
				e.preventDefault();

				let year = $('.datepicker-layer .txt-year').text();
				let month = $('.datepicker-layer .txt-month').text();
				let day = $('.datepicker-layer .txt-day').text();

				// 년 선택
				if ($(this).parent('.year').hasClass('year')) {
					$('.datepicker-layer .year li').removeClass('selected');
					$(this).addClass('selected');
					
					// 년,월에 따라 일자 29~31 설정
					let dayHtml = '';
					let dayStart = 1;
					year = $(this).text();
					let dayEnd = new Date(year, month, 0).getDate();
					let dayCheck = false;
					let j = 0;
					for (let i=dayStart; i<=dayEnd; i++) {
						if (day == i) {
							dayCheck = true;
							dayHtml += '<li data-day="' + i + '" data-index="' + j + '" class="selected">' + i + '</li>';
						} else {
							if (i == dayEnd && !dayCheck) {
								dayHtml += '<li data-day="' + i + '" class="selected">' + i + '</li>';
							} else {
								dayHtml += '<li data-day="' + i + '">' + i + '</li>';
							}
						}
						j++;
					}
				
					$('.datepicker-layer .day').html(dayHtml);

				// 월 선택
				} else if ($(this).parent('.month').hasClass('month')) {
					$('.datepicker-layer .month li').removeClass('selected');
					$(this).addClass('selected');

					// 년,월에 따라 일자 29~31 설정
					let dayHtml = '';
					let dayStart = 1;
					month = $(this).text();
					let dayEnd = new Date(year, month, 0).getDate();
					let dayCheck = false;
					let j = 0;
					for (let i=dayStart; i<=dayEnd; i++) {
						if (day == i) {
							dayCheck = true;
							dayHtml += '<li data-day="' + i + '" data-index="' + j + '" class="selected">' + i + '</li>';
						} else {
							if (i == dayEnd && !dayCheck) {
								dayHtml += '<li data-day="' + i + '" class="selected">' + i + '</li>';
							} else {
								dayHtml += '<li data-day="' + i + '">' + i + '</li>';
							}
						}
						j++;
					}

					$('.datepicker-layer .day').html(dayHtml);

				// 일 선택
				} else if ($(this).parent('.day').hasClass('day')) {
					$('.datepicker-layer .day li').removeClass('selected');
					$(this).addClass('selected');
				}

				$('.datepicker-layer .year li').each(function(index) {
					if ($('.datepicker-layer .year li').eq(index).hasClass('selected')) {
						year = $(this).text();
						$('.datepicker-layer .txt-year').text(year);
						return;
					}
				});

				$('.datepicker-layer .month li').each(function(index) {
					if ($('.datepicker-layer .month li').eq(index).hasClass('selected')) {
						month = $(this).text();
						$('.datepicker-layer .txt-month').text(month);
						return;
					}
				});

				$('.datepicker-layer .day li').each(function(index) {
					if ($('.datepicker-layer .day li').eq(index).hasClass('selected')) {
						day = $(this).text();
						$('.datepicker-layer .txt-day').text(day);
						return;
					}
				});

				let date = new Date(year + '-' + month + '-' + day);
				$('.datepicker-layer .txt-weekday').text($Util.dayWeek(date));
			});
			
			// 물주기 간격일 레이어 팝업 선택 효과
			$(document).on('click touchend', '.selectbox-layer .list li', function(e) {
				e.preventDefault();

				$('.selectbox-layer .list li').removeClass('selected');
				$(this).addClass('selected');
			});
		},

		/*
			레이어 팝업 노출
		*/
		showLayer : function(layerId, layerClass, message) {

			// 메시지 설정
			if (message) $('#' + layerId + ' .message').html(message);
			
			let _win = $(window);
			let _doc = $(document);
			let _docBody = $(document.body);
			let _winW = _win.width();
			let _winH = _win.height();
			let _docW = _doc.width();
			let _docH = _doc.height();

			let $el = $('#' + layerId);
    		let _elWidth = ($el.outerWidth())/2;

			let _scrollY = _win.scrollTop();
			let centerTop = Math.max(0, ((_win.height() - $el.outerHeight()) / 2) + _scrollY);
			let layerZindex = 110 + (this.showLayerCount * 100);

			$el.css({'display':'block', 'z-index' : 110, 'position':'absolute', 'left' : '50%', 'top' : centerTop, 'margin-left' : -_elWidth, 'z-index' : layerZindex});
			$el.addClass(layerClass)

			// dim 설정
			let dimHtml = '<div class=\'dim\'></div>';
			let opacity = 'opacity : 0.5';
			let dimZindex = 50 + (this.showLayerCount * 100);
			_winW = _win.width();
			_docH = _doc.height();
			
			if(this.showLayerCount == 0) {
				_docBody.append(dimHtml);
				$('.dim').css({ 'width':_winW, 'height' : _docH, 'opacity': opacity, 'top' : 0 , 'width' : _docW, 'height' : _docH, 'z-index' : dimZindex});
				$('.dim').css('display', 'block');
			} else {
				$('.dim').css('z-index', dimZindex);
			}

			this.showLayerCount++;
		},
		
		/*	
			레이어 팝업 비노출
		*/
		hideLayer : function(layerId, layerClass) {

			let $el = $('#' + layerId);

			if (this.showLayerCount < 2) {
				$('.dim').remove();
			} else {
				let dimZindex = 50 + ((this.showLayerCount - 2) * 100);
				$('.dim').css('z-index', dimZindex);
			}

			$el.removeClass(layerClass);
			$el.css('display', 'none');

			this.showLayerCount--;
		},

		/*	
			날짜 선택기 설정 후 레이어 팝업 노출
		*/
		showDetePicker : function(layerId, layerClass, date) {
			let yearHtml = '';
			let yearStart = 2010;
			let yearEnd = 2030;
			let year = date.getFullYear();
			let yearCount = 0;

			let monthHtml = '';
			let monthStart = 1;
			let monthEnd = 12;
			let month = date.getMonth() + 1;
			let monthCount = 0;

			let dayHtml = '';
			let dayStart = 1;
			let dayEnd = new Date(year, month, 0).getDate();
			let day = date.getDate();
			let dayCount = 0;

			$('.datepicker-layer .txt-year').text(year);
			$('.datepicker-layer .txt-month').text(month);
			$('.datepicker-layer .txt-day').text(day);
			$('.datepicker-layer .txt-weekday').text($Util.dayWeek(date));
			
			let j = 0;
			for (let i=yearStart; i<=yearEnd; i++) {
				if (year == i) {
					yearCount = i - yearStart;
					yearHtml += '<li data-year="' + i + '" data-index="' + j + '" class="selected">' + i + '</li>';
				} else {
					yearHtml += '<li data-year="' + i + '">' + i + '</li>';
				}
				j++;
			}
			$('.datepicker-layer .year').html(yearHtml);

			j = 0;
			for (let i=monthStart; i<=monthEnd; i++) {
				if (month == i) {
					monthCount = i - monthStart;
					monthHtml += '<li data-month="' + i + '" data-index="' + j + '" class="selected">' + i + '</li>';
				} else {
					monthHtml += '<li data-month="' + i + '">' + i + '</li>';
				}
				j++;
			}
			$('.datepicker-layer .month').html(monthHtml);

			j = 0;
			for (let i=dayStart; i<=dayEnd; i++) {
				if (day == i) {
					dayCount = i - dayStart;
					dayHtml += '<li data-day="' + i + '" data-index="' + j + '" class="selected">' + i + '</li>';
				} else {
					dayHtml += '<li data-day="' + i + '">' + i + '</li>';
				}
				j++;
			}
			$('.datepicker-layer .day').html(dayHtml);
			
			// 레이어 노출
			this.showLayer(layerId, layerClass);

			// 선택된 값 스크롤 설정
			let liHeight = $('.datepicker-layer li').outerHeight();
			$('.datepicker-layer .year').scrollTop((yearCount) * liHeight);
			$('.datepicker-layer .month').scrollTop((monthCount) * liHeight);
			$('.datepicker-layer .day').scrollTop((dayCount) * liHeight);
		},

		/*	
			날짜 선택기 확인 시 결과 값 출력
		*/
		selectDetePicker : function() {
			let year = $('.datepicker-layer .txt-year').text();
			let month = $('.datepicker-layer .txt-month').text();
			month = (month < 10) ? '0' + month : month;
			let day = $('.datepicker-layer .txt-day').text();
			let date = year + month + day;
			return date;
		},

		/*	
			셀렉트 박스 확인 시 결과 값 출력
		*/
		selectSelectBox : function() {
			let select = '';
			$('.selectbox-layer .list li').each(function(index) {
				if ($('.selectbox-layer .list li').eq(index).hasClass('selected')) {
					select = $('.selectbox-layer .list li').eq(index).text();
				}
			})
			return select;
		},

		// 디바이스 체크
		deviceCheck : function() {
			let userAgent = navigator.userAgent.toLowerCase(); //userAgent 값 얻기
			let check;
			if ( userAgent.indexOf('android') > -1) {
				check = 'android';
			} else if ( userAgent.indexOf("iphone") > -1||userAgent.indexOf("ipad") > -1||userAgent.indexOf("ipod") > -1 ) {
				check = 'ios';
			} else {
				check = 'other';
			}
			return check;
		},
		
	};
})(jQuery);