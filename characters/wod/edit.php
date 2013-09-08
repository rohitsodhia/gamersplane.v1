<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT wod.*, characters.userID, gms.gameID IS NOT NULL isGM FROM wod_characters wod INNER JOIN characters ON wod.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE wod.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
		if (isset($_GET['new'])) {
			foreach (array('int', 'str', 'pre', 'wit', 'dex', 'man', 'res', 'sta', 'com') as $key) $charInfo[$key] = 1;
			foreach (array('academics', 'computer', 'crafts', 'investigation', 'medicine', 'occult', 'politics', 'science', 'athletics', 'brawl', 'drive', 'firearms', 'larceny', 'stealth', 'survival', 'weaponry', 'animalKen', 'empathy', 'expression', 'intimidation', 'persuasion', 'socialize', 'streetwise', 'subterfuge') as $key) $charInfo[$key] = 0;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/wod.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/wod/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div id="nameDiv" class="tr">
				<label class="textLabel">Name:</label>
				<div><input type="text" name="name" maxlength="50" value="<?=$charInfo['name']?>"></div>
			</div>
			
			<div id="attributes">
				<h2>Attributes</h2>
				<table>
					<tr>
						<th>Power</th>
						<td class="statName">Intelligence</td>
						<td class="statVal">
							<input type="radio" name="int" value="1"<?=$charInfo['int'] == 1?' checked="checked"':''?>>
							<input type="radio" name="int" value="2"<?=$charInfo['int'] == 2?' checked="checked"':''?>>
							<input type="radio" name="int" value="3"<?=$charInfo['int'] == 3?' checked="checked"':''?>>
							<input type="radio" name="int" value="4"<?=$charInfo['int'] == 4?' checked="checked"':''?>>
							<input type="radio" name="int" value="5"<?=$charInfo['int'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Strength</td>
						<td class="statVal">
							<input type="radio" name="str" value="1"<?=$charInfo['str'] == 1?' checked="checked"':''?>>
							<input type="radio" name="str" value="2"<?=$charInfo['str'] == 2?' checked="checked"':''?>>
							<input type="radio" name="str" value="3"<?=$charInfo['str'] == 3?' checked="checked"':''?>>
							<input type="radio" name="str" value="4"<?=$charInfo['str'] == 4?' checked="checked"':''?>>
							<input type="radio" name="str" value="5"<?=$charInfo['str'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Presence</td>
						<td class="statVal">
							<input type="radio" name="pre" value="1"<?=$charInfo['pre'] == 1?' checked="checked"':''?>>
							<input type="radio" name="pre" value="2"<?=$charInfo['pre'] == 2?' checked="checked"':''?>>
							<input type="radio" name="pre" value="3"<?=$charInfo['pre'] == 3?' checked="checked"':''?>>
							<input type="radio" name="pre" value="4"<?=$charInfo['pre'] == 4?' checked="checked"':''?>>
							<input type="radio" name="pre" value="5"<?=$charInfo['pre'] == 5?' checked="checked"':''?>>
						</td>
					</tr>
					<tr>
						<th>Finesse</th>
						<td class="statName">Wits</td>
						<td class="statVal">
							<input type="radio" name="wit" value="1"<?=$charInfo['wit'] == 1?' checked="checked"':''?>>
							<input type="radio" name="wit" value="2"<?=$charInfo['wit'] == 2?' checked="checked"':''?>>
							<input type="radio" name="wit" value="3"<?=$charInfo['wit'] == 3?' checked="checked"':''?>>
							<input type="radio" name="wit" value="4"<?=$charInfo['wit'] == 4?' checked="checked"':''?>>
							<input type="radio" name="wit" value="5"<?=$charInfo['wit'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Dexterity</td>
						<td class="statVal">
							<input type="radio" name="dex" value="1"<?=$charInfo['dex'] == 1?' checked="checked"':''?>>
							<input type="radio" name="dex" value="2"<?=$charInfo['dex'] == 2?' checked="checked"':''?>>
							<input type="radio" name="dex" value="3"<?=$charInfo['dex'] == 3?' checked="checked"':''?>>
							<input type="radio" name="dex" value="4"<?=$charInfo['dex'] == 4?' checked="checked"':''?>>
							<input type="radio" name="dex" value="5"<?=$charInfo['dex'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Manipulation</td>
						<td class="statVal">
							<input type="radio" name="man" value="1"<?=$charInfo['man'] == 1?' checked="checked"':''?>>
							<input type="radio" name="man" value="2"<?=$charInfo['man'] == 2?' checked="checked"':''?>>
							<input type="radio" name="man" value="3"<?=$charInfo['man'] == 3?' checked="checked"':''?>>
							<input type="radio" name="man" value="4"<?=$charInfo['man'] == 4?' checked="checked"':''?>>
							<input type="radio" name="man" value="5"<?=$charInfo['man'] == 5?' checked="checked"':''?>>
						</td>
					</tr>
					<tr>
						<th>Resistance</th>
						<td class="statName">Resolve</td>
						<td class="statVal">
							<input type="radio" name="res" value="1"<?=$charInfo['res'] == 1?' checked="checked"':''?>>
							<input type="radio" name="res" value="2"<?=$charInfo['res'] == 2?' checked="checked"':''?>>
							<input type="radio" name="res" value="3"<?=$charInfo['res'] == 3?' checked="checked"':''?>>
							<input type="radio" name="res" value="4"<?=$charInfo['res'] == 4?' checked="checked"':''?>>
							<input type="radio" name="res" value="5"<?=$charInfo['res'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Stamina</td>
						<td class="statVal">
							<input type="radio" name="sta" value="1"<?=$charInfo['sta'] == 1?' checked="checked"':''?>>
							<input type="radio" name="sta" value="2"<?=$charInfo['sta'] == 2?' checked="checked"':''?>>
							<input type="radio" name="sta" value="3"<?=$charInfo['sta'] == 3?' checked="checked"':''?>>
							<input type="radio" name="sta" value="4"<?=$charInfo['sta'] == 4?' checked="checked"':''?>>
							<input type="radio" name="sta" value="5"<?=$charInfo['sta'] == 5?' checked="checked"':''?>>
						</td>
						<td class="statName">Composure</td>
						<td class="statVal">
							<input type="radio" name="com" value="1"<?=$charInfo['com'] == 1?' checked="checked"':''?>>
							<input type="radio" name="com" value="2"<?=$charInfo['com'] == 2?' checked="checked"':''?>>
							<input type="radio" name="com" value="3"<?=$charInfo['com'] == 3?' checked="checked"':''?>>
							<input type="radio" name="com" value="4"<?=$charInfo['com'] == 4?' checked="checked"':''?>>
							<input type="radio" name="com" value="5"<?=$charInfo['com'] == 5?' checked="checked"':''?>>
						</td>
					</tr>
				</table>
			</div>
			
			<div id="leftCol" class="triCol">
				<h2 id="leftColHeader">Skills</h2>
				<h3>Mental</h3>
				<p>(-3 unskilled)</p>
<?
		foreach (array('Academics', 'Computer', 'Crafts', 'Investigation', 'Medicine', 'Occult', 'Politics', 'Science') as $skill) {
			echo "				
				<div class=\"tr\">
					{$skill}
					<div class=\"skillRank\">
						0
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"0\"".($charInfo[strtolower($skill)] == 0?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"1\"".($charInfo[strtolower($skill)] == 1?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"2\"".($charInfo[strtolower($skill)] == 2?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"3\"".($charInfo[strtolower($skill)] == 3?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"4\"".($charInfo[strtolower($skill)] == 4?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"5\"".($charInfo[strtolower($skill)] == 5?' checked="checked"':'').">
						5
					</div>
				</div>
";
		}
?>
				
				<h3>Physical</h3>
				<p>(-1 unskilled)</p>
<?
		foreach (array('Athletics', 'Brawl', 'Drive', 'Firearms', 'Larceny', 'Stealth', 'Survival', 'Weaponry') as $skill) {
			echo "				
				<div class=\"tr\">
					{$skill}
					<div class=\"skillRank\">
						0
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"0\"".($charInfo[strtolower($skill)] == 0?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"1\"".($charInfo[strtolower($skill)] == 1?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"2\"".($charInfo[strtolower($skill)] == 2?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"3\"".($charInfo[strtolower($skill)] == 3?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"4\"".($charInfo[strtolower($skill)] == 4?' checked="checked"':'').">
						<input type=\"radio\" name=\"".strtolower($skill)."\" value=\"5\"".($charInfo[strtolower($skill)] == 5?' checked="checked"':'').">
						5
					</div>
				</div>
";
		}
?>
				
				<h3>Social</h3>
				<p>(-1 unskilled)</p>
<?
		foreach (array('Animal Ken', 'Empathy', 'Expression', 'Intimidation', 'Persuasion', 'Socialize', 'Streetwise', 'Subterfuge') as $skill) {
			echo "				
				<div class=\"tr\">
					{$skill}
					<div class=\"skillRank\">
						0
						<input type=\"radio\" name=\"".camelcase($skill)."\" value=\"0\"".($charInfo[camelcase($skill)] == 0?' checked="checked"':'').">
						<input type=\"radio\" name=\"".camelcase($skill)."\" value=\"1\"".($charInfo[camelcase($skill)] == 1?' checked="checked"':'').">
						<input type=\"radio\" name=\"".camelcase($skill)."\" value=\"2\"".($charInfo[camelcase($skill)] == 2?' checked="checked"':'').">
						<input type=\"radio\" name=\"".camelcase($skill)."\" value=\"3\"".($charInfo[camelcase($skill)] == 3?' checked="checked"':'').">
						<input type=\"radio\" name=\"".camelcase($skill)."\" value=\"4\"".($charInfo[camelcase($skill)] == 4?' checked="checked"':'').">
						<input type=\"radio\" name=\"".camelcase($skill)."\" value=\"5\"".($charInfo[camelcase($skill)] == 5?' checked="checked"':'').">
						5
					</div>
				</div>
";
		}
?>
			</div>
			
			<h2 id="rightColHeader">Other Traits</h2>
			<div class="triCol rightCol">
				<h3>Merits</h3>
				<textarea id="merits" name="merits"><?=$charInfo['merits']?></textarea>
				
				<h3 class="marginTop">Flaws</h3>
				<textarea id="flaws" name="flaws"><?=$charInfo['flaws']?></textarea>
			</div>
			<div class="triCol rightCol lastCol">
				<h3>Health</h3>
				<div class="alignCenter"><input type="text" id="health" name="health" maxlength="2" value="<?=$charInfo['health']?>"></div>
				
				<h3 class="marginTop">Willpower</h3>
				<div class="alignCenter"><input type="text" id="willpower" name="willpower" maxlength="2" value="<?=$charInfo['willpower']?>"></div>
				
				<h3 class="marginTop">Morality</h3>
				<div class="alignCenter"><input type="text" id="morality" name="morality" maxlength="2" value="<?=$charInfo['morality']?>"></div>
				
				<div class="tr marginTop">
					<label class="textLabel">Size</label>
					<input type="text" name="size" maxlength="2" value="<?=$charInfo['size']?>">
				</div>
				<div class="tr">
					<label class="textLabel">Speed</label>
					<input type="text" name="speed" maxlength="2" value="<?=$charInfo['speed']?>">
				</div>
				<div class="tr">
					<label class="textLabel">Initiative Mod</label>
					<input type="text" name="initiativeMod" maxlength="2" value="<?=$charInfo['initiativeMod']?>">
				</div>
				<div class="tr">
					<label class="textLabel">Defense</label>
					<input type="text" name="defense" maxlength="2" value="<?=$charInfo['defense']?>">
				</div>
				<div class="tr">
					<label class="textLabel">Armor</label>
					<input type="text" name="armor" maxlength="2" value="<?=$charInfo['armor']?>">
				</div>
			</div>
			
			<div id="itemsDiv">
				<h3 class="marginTop">Weapons</h3>
				<textarea id="weapons" name="weapons"><?=$charInfo['weapons']?></textarea>
				
				<h3 class="marginTop">Equipment</h3>
				<textarea id="equipment" name="equipment"><?=$charInfo['equipment']?></textarea>
			</div>
			
			<br class="clear">
			<h2 id="notesTitle" class="marginTop">Notes</h3>
			<textarea id="notes" name="notes"><?=$charInfo['notes']?></textarea>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>