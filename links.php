<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Links</h1>
		<h2 class="headerbar hbDark">Partners</h2>
		<ul id="partners" class="clearfix">
			<li ng-repeat="link in links | filter: { level: 'Partner' } | orderBy: 'sortName'" equalize-heights="maxHeight.partners">
				<div class="image"><a href="{{link.url}}" target="_blank"><img src="/images/links/{{link._id}}.{{link.image}}"></a></div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">The RPG Academy Network</h2>
		<ul id="rpgan" class="clearfix network">
			<li ng-repeat="link in links | filter: { networks: 'rpga' } | orderBy: 'sortName'" equalize-heights="maxHeight.rpgan">
				<div class="image"><a href="{{link.url}}" target="_blank"><img src="/images/links/{{link._id}}.{{link.image}}"></a></div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">Affiliates</h2>
		<ul id="affiliates" class="clearfix">
			<li ng-repeat="link in links | filter: { level: 'Affiliate', networks: '!rpga' } | orderBy: 'sortName'" equalize-heights="maxHeight.affiliates">
				<div class="image"><a href="{{link.url}}" target="_blank"><img src="/images/links/{{link._id}}.{{link.image}}"></a></div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">Links</h2>
		<div class="controls">
			<div ng-repeat="category in categories">
				<pretty-checkbox eleid="{{category.slug}}_{{link._id}}" checkbox="filter[category.slug]"></pretty-checkbox>
				<label for="{{category.slug}}_{{data._id}}">{{category.label}}</label>
			</div>
		</div>
		<ul id="links" class="clearfix">
			<li ng-repeat="link in links | filter: { level: 'Link' } | intersect: 'categories':filter | orderBy: 'sortName'" equalize-heights="maxHeight.links">
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