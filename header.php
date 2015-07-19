<!DOCTYPE html>
<html>
<head prefix="og: http://ogp.me/ns#">
<?	require_once(FILEROOT.'/meta.php'); ?>

<?	require_once(FILEROOT.'/styles/styles.php'); ?>
</head>

<body<?=(MODAL?' class="modal"':'')?> data-modal-width="<?=$dispatchInfo['modalWidth']?>"<?=strlen($dispatchInfo['ngController'])?' ng-app="gamersplane"':''?>>
<?	if (!MODAL) { ?>
<header class="bodyHeader"><div class="bodyContainer">
	<a href="/"><img id="logo" src="/images/bodyComponents/logo.png" alt="Gamers Plane Logo"></a>
	
	<div id="userMenu">
		<div id="userMenu_left"></div>
		<div id="userMenu_right"></div>
<?		if (!$loggedIn) { ?>
		<a href="/login/" class="loginLink first">Login</a>
		<a href="/register/" class="last">Register</a>
<?
		} else {
			$pmCount = $mongo->pms->find(array('recipients.userID' => $currentUser->userID, 'recipients.read' => false))->count();
?>
		<span id="menuMessage"><a href="/ucp/" class="username first"><?=$currentUser->username?></a></span>
		<a href="/pms/"><img src="/images/envelope.jpg" title="Private Messages" alt="Private Messages"> (<?=$pmCount?>)</a>
		<a href="/notifications/"><img src="/images/bodyComponents/exclamation.jpg" title="Notifications" alt="Notifications"></a>
		<a href="/logout/" class="last">Logout</a>
<?		} ?>
	</div>
	
	<div id="followLinks">
		<a id="fl_twitter" href="http://twitter.com/GamersPlane" target="_blank" title="Twitter"></a>
		<a id="fl_facebook" href="https://www.facebook.com/pages/Gamers-Plane/245904792107862" target="_blank" title="Facebook"></a>
		<a id="fl_stumbleupon" href="http://www.stumbleupon.com/submit?url=http://gamersplane.com" target="_blank" title="StumbleUpon"></a>
		<a id="fl_twitch" href="http://www.twitch.tv/gamersplane" target="_blank" title="Twitch"></a>
	</div>
	
	<div id="mainMenu">
		<ul id="mainMenu_left">
			<li>
				<a href="/tools/" class="first">Tools</a>
				<ul>
					<li><a href="/tools/dice/">Dice</a></li>
					<li><a href="/tools/cards/">Cards</a></li>
					<li><a href="/tools/music/">Music</a></li>
				</ul>
			</li>
			<li><a href="/systems/">Systems</a></li>
<?		if ($loggedIn) { ?>
			<li>
				<a href="/characters/my/">Characters</a>
<?
			$header_characters = $mysql->query("SELECT characterID, label, system FROM characters WHERE userID = {$currentUser->userID} ORDER BY label");
			if ($header_characters->rowCount()) {
				echo "				<ul>\n";
				$count = 0;
				foreach ($header_characters as $hCharacter) {
					if ($count > 5) {
						echo "					".'<li><a href="/characters/my/">All characters</a></li>'."\n";
						break;
					}
					echo "					".'<li><a href="/characters/'.$hCharacter['system'].'/'.$hCharacter['characterID'].'/">'.$hCharacter['label'].'</a></li>'."\n";
					$count++;
				}
				echo "				</ul>\n";
			}
	?>
			</li>
			<li>
				<a href="/games/">Games</a>
<?
			$header_games = $mysql->query("SELECT g.gameID, g.title, p.isGM FROM games g INNER JOIN players p ON p.userID = {$currentUser->userID} AND p.gameID = g.gameID AND g.retired IS NULL ORDER BY g.title");
			if ($header_games->rowCount()) {
				echo "				<ul>\n";
				$count = 0;
				foreach ($header_games as $game) {
					echo "					".'<li><a href="/games/'.$game['gameID'].'/">'.$game['title'].($game['isGM']?' <img src="/images/gm_icon.png">':'').'</a></li>'."\n";
					$count++;
					if ($count == 5) {
						echo "					".'<li><a href="/games/my/">All games</a></li>'."\n";
						break;
					}
				}
				echo "				</ul>\n";
			}
?>
			</li>
<?		} ?>
			<li><a href="/forums/">Forums</a></li>
<?		if ($loggedIn) { ?>
			<li><a href="/gamersList/">The Gamers</a></li>
<?		} ?>
			<li><a href="/links/">Links</a></li>
		</ul>
		<ul id="mainMenu_right">
			<li><a href="/faqs/" class="first">FAQs</a></li>
			<li><a href="/contact/">Contact Us</a></li>
		</ul>
	</div>
</div></header>

<div id="content"><div class="bodyContainer clearfix">
	<div id="page_<?=PAGE_ID?>"<?=strlen($dispatchInfo['bodyClass'])?' class="'.implode(' ', $bodyClasses).'"':''?><?=strlen($dispatchInfo['ngController'])?" ng-controller=\"{$dispatchInfo['ngController']}\"":''?>>
		<div id="stupidIE">
			<p>Hm... seems like you're using IE. Can I suggest a better browser, such as <a href="http://www.mozilla.com/en-US/firefox/" target="_blank">Firefox</a>, <a href="http://www.googlechrome.com/" target="_blank">Chrome</a> or <a href="http://www.opera.com/" target="_blank">Opera</a>? There are other choices too.</p>
			<p>If you wanna stick with IE, or can't switch, I'll warn you right now, while most of this site should work with IE, stuff might come up buggy, so you might not enjoy it as much...</p>
		</div>
<?	} else { ?>
<div id="page_<?=PAGE_ID?>" class="clearfix<?=sizeof($bodyClasses)?' '.implode(' ', $bodyClasses):''?>"<?=strlen($dispatchInfo['ngController'])?" ng-controller=\"{$dispatchInfo['ngController']}\"":''?>>
<?	} ?>
