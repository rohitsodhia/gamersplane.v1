<!DOCTYPE html>
<html>
<head prefix="og: http://ogp.me/ns#">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-NVGX2DJWQP"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', 'G-NVGX2DJWQP');
</script>
<?	require_once(FILEROOT.'/meta.php'); ?>

<?	require_once(FILEROOT.'/styles/styles.php'); ?>
</head>

<body class='<?=getUserTheme().' '.(MODAL?'modal':'')?>' data-modal-width="<?=$dispatchInfo['modalWidth']?>" ng-app="gamersplane" ng-controller="core">
	<div id="pageLoading"><loading-spinner pause="pageLoadingPause"></loading-spinner></div>
<?	if (!MODAL) { ?>
<header id="bodyHeader" ng-controller="header"<?=isset($contentClasses) && array_search('fullWidthBody', $contentClasses) >= 0?' class="fullWidthBody"':''?>>
	<div id="headerBG"></div>
	<div class="bodyContainer">
		<a id="headerLogo" href="/"><img src="/images/bodyComponents/logo.png" alt="Gamers Plane Logo"></a>

		<ul id="mainMenu">
			<li class="mob-hide" id="mainMenuTools">
				<a href="/tools/" class="first">Tools</a>
				<ul>
					<li><a href="/tools/dice/">Dice</a></li>
					<li><a href="/tools/cards/">Cards</a></li>
					<li><a href="/tools/music/">Music</a></li>
				</ul>
			</li>
			<li class="small-hide"><a href="/systems/">Systems</a></li>
			<li ng-show="loggedIn">
				<a href="/characters/my/"><i class="ra ra-double-team hide mob-show-inline-block"></i><span class="mob-hide">Characters</span></a>
				<ul ng-if="characters.length">
				<li><a href="/characters/my/"><i class="ra ra-double-team"></i>All characters</a></li>
					<li ng-repeat="char in characters | limitTo: 10"><a href="/characters/{{char.system}}/{{char.characterID}}/" ng-bind-html="char.label | trustHTML"></a></li>
				</ul>
			</li>
			<li>
				<a ng-href="{{loggedIn?'/games/':'/games/list/'}}"><i class="ra ra-d6 hide mob-show-inline-block"></i><span class="mob-hide">Games</span></a>
				<ul ng-if="loggedIn && games.length" class="gameSubmenu">
				<li><a href="/games/my/" class="forumSubmenuItem"><i class="ra ra-gamers-plane"></i> My games</a></li>
					<li ng-repeat="game in games | limitTo: 10"><a href="/games/{{game.gameID}}/" title="Game" class="gameSubmenuItem"><i class="ra {{game.isPlayer?'ra-d6':'ra-bookmark'}}"></i></a><a href="/forums/{{game.forumID}}/" title="Forum" class="forumSubmenuItem"><span ng-bind-html="game.title | trustHTML"></span> <img ng-if="game.isGM" src="/images/gm_icon.png"></a></li>
				</ul>
			</li>
			<li><a href="/forums/"><i class="ra ra-speech-bubble hide mob-show-inline-block"></i><span class="mob-hide">Forums</span></a></li>
			<li ng-show="loggedIn"><a href="/gamersList/"><i class="ra ra-gamers-plane hide mob-show-inline-block"></i><span class="mob-hide">The Gamers</span></a></li>
			<!-- <li class="small-hide"><a href="/links/">Links</a></li> -->
			<li id="headerRegister" ng-show="!loggedIn"><a href="/register/" class="last">Register</a></li>
			<li id="headerLogin" ng-show="!loggedIn"><a href="/login/" colorbox>Login</a></li>
			<li ng-show="loggedIn" id="userMenu">
				<a href="/ucp/" class="avatar"><img ng-src="{{avatar}}"></a>
				<a ng-if="pmCount > 0" href="/pms/" class="mail"><img src="/images/envelope.jpg" title="Private Messages" alt="Private Messages"></a>
				<ul>
					<li><a href="/ucp/">Settings</a></li>
					<li><a accesskey="n" id="toggleDarkMode" href='#'></a></li>
					<li><a href="/pms/">Messages ({{pmCount}})</a></li>
					<li><a href="/logout/" class="last">Logout</a></li>
				</ul>
			</li>
		</ul>
	</div>
</header>

<div id="content"<?=isset($contentClasses)?' class="'.implode(' ', $contentClasses).'"':''?>><div class="bodyContainer">
	<div id="page_<?=PAGE_ID?>"<?=sizeof($bodyClasses)?' class="'.implode(' ', $bodyClasses).'"':''?><?=strlen($dispatchInfo['ngController'])?" ng-controller=\"{$dispatchInfo['ngController']}\"":''?>>
<?	} else { ?>
<div id="page_<?=PAGE_ID?>" class="<?=sizeof($bodyClasses)?implode(' ', $bodyClasses):''?>"<?=strlen($dispatchInfo['ngController'])?" ng-controller=\"{$dispatchInfo['ngController']}\"":''?>>
<?	} ?>
