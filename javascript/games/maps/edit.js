$(function() {
	$('html').click(function () {
		$('#map .selectedTile').removeClass('selectedTile');
		$('#tileOptions').slideUp();
	});
	
	var addPosSel = $('#addPos');
	var cr = '';
	var numCols = $('.cHeader').each(function() {
		var hClass = $(this).attr('class');
		hClass = hClass.split(' ').filter(function (val) {
			if (val.match(/(row|col)_\w+/)) return true;
			else return false;
		});
		cr = hClass[0].split('_')[1];
		$(addPosSel).append('<option value="' + decToB26(cr) + '">' + decToB26(cr) + '&nbsp;</option>');
	}).size();
	var numRows = $('.rHeader').size();
	var maxCols = 15 < numCols?15:numCols;
	var maxRows = 15 < numRows?15:numRows;
	$(addPosSel).children(':last-child').attr('selected', 'selected');
	
	$('#addType').change(function () {
		$(addPosSel).html('');
		var headerSet, type;
		if ($(this).val() == 'c') { headerSet = $('.cHeader'); type = 'c'; }
		else { headerSet = $('.rHeader');  type = 'r'; }
		$(headerSet).each(function() {
			var hClass = $(this).attr('class');
			hClass = hClass.split(' ').filter(function (val) {
				if (val.match(/(row|col)_\w+/)) return true;
				else return false;
			});
			cr = hClass[0].split('_')[1];
			if (type == 'c') $(addPosSel).append('<option value="' + decToB26(cr) + '">' + decToB26(cr) + '&nbsp;</option>');
			else $(addPosSel).append('<option value="' + cr + '">' + cr + '&nbsp;</option>');
			$(addPosSel).children(':last-child').attr('selected', 'selected');
		});
	});
	
//	$('#map a').attr('href', '#');
	
/*	$('#addCol').click(function () {
		var addType = $('#addType').val() == 'c'?'column':'row';
		var addPos = b26ToDec($('#addPos').val()) + (($('#addLoc').val() == 'a')?1:-1);
		alert(addType);
		
		return false;
	});*/
	
	$('#saveMap').click(function (event) {
		event.stopPropagation();
		if ($(this).hasClass('btn_save')) {
			var bgData = {};
			$('.mapTile').each(function () {
				if ($(this).css('background-color') != 'transparent') bgData[this.id] = rgb2hex($(this).css('background-color'));
			});
			
			$.post(SITEROOT + '/tools/ajax/maps/save', { mapID: $('#mapID').val(), bgData: bgData });
			
			$(this).addClass('btn_save_disabled').removeClass('btn_save');
		}
		
		return false;
	});
	
	$('.cHeader > a:first-child').click(function (event) {
		event.stopPropagation();
		$(this).parent().toggleClass('cHeaderMin');
		
		return false;
	});
	
/*	$('.removeCol').click(function () {
		var hClass = $(this).parent().attr('class');
		hClass = hClass.split(' ').filter(function (val) {
			if (val.match(/(row|column)_\w+/)) return true;
			else return false;
		});
		if (hClass.toString().charAt(0) == 'c') $('.' + hClass).animate({ width: 0 }, 500, function () { $(this).remove(); });
		else $('.' + hClass).animate({ height: 0 }, 500, function () { $(this).remove(); });
		
		if ($('#saveMap').hasClass('btn_save_disabled')) {
			$('#saveMap').addClass('btn_save').removeClass('btn_save_disabled');
		}
											  
		return false;
	});*/
	
	$('.rHeader > a:first-child').click(function (event) {
		event.stopPropagation();
		$(this).parent().toggleClass('rHeaderMin');
		
		return false;
	});
	
	$('.mapTile').click();
	
	$('.colorOption').click(function (event) {
		event.stopPropagation();
		$('.selectedTile').css('background-color', $(this).children('.color').css('background-color')).each(function () {
//			alert($(this).attr('id'));
		}).removeClass('selectedTile');
		$('#tileOptions').slideUp();
		
		if ($('#saveMap').hasClass('btn_save_disabled')) {
			$('#saveMap').addClass('btn_save').removeClass('btn_save_disabled');
		}
	});
	
	$('#selectAll').click(function(event) {
		event.stopPropagation();
		$('.mapTile').not('.selectedTile').addClass('selectedTile');
		
		return false;
	});
	
	$('#unselectAll').click(function(event) {
		event.stopPropagation();
		$('.selectedTile').removeClass('selectedTile');
		$('#tileOptions').slideUp();
		
		return false;
	});
	
	$('#selectInverse').click(function(event) {
		event.stopPropagation();
		$('.mapTile').toggleClass('selectedTile');
		
		return false;
	});
	
	function moveMap(link) {
		if ($(link).hasClass('mapControls_up')) {
			var topPos = $('#map').css('top');
			topPos = parseInt(topPos.substring(0, topPos.length - 2));
			if (numRows - Math.abs(topPos / 40) != maxRows) {
				$('#map').css('top', (topPos - 40) + 'px');
				$('#rowHeaders > div').css('top', (topPos - 40) + 'px');
			}
		} else if ($(link).hasClass('mapControls_down')) {
			var topPos = $('#map').css('top');
			topPos = parseInt(topPos.substring(0, topPos.length - 2));
			if (topPos != 0) {
				$('#map').css('top', (topPos + 40) + 'px');
				$('#rowHeaders > div').css('top', (topPos + 40) + 'px');
			}
		} else if ($(link).hasClass('mapControls_right')) {
			var leftPos = $('#map').css('left');
			leftPos = parseInt(leftPos.substring(0, leftPos.length - 2));
			if (numCols - Math.abs(leftPos / 40) != maxCols) {
				$('#map').css('left', (leftPos - 40) + 'px');
				$('#colHeaders > div').css('left', (leftPos - 40) + 'px');
			}
		} else if ($(link).hasClass('mapControls_left')) {
			var leftPos = $('#map').css('left');
			leftPos = parseInt(leftPos.substring(0, leftPos.length - 2));
			if (leftPos != 0) {
				$('#map').css('left', (leftPos + 40) + 'px');
				$('#colHeaders > div').css('left', (leftPos +	 40) + 'px');
			}
		}
	}
	
	var mapMoveTimer;
	$('#mapControls a').mousedown(function () {
		var link = this;
		moveMap(link);
		mapMoveTimer = setInterval(function () { moveMap(link); }, 500);
	}).mouseup(function () {
		clearTimeout(mapMoveTimer);
	}).click(function () {
		return false;
	});
});