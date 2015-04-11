<?	require_once(FILEROOT.'/header.php'); ?>
<?	if ($_GET['deleteSuc'] || $_GET['sent']) { ?>
		<div class="alertBox_success">
<?
	if ($_GET['deleteSuc']) { echo "\t\t\tPM successfully deleted.\n"; }
	if ($_GET['sent']) { echo "\t\t\tPM successfully sent.\n"; }
?>
		</div>
<?	} ?>
		<h1 class="headerbar">Private Messages - {{box}}</h1>
		
		<div id="controlsContainer" class="clearfix">
			<a id="newPM" href="/pms/send/" class="fancyButton">New PM</a>
			<div id="controls" class="trapizoid sectionControls">
					<a href="/pms/" class="borderBox" ng-class="{'current': box == 'Inbox'}" ng-click="switchBox($event, 'inbox')">Inbox</a
					><a href="/pms/outbox" class="borderBox" ng-class="{'current': box == 'Outbox'}" ng-click="switchBox($event, 'outbox')">Outbox</a>
			</div>
		</div>
		<div id="pms">
			<div class="tr headerTR headerbar hbDark">
				<div class="delCol"></div>
				<div class="info">Message</div>
			</div>
			<div id="pmList">
				<div ng-repeat="pm in pms" id="pm_{{pm.pmID}}" class="pm tr" ng-class="{'lastTR': $last, 'new': !pm.read}">
					<div class="delCol"><a ng-if="pm.allowDelete" ng-click="delete(pm.pmID)" class="deletePM sprite cross"></a></div>
					<div class="info">
						<div class="title"><a href="/pms/view/{{pm.pmID}}/">{{pm.title}}</a></div>
						<div class="details" ng-show="box == 'Inbox'">
							from <a href="/user/{{pm.sender.userID}}/" class="username">{{pm.sender.username}}</a> on <span class="convertTZ" data-parse-format="YYYY-MM-DD HH:mm:ss" data-display-format="MMMM D, YYYY h:mm a">{{pm.datestamp}}</span>
						</div>
						<div class="details" ng-show="box == 'Outbox'">
							to <span ng-repeat="recipient in pm.recipients"><a ng-href="/user/{{recipient.userID}}/" class="username">{{recipient.username}}</a>{{$last?'':', '}}</span> on <span>{{pm.datestamp}}</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<paginate class="tr"></paginate>
<?	require_once(FILEROOT.'/footer.php'); ?>