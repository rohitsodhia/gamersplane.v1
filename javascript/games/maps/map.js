$(function() {
	var numCols = $('.cHeader').size(),
		numRows = $('.rHeader').size(),
		maxCols = 15 < numCols?15:numCols,
		maxRows = 15 < numRows?15:numRows,
		$pageContainer = $('#page_map').parent(),
		pageOffset = $pageContainer.offset(),
		$iconID = $('#iconID'),
		$sb_contentControls = $('#mapSidebar_contentControls a'),
		$sb_contentContainer = $('#mapSidebar_contentContainer'),
		$sidebarIconHolder = $('#sidebarIconHolder'),
		$iconBox = $('#iconBox'),
		$iconForm = $('#iconForm'),
		$iconColor = $('#iconColor'),
		$editDiv = $('.editDiv'),
		$addDiv = $('.addDiv'),
		$iconLabel = $('#iconLabel'),
		$iconName = $('#iconName'),
		$mapIconHolder = $('#mapIconHolder'),
		$map = $('#map'),
		$rowHeaderDivs = $('#rowHeaders > div'),
		$colHeaderDivs = $('#colHeaders > div'),
		$iconContextMenu = $('#iconContextMenu');

	$('#infoEdit').colorbox();

	$sb_contentControls.click(function (e) {
		e.preventDefault();

		$('#mapSidebar_contentContainer > div').not('#sidebarIconHolder').hide();
		$('#mapSidebar_content_' + this.id.split('_')[2]).show();
		$sb_contentControls.filter('.current').removeClass('current');
		$(this).addClass('current');
	});

	$('#mapSidebar_contentContainer > div').not('#mapSidebar_content_box, #sidebarIconHolder').hide();
	$iconForm.hide();
	
	$('#addIcon').click(function (e) {
		if ($iconID.val() != 0) {
			$iconForm.slideUp(function () {
				$iconID.val('');
				$editDiv.css('display', 'none');
				$addDiv.css('display', 'block');
			}).slideDown();
		} else $iconForm.slideToggle();
		
		e.preventDefault();
	});

	$iconForm.append('<input type="hidden" name="modal" value="1">').ajaxForm({
			dataType: 'json',
			beforeSubmit: function () {
				if ($iconLabel.val().length != 1 && $iconLabel.val().length != 2) return false;
				if ($iconName.val().length == 0) return false;

				return true;
			},
			success: function (data) {
				if (data.success == true) {
					$iconBox.append(data.iconHTML);
				}
			}
		});
	
	var locations = Array();
	$iconBox.add($map).on('dlbclick', '.mapIcon', function (e) {
		e.preventDefault();

		var $icon = $(this);
		$iconForm.slideUp(function () {
			if ($icon.attr('id').split('_')[1] != $iconID.val()) { $.post(SITEROOT + '/games/ajax/maps/iconData', { iconID: $icon.attr('id').split('_')[1] }, function (data) {
				data = data.split('~~~');
				$iconColor.find('option[value=' + data[0] + ']').attr('selected', 'selected');
				$iconLabel.val(data[1]);
				$iconName.val(data[2]);
				$iconID.val($icon.attr('id').split('_')[1]);
				$editDiv.css('display', 'block');
				$addDiv.css('display', 'none');
				$iconForm.slideDown();
			}); }
		});
	}).on('contextmenu', '.mapIcon', function (e) {
		e.stopPropagation();
		e.preventDefault();

		$iconContextMenu.show().css({ 'top': e.pageY - pageOffset.top, 'left': e.pageX - pageOffset.left })
	})
	$('html').click(function () {
		$iconContextMenu.hide();
	});
	$(window).scroll(function () {
		if ($iconContextMenu.is(':visible')) $iconContextMenu.hide();
	});

	var mapIcon_draggableOptions = {
		revert: 'invalid',
		start: function (event, ui) {
			if (ui.helper.parent().hasClass('mapTile')) {
				var topPos = $map.css('top');
				topPos = parseInt(topPos.substring(0, topPos.length - 2));
				var leftPos = $map.css('left');
				leftPos = parseInt(leftPos.substring(0, leftPos.length - 2));
				var loc = ui.helper.parent().attr('id').split('_');
				$mapIconHolder.css({ left: (loc[0] * 40 + leftPos), top: (loc[1] * 40 + topPos) }).show().append(ui.helper);
			} else {
				offset = ui.helper.position();
				$sidebarIconHolder.css({ top: offset.top, left: offset.left }).show().append(ui.helper);
			}
		},
		stop: function (event, ui) {
			$sidebarIconHolder.hide();
			$mapIconHolder.hide();
		}
	};
	$('.mapIcon').draggable(mapIcon_draggableOptions).click(function (e) { e.preventDefault; }).each(function () {
		locations[this.id] = $(this).parent().attr('id');
		if (locations[this.id] == 'iconBox') locations[this.id] = '';
	});
	
	function sendToBox(icon) {
		icon.fadeOut(function () {
			$(this).appendTo($iconBox).css({'top': 0, 'left': 0}).fadeIn();
		});
	}
	
	$sb_contentContainer.droppable({
		accept: '.mapIcon',
		drop: function (event, ui) {
			if (locations[ui.draggable.attr('id')] != '') $.post(SITEROOT + '/games/ajax/maps/updateLoc', { iconID: ui.draggable.attr('id').split('_')[1], location: '' });
			sendToBox(ui.draggable);
		}
	});
	
	$('.mapTile').droppable({
		accept: '.mapIcon',
		drop: function (event, ui) {
			var tile = this;
			ui.draggable.fadeOut(function () {
				if ($(tile).find('.mapIcon').length == 0 && locations[this.id] != tile.id) {
					$.post(SITEROOT + '/games/ajax/maps/updateLoc', { iconID: this.id.split('_')[1], location: tile.id });
					$(this).appendTo('#' + tile.id).css({'top': 0, 'left': 0}).fadeIn();
					locations[this.id] = tile.id;
				} else $(this).appendTo('#' + locations[this.id]).css({'top': 0, 'left': 0}).fadeIn();
			});
		}
	});
	
	function moveMap(link) {
		if ($(link).hasClass('mapControls_up')) {
			var topPos = $map.css('top');
			topPos = parseInt(topPos.substring(0, topPos.length - 2));
			if (numRows - Math.abs(topPos / 40) != maxRows) {
				$map.css('top', (topPos - 40) + 'px');
				$rowHeaderDivs.css('top', (topPos - 40) + 'px');
			}
		} else if ($(link).hasClass('mapControls_down')) {
			var topPos = $map.css('top');
			topPos = parseInt(topPos.substring(0, topPos.length - 2));
			if (topPos != 0) {
				$map.css('top', (topPos + 40) + 'px');
				$rowHeaderDivs.css('top', (topPos + 40) + 'px');
			}
		} else if ($(link).hasClass('mapControls_right')) {
			var leftPos = $map.css('left');
			leftPos = parseInt(leftPos.substring(0, leftPos.length - 2));
			if (numCols - Math.abs(leftPos / 40) != maxCols) {
				$map.css('left', (leftPos - 40) + 'px');
				$colHeaderDivs.css('left', (leftPos - 40) + 'px');
			}
		} else if ($(link).hasClass('mapControls_left')) {
			var leftPos = $map.css('left');
			leftPos = parseInt(leftPos.substring(0, leftPos.length - 2));
			if (leftPos != 0) {
				$map.css('left', (leftPos + 40) + 'px');
				$colHeaderDivs.css('left', (leftPos +	 40) + 'px');
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