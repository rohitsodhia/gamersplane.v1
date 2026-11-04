$(function () {
	function setupOrderedList($list, type) {
		function refresh() {
			var $rows = $list.children('.' + type);
			$rows.each(function (index) {
				var $row = $(this);
				if (!$row.children('.ffgControls').length) {
					var $controls = $('<span class="ffgControls"></span>');
					if (type === 'talent') {
						$row.find('.talent_notesLink').remove();
						$('<button type="button" class="ffgControl ffgNotes" title="Talent notes" aria-label="Talent notes" aria-expanded="false"><i class="ra ra-quill-ink" aria-hidden="true"></i></button>').appendTo($controls);
					}
					['up', 'down', 'delete'].forEach(function (action) {
						var label = action === 'delete' ? 'Delete ' + type : 'Move ' + type + ' ' + action;
						$('<button type="button" class="ffgControl"></button>').attr({ 'data-action': action, title: label, 'aria-label': label })
							.append($('<span aria-hidden="true" class="sprite"></span>').addClass(action === 'delete' ? 'cross' : action + 'Arrow')).appendTo($controls);
					});
					if (type === 'weapon') $row.find('a.remove').parent().remove();
					else $row.find('.' + type + '_remove').remove();
					$row.append($controls);
				}
				$row.find('[data-action="up"]').prop('disabled', index === 0);
				$row.find('[data-action="down"]').prop('disabled', index === $rows.length - 1);
			});
		}
		$list.on('click', '.ffgControl', function (e) {
			e.preventDefault();
			var $button = $(this), $row = $button.closest('.' + type), action = $button.attr('data-action');
			if ($button.hasClass('ffgNotes')) {
				var $notes = $row.children('textarea').toggle();
				$button.attr('aria-expanded', $notes.is(':visible') ? 'true' : 'false');
				return;
			}
			if (action === 'delete') {
				var $focusRow = $row.next('.' + type);
				if (!$focusRow.length) $focusRow = $row.prev('.' + type);
				$row.remove();
				refresh();
				if ($focusRow.length) $focusRow.find('.ffgControl').last().focus();
				else $list.closest('#skills, #talents, #weapons').find('.addItem, #addTalent, #addWeapon').focus();
				return;
			}
			var $neighbor = action === 'up' ? $row.prev('.' + type) : $row.next('.' + type);
			if (!$neighbor.length) return;
			if (action === 'up') $row.insertBefore($neighbor);
			else $row.insertAfter($neighbor);
			refresh();
			// Keep keyboard focus usable when the moved button reaches a boundary.
			if ($button.prop('disabled')) $row.find('[data-action="' + (action === 'up' ? 'down' : 'up') + '"]').focus();
			else $button.focus();
		});
		if ($list.length) new MutationObserver(refresh).observe($list[0], { childList: true });
		refresh();
	}

	setupOrderedList($('#skillList'), 'skill');
	setupOrderedList($('#talentList'), 'talent');
	setupOrderedList($('#weapons > .hbMargined'), 'weapon');

	if ($('#talents').length) {
		var nextTalentCount = 0;
		$('#talentList input[name]').each(function () {
			var match = this.name.match(/^talents\[(\d+)\]/);
			if (match) nextTalentCount = Math.max(nextTalentCount, Number(match[1]));
		});
		$('#addTalent').on('click', function (e) {
			e.preventDefault();
			$.post('/characters/ajax/starwarsffg/addTalent/', { key: ++nextTalentCount }, function (data) {
				$(data).appendTo('#talentList').find('.talent_name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'talent', characterID: characterID, system: system }).find('input').focus();
			});
		});
		$('.talent_name').placeholder().each(function () {
			$(this).autocomplete('/characters/ajax/autocomplete/', { type: 'talent', characterID: characterID, system: system });
		});
	}
});
