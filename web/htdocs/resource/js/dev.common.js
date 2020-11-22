
// 날짜 포맷 변경
function formatDate(date, type) {
	let result;

	if (type == 'noDivision') {
		result = date.substring(0, 4) + '-' + date.substring(4, 6) + '-' + date.substring(6, 8);
	} else {
		let d = new Date(date),
		month = '' + (d.getMonth() + 1),
		day = '' + d.getDate(),
		year = d.getFullYear();

		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;
		
		result = [year, month, day].join('-');
	}

	return result;
}

// 디데이 계산
function dday(selectedDay) {
	let toDay = new Date('2020-11-20');
	selectedDay = new Date(selectedDay);
	
	let gap = toDay.getTime() - selectedDay.getTime();
	let result = Math.floor(gap / (1000 * 60 * 60 * 24)) * -1;
	return result;
}