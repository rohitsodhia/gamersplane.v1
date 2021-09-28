<?	$responsivePage=true;
	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">{{headerTitle}}</h1>

		<form ng-submit="sendPM()">
			<input id="replyTo" type="hidden" name="pmID" value="{{replyTo}}">
			<div class="tr clearfix">
				<label for="username">Username:</label>
				<input id="username" type="text" name="username" maxlength="24" ng-model="username" ng-blur="checkUser()">
				<div id="invalidUser" class="alert" ng-hide="formError.validUser">Invalid User</div>
			</div>
			<div class="tr">
				<label for="title">Title:</label>
				<input id="title" type="text" name="title" maxlength="100" ng-model="title" ng-change="checkTitle()" ng-blur="checkTitle()">
			</div>
			<div id="titleRequired" class="tr alert" ng-hide="formError.validTitle">Title required!</div>
			<textarea id="messageTextArea" name="message" ng-model="message"></textarea>
			<div id="messageRequired" class="alert" ng-hide="formError.validMessage">Message required!</div>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="send" class="fancyButton">Send</button></div>
		</form>

		<div id="history" ng-if="hasHistory">
			<div ng-repeat="pm in history" class="historyPM" ng-class="{'first': $first}">
				<p ng-if="hasAccess" class="title"><a href="/pms/view/{{pm.pmID}}/">{{pm.title}}</a></p>
				<p ng-if="!hasAccess" class="title">{{pm.title}}</p>
				<p class="user">from <a href="/user/{{pm.sender.userID}}/" class="username">{{pm.sender.username}}</a> on <span>{{pm.datestamp}}</span></p>
				<p class="user">to <span ng-repeat="recipient in pm.recipients"><a ng-href="/user/{{recipient.userID}}/" class="username">{{recipient.username}}</a>{{$last?'':', '}}</span></p>
				<div class="message" ng-bind-html="pm.message"></div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>