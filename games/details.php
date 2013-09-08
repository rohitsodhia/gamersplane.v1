<?
	$loggedIn = checkLogin(0);
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	
	$gameInfo = $mysql->query("SELECT g.gameID, g.open, g.title, g.systemID, s.shortName systemShort, s.fullName systemFull, g.created, g.postFrequency, g.numPlayers, g.description, g.charGenInfo, g.forumID, g.start, g.gmID, users.username, gms.primary IS NOT NULL isGM, gms.primary FROM games g INNER JOIN users ON g.gmID = users.userID INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE gms.userID = $userID) gms ON g.gameID = gms.gameID WHERE g.gameID = $gameID");
	if ($gameInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/games/list'); exit; }
	$gameInfo = $gameInfo->fetch();
	$gameInfo['created'] = switchTimezone($_SESSION['timezone'], $gameInfo['created']);

	$postFrequency = explode('/', $gameInfo['postFrequency']);
	$isGM = $gameInfo['isGM']?TRUE:FALSE;
	
	if (!$isGM) {
		$charCheck = $mysql->query('SELECT characterID, label, approved FROM characters WHERE gameID = '.$gameInfo['gameID'].' AND userID = '.$userID);
		
		$isInGame = $charCheck->rowCount()?TRUE:FALSE;
		if ($isInGame) $userCharInfo = $charCheck->fetch();
	}
	
	$approvedPlayers = $mysql->query("SELECT characters.characterID, characters.label, characters.approved, users.userID, users.username FROM characters, users WHERE characters.gameID = $gameID AND users.userID = characters.userID AND characters.approved = 1 ORDER BY characters.approved DESC, characters.label ASC");
	$numPlayersActive = $approvedPlayers->rowCount();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Game Details<?=$gameInfo['isGM']?' <a href="'.SITEROOT.'/games/'.$gameInfo['gameID'].'/edit">[ EDIT ]</a>':''?></h1>
		
<? if ($_GET['submitted'] || $_GET['wrongSystem'] || $_GET['approveError']) { ?>
		<div class="alertBox_error"><ul>
<?
		if ($_GET['submitted']) { echo "\t\t\t<li>You already submitted that character to a game.</li>\n"; }
		if ($_GET['wrongSystem']) { echo "\t\t\t<li>That character isn't made for this game.</li>\n"; }
		if ($_GET['approveError']) { echo "\t\t\t<li>There was an issue approving the character.</li>\n"; }
?>
		</ul></div>
<? } if ($_GET['removed'] || $_GET['gmAdded'] || $_GET['gmRemoved']) { ?>
		<div class="alertBox_success"><ul>
<?
		if ($_GET['gmAdded']) echo "\t\t\t<li>GM successfully added.</li>\n";
		if ($_GET['gmRemoved']) echo "\t\t\t<li>GM successfully removed.</li>\n";
		if ($_GET['removed']) { echo "\t\t\t<li>Character successfully removed from game.</li>\n"; }
?>
		</ul></div>
<? } ?>
		
		<div class="tr">
			<label>Game Status</label>
			<div><?=$gameInfo['open']?'Open':'Closed'?> <a id="changeStatus" href="<?=SITEROOT.'/games/changeStatus/'.$gameID?>">[ Change ]</a></div>
		</div>
		<div class="tr">
			<label>Game Title</label>
			<div><?=printReady($gameInfo['title'])?></div>
		</div>
		<div class="tr">
			<label>System</label>
			<div><?=printReady($gameInfo['systemFull'])?></div>
		</div>
		<div class="tr">
			<label>Game Master</label>
			<div><a href="<?=SITEROOT.'/ucp/'.$gameInfo['gmID']?>" class="username"><?=$gameInfo['username']?></a></div>
		</div>
		<div class="tr">
			<label>Created</label>
			<div><?=date('F j, Y g:i a', $gameInfo['created'])?></div>
		</div>
		<div class="tr">
			<label>Post Frequency</label>
			<div><?=$postFrequency[0].' post'.($postFrequency[0] == 1?'':'s').' per '.($postFrequency[1] == 'd'?'day':'week')?></div>
		</div>
		<div class="tr">
			<label>Number of Players</label>
			<div><?=$numPlayersActive.' / '.$gameInfo['numPlayers']?></div>
		</div>
		<div class="tr">
			<label>Description</label>
			<div><?=$gameInfo['description']?printReady($gameInfo['description']):'None Provided'?></div>
		</div>
		<div class="tr">
			<label>Character Generation Info</label>
			<div><?=$gameInfo['charGenInfo']?printReady($gameInfo['charGenInfo']):'None Provided'?></div>
		</div>
<?
	if ($loggedIn) {
		if ($isGM || $isInGame && $userCharInfo['approved']) {
			echo "\t\t<div id=\"userLists\">\n";
			if ($isGM) {
				$gms = $mysql->query("SELECT gms.userID, users.username, gms.primary FROM gms, users WHERE gms.userID = users.userID AND gms.gameID = $gameID AND gms.primary = 0 ORDER BY users.username");
				echo "\t\t\t<h3>GMs</h3>\n";
				echo "\t\t\t<a id=\"addGM\" href=\"".SITEROOT."/games/$gameID/addGM\">Add a GM</a>\n";
				$first = TRUE;
				if ($gms->rowCount()) { foreach ($gms as $gmInfo) {
					echo "\t\t\t<div class=\"tr".($first?' firstTR':'')."\">\n";
					echo "\t\t\t\t<div class=\"gm\"><a href=\"".SITEROOT."/ucp/{$gmInfo['userID']}\" class=\"username\">{$gmInfo['username']}</a></div>\n";
					echo "\t\t\t\t<div class=\"gmLinks\">\n";
					if ($gameInfo['primary']) echo "\t\t\t\t\t<a href=\"".SITEROOT."/games/$gameID/removeGM/{$gmInfo['userID']}\" class=\"removeGM\">Remove GM</a>\n";
					else "\t\t\t\t\t&nbsp;\n";
					echo "\t\t\t\t</div>\n";
					echo "\t\t\t</div>\n";
					if ($first) { $first = FALSE; }
				} } else echo "\t\t\t<h2>No Other GMs</h2>\n";
			}
			
			echo "\t\t\t<h3>Characters in game</h3>\n";
			if ($numPlayersActive) { foreach ($approvedPlayers as $charInfo) {
				echo "\t\t\t<div class=\"tr\">\n";
				echo "\t\t\t\t".(($isGM || $userID == $charInfo['userID'])?'<a href="'.SITEROOT.'/characters/'.$gameInfo['systemShort'].'/'.$charInfo['characterID'].'/sheet"':'<div').' class="charTitle">'.$charInfo['label'].(($isGM || $userID == $charInfo['userID'])?"</a>\n":"</div>\n");
				echo "\t\t\t\t".'<div class="player"><a href="'.SITEROOT.'/ucp/'.$charInfo['userID'].'" class="username">'.$charInfo['username']."</a></div>\n";
				echo "\t\t\t\t<div class=\"charLinks\">\n";
				if ($isGM) echo "\t\t\t\t\t".'<a href="'.SITEROOT.'/games/'.$gameID.'/remove/'.$charInfo['characterID'].'" class="removeChar">Remove Character from Game</a>';
				elseif ($userID == $charInfo['userID']) echo "\t\t\t\t\t".'<a href="'.SITEROOT.'/games/'.$gameID.'/leave/'.$charInfo['characterID'].'">Leave Game</a>';
				else echo "\t\t\t\t\t&nbsp;\n";
				echo "\t\t\t\t</div>\n";
				echo "\t\t\t</div>\n";
			} } else echo "\t\t\t<h2>No Characters Joined Yet!</h2>\n";
			
			if ($isGM) {
				$waitingChars = $mysql->query("SELECT characters.characterID, characters.label, characters.approved, users.userID, users.username FROM characters, users WHERE characters.gameID = $gameID AND users.userID = characters.userID AND characters.approved = 0 ORDER BY characters.approved DESC, characters.label ASC");
				echo "\t\t\t<h3 id=\"waitingChars\">Characters awaiting approval</h3>\n";
				if ($waitingChars->rowCount()) { foreach ($waitingChars as $charInfo) {
					echo "\t\t\t<div class=\"tr\">\n";
					echo "\t\t\t\t".(($isGM || $userID == $charInfo['userID'])?'<a href="'.SITEROOT.'/characters/'.$gameInfo['systemShort'].'/'.$charInfo['characterID'].'/sheet"':'<div').' class="charTitle">'.$charInfo['label'].(($isGM || $userID == $charInfo['userID'])?"</a>\n":"</div>\n");
					echo "\t\t\t\t".'<div class="player"><a href="'.SITEROOT.'/ucp/'.$charInfo['userID'].'" class="username">'.$charInfo['username']."</a></div>\n";
					echo "\t\t\t\t<div class=\"charLinks\">\n";
					echo "\t\t\t\t\t".'<a href="'.SITEROOT.'/games/'.$gameID.'/approve/'.$charInfo['characterID'].'" class="approveChar">Approve Character</a>';
					echo "\t\t\t\t\t".'<a href="'.SITEROOT.'/games/'.$gameID.'/remove/'.$charInfo['characterID'].'" class="removeChar">Remove Character from Game</a>';
					echo "\t\t\t\t</div>\n";
					echo "\t\t\t</div>\n";
				} } else echo "\t\t\t<h2>No Characters Awaiting Approval.</h2>\n";
			}
			echo "\t\t</div>\n";
		} elseif ($isInGame) echo "\t\t".'<div id="charList"><p><a href="'.SITEROOT.'/characters/'.$gameInfo['systemShort'].'/'.$userCharInfo['characterID'].'/sheet">'.$userCharInfo['label']."</a> is awaiting approval.</p></div>\n";
		elseif (!$isGM && !$isInGame && $numPlayersActive < $gameInfo['numPlayers']) {
?>
		<div id="submitChar">
<?
			$readyChars = $mysql->query('SELECT characterID, label FROM characters WHERE userID = '.$userID.' AND systemID = "'.$gameInfo['systemID'].'" AND ISNULL(gameID)');
			if ($readyChars->rowCount()) {
?>
			<h3>Submit a Character</h3>
			<form method="post" action="<?=SITEROOT?>/games/process/join">
				<input type="hidden" name="gameID" value="<?=$gameID?>">
				<select name="characterID">
<?
				foreach ($readyChars as $charInfo) {
					echo "\t\t\t\t\t".'<option value="'.$charInfo['characterID'].'">'.$charInfo['label']."</option>\n";
				}
?>
				</select><br>
				<button type="submit" name="submitCharacter" class="btn_submitCharacter"></button>
			</form>
<?	 		} else { ?>
			<h2>You have no characters you can submit at this time.</h2>
<? 			} ?>
		</div>
<?
		}
	} else echo "			".'<h2>Interested in this game? <a href="'.SITEROOT.'/login" class="loginLink">Login</a> or <a href="'.SITEROOT.'/register" class="last">Register</a> to join!</h2>'."\n";
?>
<? require_once(FILEROOT.'/footer.php'); ?>