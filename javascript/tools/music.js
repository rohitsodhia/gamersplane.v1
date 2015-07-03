$(function () {
	$('#addMusic').colorbox();

	$('ul.hbAttachedList').on('click', '.manageSong button', function (e) {
		e.preventDefault();

		$song = $(this).closest('li');
		songID = $song.data('id');
		action = $(this).attr('class');
		$.post('/tools/ajax/music/manage/', { songID: songID, action: action }, function (data) {
			if (data.length == 0 && action == 'toggleApproval') $song.toggleClass('unapproved').find('.toggleApproval').text($song.find('.toggleApproval').text() == 'Approve'?'Unapprove':'Approve');
			else if (data.length == 0) $song.remove();
		});
	});
	$('ul.hbAttachedList > li > .clearfix').each(function () {
		var tallest = 0;
		$(this).children().each(function () {
			if ($(this).height() > tallest) tallest = $(this).height();
		}).height(tallest);
	})
});

app.directive('musicForm', ['$http', '$filter', '$timeout', function ($http, $filter, $timeout) {
	return {
		restrict: 'E',
		templateUrl: '/angular/templates/tools/musicForm.html',
		scope: {
			'data': '=data'
		},
		link: function (scope, element, attrs) {
			scope.submitted = false;
			scope.errors = { 'duplicate': false, 'invalidURL': false }
			scope.genres = {
				'Horror/Survival': false, 
				'Wild West': false, 
				'Fantasy': false, 
				'Modern': false, 
				'Epic': false, 
				'Cyberpunk': false, 
				'Espionage': false, 
				'Sci-fi': false
			};
			scope.data.hasLyrics = false;

			scope.$watch(function () { return scope.data.genres; }, function () {});

			scope.save = function () {
				if (scope.data.url.length == 0 || scope.data.title.length == 0 || scope.data.genres.length == 0) 
					scope.submitted = true;
				else {
					$http.post(API_HOST + '/music/addSong/', scope.data).success(function (data) {
						console.log(data);
					});
				}
			};
		}
	}
}]);