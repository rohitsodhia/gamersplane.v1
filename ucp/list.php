<?	$responsivePage=true;
	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><i class="ra ra-gamers-plane"></i> GP's Gamers <input type="text" ng-model="filter.search" placeholder="Search..." class="headerSearch"/></h1>

		<p class="hbMargined" hb-margined>
			<label>
				<pretty-checkbox checkbox="showInactive"></pretty-checkbox>
				<span class="labelText">Show inactive users</span>
			</label>
			<label>
				<pretty-checkbox checkbox="lookingForAGame"></pretty-checkbox>
				<span class="labelText">Looking for a game</span>
			</label>
			<paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current"></paginate>
		</p>
		<ul id="gamersList" hb-margined>
			<li ng-repeat="user in users| filter:filterItems| paginateItems: 25:(pagination.current - 1) * 25" ng-class="{ 'last': $index % 5 == 4 }">
				<div class="onlineIndicator" ng-class="{ 'online': user.online, 'offline': !user.online }"></div>
				<div class="lfgIndicator" ng-if="user.lfgStatus"><i class="ra ra-health"></i></div>
				<a href="/user/{{user.userID}}/" class="avatar">
					<img src="{{user.avatar}}" on-error-src="/ucp/avatars/avatar.png"/>
				</a>
				<p><a href="/user/{{user.userID}}/">{{user.username}}</a><span ng-bind-html="user.inactive | trustHTML"></span></p>
			</li>
		</ul>
		<paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current"></paginate>
<?	require_once(FILEROOT.'/footer.php'); ?>