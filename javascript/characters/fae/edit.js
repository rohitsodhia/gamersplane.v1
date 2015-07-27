controllers.controller('editCharacter_fae', function ($scope, $http, $sce, $timeout, currentUser, range) {
	currentUser.then(function (currentUser) {
		pathElements = getPathElements();
		$scope.range = range.get;
		$scope.character = {};
		$scope.blanks = {
			'aspects': { 'name': '' },
			'stunts': { 'name': '' }
		};
		$http.post(API_HOST + '/characters/load/', { 'characterID': pathElements[2] }).success(function (data) {
			$scope.character = copyObject(data);
			for (key in $scope.character.aspects) 
				$scope.character.aspects[key] = { 'value': $scope.character.aspects[key] };
			for (key in $scope.blanks) 
				if ($scope.character[key].length == 0) 
					$scope.character[key].push($scope.blanks[key]);
		});

		$scope.setStress = function (stress) {
			if (stress >= 0 && stress <= 3) 
				$scope.character.stress = stress;
		}
		$scope.save = function () {
			console.log($scope.character);
		}
	});
});
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