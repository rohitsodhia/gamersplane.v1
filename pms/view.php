<?	$responsivePage=true;
	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Private Message</h1>

		<div id="buttonsDiv">
			<a href="/pms/reply/{{id}}/" class="fancyButton">Reply</a>
			<a class="fancyButton deletePM" ng-click="delete()">Delete</a>
		</div>
		<div class="tr">
			<div class="leftCol">Title</div>
			<div class="rightCol">{{title}}</div>
		</div>
		<div class="tr">
			<div class="leftCol">From</div>
			<div class="rightCol"><a href="/user/{{sender.id}}/" class="username">{{sender.username}}</a></div>
		</div>
		<div class="tr">
			<div class="leftCol">To</div>
			<div class="rightCol"><a href="/user/{{recipient.id}}/" class="username">{{recipient.username}}</a></div>
		</div>
		<div class="tr">
			<div class="leftCol">When</div>
			<div class="rightCol">{{datestamp}}</div>
		</div>
		<div id="messageDiv" class="tr" ng-bind-html="message"></div>

		<div id="history" ng-if="hasHistory">
			<div ng-repeat="pm in history" class="historyPM" ng-class="{'first': $first}">
				<p ng-if="hasAccess" class="title"><a href="/pms/view/{{pm.id}}/">{{pm.title}}</a></p>
				<p ng-if="!hasAccess" class="title">{{pm.title}}</p>
				<p class="user">from <a href="/user/{{pm.sender.id}}/" class="username">{{pm.sender.username}}</a> on <span>{{pm.datestamp}}</span></p>
				<p class="user">to <a ng-href="/user/{{recipient.id}}/" class="username">{{recipient.username}}</a></p>
				<div class="message" ng-bind-html="pm.message"></div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
