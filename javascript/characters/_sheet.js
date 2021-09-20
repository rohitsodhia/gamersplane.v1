function toggleNotes(e) {
	e.preventDefault();

	$(this).siblings('.notes').slideToggle();
	$(this).toggleClass('open');
}

$(function () {
	$('.favoriteChar').click(function (e) {
		e.preventDefault();
		$link = $(this);
		characterID = $('#characterID').val();
		$.ajax({
			method: 'POST',
			url: API_HOST + '/characters/toggleFavorite/',
			data: { characterID: characterID },
			xhrFields: { withCredentials: true },
			success: function (data) {
				if (data !== 0 && $link.hasClass('off')) {
					$link.removeClass('off').attr('title', 'Unfavorite').attr('alt', 'Unfavorite');
				} else if (data !== 0) {
					$link.addClass('off').attr('title', 'Favorite').attr('alt', 'Favorite');
				}
			}
		});
	});

	$('#feats,.abilities').on('click', '.feat_notesLink,.ability_notesLink', toggleNotes);

	applyPageStyle($('.style:first').text());
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
}]).controller('viewCharacter_custom', ['$scope', 'CurrentUser', function ($scope, CurrentUser) {
	CurrentUser.load().then(function () {
		$scope.loadChar();
	});
}]);


