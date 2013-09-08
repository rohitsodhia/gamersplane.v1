<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT gurps.*, characters.userID, gms.gameID IS NOT NULL isGM FROM gurps_characters gurps INNER JOIN characters ON gurps.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE gurps.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/gurps.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<a href="<?=SITEROOT?>/characters/gurps/<?=$characterID?>/edit">Edit Character</a>
		
		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=printReady($charInfo['name'])?></div>
		</div>
		
		<div id="stats">
			<div class="statCol">
				<label class="statRow" for="st">ST</label>
				<label class="statRow" for="dx">DX</label>
				<label class="statRow" for="iq">IQ</label>
				<label class="statRow" for="ht">HT</label>
			</div>
			<div class="statCol">
				<div class="statRow"><?=$charInfo['st']?></div>
				<div class="statRow"><?=$charInfo['dx']?></div>
				<div class="statRow"><?=$charInfo['iq']?></div>
				<div class="statRow"><?=$charInfo['ht']?></div>
			</div>
			<div class="statCol">
				<label class="statRow" for="hp">HP</label>
				<label class="statRow" for="will">Will</label>
				<label class="statRow" for="per">Per</label>
				<label class="statRow" for="fp">FP</label>
			</div>
			<div class="statCol">
				<div class="statRow"><?=$charInfo['hp']?></div>
				<div class="statRow"><?=$charInfo['will']?></div>
				<div class="statRow"><?=$charInfo['per']?></div>
				<div class="statRow"><?=$charInfo['fp']?></div>
			</div>
			<div class="statCol">
				<div class="statRow"><?=$charInfo['hp_current']?></div>
				<div class="statRow blank">&nbsp;</div>
				<div class="statRow blank">&nbsp;</div>
				<div class="statRow"><?=$charInfo['fp_current']?></div>
			</div>
			<div class="statCol largeCol">
				<label class="statRow" for="dmg_thr">Damage (Thrown)</label>
				<label class="statRow" for="dmg_sw">Damage (Swing)</label>
				<label class="statRow" for="speed">Speed</label>
				<label class="statRow" for="move">Move</label>
			</div>
			<div class="statCol">
				<div class="statRow"><?=$charInfo['dmg_thr']?></div>
				<div class="statRow"><?=$charInfo['dmg_sw']?></div>
				<div class="statRow"><?=$charInfo['speed']?></div>
				<div class="statRow"><?=$charInfo['move']?></div>
			</div>
		</div>
		
		<div id="langDiv">
			<h2>Languages</h2>
			<p><?=printReady($charInfo['languages'])?></p>
		</div>
		
		<br class="clear">
		<div class="twoCol floatLeft">
			<h2>Advantages</h2>
			<p><?=printReady($charInfo['advantages'])?></p>
		</div>
		
		<div class="twoCol floatRight">
			<h2>Disadvantages</h2>
			<p><?=printReady($charInfo['disadvantages'])?></p>
		</div>
		
		<br class="clear">
		<div class="twoCol floatLeft">
			<h2>Skills</h2>
			<p><?=printReady($charInfo['skills'])?></p>
		</div>
		
		<div class="twoCol floatRight">
			<h2>Items</h2>
			<p><?=printReady($charInfo['items'])?></p>
		</div>
		
		<br class="clear">
		<div id="notesDiv">
			<h2 class="marginTop">Notes</h2>
			<p><?=printReady($charInfo['notes'])?></p>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>