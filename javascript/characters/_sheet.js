function toggleNotes(e) {
	e.preventDefault();

	$(this).siblings('.notes').slideToggle();
}

$(function () {
	$('.favoriteChar').click(function (e) {
		e.preventDefault();
		$link = $(this);
		characterID = $('#characterID').val();
		$.post('/characters/process/favorite/', { characterID: characterID }, function (data) {
			if (data != 0 && $link.hasClass('off')) {
				$link.removeClass('off').attr('title', 'Unfavorite').attr('alt', 'Unfavorite');
			} else if (data != 0) {
				$link.addClass('off').attr('title', 'Favorite').attr('alt', 'Favorite');
			}
		});
	});

	if ($('#feats').length) {
		$('#feats').on('click', '.feat_notesLink', toggleNotes);
	}
});

controllers.controller('viewCharacter', ['$scope', 'CharactersService', function ($scope, CharactersService) {
	pathElements = getPathElements();
	$scope.loadChar = function () {
		return CharactersService.load(pathElements[2], true).then(function (data) {
			$scope.character = data;
		});
	};
	$scope.toggleNotes = function ($event) {
		$($event.target).siblings('.notes').slideToggle();
	};
}]).controller('viewCharacter_custom', ['$scope', 'currentUser', function ($scope, currentUser) {
	currentUser.then(function (currentUser) {
		$scope.loadChar();
	});
}]);