<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Links</h1>
		<h2 class="headerbar hbDark">Partners</h2>
		<ul id="partners" class="clearfix">
			<li ng-repeat="link in links | filter: { level: 'Partner' } | orderBy: 'sortName'" equalize-heights="maxHeight.partners" style="height: {{maxHeight.partners}}px">
				<div class="image"><a href="{{link.url}}" target="_blank"><img src="/images/links/{{link._id}}.{{link.image}}"></a></div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">The RPG Academy Network</h2>
		<ul id="rpgan" class="clearfix network">
			<li ng-repeat="link in links | filter: { networks: 'rpga' } | orderBy: 'sortName'" equalize-heights="maxHeight.rpgan" style="height: {{maxHeight.rpgan}}px">
				<div class="image"><a href="{{link.url}}" target="_blank"><img src="/images/links/{{link._id}}.{{link.image}}"></a></div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">Affiliates</h2>
		<ul id="affiliates" class="clearfix">
			<li ng-repeat="link in links | filter: { level: 'Affiliate' } | orderBy: 'sortName'" equalize-heights="maxHeight.affiliates" style="height: {{maxHeight.affiliates}}px">
				<div class="image"><a href="{{link.url}}" target="_blank"><img src="/images/links/{{link._id}}.{{link.image}}"></a></div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>

		<h2 class="headerbar hbDark">Links</h2>
		<div class="controls">
			<pretty-checkbox eleid="blog_{{link._id}}" checkbox="filter.blog"></pretty-checkbox> <label for="blog_{{data._id}}">Blog</label>
			<pretty-checkbox eleid="podcast_{{link._id}}" checkbox="filter.podcast"></pretty-checkbox> <label for="podcast_{{data._id}}">Podcast</label>
		</div>
		<ul id="links" class="clearfix">
			<li ng-repeat="link in links | filter: { level: 'Link' } | intersect: 'categories':filter | orderBy: 'sortName'" equalize-heights="maxHeight.links" style="height: {{maxHeight.links}}px">
				<div class="image"><a href="{{link.url}}" target="_blank"><img src="/images/links/{{link._id}}.{{link.image}}"></a></div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>
<?	require_once(FILEROOT.'/footer.php'); ?>