<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT deadlands.*, characters.userID, gms.gameID IS NOT NULL isGM FROM deadlands_characters deadlands INNER JOIN characters ON deadlands.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE deadlands.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$charInfo['wounds'] = explode(',', $charInfo['wounds']);
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/deadlands.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/deadlands/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div id="nameDiv" class="tr">
				<label class="textLabel">Name:</label>
				<div><input type="text" name="name" maxlength="50" value="<?=$charInfo['name']?>"></div>
			</div>
			
			<div class="triCol">
				<h2>Mental</h2>
				<div class="statDiv firstStatDiv">
					<input type="text" name="cogNumDice" maxlength="2" class="numDice" value="<?=$charInfo['cogNumDice']?>"> d <input type="text" name="cogDieType" class="dieType" value="<?=$charInfo['cogDieType']?>"> <h3>Cognition</h3>
				</div>
				<h3 class="skillTitle">Cognition Skills</h3>
				<textarea name="cogSkills"><?=strlen($charInfo['cogSkills'])?$charInfo['cogSkills']:'Search - 1'?></textarea>
				
				<div class="statDiv">
					<input type="text" name="knoNumDice" maxlength="2" class="numDice" value="<?=$charInfo['knoNumDice']?>"> d <input type="text" name="knoDieType" class="dieType" value="<?=$charInfo['knoDieType']?>"> <h3>Knowledge</h3>
				</div>
				<h3 class="skillTitle">Knowledge Skills</h3>
				<textarea name="knoSkills"><?=strlen($charInfo['knoSkills'])?$charInfo['knoSkills']:"Area Knowledge: Home County - 2\nLanguage: Native Tongue - 2"?></textarea>
				
				<div class="statDiv">
					<input type="text" name="mieNumDice" maxlength="2" class="numDice" value="<?=$charInfo['mieNumDice']?>"> d <input type="text" name="mieDieType" class="dieType" value="<?=$charInfo['mieDieType']?>"> <h3>Mien</h3>
				</div>
				<h3 class="skillTitle">Mien Skills</h3>
				<textarea name="mieSkills"><?=$charInfo['mieSkills']?></textarea>
				
				<div class="statDiv">
					<input type="text" name="smaNumDice" maxlength="2" class="numDice" value="<?=$charInfo['smaNumDice']?>"> d <input type="text" name="smaDieType" class="dieType" value="<?=$charInfo['smaDieType']?>"> <h3>Smarts</h3>
				</div>
				<h3 class="skillTitle">Smarts Skills</h3>
				<textarea name="smaSkills"><?=$charInfo['smaSkills']?></textarea>
				
				<div class="statDiv">
					<input type="text" name="spiNumDice" maxlength="2" class="numDice" value="<?=$charInfo['spiNumDice']?>"> d <input type="text" name="spiDieType" class="dieType" value="<?=$charInfo['spiDieType']?>"> <h3>Spirits</h3>
				</div>
				<h3 class="skillTitle">Spirits Skills</h3>
				<textarea name="spiSkills"><?=$charInfo['spiSkills']?></textarea>
			</div>
			<div class="triCol">
				<h2>Corporeal</h2>
				<div class="statDiv firstStatDiv">
					<input type="text" name="defNumDice" maxlength="2" class="numDice" value="<?=$charInfo['defNumDice']?>"> d <input type="text" name="defDieType" class="dieType" value="<?=$charInfo['defDieType']?>"> <h3>Deftness</h3>
				</div>
				<h3 class="skillTitle">Deftness Skills</h3>
				<textarea name="defSkills"><?=$charInfo['defSkills']?></textarea>
				
				<div class="statDiv">
					<input type="text" name="nimNumDice" maxlength="2" class="numDice" value="<?=$charInfo['nimNumDice']?>"> d <input type="text" name="nimDieType" class="dieType" value="<?=$charInfo['nimDieType']?>"> <h3>Nimbleness</h3>
				</div>
				<h3 class="skillTitle">Nimbleness Skills</h3>
				<textarea name="nimSkills"><?=$charInfo['nimSkills']?></textarea>
				
				<div class="statDiv">
					<input type="text" name="strNumDice" maxlength="2" class="numDice" value="<?=$charInfo['strNumDice']?>"> d <input type="text" name="strDieType" class="dieType" value="<?=$charInfo['strDieType']?>"> <h3>Strength</h3>
				</div>
				<h3 class="skillTitle">Strength Skills</h3>
				<textarea name="strSkills"><?=$charInfo['strSkills']?></textarea>
				
				<div class="statDiv">
					<input type="text" name="quiNumDice" maxlength="2" class="numDice" value="<?=$charInfo['quiNumDice']?>"> d <input type="text" name="quiDieType" class="dieType" value="<?=$charInfo['quiDieType']?>"> <h3>Quickness</h3>
				</div>
				<h3 class="skillTitle">Quickness Skills</h3>
				<textarea name="quiSkills"><?=$charInfo['quiSkills']?></textarea>
				
				<div class="statDiv">
					<input type="text" name="vigNumDice" maxlength="2" class="numDice" value="<?=$charInfo['vigNumDice']?>"> d <input type="text" name="vigDieType" class="dieType" value="<?=$charInfo['vigDieType']?>"> <h3>Vigor</h3>
				</div>
				<h3 class="skillTitle">Vigor Skills</h3>
				<textarea name="vigSkills"><?=$charInfo['vigSkills']?></textarea>
			</div>
			<div class="triCol lastTriCol">
				<h2>Edges &amp; Hindrances</h2>
				<textarea id="edge_hind" name="edge_hind"><?=$charInfo['edge_hind']?></textarea>
				
				<h2>Worst Nightmare</h2>
				<textarea id="nightmare" name="nightmare"><?=$charInfo['nightmare']?></textarea>
				
				<h2>Wounds</h2>
				<div id="woundsDiv">
					<div class="indivWoundDiv">
						<h3>Head</h3>
						<input type="text" name="wounds[head]" maxlength="2" value="<?=$charInfo['wounds'][0]?>">
					</div>
					<div class="indivWoundDiv subTwoCol">
						<h3>Left Hand</h3>
						<input type="text" name="wounds[leftHand]" maxlength="2" value="<?=$charInfo['wounds'][1]?>">
					</div>
					<div class="indivWoundDiv subTwoCol">
						<h3>Right Hand</h3>
						<input type="text" name="wounds[rightHand]" maxlength="2" value="<?=$charInfo['wounds'][2]?>">
					</div>
					<div class="indivWoundDiv">
						<h3>Guts</h3>
						<input type="text" name="wounds[guts]" maxlength="2" value="<?=$charInfo['wounds'][3]?>">
					</div>
					<div class="indivWoundDiv subTwoCol">
						<h3>Left Leg</h3>
						<input type="text" name="wounds[leftLeg]" maxlength="2" value="<?=$charInfo['wounds'][4]?>">
					</div>
					<div class="indivWoundDiv subTwoCol">
						<h3>Right Leg</h3>
						<input type="text" name="wounds[rightLeg]" maxlength="2" value="<?=$charInfo['wounds'][5]?>">
					</div>
				</div>
				
				<div id="windDiv">
					<h3>Wind</h3>
					<input type="text" name="wind" maxlength="2" value="<?=$charInfo['wind']?>">
				</div>
			</div>
			
			<br class="clear">
			<div class="twoCol">
				<h2 class="leftTitle">Shootin Irons & Such</h2>
				<textarea id="weapons" name="weapons"><?=$charInfo['weapons']?></textarea>
				
				<h2 class="leftTitle">Arcane Abilities</h2>
				<textarea id="arcane" name="arcane"><?=$charInfo['arcane']?></textarea>
			</div>
			<div class="twoCol lastTwoCol">
				<h2 class="leftTitle">Equipment</h2>
				<textarea id="equipment" name="equipment"><?=$charInfo['equipment']?></textarea>
			</div>
			
			<h2 class="leftTitle">Background/Notes</h2>
			<textarea id="notes" name="notes"><?=$charInfo['notes']?></textarea>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>