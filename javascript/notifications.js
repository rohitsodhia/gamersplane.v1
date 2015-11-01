controllers.controller('notifications', ['$scope', '$http', 'CurrentUser', 'LanguageService', function ($scope, $http, CurrentUser, LanguageService) {
	CurrentUser.load().then(function () {
		$scope.CurrentUser = CurrentUser.get();
		$scope.histories = {};
		$scope.LanguageService = LanguageService;
		$scope.pagination = { current: 1, numItems: 0, itemsPerPage: 20 };
		if ($.urlParam('page')) 
			$scope.pagination.current = parseInt($.urlParam('page'));
		$scope.$watch(function () { return $scope.pagination; }, function () {
			console.log($scope.pagination);
		}, true);
		$scope.loadHistories = function () {
			$scope.$emit('pageLoading');
			$http.post(API_HOST + '/notifications/user/', { page: $scope.pagination.current }).then(function (data) {
				data = data.data;
				if (data.success) {
					$scope.pagination.numItems = data.numHistories;
					$scope.histories = {};
					data.histories.forEach(function (history) {
						hDate = moment.utc(history.timestamp).local().format('YYYY-MM-DD');
						if (typeof $scope.histories[hDate] == 'undefined') 
							$scope.histories[hDate] = [];
						history.language = {};
						if (['addToLibrary', 'removeFromLibrary', 'characterApplied', 'characterFavorited', 'characterUnfavorited', 'playerApplied', 'inviteAccepted', 'inviteDeclined', 'playerLeft'].indexOf(history.action) >= 0) 
								history.language.actor = history.user.userID == $scope.CurrentUser.userID?'You':LanguageService.userProfileLink(history.user.userID, history.user.username);
						else if (['characterEdited', 'characterDeleted', 'characterApplied'].indexOf(history.action) >= 0) 
								history.language.actor = history.character.user.userID == $scope.CurrentUser.userID?'You':LanguageService.userProfileLink(history.character.user.userID, history.character.user.username);
						else if (['characterApproved', 'characterRejected', 'characterRemoved', 'playerInvited', 'inviteWithdrawn', 'playerApproved', 'playerRejected', 'playerRemoved', 'gmAdded', 'gmRemoved', 'gameRetired'].indexOf(history.action) >= 0) 
							history.language.actor = history.gm.userID == $scope.CurrentUser.userID?'You':LanguageService.userProfileLink(history.gm.userID, history.gm.username);

						if (['characterFavorited', 'characterUnfavorited', 'playerInvited', 'inviteWithdrawn', 'playerApproved', 'playerRejected', 'playerRemoved', 'gmAdded', 'gmRemoved'].indexOf(history.action) >= 0) 
							history.language.targetUser = history.user.userID == $scope.CurrentUser.userID?'your':LanguageService.userProfileLink(history.user.userID, history.user.username);
						else if (['characterEdited', 'characterDeleted', 'characterApproved', 'characterRejected', 'characterRemoved'].indexOf(history.action) >= 0) 
							history.language.targetUser = history.character.user.userID == $scope.CurrentUser.userID?'your':'their';
						if (['characterEdited', 'characterFavorited', 'characterUnfavorited', 'characterApproved', 'characterRejected', 'characterRemoved', 'inviteWithdrawn'].indexOf(history.action) >= 0 && history.language.targetUser != 'your' && history.language.targetUser != 'their')
							history.language.targetUser += '\'s';

						if (['characterApplied', 'characterApproved', 'characterRejected', 'characterRemoved', 'playerInvited', 'inviteAccepted', 'inviteWithdrawn', 'inviteDeclined', 'playerApproved', 'playerRejected', 'playerRemoved', 'playerLeft', 'gmAdded', 'gmRemoved', 'gameRetired'].indexOf(history.action) >= 0) 
							history.language.targetGM = history.game.gm.userID == $scope.CurrentUser.userID?'your':LanguageService.userProfileLink(history.game.gm.userID, history.game.gm.username) + '\'s';

						if (['characterCreated', 'basicEdited', 'characterEdited', 'characterDeleted', 'addToLibrary', 'removeFromLibrary', 'characterFavorited', 'characterUnfavorited', 'characterApplied', 'characterApproved', 'characterRejected', 'characterRemoved'].indexOf(history.action) >= 0) 
							history.language.characterLink = LanguageService.characterLink(history.character.characterID, history.character.system.short, history.character.label);

						history.timestamp = moment.utc(history.timestamp);
						$scope.histories[hDate].push(history);
					});
					console.log($scope.histories);
				}
				$scope.$emit('pageLoading');
			});
		};
		$scope.loadHistories();
	});
}]);