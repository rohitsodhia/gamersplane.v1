<!DOCTYPE html>
<html>
<head>
<? require_once(FILEROOT.'/meta.php'); ?>

<? require_once(FILEROOT.'/styles/styles.php'); ?>
</head>

<body<?=(MODAL?' class="modal"':'')?> data-modal-width="<?=$dispatchInfo['modalWidth']?>">
<? if (!MODAL) { ?>
<header class="bodyHeader"><div class="bodyContainer">
	<a href="/"><img id="logo" src="/images/bodyComponents/logo.png" alt="Gamers Plane Logo"></a>
	
	<div id="userMenu">
		<div id="userMenu_left"></div>
		<div id="userMenu_right"></div>
<? if (!$loggedIn) { ?>
		<a href="/login/" class="loginLink first">Login</a>
		<a href="/register/" class="last">Register</a>
<?
	} else {
		$header_numMessages = $mysql->query('SELECT COUNT(*) FROM pms WHERE recipientID = '.intval($_SESSION['userID']).' AND viewed = 0');
		$header_numNewMessages = $header_numMessages->fetchColumn();
?>
		<span id="menuMessage"><a href="/ucp/" class="username first"><?=$_SESSION['username']?></a></span>
		<a href="/pms/"><img src="/images/envelope.jpg" title="Private Messages" alt="Private Messages"> (<?=$header_numNewMessages?>)</a>
		<a href="/logout/" class="last">Logout</a>
<? } ?>
	</div>
	
	<div id="followLinks">
		<a id="fl_twitter" href="http://twitter.com/GamersPlane" target="_blank" title="Twitter"></a>
		<a id="fl_facebook" href="https://www.facebook.com/pages/Gamers-Plane/245904792107862" target="_blank" title="Facebook"></a>
		<a id="fl_stumbleupon" href="http://www.stumbleupon.com/submit?url=http://gamersplane.com" target="_blank" title="StumbleUpon"></a>
		<a id="fl_twitch" href="http://www.twitch.tv/gamersplane" target="_blank" title="Twitch"></a>
	</div>
	
	<div id="mainMenu">
		<ul id="mainMenu_left">
			<li><a href="/tools/" class="first">Tools</a></li>
<? if ($loggedIn) { ?>
			<li>
				<a href="/characters/my/">Characters</a>
<?
		$header_characters = $mysql->query('SELECT c.characterID, c.label, s.shortName FROM characters c, systems s WHERE c.systemID = s.systemID AND c.userID = '.intval($_SESSION['userID']).' ORDER BY c.label');
		if ($header_characters->rowCount()) {
			echo "				<ul>\n";
			$count = 0;
			foreach ($header_characters as $hCharacter) {
				if ($count > 5) {
					echo "					".'<li><a href="/characters/my/">All characters</a></li>'."\n";
					break;
				}
				echo "					".'<li><a href="/characters/'.$hCharacter['shortName'].'/'.$hCharacter['characterID'].'/">'.$hCharacter['label'].'</a></li>'."\n";
				$count++;
			}
			echo "				</ul>\n";
		}
?>
			</li>
			<li>
				<a href="/games/">Games</a>
<?
		$header_games = $mysql->query('SELECT g.gameID, g.title, p.isGM FROM games g INNER JOIN players p ON p.userID = '.intval($_SESSION['userID']).' AND p.gameID = g.gameID ORDER BY g.title');
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
<? } ?>
			<li><a href="/forums/">Forums</a></li>
			<li><a href="/contact/" class="last">Contact Us</a></li>
		</ul>
<? if ($loggedIn) { ?>
		<ul id="mainMenu_right">
			<li><a href="/gamersList/" class="first">The Gamers</a></li>
		</ul>
<? } ?>
	</div>
</div></header>

<div id="content"><div class="bodyContainer clearfix">
	<div id="page_<?=PAGE_ID?>"<?=strlen($dispatchInfo['bodyClass'])?' class="'.implode(' ', $bodyClasses).'"':''?>>
		<div id="stupidIE">
			<p>Hm... seems like you're using IE. Can I suggest a better browser, such as <a href="http://www.mozilla.com/en-US/firefox/" target="_blank">Firefox</a>, <a href="http://www.googlechrome.com/" target="_blank">Chrome</a> or <a href="http://www.opera.com/" target="_blank">Opera</a>? There are other choices too.</p>
			<p>If you wanna stick with IE, or can't switch, I'll warn you right now, while most of this site should work with IE, stuff might come up buggy, so you might not enjoy it as much...</p>
		</div>
		<div style="background: #FCC; border: 2px solid #F88; padding: 10px; margin-bottom: 10px;">
			<p style="margin-left: 0;"><span style="font-weight: bold; font-size: 1.5em;">Warning!</span></p>
			<p style="margin-left: 0;">If you've been on the site the last day, you noticed things were broken. I think I've got it all working again, but please, if you find something not working, post a thread in the bugs section of the forums or contact me. If you have experience with Apache or know someone who does, please have them get in touch with me on Twitter @GamersPlane or by email at <a href="mailto:contact@gamersplane.com">contact@gamersplane.com</a>. I'd love to get help to make sure I actually fixed it.</p>
		</div>
<? } else { ?>
<div id="page_<?=PAGE_ID?>" class="clearfix<?=sizeof($bodyClasses)?' '.implode(' ', $bodyClasses):''?>">
<? } ?>