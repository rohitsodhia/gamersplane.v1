controllers.controller('editCharacter_fae', ['$scope', '$http', '$sce', '$timeout', 'currentUser', 'character', 'range', function ($scope, $http, $sce, $timeout, currentUser, character, range) {
	currentUser.then(function (currentUser) {
		pathElements = getPathElements();
		$scope.range = range.get;
		$scope.character = {};
		$scope.blanks = {
			'aspects': { 'name': '' },
			'stunts': { 'name': '' }
		};
		character.load(pathElements[2]).then(function (data) {
			$scope.character = copyObject(data);
			for (key in $scope.character.aspects) 
				$scope.character.aspects[key] = { 'name': $scope.character.aspects[key] };
			for (key in $scope.character.stunts) 
				$scope.character.stunts[key] = { 'name': $scope.character.stunts[key] };
			for (key in $scope.blanks) {
				if ($scope.character[key].length == 0) 
					$scope.character[key].push(copyObject($scope.blanks[key]));
			}
		});
		$scope.addItem = function (key) {
			$scope.character[key].push(copyObject($scope.blanks[key]));
		};
		$scope.setStress = function (stress) {
			if (stress >= 0 && stress <= 3) 
				$scope.character.stress = stress;
		};
		$scope.save = function () {
			character.save($scope.character.characterID, $scope.character).then(function (data) {
				if (data.saved) 
					window.location = '/characters/' + $scope.character.system + '/' + data.characterID;
			});
		};
	});
}]);
/*
$(function() {
	itemizationFunctions['aspects'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#aspectList').find('input').placeholder().focus();
		},
		init: function ($list) {
			$list.find('input').placeholder();
		}
	}
	setupItemized($('#aspects'));

	itemizationFunctions['skills'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#skillList').prettify().find('.name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system }).find('input').focus();
		},
		init: function ($list) {
			$list.find('.name').placeholder().autocomplete('/characters/ajax/autocomplete/', { type: 'skill', characterID: characterID, system: system });
		}
	}
	setupItemized($('#skills'));

	itemizationFunctions['stunts'] = {
		newItem: function ($newItem) {
			$newItem.appendTo('#stuntsList').find('.name').placeholder().focus();
		},
		init: function ($list) {
			$list.find('.name').placeholder();
		}
	}
	setupItemized($('#stunts'));
	$('#stunts').on('click', '.notesLink', function(e) {
		e.preventDefault();

		$(this).siblings('textarea').slideToggle();
	});

	$('#stress h3 a').click(function (e) {
		e.preventDefault();
		$labels = $(this).parent().siblings('.labels');
		$track = $labels.siblings('.track');
		numBoxes = $labels.find('label').length;
		stressType = $labels.parent().data('type');
		if ($(this).hasClass('add')) {
			if (numBoxes == 4) return false;
			numBoxes++;
			$stressBox = $('<div><input id="stress_' + stressType + '_' + numBoxes + '" type="checkbox" name="stresses[' + stressType + '][' + numBoxes + ']"></div>');
			$labels.append('<label for="stress_' + stressType + '_' + numBoxes + '">' + numBoxes + '</label>')
			$stressBox.appendTo($track).find('input[type="checkbox"]').prettyCheckbox();
		} else {
			if (numBoxes == 1) return false;
			numBoxes--
			$labels.children('label').last().remove();
			$track.children('div').last().remove();
		}
		$track.parent().find('input[type="hidden"]').val(numBoxes);
	});
});
*/