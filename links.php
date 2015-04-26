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
			<li ng-repeat="link in links | filter: { network: 'rpga' } | orderBy: 'sortName'" equalize-heights="maxHeight.rpgan" style="height: {{maxHeight.rpgan}}px">
				<div class="image"><a href="{{link.url}}" target="_blank"><img src="/images/links/{{link._id}}.{{link.image}}"></a></div>
				<p><a href="{{link.url}}" target="_blank">{{link.title}}</a></p>
			</li>
		</ul>
<?	require_once(FILEROOT.'/footer.php'); ?>