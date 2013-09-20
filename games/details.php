<?
	$loggedIn = checkLogin(0);
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	
	$gameInfo = $mysql->query("SELECT g.gameID, g.open, g.title, g.systemID, s.shortName systemShort, s.fullName systemFull, g.created, g.postFrequency, g.numPlayers, g.charsPerPlayer, g.description, g.charGenInfo, g.forumID, g.start, g.gmID, u.username, gms.primaryGM IS NOT NULL isGM FROM games g INNER JOIN users u ON g.gmID = u.userID INNER JOIN systems s ON g.systemID = s.systemID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON g.gameID = gms.gameID WHERE g.gameID = $gameID");
	if ($gameInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/games/list'); exit; }
	$gameInfo = $gameInfo->fetch();
	$gameInfo['created'] = switchTimezone($_SESSION['timezone'], $gameInfo['created']);

	$postFrequency = explode('/', $gameInfo['postFrequency']);
	$isGM = $gameInfo['isGM']?TRUE:FALSE;
	
	if (!$isGM) {
		$userCheck = $mysql->query('SELECT approved FROM players WHERE gameID = '.$gameInfo['gameID'].' AND userID = '.$userID);
		if ($userCheck->rowCount()) {
			$inGame = TRUE;
			$approved = $userCheck->fetchColumn();
		} else $inGame = FALSE;
	} else {
		$inGame = TRUE;
		$approved = TRUE;
	}
	
	$approvedPlayers = $mysql->query("SELECT u.userID, u.username, p.isGM, p.primaryGM FROM users u, players p WHERE p.gameID = $gameID AND u.userID = p.userID AND p.approved = 1 ORDER BY u.username ASC");

	$characters = array();
	foreach ($mysql->query('SELECT characterID, userID, label, approved FROM characters WHERE gameID = '.$gameID) as $character) $characters[$character['userID']][] = $character;
	$playerApprovedChars = $mysql->query('SELECT COUNT(characterID) numChars FROM characters WHERE gameID = '.$gameID.' AND approved = 1');
	$playerApprovedChars = $playerApprovedChars->fetchColumn();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Game Details<?=$gameInfo['isGM']?' <a href="'.SITEROOT.'/games/'.$gameInfo['gameID'].'/edit">[ EDIT ]</a>':''?></h1>
		
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
		if ($_GET['removed']) echo "\t\t\t<li>Character successfully removed from game.</li>\n";
?>
		</ul></div>
<? } ?>
		
		<div id="details">
			<div class="tr clearfix">
				<label>Game Status</label>
<? if ($isGM) { ?>
				<div><?=$gameInfo['open']?'Open':'Closed'?> <a id="changeStatus" href="<?=SITEROOT.'/games/changeStatus/'.$gameID?>">[ Change ]</a></div>
<? } else { ?>
				<div><?=$gameInfo['open']?'Open':'Closed'?></div>
<? } ?>
			</div>
			<div class="tr clearfix">
				<label>Game Title</label>
				<div><?=printReady($gameInfo['title'])?></div>
			</div>
			<div class="tr clearfix">
				<label>System</label>
				<div><?=printReady($gameInfo['systemFull'])?></div>
			</div>
			<div class="tr clearfix">
				<label>Game Master</label>
				<div><a href="<?=SITEROOT.'/user/'.$gameInfo['gmID']?>" class="username"><?=$gameInfo['username']?></a></div>
			</div>
			<div class="tr clearfix">
				<label>Created</label>
				<div><?=date('F j, Y g:i a', $gameInfo['created'])?></div>
			</div>
			<div class="tr clearfix">
				<label>Post Frequency</label>
				<div><?=$postFrequency[0].' post'.($postFrequency[0] == 1?'':'s').' per '.($postFrequency[1] == 'd'?'day':'week')?></div>
			</div>
			<div class="tr clearfix">
				<label>Number of Players</label>
				<div><?=($approvedPlayers->rowCount() - 1).' / '.$gameInfo['numPlayers']?></div>
			</div>
			<div class="tr clearfix">
				<label>Number of Characters per Player</label>
				<div><?=$gameInfo['charsPerPlayer']?></div>
			</div>
			<div class="tr clearfix">
				<label>Description</label>
				<div><?=$gameInfo['description']?printReady($gameInfo['description']):'None Provided'?></div>
			</div>
			<div class="tr clearfix">
				<label>Character Generation Info</label>
				<div><?=$gameInfo['charGenInfo']?printReady($gameInfo['charGenInfo']):'None Provided'?></div>
			</div>
		</div>

<?
	if ($loggedIn) {
		$hasRightCol = FALSE;
?>
		<div class="clearfix">
<?
		if ($inGame && $approved) {
			$hasRightCol = TRUE;
?>
			<div class="rightCol">
				<div id="submitChar">
					<h2 class="headerbar hbDark">Submit a Character</h2>
<?
			if ($playerApprovedChars < $gameInfo['charsPerPlayer']) {
				$readyChars = $mysql->query('SELECT characterID, label FROM characters WHERE userID = '.$userID.' AND systemID = '.$gameInfo['systemID'].' AND ISNULL(gameID)');
				if ($readyChars->rowCount()) {
?>
					<form method="post" action="<?=SITEROOT?>/games/process/addCharacter" class="hbdMargined">
						<input type="hidden" name="gameID" value="<?=$gameID?>">
						<select name="characterID">
<?
					foreach ($readyChars as $charInfo) {
						echo "\t\t\t\t\t\t\t".'<option value="'.$charInfo['characterID'].'">'.$charInfo['label']."</option>\n";
					}
?>
						</select>
						<div><button type="submit" name="submitCharacter" class="fancyButton">Submit</button></div>
					</form>
<?	 			} else { ?>
					<p class="hbMargined notice">You have no characters you can submit at this time.</p>
<?
	 			}
	 		} else {
?>
					<p class="hbMargined notice">You cannot submit any more characters to this game.</p>
<?			} ?>
				</div>
			</div>
<?
		} elseif (!$inGame) {
			$hasRightCol = TRUE;
?>
			<div class="rightCol">
				<div id="applyToGame">
					<h2 class="headerbar hbDark">Join Game</h2>
					<p class="hbdMargined notice">Your request to join this game is awaiting approval.</p>
				</div>
			</div>
<?
		} elseif (!$inGame && $loggedIn && $approvedPlayers->rowCount() - 1 < $gameInfo['numPlayers']) {
			$hasRightCol = TRUE;
?>
			<div class="rightCol">
				<div id="applyToGame">
					<h2 class="headerbar hbDark">Join Game</h2>
					<form method="post" action="<?=SITEROOT?>/games/process/join" class="alignCenter">
						<input type="hidden" name="gameID" value="<?=$gameID?>">
						<button type="submit" name="apply" class="fancyButton">Apply to Game</button>
					</form>
				</div>
			</div>
<?
		}
?>
			
			<div<?=$hasRightCol?' class="leftCol"':''?>>
				<h2 class="headerbar hbDark hb_hasList">Players in Game</h2>
				<ul id="playersInGame" class="hbdMargined hbAttachedList">
<?
	foreach ($approvedPlayers as $playerInfo) {
?>
					<li id="userID_<?=$playerInfo['userID']?>"<?=sizeof($characters[$playerInfo['userID']])?' class="hasChars"':''?>>
						<div class="playerInfo clearfix">
							<div class="player"><a href="<?=SITEROOT.'/user/'.$playerInfo['userID']?>" class="username"><?=$playerInfo['username']?></a><?=$playerInfo['isGM']?' <img src="'.SITEROOT.'/images/gm_icon.png">':''?></div>
							<div class="actionLinks">
<?		if ($isGM && !$playerInfo['primaryGM']) { ?>
								<a href="<?=SITEROOT.'/games/'.$gameID.'/removePlayer/'.$playerInfo['userID']?>" class="removePlayer">Remove player from Game</a>
								<a href="<?=SITEROOT.'/games/'.$gameID.'/toggleGM/'.$playerInfo['userID']?>" class="toggleGM"><?=$playerInfo['isGM']?'Remove as GM':'Make GM'?></a>
<?		} elseif ($playerInfo['userID'] == $userID && !$playerInfo['primaryGM']) { ?>
								<a href="<?=SITEROOT.'/games/'.$gameID.'/leaveGame/'.$playerInfo['userID']?>" class="leaveGame">Leave Game</a>
<?		} ?>
							</div>
						</div>
<?		if (sizeof($characters[$playerInfo['userID']])) { ?>
						<ul class="characters">
<?			foreach ($characters[$playerInfo['userID']] as $character) { ?>
							<li class="clearfix">
								<div class="charLabel"><?=(($isGM || $userID == $playerInfo['userID'])?'<a href="'.SITEROOT.'/characters/'.$gameInfo['systemShort'].'/'.$character['characterID'].'/sheet"':'<div').'>'.$character['label'].(($isGM || $userID == $playerInfo['userID'])?"</a>\n":"</div>\n"); ?></div>
								<div class="actionLinks">
<?				if ($isGM && !$character['approved']) { ?>
									<a href="<?=SITEROOT.'/games/'.$gameID.'/approveChar/'.$character['characterID']?>" class="approveChar">Approve Character</a>
<?
				}
				if ($isGM || $character['userID'] == $userID) {
?>
									<a href="<?=SITEROOT.'/games/'.$gameID.'/removeChar/'.$character['characterID']?>" class="removeChar"><?=$character['userID'] == $userID?'Withdraw':'Remove'?> Character</a>
<?				} ?>
								</div>
							</li>
<?			} ?>
						</ul>
<?		} ?>
					</li>
<? 	} ?>
				</ul>

<?
	if ($isGM) {
		$waitingPlayers = $mysql->query("SELECT u.userID, u.username FROM users u, players p WHERE p.gameID = $gameID AND u.userID = p.userID AND p.approved = 0 ORDER BY u.username ASC");
		if ($waitingPlayers->rowCount()) {
?>
				<h2 id="waitingChars" class="headerbar hbDark hb_hasList">Players awaiting approval</h2>
				<ul id="waitingPlayers" class="hbAttachedList hbdMargined">
<?			foreach ($waitingPlayers as $playerInfo) { ?>
					<li id="userID_<?=$playerInfo['userID']?>" class="playerInfo clearfix">
						<div class="player"><a href="<?=SITEROOT.'/user/'.$playerInfo['userID']?>" class="username"><?=$playerInfo['username']?></a></div>
						<div class="actionLinks">
							<a href="<?=SITEROOT.'/games/'.$gameID.'/approvePlayer/'.$playerInfo['userID']?>" class="approvePlayer">Approve Player</a>
							<a href="<?=SITEROOT.'/games/'.$gameID.'/rejectPlayer/'.$playerInfo['userID']?>" class="rejectPlayer">Reject Player</a>
						</div>
					</li>
<?			} ?>
				</ul>
<?
		}
	}
?>
			</div>
		</div>
<?
	} else echo "		".'<div id="loggedOutNotice">Interested in this game? <a href="'.SITEROOT.'/login" class="loginLink">Login</a> or <a href="'.SITEROOT.'/register" class="last">Register</a> to join!</div>'."\n";

?>
<? require_once(FILEROOT.'/footer.php'); ?>