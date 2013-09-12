<!DOCTYPE html>
<html>
<head>
<? require_once(FILEROOT.'/meta.php'); ?>

<? require_once(FILEROOT.'/styles/styles.php'); ?>

<? require_once(FILEROOT.'/javascript/js.php'); ?>
</head>

<body<?=(MODAL?' class="modal"':'')?>>
<? if (!MODAL) { ?>
<header><div class="bodyContainer">
	<a href="<?=SITEROOT?>/"><img id="logo" src="<?=SITEROOT?>/images/header_logo.png" alt="Gamers Plane Logo"></a>
	
	<div id="userMenu">
		<div id="userMenu_left"></div>
		<div id="userMenu_right"></div>
<? if (!$loggedIn) { ?>
		<a href="<?=SITEROOT?>/login" class="loginLink first">Login</a>
		<a href="<?=SITEROOT?>/register" class="last">Register</a>
<?
	} else {
		$numMessages = $mysql->query('SELECT COUNT(*) FROM pms WHERE recipientID = '.intval($_SESSION['userID']).' AND viewed = 0');
		$numNewMessages = $numMessages->fetchColumn();
?>
		<span id="menuMessage"><a href="<?=SITEROOT?>/ucp" class="username first"><?=$_SESSION['username']?></a></span>
		<a href="<?=SITEROOT?>/pms"><img src="<?=SITEROOT?>/images/envelope.jpg" title="Private Messages" alt="Private Messages"> (<?=$numNewMessages?>)</a>
		<a href="<?=SITEROOT?>/"><img src="<?=SITEROOT?>/images/exclamation.jpg" title="Notifications" alt="Notifications"></a>
		<a href="<?=SITEROOT?>/logout" class="last">Logout</a>
<? } ?>
	</div>
	
	<div id="followLinks">
		<a href="http://twitter.com/GamersPlane" target="_blank"><img src="<?=SITEROOT?>/images/bodyComponents/twitter.png" height="20"></a>
		<a href="https://www.facebook.com/pages/Gamers-Plane/245904792107862" target="_blank"><img src="<?=SITEROOT?>/images/bodyComponents/facebook.png" height="20"></a>
<!--		<script src="http://www.stumbleupon.com/hostedbadge.php?s=6"></script>-->
		<a href="http://www.stumbleupon.com/submit?url=http://gamersplane.com" target="_blank"><img src="<?=SITEROOT?>/images/bodyComponents/stumble.png" height="20"></a>
	</div>
	
	<div id="mainMenu">
		<ul id="mainMenu_left">
			<li><a href="<?=SITEROOT?>/tools" class="first">Tools</a></li>
<? if ($loggedIn) { ?>
			<li>
				<a href="<?=SITEROOT?>/characters">My Characters</a>
<?
		$characters = $mysql->query('SELECT c.characterID, c.label, s.shortName FROM characters c, systems s WHERE c.systemID = s.systemID AND c.userID = '.intval($_SESSION['userID']).' ORDER BY c.label');
		if ($characters->rowCount()) {
			echo "				<ul>\n";
			$count = 0;
			foreach ($characters as $character) {
				if ($count > 5) {
					echo "					".'<li><a href="'.SITEROOT.'/characters/my">All characters</a></li>'."\n";
					break;
				}
				echo "					".'<li><a href="'.SITEROOT.'/characters/'.$character['shortName'].'/'.$character['characterID'].'">'.$character['label'].'</a></li>'."\n";
				$count++;
			}
			echo "				</ul>\n";
		}
?>
			</li>
			<li>
				<a href="<?=SITEROOT?>/games">My Games</a>
<?
		$games = $mysql->query('SELECT g.gameID, g.title, p.isGM FROM games g INNER JOIN players p ON p.userID = '.intval($_SESSION['userID']).' AND p.gameID = g.gameID ORDER BY g.title');
		if ($games->rowCount()) {
			echo "				<ul>\n";
			$count = 0;
			foreach ($games as $game) {
				echo "					".'<li><a href="'.SITEROOT.'/games/'.$game['gameID'].'">'.$game['title'].($game['isGM']?' <img src="'.SITEROOT.'/images/gm_icon.png">':'').'</a></li>'."\n";
				$count++;
				if ($count == 5) {
					echo "					".'<li><a href="'.SITEROOT.'/games/my">All games</a></li>'."\n";
					break;
				}
			}
			echo "				</ul>\n";
		}
?>
			</li>
<? } ?>
			<li><a href="<?=SITEROOT?>/forums">Forums</a></li>
			<li><a href="<?=SITEROOT?>/contact" class="last">Contact Us</a></li>
		</ul>
<? if ($loggedIn) { ?>
		<ul id="mainMenu_right">
			<li><a href="<?=SITEROOT?>/gamersList" class="first">The Gamers</a></li>
		</ul>
<? } ?>
	</div>
</div></header>

<div id="content"><div class="bodyContainer clearfix">
	<div id="page_<?=PAGE_ID?>"<?=strlen($dispatchInfo['bodyClass'])?" class=\"{$dispatchInfo['bodyClass']}\"":''?>>
		<div id="stupidIE">
			<p>Hm... seems like you're using IE. Can I suggest a better browser, such as <a href="http://www.mozilla.com/en-US/firefox/" target="_blank">Firefox</a>, <a href="http://www.googlechrome.com/" target="_blank">Chrome</a> or <a href="http://www.opera.com/" target="_blank">Opera</a>? There are other choices too.</p>
			<p>If you wanna stick with IE, or can't switch, I'll warn you right now, while most of this site should work with IE, stuff might come up buggy, so you might not enjoy it as much...</p>
		</div>
<? } else { ?>
<div id="page_<?=PAGE_ID?>" class="clearfix">
<? } ?>
