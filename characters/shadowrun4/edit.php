<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT shadowrun4.*, characters.userID, gms.gameID IS NOT NULL isGM FROM shadowrun4_characters shadowrun4 INNER JOIN characters ON shadowrun4.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE shadowrun4.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array();
			$textVals = array();
/*			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = printReady($value);
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}*/
			foreach ($numVals as $key) $charInfo[$key] = intval($charInfo[$key]);
			$noChar = FALSE;
		}
	} else $noChar = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/shadowrun4.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="jsHide">
			Javascript is disabled. This page won't show/work up properly.
		</div>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/shadowrun4/<?=$pathOptions[1]?>">
			<input type="hidden" name="characterID" value="<?=$pathOptions[2]?>">
			
			<div class="tr">
				<label for="name" class="textLabel">Name:</label>
				<input type="text" name="name" value="<?=$charInfo['name']?>" maxlength="50">
			</div>
			<div class="tr">
				<label for="metatype" class="textLabel">Metatype:</label>
				<input type="text" name="metatype" value="<?=$charInfo['metatype']?>" maxlength="20">
			</div>
			
			<div id="stats">
<?
	foreach (array('body' => 'Body', 'agility' => 'Agility', 'reaction' => 'Reaction', 'strength' => 'Strength', 'charisma' => 'Charisma', 'intuition' => 'Intuition', 'logic' => 'Logic', 'willpower' => 'Willpower', 'edge_total' => 'Total Edge', 'edge_current' => 'Current Edge', 'essence' => 'Essence', 'mag_res' => 'Magic or Resonance', 'initiative' => 'Initiative', 'initiative_passes' => 'Initiative Passes', 'matrix_initiative' => 'Matrix Initiative', 'astral_initiative' => 'Astral Initiative') as $stat => $statName) {
		if ($stat == 'body' || $stat == 'edge_total') echo "\t\t\t\t<div class=\"statCol\">\n";
		echo "\t\t\t\t\t<div class=\"tr\">\n";
		echo "\t\t\t\t\t\t<label for=\"{$stat}\" class=\"textLabel\">{$statName}:</label>\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"{$stat}\" value=\"{$charInfo[$stat]}\" maxlength=\"2\">\n";
		echo "\t\t\t\t\t</div>\n";
		if ($stat == 'willpower' || $stat == 'astral_initiative') echo "\t\t\t\t</div>\n";
	}
	
?>
			</div>
			
			<div id="qualities">
				<h2>Qualities</h2>
				<textarea name="qualities"><?=$charInfo['qualities']?></textarea>
			</div>
			
			<div id="damage">
				<h2>Damage Tracks</h2>
				<div>
					<label for="physical" class="textLabel">Physical Damage</label>
					<input type="text" name="physicalDamage" value="<?=$charInfo['physicalDamage']?>" maxlength="2">
				</div>
				<div>
					<label for="stun" class="textLabel">Stun Damage</label>
					<input type="text" name="stunDamage" value="<?=$charInfo['stunDamage']?>" maxlength="2">
				</div>
			</div>
			
			<br class="clear">
			<div id="skills" class="twoCol floatLeft">
				<h2>Skills</h2>
				<textarea name="skills"><?=$charInfo['skills']?></textarea>
			</div>
			<div id="spells" class="twoCol floatRight">
				<h2>Spells</h2>
				<textarea name="spells"><?=$charInfo['spells']?></textarea>
			</div>
			
			<br class="clear">
			<div id="weapons" class="twoCol floatLeft">
				<h2>Weapons</h2>
				<textarea name="weapons"><?=$charInfo['weapons']?></textarea>
			</div>
			<div id="armor" class="twoCol floatRight">
				<h2>Armor</h2>
				<textarea name="armor"><?=$charInfo['armor']?></textarea>
			</div>
			
			<br class="clear">
			<div id="augments" class="twoCol floatLeft">
				<h2>Augments</h2>
				<textarea name="augments"><?=$charInfo['augments']?></textarea>
			</div>
			<div id="contacts" class="twoCol floatRight">
				<h2>Contacts</h2>
				<textarea name="contacts"><?=$charInfo['contacts']?></textarea>
			</div>
			
			<br class="clear">
			<div id="items">
				<h2>Items</h2>
				<textarea name="items"><?=$charInfo['items']?></textarea>
			</div>
			
			<div id="notes">
				<h2>Notes</h2>
				<textarea name="notes"><?=$charInfo['notes']?></textarea>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>