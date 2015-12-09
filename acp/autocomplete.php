<?
	$currentUser->checkACP('autocomplete');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar" skew-element>Manage Autocomplete</h1>
			<h2 class="headerbar hbDark" skew-element>Skills</h2>
			<div class="hbdMargined">
				<div id="newItems">
					<h3>New Items</h3>
					<div class="tr headerTR">
						<div class="type">Type</div>
						<div class="name">Name</div>
						<div class="system">System</div>
						<div class="addedBy">Added By</div>
					</div>
					<div ng-repeat="item in newItems | orderBy: ['+type', '+addedBy.username']" class="tr newItem">
						<div class="type">{{item.type}}</div>
						<input type="text" ng-model="item.name" class="name">
						<div class="system">{{item.system}}</div>
						<div class="addedBy"><a href="/ucp/{{item.addedBy.userID}}/" target="_blank" class="username" ng-bind-html="item.addedBy.username"></a></div>
						<div class="actions">
							<a href="" ng-click="processUAI(item, 'add')" class="sprite check"></a>
							<a href="" ng-click="processUAI(item, 'reject')" class="sprite cross"></a>
						</div>
					</div>
				</div>
				<div id="addToSystem">
					<h3>Add to System</h3>
					<div class="tr headerTR">
						<div class="name">Name</div>
						<div class="system">System</div>
						<div class="addedBy">Added By</div>
					</div>
					<div ng-repeat="set in addToSystem | orderBy: '+type'">
						<div class="typeHeader">{{set.type}}</div>
						<div ng-repeat="item in set.items | orderBy: '+system'" class="tr item">
							<div class="name">{{item.name}}</div>
							<div class="system">{{item.system}}</div>
							<div class="addedBy"><a href="/ucp/{{item.addedBy.userID}}/" class="username" ng-bind-html="item.addedBy.username"></a></div>
							<div class="actions">
								<a href="" ng-click="processUAI(item, 'add')" class="sprite check"></a>
								<a href="" ng-click="processUAI(item, 'reject')" class="sprite cross"></a>
							</div>
						</div>
				</div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>