<?
	addPackage('forum');

	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Manage Subscriptions</h1>
		
		<div id="topLinks">
			<a href="/forums/">Return to forum</a>
		</div>
<script type="text/ng-template" id="forumList">
<span ng-class="{ 'notSubbed': !forum.isSubbed }">{{forum.title}} <a ng-if="forum.isSubbed" href="" ng-click="unsubscribe('forum', forum.forumID, forum)" class="sprite cross small" alt="Unsubscribe" title="Unsubscribe"></a></span>
<ul>
	<li ng-repeat="forum in forums | filter: { parentID: forum.forumID }" ng-include="'forumList'"></li>
</ul>
</script>
		<div id="forums" class="column">
			<h2 class="headerbar hbDark">Forums</h2>
			<ul>
				<li ng-repeat="forum in forums | filter: { parentID: 0 }" ng-include="'forumList'"></li>
			</ul>
		</div>
		<div id="threads" class="column">
			<h2 class="headerbar hbDark">Threads</h2>
			<ul>
				<li ng-repeat="forum in threads">
					<span>{{forum.title}}</span>
					<ul>
						<li ng-repeat="thread in forum.threads">
							<span><a href="/forums/thread/{{thread.threadID}}/" target="_blank">{{thread.title}}</a> <a href="" ng-click="unsubscribe('thread', thread.threadID, thread)" class="sprite cross small" alt="Unsubscribe" title="Unsubscribe"></a></span>
						</li>
					</ul>
				</li>
			</ul>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>