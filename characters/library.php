<?	require_once(FILEROOT.'/header.php'); ?>
		<div class="sideWidget left">
			<h2>Filter</h2>
			<form id="filterChars" ng-submit="filterLibrary()">
				<ul class="clearfix">
					<li ng-repeat="system in systems | orderBy: 'name'"><label><pretty-checkbox checkbox="search.systems" value="system.slug"></pretty-checkbox><span ng-bind-html="system.name"></span></label></li>
				</ul>
				<div class="alignCenter"><button name="filter" value="filter" class="fancyButton" skew-element>Filter</button></div>
			</form>
		</div>

		<div class="mainColumn right">
			<h1 class="headerbar hb_hasList">Character Library</h1>
			
			<ul id="charList" ng-show="characters.length" class="hbAttachedList hbMargined">
				<li ng-repeat="character in characters | orderBy: ['system.name', 'label']" class="clearfix">
					<a href="/characters/{{character.system.slug}}/{{character.characterID}}/" class="charLabel" ng-bind-html="character.label"></a
					><div class="systemType" ng-bind-html="character.system.name"></div
					><div class="playerLink"><a href="/ucp/{{character.user.userID}}" class="username" ng-bind-html="character.user.username"></a></div>
				</li>
			</ul>
			<div id="noResults" ng-hide="characters.length">Doesn't seem like anyone is sharing any characters right now. Maybe you should share one of yours?</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>