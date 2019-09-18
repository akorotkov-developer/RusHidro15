jQuery(function($){
	$.datepicker.regional['ru'] = {
		closeText: 'Закрыть',
		prevText: 'Назад',
		nextText: 'Вперед',
		currentText: 'Сегодня',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		dateFormat: 'yy-mm-dd', firstDay: 1,
		isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
});

function get_date(dt) {
	var s = dt.split('-');
	if (s[1].substring(0,1) == '0') {
		s[1] = s[1].substring(1,2);
	}
	if (s[2].substring(0,1) == '0') {
		s[2] = s[2].substring(1,2);
	}
	rus_month = ['января','февраля','марта','апреля','мая','июня','июля','августа','сентября','октября','ноября','декабря'];
	return parseInt(s[2])+' '+rus_month[parseInt(s[1])-1]+' '+s[0];
}

function get_date_object(dt) {
	var s = dt.split('-');
	return new Date(parseInt(s[0], 10), parseInt(s[1], 10)-1, parseInt(s[2], 10));
} 