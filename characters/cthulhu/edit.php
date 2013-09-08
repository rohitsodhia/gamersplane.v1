<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cthulhu.*, characters.userID, gms.gameID IS NOT NULL isGM FROM cthulhu_characters cthulhu INNER JOIN characters ON cthulhu.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE cthulhu.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] != $userID && !$charInfo['isGM']) {
			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_magic', 'fort_race', 'fort_misc', 'ref_base', 'ref_magic', 'ref_race', 'ref_misc', 'will_base', 'will_magic', 'will_race', 'will_misc', 'hp', 'ac_total', 'ac_armor', 'ac_shield', 'ac_dex', 'ac_class', 'ac_natural', 'ac_deflection', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc');
			$textVals = array('name', 'race', 'class', 'dr', 'skills', 'feats', 'weapons', 'armor', 'items', 'spells', 'notes');
/*			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = printReady($value);
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}*/
			foreach ($numVals as $key) $charInfo[$key] = intval($charInfo[$key]);
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/cthulhu.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="jsHide">
			Javascript is disabled. This page won't show/work up properly.
		</div>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/cthulhu/<?=$pathOptions[1]?>">
			<input type="hidden" name="characterID" value="<?=$pathOptions[2]?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="col1 lrBuffer shiftRight">Name</label>
				<label id="label_race" class="col1 lrBuffer shiftRight">Race</label>
				<label id="label_size" class="col1 lrBuffer">Size Modifier</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="lrBuffer">
				<input type="text" name="race" value="<?=$charInfo['race']?>" class="lrBuffer">
				<input id="size" type="text" name="size" value="<?=$charInfo['size']?>" class="lrBuffer">
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="col2 lrBuffer shiftRight">Class(es)</label>
<!--					<label id="label_levels" class="col3 lrBuffer shiftRight">Level(s)</label>-->
				<label id="label_alignment" class="col1 lrBuffer shiftRight">Alignment</label>
			</div>
			<div class="tr">
				<input type="text" id="classes" name="class" value="<?=$charInfo['class']?>" class="lrBuffer">
<!--					<input type="text" id="levels" name="levels" class="lrBuffer">-->
				<select name="alignment" class="lrBuffer">
					<option value="lg"<?=$charInfo['alignment'] == 'lg'?' selected="selected"':''?>>Lawful Good</option>
					<option value="ng"<?=$charInfo['alignment'] == 'ng'?' selected="selected"':''?>>Neutral Good</option>
					<option value="cg"<?=$charInfo['alignment'] == 'cg'?' selected="selected"':''?>>Chaotic Good</option>
					<option value="ln"<?=$charInfo['alignment'] == 'ln'?' selected="selected"':''?>>Lawful Neutral</option>
					<option value="tn"<?=$charInfo['alignment'] == 'tn'?' selected="selected"':''?>>True Neutral</option>
					<option value="cn"<?=$charInfo['alignment'] == 'cn'?' selected="selected"':''?>>Chaotic Neutral</option>
					<option value="le"<?=$charInfo['alignment'] == 'le'?' selected="selected"':''?>>Lawful Evil</option>
					<option value="ne"<?=$charInfo['alignment'] == 'ne'?' selected="selected"':''?>>Neutral Evil</option>
					<option value="ce"<?=$charInfo['alignment'] == 'ce'?' selected="selected"':''?>>Chaotic Evil</option>
				</select>
			</div>
			
			<div id="stats">
<?
	$statBonus = array();
	foreach (array('Strength', 'Dexterity', 'Constitution', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = floor(($charInfo[$short] - 10)/2);
		if ($bonus >= 0) $bonus = '+'.$bonus;
		echo "				<div class=\"tr\">
					<label id=\"label_{$short}\" class=\"textLabel col3 lrBuffer leftLabel\">{$stat}</label>
					<input type=\"text\" id=\"{$short}\" name=\"{$short}\" value=\"".$charInfo[$short]."\" maxlength=\"2\" class=\"stat lrBuffer\">
					<span id=\"{$short}Modifier\">{$bonus}</span>
				</div>
";
		$statBonus[$short] = $bonus;
	}
	
	if ($charInfo['size'] > 0) $charInfo['size'] = '+'.$charInfo['size'];
/*				<div class="tr">
					<label id="label_str" class="textLabel col3 lrBuffer leftLabel">Strength</label>
					<input type="text" id="str" name="str" class="stat lrBuffer">
					<span id="strModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_dex" class="textLabel col3 lrBuffer leftLabel">Dexterity</label>
					<input type="text" id="dex" name="dex" class="stat lrBuffer">
					<span id="dexModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_con" class="textLabel col3 lrBuffer leftLabel">Constitution</label>
					<input type="text" id="con" name="con" class="stat lrBuffer">
					<span id="conModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_int" class="textLabel col3 lrBuffer leftLabel">Intelligence</label>
					<input type="text" id="int" name="int" class="stat lrBuffer">
					<span id="intModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_wis" class="textLabel col3 lrBuffer leftLabel">Wisdom</label>
					<input type="text" id="wis" name="wis" class="stat lrBuffer">
					<span id="wisModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_cha" class="textLabel col3 lrBuffer leftLabel">Charisma</label>
					<input type="text" id="cha" name="cha" class="stat lrBuffer">
					<span id="chaModifier">0</span>
				</div>*/
?>
			</div>
			
			<div id="savingThrows">
				<div class="tr labelTR">
					<label class="statCol col4 lrBuffer first">Total</label>
					<label class="statCol col4 lrBuffer">Base</label>
					<label class="statCol col4 lrBuffer">Ability</label>
					<label class="statCol col4 lrBuffer">Magic</label>
					<label class="statCol col4 lrBuffer">Race</label>
					<label class="statCol col4 lrBuffer">Misc</label>
				</div>
<?
	$charInfo['size'] = showSign($charInfo['size']);
	$fortBonus = showSign($charInfo['fort_base'] + $statBonus['con'] + $charInfo['fort_magic'] + $charInfo['fort_race'] + $charInfo['fort_misc']);
	$refBonus = showSign($charInfo['ref_base'] + $statBonus['dex'] + $charInfo['ref_magic'] + $charInfo['ref_race'] + $charInfo['ref_misc']);
	$willBonus = showSign($charInfo['will_base'] + $statBonus['wis'] + $charInfo['will_magic'] + $charInfo['will_race'] + $charInfo['will_misc']);
?>
				<div id="fortRow" class="tr">
					<label class="leftLabel">Fortitude</label>
					<span id="fortTotal" class="col4 lrBuffer addStat_con"><?=$fortBonus?></span>
					<input type="text" name="fort_base"  value="<?=$charInfo['fort_base']?>" class="lrBuffer">
					<span class="col4 lrBuffer statBonus_con"><?=$statBonus['con']?></span>
					<input type="text" name="fort_magic"  value="<?=$charInfo['fort_magic']?>" class="lrBuffer">
					<input type="text" name="fort_race"  value="<?=$charInfo['fort_race']?>" class="lrBuffer">
					<input type="text" name="fort_misc"  value="<?=$charInfo['fort_misc']?>" class="lrBuffer">
				</div>
				<div id="refRow" class="tr">
					<label class="leftLabel">Reflex</label>
					<span id="refTotal" class="col4 lrBuffer addStat_dex"><?=$refBonus?></span>
					<input type="text" name="ref_base"  value="<?=$charInfo['ref_base']?>" class="lrBuffer">
					<span class="col4 lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
					<input type="text" name="ref_magic"  value="<?=$charInfo['ref_magic']?>" class="lrBuffer">
					<input type="text" name="ref_race"  value="<?=$charInfo['ref_race']?>" class="lrBuffer">
					<input type="text" name="ref_misc"  value="<?=$charInfo['ref_misc']?>" class="lrBuffer">
				</div>
				<div id="willRow" class="tr">
					<label class="leftLabel">Will</label>
					<span id="willTotal" class="col4 lrBuffer addStat_wis"><?=$willBonus?></span>
					<input type="text" name="will_base"  value="<?=$charInfo['will_base']?>" class="lrBuffer">
					<span class="col4 lrBuffer statBonus_wis"><?=$statBonus['wis']?></span>
					<input type="text" name="will_magic"  value="<?=$charInfo['will_magic']?>" class="lrBuffer">
					<input type="text" name="will_race"  value="<?=$charInfo['will_race']?>" class="lrBuffer">
					<input type="text" name="will_misc"  value="<?=$charInfo['will_misc']?>" class="lrBuffer">
				</div>
			</div>
			
			<div id="hp">
				<label class="leftLabel textLabel">Total HP</label>
				<input type="text" name="hp" value="<?=$charInfo['hp']?>">
				<label class="leftLabel textLabel">Damage Reduction</label>
				<input id="damageReduction" type="text" name="dr" value="<?=$charInfo['dr']?>">
			</div>
			
			<div id="ac">
				<div class="tr labelTR">
					<label class="col5 lrBuffer first">Total AC</label>
					<label class="col5">Armor</label>
					<label class="col5">Shield</label>
					<label class="col5">Dex</label>
					<label class="col5">Class</label>
					<label class="col5">Size</label>
					<label class="col5">Natural</label>
					<label class="col5">Deflection</label>
					<label class="col5">Misc</label>
				</div>
<? $acTotal = 10 + $charInfo['ac_armor'] + $charInfo['ac_shield'] + $charInfo['ac_dex'] + $charInfo['ac_class'] + $charInfo['size'] + $charInfo['ac_natural'] + $charInfo['ac_deflection'] + $charInfo['ac_misc']; ?>
				<div class="tr">
					<span id="ac_total" class="lrBuffer addSize"><?=$acTotal?></span>
					<span> = 10 + </span>
					<input type="text" name="ac_armor" value="<?=$charInfo['ac_armor']?>" class="acComponents">
					<input type="text" name="ac_shield" value="<?=$charInfo['ac_shield']?>" class="acComponents">
					<input type="text" name="ac_dex" value="<?=$charInfo['ac_dex']?>" class="acComponents">
					<input type="text" name="ac_class" value="<?=$charInfo['ac_class']?>" class="acComponents">
					<span class="sizeVal"><?=$charInfo['size']?></span>
					<input type="text" name="ac_natural" value="<?=$charInfo['ac_natural']?>" class="acComponents">
					<input type="text" name="ac_deflection" value="<?=$charInfo['ac_deflection']?>" class="acComponents">
					<input type="text" name="ac_misc" value="<?=$charInfo['ac_misc']?>" class="acComponents">
				</div>
			</div>
			
			<br class="clear">
			<div id="combatBonuses" class="clearFix">
				<div class="tr labelTR">
					<label class="statCol col4 lrBuffer first">Total</label>
					<label class="statCol col4 lrBuffer">Base</label>
					<label class="statCol col4 lrBuffer">Ability</label>
					<label class="statCol col4 lrBuffer">Size</label>
					<label class="statCol col4 lrBuffer">Misc</label>
				</div>
<?
	$initTotal = showSign($statBonus['dex'] + $charInfo['initiative_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['size'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['size'] + $charInfo['ranged_misc']);
?>
				<div id="init" class="tr">
					<label class="leftLabel col3">Initiative</label>
					<span id="initTotal" class="col4 lrBuffer addStat_dex"><?=$initTotal?></span>
					<span class="lrBuffer">&nbsp;</span>
					<span class="col4 lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
					<span class="lrBuffer">&nbsp;</span>
					<input type="text" name="initiative_misc" value="<?=$charInfo['initiative_misc']?>" class="lrBuffer">
				</div>
				<div id="melee" class="tr">
					<label class="leftLabel col3">Melee</label>
					<span id="meleeTotal" class="col4 lrBuffer addStat_str addSize"><?=$meleeTotal?></span>
					<input id="bab" type="text" name="bab" value="<?=$charInfo['bab']?>" class="lrBuffer">
					<span class="col4 lrBuffer statBonus_str"><?=$statBonus['str']?></span>
					<span class="col4 lrBuffer sizeVal"><?=$charInfo['size']?></span>
					<input id="melee_misc" type="text" name="melee_misc" value="<?=$charInfo['melee_misc']?>" class="lrBuffer">
				</div>
<? $charInfo['bab'] = showSign($charInfo['bab']); ?>
				<div id="ranged" class="tr">
					<label class="leftLabel col3">Ranged</label>
					<span id="rangedTotal" class="col4 lrBuffer addStat_dex addSize"><?=$rangedTotal?></span>
					<span class="col4 lrBuffer bab"><?=$charInfo['bab']?></span>
					<span class="col4 lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
					<span class="col4 lrBuffer sizeVal"><?=$charInfo['size']?></span>
					<input id="ranged_misc" type="text" name="ranged_misc" value="<?=$charInfo['ranged_misc']?>" class="lrBuffer">
				</div>
			</div>
			
<?
	$cmb = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['size']);
	$cmd = showSign($charInfo['bab'] + $statBonus['str'] + $statBonus['dex'] + $charInfo['size'] + 10);
?>
			<div id="combatManuvers">
				<div id="cmb">
					<div class="tr labelTR">
						<label class="statCol col4 first">Total</label>
						<label class="statCol col4">Base</label>
						<label class="statCol col4">Str</label>
						<label class="statCol col4">Size</label>
					</div>
					<div class="tr">
						<label class="leftLabel col5">CMB</label>
						<div class="col4 cell addStat_str subSize addBAB"><?=$cmb?></div>
						<div class="col4 cell bab"><?=$charInfo['bab']?></div>
						<div class="col4 cell statBonus_str"><?=$statBonus['str']?></div>
						<div class="col4 cell nSizeVal"><?=showSign(0 - $charInfo['size'])?></div>
					</div>
				</div>
				
				<div id="cmd">
					<div class="tr labelTR">
						<label class="statCol col4 first">Total</label>
						<label class="statCol col4">Base</label>
						<label class="statCol col4">Str</label>
						<label class="statCol col4">Dex</label>
						<label class="statCol col4">Size</label>
					</div>
					<div class="tr">
						<label class="leftLabel col5">CMD</label>
						<div class="col4 cell addStat_str addStat_dex subSize addBAB"><?=$cmd?></div>
						<div class="col4 cell bab"><?=$charInfo['bab']?></div>
						<div class="col4 cell statBonus_str"><?=$statBonus['str']?></div>
						<div class="col4 cell statBonus_dex"><?=$statBonus['dex']?></div>
						<div class="col4 cell nSizeVal"><?=showSign(0 - $charInfo['size'])?></div>
						<div class="col4 cell">+ 10</div>
					</div>
				</div>
			</div>
			
			<br class="clear">
			<div id="skills" class="textareaDiv floatLeft">
				<h2>Skills</h2>
				<textarea name="skills"><?=$charInfo['skills']?></textarea>
			</div>
			<div id="feats" class="textareaDiv floatRight">
				<h2>Feats/Abilities</h2>
				<textarea name="feats"><?=$charInfo['feats']?></textarea>
			</div>
			
			<div id="weapons" class="textareaDiv floatLeft">
				<h2>Weapons</h2>
				<textarea name="weapons"><?=$charInfo['weapons']?></textarea>
			</div>
			<div id="armor" class="textareaDiv floatRight">
				<h2>Armor</h2>
				<textarea name="armor"><?=$charInfo['armor']?></textarea>
			</div>
			
			<br class="clear">
			<div id="items">
				<h2>Items</h2>
				<textarea name="items"><?=$charInfo['items']?></textarea>
			</div>
			
			<div id="spells">
				<h2>Spells</h2>
				<textarea name="spells"><?=$charInfo['spells']?></textarea>
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