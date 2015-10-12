<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar" skew-element>GP's Gamers</h1>
		
		<p class="hbMargined" hb-margined>
			<label>
				<pretty-checkbox checkbox="showInactive"></pretty-checkbox>
				<span class="labelText">Show inactive users</span>
			</label>
		</p>
		<div class="relativeWrapper hbMargined" hb-margined>
			<ul class="clearfix">
				<li ng-repeat="user in users" ng-class="{ 'last': $index % 5 == 4 }">
					<div class="onlineIndicator" ng-class="{ 'online': user.online, 'offline': !user.online }"></div>
					<a href="/user/{{user.userID}}/" class="avatar">
						<img src="{{user.avatar}}">
					</a>
					<p><a href="/user/{{user.userID}}/">{{user.username}}</a><span ng-bind-html="user.inactive | trustHTML"></span></p>
				</li>
			</ul>
		</div>
		<p class="hbMargined" hb-margined><paginate num-items="pagination.numItems" items-per-page="pagination.itemsPerPage" current="pagination.current" change-func="getGamers"></paginate></p>
<?	require_once(FILEROOT.'/footer.php'); ?>