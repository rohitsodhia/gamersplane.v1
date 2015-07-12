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

var musicGenres = [ 'Horror/Survival', 'Wild West', 'Fantasy', 'Modern', 'Epic', 'Cyberpunk', 'Espionage', 'Sci-fi' ];
app.controller('music', function ($scope, $http, $sce, $timeout) {
	scope.genres = copyObject(musicGenres);
	for (key in musicGenres) 
		scope.genresCB[musicGenres[key]] = false;
	scope.filter = [];
}).directive('musicForm', ['$http', '$filter', '$timeout', function ($http, $filter, $timeout) {
	return {
		restrict: 'E',
		templateUrl: '/angular/templates/tools/musicForm.html',
		scope: {
			'data': '=data'
		},
		link: function (scope, element, attrs) {
			scope.formValues = copyObject(scope.data);
			scope.submitted = false;
			scope.errors = { 'duplicate': false, 'invalidURL': false }
			scope.genres = {};
			for (key in musicGenres) 
				scope.genres[musicGenres[key]] = false;
			if (scope.formValues.genres.length) 
				for (key in scope.formValues.genres) 
					scope.genres[scope.formValues.genres[key]] = true;
			else 
				scope.formValues.lyrics = false;

			scope.save = function () {
				if (scope.formValues.url.length == 0 || scope.formValues.title.length == 0 || scope.formValues.genres.length == 0) 
					scope.submitted = true;
				else {
					$http.post(API_HOST + '/music/saveSong/', scope.formValues).success(function (data) {
						if (data.success) {
							scope.data = copyObject(data.song);
							if (!scope.formValues.id) 
								scope.$emit('addNew');
							scope.$emit('closeSongEdit');
						}
					});
				}
			};
			scope.cancel = function () {
				scope.$emit('closeSongEdit');
			};

			scope.$on('resetSongForm', function (event, id) {
				if ((id == 'new' && scope.formValues.id == undefined) || id == scope.formValues.id) {
					scope.formValues = copyObject(scope.data);
					for (key in musicGenres) 
						scope.genres[musicGenres[key]] = false;
				}
			});
		}
	}
}]);