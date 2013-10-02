$(function() {
	$('html').bind('contextmenu', function (e) {
		alert(1);

		e.preventDefault();
	});
	$('.mapIcon').bind('contextmenu', function (e) {
		e.stopPropagation();

		alert(2);
	});

	var numCols = $('.cHeader').size();
	var numRows = $('.rHeader').size();
	var maxCols = 15 < numCols?15:numCols;
	var maxRows = 15 < numRows?15:numRows;
	
	$('#detailsEdit').colorbox({inline: true, href: '#saveDetails'});
	
	$('#addIcon').click(function (e) {
		if ($('#iconID').val() != 0) {
			$('#iconForm').slideUp(function () {
				$('#iconID').val('');
				$('.editDiv').css('display', 'none');
				$('.addDiv').css('display', 'block');
			}).slideDown();
		} else $('#iconForm').slideToggle();
		
		e.preventDefault();
	});
	
	var locations = Array();
	$('.mapIcon').dblclick(function () {
		var icon = this;
		$('#iconForm').slideUp(function () {
			if ($(icon).attr('id').split('_')[1] != $('#iconID').val()) { $.post(SITEROOT + '/tools/ajax/maps/iconData', { iconID: $(icon).attr('id').split('_')[1] }, function (data) {
				data = data.split('~~~');
				$('#iconColor option[value=' + data[0] + ']').attr('selected', 'selected');
				$('#iconLabel').val(data[1]);
				$('#iconName').val(data[2]);
				$('#iconID').val($(icon).attr('id').split('_')[1]);
				$('.editDiv').css('display', 'block');
				$('.addDiv').css('display', 'none');
				$('#iconForm').slideDown();
			}); }
		});
	}).draggable({
		revert: 'invalid',
		start: function (event, ui) {
			if (ui.helper.parent().hasClass('mapTile')) {
				var topPos = $('#map').css('top');
				topPos = parseInt(topPos.substring(0, topPos.length - 2));
				var leftPos = $('#map').css('left');
				leftPos = parseInt(leftPos.substring(0, leftPos.length - 2));
				var loc = ui.helper.parent().attr('id').split('_');
//				ui.helper.appendTo('#mapContainer').css('left', ((loc[0] - 1) * 40 + leftPos) + 'px').css('top', ((loc[1] - 1) * 40 + topPos) + 'px');
				$('#mapIconHolder').css('left', (loc[0] * 40 + leftPos) + 'px').css('top', (loc[1] * 40 + topPos) + 'px').append(ui.helper);
			}
		}
	}).each(function () {
		locations[this.id] = $(this).parent().attr('id');
		if (locations[this.id] == 'iconBox_icons') locations[this.id] = '';
	});
	
	function sendToBox(icon) {
		icon.fadeOut(function () {
			$(this).appendTo('#iconBox_icons').css({'top': 0, 'left': 0}).fadeIn();
		});
	}
	
	$('#iconBox').droppable({
		accept: '.mapIcon',
		drop: function (event, ui) {
			if (locations[ui.draggable.attr('id')] != '') $.post(SITEROOT + '/tools/ajax/maps/updateLoc', { iconID: ui.draggable.attr('id').split('_')[1], location: '' });
			sendToBox(ui.draggable);
		}
	});
	
	$('.mapTile').droppable({
		accept: '.mapIcon',
		drop: function (event, ui) {
			var tile = this;
			ui.draggable.fadeOut(function () {
				if ($(tile).find('.mapIcon').length == 0 && locations[this.id] != tile.id) {
					$.post(SITEROOT + '/tools/ajax/maps/updateLoc', { iconID: this.id.split('_')[1], location: tile.id });
					$(this).appendTo('#' + tile.id).css({'top': 0, 'left': 0}).fadeIn();
					locations[this.id] = tile.id;
				} else $(this).appendTo('#' + locations[this.id]).css({'top': 0, 'left': 0}).fadeIn();
			});
/*			if ($(this).find('.mapIcon').length == 0) {
				ui.draggable.fadeOut(function () {
					$.post(SITEROOT + '/tools/ajax/maps/updateLoc', { iconID: this.id.split('_')[1], location: tileID });
					$(this).appendTo('#' + tileID + ' .tileBorder').css({'top': 0, 'left': 0}).fadeIn();
				});
			} else sendToBox(ui.draggable);*/
		}
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
	}).click(function (e) {
		e.preventDefault();
	});
});