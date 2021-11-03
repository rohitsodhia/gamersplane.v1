<?
	$currentUser->checkACP('users');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Users</h1>
			<div id="controls">
				<input type="text" ng-model="search" ng-keypress="searchChange()">
				<a href="" ng-click="loadUsers('all')">All</a>
				<a href="" ng-click="loadUsers('active')">Active</a>
				<a href="" ng-click="loadUsers('inactive')">Inactive</a>
				<a href="" ng-click="loadUsers('suspended')">Suspended</a>
			</div>
			<ul id="userList" class="prettyList">
				<li ng-repeat="user in users" ng-class="{ 'not_activated': user.activatedOn }">
					<div class="info">
						<div class="user">
							<a ng-href="/user/{{user.userID}}/" class="username" ng-bind-html="user.username | trustHTML"></a>
							<span ng-if="user.suspendedUntil != null"> (suspended until {{user.suspendedUntil | amUtc | amLocal | amDateFormat:'M/D/YY h:mm a'}})</span>
						</div>
						<div class="actions">
							<a ng-href="/ucp/{{user.userID}}/">Edit</a>
							<a href="" ng-click="delete(user.userID)">Delete</a>
							<a ng-if="user.activatedOn != null" href="" ng-click="suspend(user)">{{user.suspendedUntil?'Uns':'S'}}uspend</a>
<!--							<a ng-if="!user.banned" href="" ng-click="ban(user.userID)">Ban</a>-->
							<a ng-if="user.activatedOn == null" href="" ng-click="getActivation(user)">Activation link</a>
						</div>
					</div>
					<form ng-show="user.showForm == 'suspend'" ng-submit="confirmSuspend(user)" class="suspendDate">
						<div ng-if="user.suspendedUntil == null">
							<span>Suspend until:</span>
							<combobox ng-repeat="part in ['month', 'day', 'year']" data="combobox.values[part]" search="suspendUntil[part]" change="setDatePart(suspendUntil, part, value)" select></combobox>
							<input type="text" ng-model="suspendUntil.hour">:<input type="text" ng-model="suspendUntil.minutes">
							<button type="submit" name="suspend" class="normal">Confirm</button>
						</div>
						<div ng-if="user.suspendedUntil">
							<span>Unsuspend?</span>
							<button type="submit" name="suspend" class="normal">Confirm</button>
						</div>
					</form>
					<div ng-show="user.showForm == 'activationLink'" class="activationLink">
						<input type="text" value="http://gamersplane.com/register/activate/{{user.userHash}}/">
					</div>
				</li>
			</ul>
			<paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current" change-func="loadUsers" class="tr"></paginate>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>
