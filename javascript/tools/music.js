var musicGenres = [ 'Horror/Survival', 'Wild West', 'Fantasy', 'Modern', 'Epic', 'Cyberpunk', 'Espionage', 'Sci-fi' ];
app.controller('music', function ($scope, $http, $sce, $timeout, currentUser) {
	pathElements = getPathElements();
	currentUser.then(function (currentUser) {
		$scope.loggedIn = currentUser.loggedOut?false:true;
		$scope.genres = copyObject(musicGenres);
		$scope.filter = { 'genres': [], 'lyrics': [] };
		$scope.music = [];
		$scope.addSong = false;
		$scope.toggleAddSong = function () { $scope.addSong = !$scope.addSong; };
		$scope.newSong = { 'url': '', 'title': '', 'lyrics': false, 'battlebards': false, 'genres': [], 'notes': '' };
		$scope.pagination = {};
		if ($.urlParam('page')) 
			$scope.pagination.current = parseInt($.urlParam('page'));
		else 
			$scope.pagination.current = 1;
		$scope.showPagination = true;
		initialLoad = true;
		$scope.loadMusic = function () {

			if (!initialLoad) 
				$scope.pagination.current = 1;
			else 
				initialLoad = false;
			$http.post(API_HOST + '/music/get/', { 'page': $scope.pagination.current, 'filter': $scope.filter }).success(function (data) {
				if (data.success) {
					$scope.music = data.music;
					$scope.pagination.numItems = Math.ceil(data.count / 10);
					$scope.pagination.pages = new Array();
					for (count = $scope.pagination.numItems - 2 > 0?$scope.pagination.numItems - 2:1; count <= $scope.pagination.numItems + 2 && count <= $scope.pagination.numItems; count++) {
						$scope.pagination.pages.push(count);
					}
				}
			});
		};
		$scope.loadMusic();
		$scope.changePage = function (page) {
			page = parseInt(page);
			if (page < 0 && page > $scope.pagination.numItems) 
				page = 1;
			$scope.pagination.current = page;
			$scope.loadMusic();
		};
		$scope.songSubmitted = false;
		$scope.$on('closeSongEdit', function (event) {
			$scope.addSong = false;
			$scope.songSubmitted = true;
			$timeout(function () { $scope.songSubmitted = false; }, 2000);
		});
	});
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
							if (!scope.formValues._id) 
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
				if ((id == 'new' && scope.formValues._id == undefined) || id == scope.formValues._id) {
					scope.formValues = copyObject(scope.data);
					for (key in musicGenres) 
						scope.genres[musicGenres[key]] = false;
				}
			});
		}
	}
}]);