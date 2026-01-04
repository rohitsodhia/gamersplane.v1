<?	$responsivePage=true;
	require_once(FILEROOT.'/header.php'); ?>
<?	if ($_GET['deleteSuc'] || $_GET['sent']) { ?>
		<div class="alertBox_success">
<?
	if ($_GET['deleteSuc']) { echo "\t\t\tPM successfully deleted.\n"; }
	if ($_GET['sent']) { echo "\t\t\tPM successfully sent.\n"; }
?>
		</div>
<?	} ?>
		<h1 class="headerbar">Private Messages - {{box}}</h1>

		<div id="controlsContainer" class="hbTopper clearfix" hb-margined="dark">
			<a href="/pms/send/" class="fancyButton">New PM</a>
			<div class="trapezoid sectionControls">
				<a href="/pms/" class="borderBox" ng-class="{'current': box == 'Inbox'}" ng-click="switchBox($event, 'inbox')">Inbox</a
				><a href="/pms/outbox" class="borderBox" ng-class="{'current': box == 'Outbox'}" ng-click="switchBox($event, 'outbox')">Outbox</a>
			</div>
		</div>
		<div id="pms">
			<div class="tr headerTR headerbar hbDark">
				<div class="delCol"></div>
				<div class="info">Message</div>
			</div>
			<div id="pmList" hb-margined>
				<div ng-repeat="pm in pms" id="pm_{{pm.id}}" class="pm tr" ng-class="{'lastTR': $last, 'new': !pm.read}">
					<div class="delCol"><a ng-click="delete(pm.id)" class="deletePM sprite cross"></a></div>
					<div class="info">
						<div class="title"><a href="/pms/view/{{pm.id}}/">{{pm.title}}</a></div>
						<div class="details" ng-show="box == 'Inbox'">
							from <a href="/user/{{pm.sender.userID}}/" class="username">{{pm.sender.username}}</a> on <span>{{pm.datestamp}}</span>
						</div>
						<div class="details" ng-show="box == 'Outbox'">
							to <a ng-href="/user/{{pm.recipient.userID}}/" class="username">{{pm.recipient.username}}</a> on <span>{{pm.datestamp}}</span>
						</div>
					</div>
				</div>
			</div>
			<div class="tr" hb-margined><paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current" change-func="getPMs" class="tr"></paginate><loading-spinner ng-show="!spinnerPause" size="mini" pause="spinnerPause"></loading-spinner></div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
