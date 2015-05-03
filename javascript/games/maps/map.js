$(function() {
	var numCols = $('.cHeader').size(),
		numRows = $('.rHeader').size(),
		maxCols = 15 < numCols?15:numCols,
		maxRows = 15 < numRows?15:numRows,
		$pageContainer = $('#page_map').parent(),
		pageOffset = $pageContainer.offset(),
		$editToggle = $('#editToggle'),
		editing = false,
		$iconID = $('#iconID'),
		$sb_contentControl = $('#mapSidebar_contentControls select'),
		$sb_contentContainer = $('#mapSidebar_contentContainer'),
		$sidebarIconHolder = $('#sidebarIconHolder'),
		$iconBox = $('#iconBox'),
		$iconForm = $('#iconForm'),
		$iconColor = $('#iconColor'),
		$editDiv = $('.editDiv'),
		$addDiv = $('.addDiv'),
		$iconLabel = $('#iconLabel'),
		$iconName = $('#iconName'),
		$sb_history = $('#mapSidebar_content_history'),
		$mapIconHolder = $('#mapIconHolder'),
		$map = $('#map'),
		$rowHeaderDivs = $('#rowHeaders > div'),
		$colHeaderDivs = $('#colHeaders > div'),
		icm_icon = null,
		$iconContextMenu = $('#iconContextMenu'),
		$icm_edit = $('#icm_edit'),
		$icm_stb = $('#icm_stb'),
		$addCRForm = $('#addCR form'),
		$mapTiles = $('.mapTile'),
		$tileOptions = $('#tileOptions > div'),
		$saveMap = $('#ww_saveMap'),
		isGM = false;

	if ($sb_contentContainer.children('#mapSidebar_content_mapOptions').length) 
		isGM = true;

	$('#infoEdit').colorbox();

	$sb_contentControl.change(function () {
		section = $(this).val();
		$('#mapSidebar_contentContainer > div').hide();
		$('#mapSidebar_content_' + section).show().find('select').prettySelect('updateOptions');
		if (section == 'mapOptions') 
			toggleMapEdit(true);
		else 
			toggleMapEdit(false);
	});

	$('#mapSidebar_contentContainer > div').not('#mapSidebar_content_box, #sidebarIconHolder').hide();
	$iconForm.find('.editDiv').hide();
	$iconForm.hide();
	
	$('#addIcon').click(function (e) {
		if ($iconID.val() != 0) {
			$iconForm.slideUp(function () {
				$iconID.val('');
				$editDiv.css('display', 'none');
				$addDiv.css('display', 'block');
			}).slideDown(function () { $iconColor.prettySelect('updateOptions'); });
		} else $iconForm.slideToggle(function () { $iconColor.prettySelect('updateOptions'); });
		
		e.preventDefault();
	});

	$iconForm.append('<input type="hidden" name="modal" value="1">').ajaxForm({
		dataType: 'json',
		beforeSubmit: function (data) {
			if ($iconLabel.val().length != 1 && $iconLabel.val().length != 2) return false;
			if ($iconName.val().length == 0) return false;

			return true;
		},
		success: function (data) {
			if (data.success == true) {
				$sb_history.prepend(data.history);
				if (data.action == 'new') {
					$icon = $(data.iconHTML);
					$icon.draggable(mapIcon_draggableOptions).appendTo($iconBox);
					locations[$icon.attr('id')] = '';
				} else if (data.action == 'edit') {
					$icon = $('#icon_' + $iconForm.find('#iconID').val());
					$icon.css('background-color', '#' + $iconColor.val()).text($iconLabel.val()).attr('title', $iconName.val());
				} else if (data.action == 'delete') {
					$('#icon_' + $iconForm.find('#iconID').val()).remove();
				}
			}
		}
	});
	
	var locations = Array();
	$iconBox.add($map).on('dblclick', '.mapIcon', function (e) {
		e.preventDefault();

		var $icon = $(this);
		$iconForm.slideUp(function () {
			if ($icon.attr('id').split('_')[1] != $iconID.val()) {
				$iconColor.val($icon.css('background-color')).change();
				$iconLabel.val($icon.text());
				$iconName.val($icon.attr('title'));
				$iconID.val($icon.attr('id').split('_')[1]);
				$editDiv.css('display', 'block');
				$addDiv.css('display', 'none');
				$iconForm.slideDown();
			}
		});
	}).on('contextmenu', '.mapIcon', function (e) {
		e.stopPropagation();
		e.preventDefault();

		icm_icon = this;
		$iconContextMenu.show().css({ 'top': e.pageY - pageOffset.top, 'left': e.pageX - pageOffset.left })
		if (locations[this.id] == '') $iconContextMenu.addClass('inBox');
		else $iconContextMenu.removeClass('inBox');
	})
	$('html').click(function () {
		if ($iconContextMenu.is(':visible')) $iconContextMenu.hide();
	});
	$(window).scroll(function () {
		if ($iconContextMenu.is(':visible')) $iconContextMenu.hide();
	});

	$icm_edit.click(function (e) {
		e.preventDefault();


	});
	$icm_stb.click(function (e) {
		e.preventDefault();

		sendToBox($(icm_icon));
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
			$sidebarIconHolder.fadeOut();
			$mapIconHolder.fadeOut();
			containerID = ui.helper.parent().attr('id');
			if (containerID == 'mapIconHolder') {
				ui.helper.appendTo('#' + locations[ui.helper.attr('id')]).css({'top': 0, 'left': 0}).fadeIn();
			} else if (containerID == 'sidebarIconHolder') {
				ui.helper.appendTo($iconBox).css({'top': 0, 'left': 0}).fadeIn();
			}
		}
	};
	$('.mapIcon').draggable(mapIcon_draggableOptions).each(function () {
		locations[this.id] = $(this).parent().attr('id');
		if (locations[this.id] == 'iconBox') locations[this.id] = '';
	});
	
	function sendToBox(icon) {
		if (locations[icon.attr('id')] != '') $.post('/games/ajax/maps/updateLoc', { iconID: icon.attr('id').split('_')[1], location: '' }, function (data) {
						$sb_history.prepend(data);
					});
		icon.fadeOut(function () {
			$(this).appendTo($iconBox).css({'top': 0, 'left': 0}).fadeIn();
		});
	}
	
	$sb_contentContainer.droppable({
		accept: '.mapIcon',
		drop: function (event, ui) {
			sendToBox(ui.draggable);
		}
	});
	
	$mapTiles.droppable({
		accept: '.mapIcon',
		drop: function (event, ui) {
			var tile = this;
			ui.draggable.fadeOut(function () {
				if ($(tile).find('.mapIcon').length == 0 && locations[this.id] != tile.id) {
					$.post('/games/ajax/maps/updateLoc', { iconID: this.id.split('_')[1], location: tile.id }, function (data) {
						$sb_history.prepend(data);
					});
					$(this).appendTo('#' + tile.id).css({'top': 0, 'left': 0}).fadeIn();
					locations[this.id] = tile.id;
				}
			});
		}
	});
	
	function moveMap(link) {
		if ($(link).hasClass('mapControls_up')) {
			var topPos = $map.css('top');
			topPos = parseInt(topPos.substring(0, topPos.length - 2));
			if (topPos != 0) {
				$map.css('top', (topPos + 40) + 'px');
				$rowHeaderDivs.css('top', (topPos + 40) + 'px');
			}
		} else if ($(link).hasClass('mapControls_down')) {
			var topPos = $map.css('top');
			topPos = parseInt(topPos.substring(0, topPos.length - 2));
			if (numRows - Math.abs(topPos / 40) != maxRows) {
				$map.css('top', (topPos - 40) + 'px');
				$rowHeaderDivs.css('top', (topPos - 40) + 'px');
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


	$rowHeaderDivs.add($colHeaderDivs).find('a').click(function (e) {
		e.preventDefault();
	});

	var $addPosSel = $('#addPos');
	var cr = '';
	var numCols = $('.cHeader').each(function() {
		var hClass = $(this).attr('class');
		hClass = hClass.split(' ').filter(function (val) {
			if (val.match(/(row|col)_\w+/)) return true;
			else return false;
		});
		cr = hClass[0].split('_')[1];
		$addPosSel.append('<option value="' + decToB26(cr) + '">' + decToB26(cr) + '&nbsp;</option>');

		$addPosSel.children(':last-child').attr('selected', 'selected');
		$addPosSel.change().prettySelect('updateOptions');
	}).size();

	function toggleMapEdit(setTo) {
		if (setTo && setTo != editing) {
			editing = true;
			$map.addClass('editing');
			$map.find('.mapIcon').hide();
			$mapTiles.click(selectMapTile);
//			$map.find('.mapIcon').addClass('tempDisableDrag').draggable('disable');
		} else if (!setTo && setTo != editing) {
			editing = false;
			$map.removeClass('editing');
			$map.find('.mapIcon').show();
			$mapTiles.unbind('click');
//			$map.find('.mapIcon').removeClass('tempDisableDrag').draggable('enable');
		}
	}

	$('#addCR > a').click(function (e) {
		e.preventDefault();

		$(this).parent().find('select').prettySelect('updateOptions');
		$addCRForm.slideToggle();
	})

	function selectMapTile (e) {
		e.stopPropagation();
		$(this).toggleClass('selectedTile');
		
		if ($('.selectedTile').size()) $tileOptions.slideDown();
		else $tileOptions.slideUp();
	}

	$('#selectAll').click(function(e) {
		e.preventDefault();
		$mapTiles.not('.selectedTile').addClass('selectedTile');
		$tileOptions.slideDown();
	});
	
	$('#unselectAll').click(function(e) {
		e.preventDefault();
		$mapTiles.filter('.selectedTile').removeClass('selectedTile');
		$tileOptions.slideUp();
	});
	
	$('#selectInverse').click(function(e) {
		e.preventDefault();
		$mapTiles.toggleClass('selectedTile');
	});

	$('.colorOption').click(function (e) {
		$mapTiles.filter('.selectedTile').css('background-color', '#' + $(this).children('.color').css('background-color')).removeClass('selectedTile');
		$tileOptions.slideUp();
		
		if ($saveMap.hasClass('disabled')) {
			$saveMap.removeClass('disabled');
		}
	});

	$saveMap.click(function (e) {
		e.preventDefault();
		if (!$(this).hasClass('disabled')) {
			var bgData = {};
			$('.mapTile').each(function () {
				if ($(this).css('background-color') != 'transparent') bgData[this.id] = $(this).css('background-color');
			});
			
			$.post('/games/ajax/maps/save', { mapID: $('#mapID').val(), bgData: bgData });
			
			$(this).addClass('disabled');
		}
	});
});