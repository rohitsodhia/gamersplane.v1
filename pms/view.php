<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Private Message</h1>
		
		<div id="buttonsDiv">
			<a href="/pms/reply/{{pmID}}/" class="fancyButton">Reply</a>
			<a class="fancyButton deletePM" ng-show="allowDelete" ng-click="delete()">Delete</a>
		</div>
		<div class="tr">
			<div class="leftCol">Title</div>
			<div class="rightCol">{{title}}</div>
		</div>
		<div class="tr">
			<div class="leftCol">From</div>
			<div class="rightCol"><a href="/user/{{sender.userID}}/" class="username">{{sender.username}}</a></div>
		</div>
		<div class="tr">
			<div class="leftCol">When</div>
			<div class="rightCol">{{datestamp}}</div>
		</div>
		<div id="messageDiv" class="tr" ng-bind-html="message"></div>

		<div id="history" ng-if="hasHistory">
			<div ng-repeat="pm in history" class="historyPM" ng-class="{'first': $first}">
				<p ng-if="hasAccess" class="title"><a href="/pms/view/{{pm.pmID}}/">{{pm.title}}</a></p>
				<p ng-if="!hasAccess" class="title">{{pm.title}}</p>
				<p class="user">from <a href="/user/{{pm.sender.userID}}/" class="username">{{pm.sender.username}}</a> on <span>{{pm.datestamp}}</span></p>
				<p class="user">to <span ng-repeat="recipient in pm.recipients"><a ng-href="/user/{{recipient.userID}}/" class="username">{{recipient.username}}</a>{{$last?'':', '}}</span></p>
				<div class="message">{{pm.message}}</div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>