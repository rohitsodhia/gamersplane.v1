<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM pathfinder_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
		includeSystemInfo('pathfinder');
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/pathfinder.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/pathfinder/<?=$pathOptions[1]?>">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
				<label id="label_race" class="medText lrBuffer borderBox shiftRight">Race</label>
				<label id="label_size" class="medText lrBuffer borderBox">Size Modifier</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="medText lrBuffer">
				<input type="text" name="race" value="<?=$charInfo['race']?>" class="medText lrBuffer">
				<input id="size" type="text" name="size" value="<?=$charInfo['size']?>" class="lrBuffer">
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText lrBuffer borderBox shiftRight">Class(es)/Level(s)</label>
				<label id="label_alignment" class="medText lrBuffer borderBox shiftRight">Alignment</label>
			</div>
			<div class="tr">
				<input type="text" id="classes" name="class" value="<?=$charInfo['class']?>" class="lrBuffer">
				<select name="alignment" class="lrBuffer">
<?
	$alignments = array('lg' => 'Lawful Good', 'ng' => 'Neutral Good', 'cg' => 'Chaotic Good', 'ln' => 'Lawful Neutral', 'tn' => 'True Neutral', 'cn' => 'Chaotic Neutral', 'le' => 'Lawful Evil', 'ne' => 'Neutral Evil', 'ce' => 'Chaotic Evil');
	foreach ($alignments as $alignShort => $alignment) {
?>
					<option value="<?=$alignShort?>"<?=$charInfo['alignment'] == $alignShort?' selected="selected"':''?>><?=$alignment?></option>
<?	} ?>
				</select>
			</div>
			
			<div class="clearfix">
				<div id="stats">
<?
	$statBonus = array();
	foreach (array('Strength', 'Dexterity', 'Constitution', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = floor(($charInfo[$short] - 10)/2);
		if ($bonus >= 0) $bonus = '+'.$bonus;
?>
					<div class="tr">
						<label id="label_<?=$short?>" class="textLabel shortText lrBuffer leftLabel"><?=$stat?></label>
						<input type="text" id="<?=$short?>" name="<?=$short?>" value="<?=$charInfo[$short]?>" maxlength="2" class="stat lrBuffer">
						<span id="<?=$short?>Modifier"><?=$bonus?></span>
					</div>
<?
		$statBonus[$short] = $bonus;
	}
	
	if ($charInfo['size'] > 0) $charInfo['size'] = '+'.$charInfo['size'];
?>
				</div>
				
				<div id="savingThrows">
					<div class="tr labelTR">
						<div class="fillerBlock cell">&nbsp;</div>
						<label class="shortNum lrBuffer">Total</label>
						<label class="shortNum lrBuffer">Base</label>
						<label class="shortNum lrBuffer">Ability</label>
						<label class="shortNum lrBuffer">Magic</label>
						<label class="shortNum lrBuffer">Race</label>
						<label class="shortNum lrBuffer">Misc</label>
					</div>
<?
	$charInfo['size'] = showSign($charInfo['size']);
	$fortBonus = showSign($charInfo['fort_base'] + $statBonus['con'] + $charInfo['fort_magic'] + $charInfo['fort_race'] + $charInfo['fort_misc']);
	$refBonus = showSign($charInfo['ref_base'] + $statBonus['dex'] + $charInfo['ref_magic'] + $charInfo['ref_race'] + $charInfo['ref_misc']);
	$willBonus = showSign($charInfo['will_base'] + $statBonus['wis'] + $charInfo['will_magic'] + $charInfo['will_race'] + $charInfo['will_misc']);
?>
					<div id="fortRow" class="tr">
						<label class="leftLabel">Fortitude</label>
						<span id="fortTotal" class="shortNum lrBuffer addStat_con"><?=$fortBonus?></span>
						<input type="text" name="fort_base"  value="<?=$charInfo['fort_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_con"><?=$statBonus['con']?></span>
						<input type="text" name="fort_magic"  value="<?=$charInfo['fort_magic']?>" class="lrBuffer">
						<input type="text" name="fort_race"  value="<?=$charInfo['fort_race']?>" class="lrBuffer">
						<input type="text" name="fort_misc"  value="<?=$charInfo['fort_misc']?>" class="lrBuffer">
					</div>
					<div id="refRow" class="tr">
						<label class="leftLabel">Reflex</label>
						<span id="refTotal" class="shortNum lrBuffer addStat_dex"><?=$refBonus?></span>
						<input type="text" name="ref_base"  value="<?=$charInfo['ref_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
						<input type="text" name="ref_magic"  value="<?=$charInfo['ref_magic']?>" class="lrBuffer">
						<input type="text" name="ref_race"  value="<?=$charInfo['ref_race']?>" class="lrBuffer">
						<input type="text" name="ref_misc"  value="<?=$charInfo['ref_misc']?>" class="lrBuffer">
					</div>
					<div id="willRow" class="tr">
						<label class="leftLabel">Will</label>
						<span id="willTotal" class="shortNum lrBuffer addStat_wis"><?=$willBonus?></span>
						<input type="text" name="will_base"  value="<?=$charInfo['will_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_wis"><?=$statBonus['wis']?></span>
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
			</div>
			
			<div id="ac">
				<div class="tr labelTR">
					<label class="lrBuffer">Total AC</label>
					<div class="fillerBlock cell">&nbsp;</div>
					<label>Armor</label>
					<label>Shield</label>
					<label>Dex</label>
					<label>Class</label>
					<label>Size</label>
					<label>Natural</label>
					<label>Deflection</label>
					<label>Misc</label>
				</div>
<? $acTotal = 10 + $charInfo['ac_armor'] + $charInfo['ac_shield'] + $charInfo['ac_dex'] + $charInfo['ac_class'] + $charInfo['size'] + $charInfo['ac_natural'] + $charInfo['ac_deflection'] + $charInfo['ac_misc']; ?>
				<div class="tr">
					<div id="ac_total" class="lrBuffer addSize"><?=$acTotal?></div>
					<div> = 10 + </div>
					<input type="text" name="ac_armor" value="<?=$charInfo['ac_armor']?>" class="acComponents">
					<input type="text" name="ac_shield" value="<?=$charInfo['ac_shield']?>" class="acComponents">
					<input type="text" name="ac_dex" value="<?=$charInfo['ac_dex']?>" class="acComponents">
					<input type="text" name="ac_class" value="<?=$charInfo['ac_class']?>" class="acComponents">
					<div class="sizeVal"><?=$charInfo['size']?></div>
					<input type="text" name="ac_natural" value="<?=$charInfo['ac_natural']?>" class="acComponents">
					<input type="text" name="ac_deflection" value="<?=$charInfo['ac_deflection']?>" class="acComponents">
					<input type="text" name="ac_misc" value="<?=$charInfo['ac_misc']?>" class="acComponents">
				</div>
			</div>
			
			<div class="clearfix">
				<div id="combatBonuses">
					<div class="tr labelTR">
						<div class="fillerBlock cell shortText">&nbsp;</div>
						<label class="shortNum lrBuffer">Total</label>
						<label class="shortNum lrBuffer">Base</label>
						<label class="shortNum lrBuffer">Ability</label>
						<label class="shortNum lrBuffer">Size</label>
						<label class="shortNum lrBuffer">Misc</label>
					</div>
<?
	$initTotal = showSign($statBonus['dex'] + $charInfo['initiative_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['size'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['size'] + $charInfo['ranged_misc']);
?>
					<div id="init" class="tr">
						<label class="leftLabel shortText">Initiative</label>
						<span id="initTotal" class="shortNum lrBuffer addStat_dex"><?=$initTotal?></span>
						<span class="lrBuffer">&nbsp;</span>
						<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
						<span class="lrBuffer">&nbsp;</span>
						<input type="text" name="initiative_misc" value="<?=$charInfo['initiative_misc']?>" class="lrBuffer">
					</div>
					<div id="melee" class="tr">
						<label class="leftLabel shortText">Melee</label>
						<span id="meleeTotal" class="shortNum lrBuffer addStat_str addSize"><?=$meleeTotal?></span>
						<input id="bab" type="text" name="bab" value="<?=$charInfo['bab']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_str"><?=$statBonus['str']?></span>
						<span class="shortNum lrBuffer sizeVal"><?=$charInfo['size']?></span>
						<input id="melee_misc" type="text" name="melee_misc" value="<?=$charInfo['melee_misc']?>" class="lrBuffer">
					</div>
<? $charInfo['bab'] = showSign($charInfo['bab']); ?>
					<div id="ranged" class="tr">
						<label class="leftLabel shortText">Ranged</label>
						<span id="rangedTotal" class="shortNum lrBuffer addStat_dex addSize"><?=$rangedTotal?></span>
						<span class="shortNum lrBuffer bab"><?=$charInfo['bab']?></span>
						<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
						<span class="shortNum lrBuffer sizeVal"><?=$charInfo['size']?></span>
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
							<div class="fillerBlock cell medNum">&nbsp;</div>
							<label class="shortNum first">Total</label>
							<label class="shortNum">Base</label>
							<label class="shortNum">Str</label>
							<label class="shortNum">Size</label>
						</div>
						<div class="tr">
							<label class="leftLabel medNum">CMB</label>
							<div class="shortNum cell addStat_str subSize addBAB"><?=$cmb?></div>
							<div class="shortNum cell bab"><?=$charInfo['bab']?></div>
							<div class="shortNum cell statBonus_str"><?=$statBonus['str']?></div>
							<div class="shortNum cell nSizeVal"><?=showSign(0 - $charInfo['size'])?></div>
						</div>
					</div>
					
					<div id="cmd">
						<div class="tr labelTR">
							<div class="medNum">&nbsp;</div>
							<label class="shortNum first">Total</label>
							<label class="shortNum">Base</label>
							<label class="shortNum">Str</label>
							<label class="shortNum">Dex</label>
							<label class="shortNum">Size</label>
						</div>
						<div class="tr">
							<label class="leftLabel medNum">CMD</label>
							<div class="shortNum cell addStat_str addStat_dex subSize addBAB"><?=$cmd?></div>
							<div class="shortNum cell bab"><?=$charInfo['bab']?></div>
							<div class="shortNum cell statBonus_str"><?=$statBonus['str']?></div>
							<div class="shortNum cell statBonus_dex"><?=$statBonus['dex']?></div>
							<div class="shortNum cell nSizeVal"><?=showSign(0 - $charInfo['size'])?></div>
							<div class="shortNum cell">+ 10</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="skills" class="floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div id="addSkillWrapper">
							<input id="skillName" type="text" name="newSkill[name]" class="medText placeholder" autocomplete="off" data-placeholder="Skill Name">
							<select id="skillStat" name="newSkill[stat]">
								<option value="str">Str</option>
								<option value="dex">Dex</option>
								<option value="con">Con</option>
								<option value="int">Int</option>
								<option value="wis">Wis</option>
								<option value="cha">Cha</option>
							</select>
							<button id="addSkill" type="submit" name="newSkill_add" class="fancyButton">Add</button>
						</div>
						<div class="tr labelTR">
							<label class="medText">Skill</label>
							<label class="shortNum alignCenter lrBuffer">Total</label>
							<label class="shortNum alignCenter lrBuffer">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Ranks</label>
							<label class="shortNum alignCenter lrBuffer">Misc</label>
						</div>
<?
	$skills = $mysql->query('SELECT pathfinder_skills.skillID, skillsList.name, pathfinder_skills.stat, pathfinder_skills.ranks, pathfinder_skills.misc FROM pathfinder_skills INNER JOIN skillsList USING (skillID) WHERE pathfinder_skills.characterID = '.$characterID.' ORDER BY skillsList.name');
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
		skillFormFormat($skillInfo, $statBonus[$skillInfo['stat']]);
	} } else { ?>
						<p id="noSkills">This character currently has no skills.</p>
<?	} ?>
					</div>
				</div>
				<div id="feats" class="floatRight">
					<h2 class="headerbar hbDark">Feats/Abilities</h2>
					<div class="hbdMargined">
						<div id="addFeatWrapper">
							<input id="featName" type="text" name="newFeat_name" class="medText placeholder" autocomplete="off" data-placeholder="Feat Name">
							<button id="addFeat" type="submit" name="newFeat_add" class="fancyButton">Add</button>
						</div>
<?
	$feats = $mysql->query('SELECT pathfinder_feats.featID, featsList.name FROM pathfinder_feats INNER JOIN featsList USING (featID) WHERE pathfinder_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		featFormFormat($characterID, $featInfo);
	} } else { ?>
					<p id="noFeats">This character currently has no feats/abilities.</p>
<?	} ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark">Weapons <a id="addWeapon" href="">[ Add Weapon ]</a></h2>
					<div>
<?
	$weapons = $mysql->query('SELECT * FROM pathfinder_weapons WHERE characterID = '.$characterID);
	$weaponNum = 1;
	while (($weaponInfo = $weapons->fetch()) || $weaponNum <= 2) weaponFormFormat($weaponNum++, $weaponInfo);
?>
					</div>
				</div>
				<div id="armor" class="floatRight">
					<h2 class="headerbar hbDark">Armor <a id="addArmor" href="">[ Add Armor ]</a></h2>
					<div>
<?
	$armors = $mysql->query('SELECT * FROM pathfinder_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	foreach ($armors as $armorInfo) {
		armorFormFormat($armorNum++, $armorInfo);
	}
	if ($armorNum == 1) armorFormFormat(1);
?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="items">
					<h2 class="headerbar hbDark">Items</h2>
					<textarea name="items" class="hbdMargined"><?=$charInfo['items']?></textarea>
				</div>
				
				<div id="spells">
					<h2 class="headerbar hbDark">Spells</h2>
					<textarea name="spells" class="hbdMargined"><?=$charInfo['spells']?></textarea>
				</div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<textarea name="notes" class="hbdMargined"><?=$charInfo['notes']?></textarea>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>