<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Links</h1>
		<p class="hbMargined"><strong>Please note:</strong> Gamers' Plane links out to a wide variety of sites. Gamers' Plane is not responsible for the individual content on any external sites.</p>
		<h2 class="headerbar hbDark">Partners</h2>
		<ul id="partners" class="clearfix">
			<li ng-repeat="link in links.partners | orderBy: 'sortName'" equalize-heights="maxHeight.partners">
				<div class="image" ng-class="{ 'noImg': !link.image }">
					<a href="{{link.url}}" target="_blank" ng-class="{ 'noImg': !link.image }">
						<p ng-if="!link.image">No Image</p>
						<img ng-if="link.image" src="/images/links/{{link._id}}.{{link.image}}">
					</a>
				</div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">The RPG Academy Network</h2>
		<ul id="rpgan" class="clearfix network">
			<li ng-repeat="link in links.rpgan | orderBy: 'sortName'" equalize-heights="maxHeight.rpgan">
				<div class="image" ng-class="{ 'noImg': !link.image }">
					<a href="{{link.url}}" target="_blank" ng-class="{ 'noImg': !link.image }">
						<p ng-if="!link.image">No Image</p>
						<img ng-if="link.image" src="/images/links/{{link._id}}.{{link.image}}">
					</a>
				</div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">Affiliates</h2>
		<ul id="affiliates" class="clearfix">
			<li ng-repeat="link in links.affiliates | filter: { networks: '!rpga' } | orderBy: 'sortName'" equalize-heights="maxHeight.affiliates">
				<div class="image" ng-class="{ 'noImg': !link.image }">
					<a href="{{link.url}}" target="_blank" ng-class="{ 'noImg': !link.image }">
						<p ng-if="!link.image">No Image</p>
						<img ng-if="link.image" src="/images/links/{{link._id}}.{{link.image}}">
					</a>
				</div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">Links</h2>
		<div class="controls">
			<div ng-repeat="category in categories">
				<pretty-checkbox eleid="{{category}}" checkbox="filter" value="category"></pretty-checkbox>
				<label for="{{category}}">{{category}}</label>
			</div>
		</div>
		<ul id="links" class="clearfix">
			<li ng-repeat="link in links.links | intersect: 'categories':filter | orderBy: 'sortName'" equalize-heights="maxHeight.links">
				<div class="image" ng-class="{ 'noImg': !link.image }">
					<a href="{{link.url}}" target="_blank" ng-class="{ 'noImg': !link.image }">
						<p ng-if="!link.image">No Image</p>
						<img ng-if="link.image" src="/images/links/{{link._id}}.{{link.image}}">
					</a>
				</div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>
<?	require_once(FILEROOT.'/footer.php'); ?>