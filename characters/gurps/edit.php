		<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM gurps_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
		if (isset($_GET['new'])) {
			foreach(array('st', 'dx', 'iq', 'ht', 'hp', 'will', 'per', 'fp') as $key) $charInfo[$key] = 10;
			$charInfo['speed'] = 5;
			$charInfo['move'] = 5;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/gurps.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/gurps/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div id="nameDiv" class="tr">
				<label class="textLabel">Name:</label>
				<input type="text" name="name" maxlength="50" value="<?=$charInfo['name']?>">
			</div>
			
			<div class="clearfix">
				<div id="stats">
					<div class="statCol">
						<label class="statRow" for="st">ST</label>
						<label class="statRow" for="dx">DX</label>
						<label class="statRow" for="iq">IQ</label>
						<label class="statRow" for="ht">HT</label>
					</div>
					<div class="statCol">
						<div class="statRow"><input type="text" id="st" name="st" maxlength="2" value="<?=$charInfo['st']?>"></div>
						<div class="statRow"><input type="text" id="dx" name="dx" maxlength="2" value="<?=$charInfo['dx']?>"></div>
						<div class="statRow"><input type="text" id="iq" name="iq" maxlength="2" value="<?=$charInfo['iq']?>"></div>
						<div class="statRow"><input type="text" id="ht" name="ht" maxlength="2" value="<?=$charInfo['ht']?>"></div>
					</div>
					<div class="statCol">
						<label class="statRow" for="hp">HP</label>
						<label class="statRow" for="will">Will</label>
						<label class="statRow" for="per">Per</label>
						<label class="statRow" for="fp">FP</label>
					</div>
					<div class="statCol">
						<div class="statRow"><input type="text" id="hp" name="hp" maxlength="2" value="<?=$charInfo['hp']?>"></div>
						<div class="statRow"><input type="text" id="will" name="will" maxlength="2" value="<?=$charInfo['will']?>"></div>
						<div class="statRow"><input type="text" id="per" name="per" maxlength="2" value="<?=$charInfo['per']?>"></div>
						<div class="statRow"><input type="text" id="fp" name="fp" maxlength="2" value="<?=$charInfo['fp']?>"></div>
					</div>
					<div class="statCol">
						<div class="statRow"><input type="text" id="hp_current" name="hp_current" maxlength="2" value="<?=$charInfo['hp_current']?>"></div>
						<div class="statRow blank">&nbsp;</div>
						<div class="statRow blank">&nbsp;</div>
						<div class="statRow"><input type="text" id="fp_current" name="fp_current" maxlength="2" value="<?=$charInfo['fp_current']?>"></div>
					</div>
					<div class="statCol largeCol">
						<label class="statRow" for="dmg_thr">Damage (Thrown)</label>
						<label class="statRow" for="dmg_sw">Damage (Swing)</label>
						<label class="statRow" for="speed">Speed</label>
						<label class="statRow" for="move">Move</label>
					</div>
					<div class="statCol">
						<div class="statRow"><input type="text" id="dmg_thr" name="dmg_thr" maxlength="2" value="<?=$charInfo['dmg_thr']?>"></div>
						<div class="statRow"><input type="text" id="dmg_sw" name="dmg_sw" maxlength="2" value="<?=$charInfo['dmg_sw']?>"></div>
						<div class="statRow"><input type="text" id="speed" name="speed" maxlength="5" value="<?=$charInfo['speed']?>"></div>
						<div class="statRow"><input type="text" id="move" name="move" maxlength="5" value="<?=$charInfo['move']?>"></div>
					</div>
				</div>
				
				<div id="langDiv" class="floatRight">
					<h2 class="headerbar hbDark">Languages</h2>
					<textarea name="languages" class="hbdMargined"><?=$charInfo['languages']?></textarea>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="twoCol floatLeft">
					<h2 class="headerbar hbDark">Advantages</h2>
					<textarea name="advantages" class="hbdMargined"><?=$charInfo['advantages']?></textarea>
				</div>
				
				<div class="twoCol floatRight">
					<h2 class="headerbar hbDark">Disadvantages</h2>
					<textarea name="disadvantages" class="hbdMargined"><?=$charInfo['disadvantages']?></textarea>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="twoCol floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<textarea name="skills" class="hbdMargined"><?=$charInfo['skills']?></textarea>
				</div>
				
				<div class="twoCol floatRight">
					<h2 class="headerbar hbDark">Items</h2>
					<textarea name="items" class="hbdMargined"><?=$charInfo['items']?></textarea>
				</div>
			</div>
			
			<div id="notesDiv">
				<h2 class="headerbar hbDark">Notes</h2>
				<textarea name="notes" class="hbdMargined"><?=$charInfo['notes']?></textarea>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>