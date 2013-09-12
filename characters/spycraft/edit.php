<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'spycraft');
	if ($charInfo) {
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
		includeSystemInfo('spycraft');
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/spycraft.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/spycraft/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
				<label id="label_codename" class="medText lrBuffer borderBox shiftRight">Codename</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="medText lrBuffer">
				<input type="text" name="codename" value="<?=$charInfo['codename']?>" class="medText lrBuffer">
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText lrBuffer borderBox shiftRight">Class(es)/Level(s)</label>
				<label id="label_department" class="medText lrBuffer borderBox shiftRight">Department</label>
			</div>
			<div class="tr">
				<input id="classes" type="text" name="class" value="<?=$charInfo['class']?>" class="lrBuffer">
				<input id="department" type="text" name="department" value="<?=$charInfo['department']?>" class="medText lrBuffer alignLeft">
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
						<label class="statCol shortNum lrBuffer">Total</label>
						<label class="statCol shortNum lrBuffer">Base</label>
						<label class="statCol shortNum lrBuffer">Ability</label>
						<label class="statCol shortNum lrBuffer">Misc</label>
					</div>
<?
	$fortBonus = showSign($charInfo['fort_base'] + $statBonus['con'] + $charInfo['fort_misc']);
	$refBonus = showSign($charInfo['ref_base'] + $statBonus['dex'] + $charInfo['ref_misc']);
	$willBonus = showSign($charInfo['will_base'] + $statBonus['wis'] + $charInfo['will_misc']);
?>
					<div id="fortRow" class="tr">
						<label class="leftLabel">Fortitude</label>
						<span id="fortTotal" class="shortNum lrBuffer addStat_con"><?=$fortBonus?></span>
						<input type="text" name="fort_base"  value="<?=$charInfo['fort_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_con"><?=$statBonus['con']?></span>
						<input type="text" name="fort_misc"  value="<?=$charInfo['fort_misc']?>" class="lrBuffer">
					</div>
					<div id="refRow" class="tr">
						<label class="leftLabel">Reflex</label>
						<span id="refTotal" class="shortNum lrBuffer addStat_dex"><?=$refBonus?></span>
						<input type="text" name="ref_base"  value="<?=$charInfo['ref_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
						<input type="text" name="ref_misc"  value="<?=$charInfo['ref_misc']?>" class="lrBuffer">
					</div>
					<div id="willRow" class="tr">
						<label class="leftLabel">Will</label>
						<span id="willTotal" class="shortNum lrBuffer addStat_wis"><?=$willBonus?></span>
						<input type="text" name="will_base"  value="<?=$charInfo['will_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_wis"><?=$statBonus['wis']?></span>
						<input type="text" name="will_misc"  value="<?=$charInfo['will_misc']?>" class="lrBuffer">
					</div>
				</div>
				
				<div id="hp">
					<div class="tr">
						<label class="leftLabel textLabel">Vitality</label>
						<input type="text" name="vitality" value="<?=$charInfo['vitality']?>" class="medNum">
					</div>
					<div class="tr">
						<label class="leftLabel textLabel">Wounds</label>
						<input type="text" name="wounds" value="<?=$charInfo['wounds']?>" class="medNum">
					</div>
					<div class="tr">
						<label class="leftLabel textLabel">Base Speed</label>
						<input type="text" name="speed" value="<?=$charInfo['speed']?>" class="shortNum">
					</div>
				</div>
				
				<div id="ac">
					<div class="tr labelTR">
						<label>Total Def</label>
						<div class="fillerBlock cell medNum">&nbsp;</div>
						<label>Class/ Armor</label>
						<label>Dex</label>
						<label>Size</label>
						<label>Misc</label>
					</div>
<? $acTotal = 10 + $charInfo['ac_armor'] + $charInfo['ac_dex'] + $charInfo['ac_size'] + $charInfo['ac_misc']; ?>
					<div class="tr">
						<span id="ac_total" class="addSize"><?=$acTotal?></span>
						<span> = 10 + </span>
						<input type="text" name="ac_armor" value="<?=$charInfo['ac_armor']?>" class="acComponents lrBuffer">
						<input type="text" name="ac_dex" value="<?=$charInfo['ac_dex']?>" class="acComponents lrBuffer">
						<input type="text" name="ac_size" value="<?=$charInfo['ac_size']?>" class="acComponents lrBuffer">
						<input type="text" name="ac_misc" value="<?=$charInfo['ac_misc']?>" class="acComponents lrBuffer">
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="col">
					<div id="combatBonuses">
						<div class="tr labelTR">
							<div class="fillerBlock cell shortText">&nbsp;</div>
							<label class="statCol shortNum lrBuffer first">Total</label>
							<label class="statCol shortNum lrBuffer">Base</label>
							<label class="statCol shortNum lrBuffer">Ability</label>
							<label class="statCol shortNum lrBuffer">Misc</label>
						</div>
<?
	$initTotal = showSign($statBonus['dex'] + $charInfo['initiative_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['ranged_misc']);
?>
						<div id="init" class="tr">
							<label class="leftLabel shortText">Initiative</label>
							<span id="initTotal" class="shortNum lrBuffer addStat_dex"><?=$initTotal?></span>
							<span class="lrBuffer">&nbsp;</span>
							<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
							<input type="text" name="initiative_misc" value="<?=$charInfo['initiative_misc']?>" class="lrBuffer">
						</div>
						<div id="melee" class="tr">
							<label class="leftLabel shortText">Melee</label>
							<span id="meleeTotal" class="shortNum lrBuffer addStat_str addSize"><?=$meleeTotal?></span>
							<input id="bab" type="text" name="bab" value="<?=$charInfo['bab']?>" class="lrBuffer">
							<span class="shortNum lrBuffer statBonus_str"><?=$statBonus['str']?></span>
							<input id="melee_misc" type="text" name="melee_misc" value="<?=$charInfo['melee_misc']?>" class="lrBuffer">
						</div>
<? $charInfo['bab'] = showSign($charInfo['bab']); ?>
						<div id="ranged" class="tr">
							<label class="leftLabel shortText">Ranged</label>
							<span id="rangedTotal" class="shortNum lrBuffer addStat_dex addSize"><?=$rangedTotal?></span>
							<span class="shortNum lrBuffer bab"><?=$charInfo['bab']?></span>
							<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
							<input id="ranged_misc" type="text" name="ranged_misc" value="<?=$charInfo['ranged_misc']?>" class="lrBuffer">
						</div>
					</div>

					<div id="actionDie">
						<div class="tr labelTR">
							<div class="fillerBlock cell shortText">&nbsp;</div>
							<label class="statCol shortNum lrBuffer first">Total</label>
							<label class="statCol medNum lrBuffer">Dice Type</label>
						</div>
						<div class="tr">
							<label class="leftLabel shortText alignRight">Action Die</label>
							<input type="text" name="actionDie_total" value="<?=$charInfo['actionDie_total']?>" class="lrBuffer">
							<input type="text" name="actionDie_dieType" value="<?=$charInfo['actionDie_dieType']?>" class="medNum lrBuffer">
						</div>
					</div>
					
					<div id="extraStats">
						<div class="tr labelTR">
							<div class="fillerBlock cell shortText">&nbsp;</div>
							<label class="statCol shortNum lrBuffer first">Total</label>
							<label class="statCol shortNum lrBuffer">Stat</label>
							<label class="statCol shortNum lrBuffer">Misc</label>
						</div>
						<div class="tr">
							<label class="leftLabel shortText alignRight">Inspiration</label>
							<span id="inspiration_total" class="shortNum lrBuffer addStat_wis"><?=showSign($charInfo['inspiration_misc'] + $statBonus['wis'])?></span>
							<span class="shortNum lrBuffer statBonus_wis"><?=$statBonus['wis']?></span>
							<input id="inspiration_misc" type="text" name="inspiration_misc" value="<?=$charInfo['inspiration_misc']?>" class="lrBuffer">
						</div>
						<div class="tr">
							<label class="leftLabel shortText alignRight">Education</label>
							<span id="education_total" class="shortNum lrBuffer addStat_int"><?=showSign($charInfo['education_misc'] + $statBonus['int'])?></span>
							<span class="shortNum lrBuffer statBonus_int"><?=$statBonus['int']?></span>
							<input id="education_misc" type="text" name="education_misc" value="<?=$charInfo['education_misc']?>" class="lrBuffer">
						</div>
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
	$feats = $mysql->query('SELECT spycraft_feats.featID, featsList.name FROM spycraft_feats INNER JOIN featsList USING (featID) WHERE spycraft_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) featFormFormat($characterID, $featInfo); }
	else echo "\t\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
?>
					</div>
				</div>
			</div>
			
			<div id="skills" class="floatLeft">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined">
					<div id="addSkillWrapper">
						<input id="skillName" type="text" name="newSkill[name]" value="Skill Name" class="medText placeholder" autocomplete="off" data-placeholder="Skill Name">
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
						<label class="medText skill_name">Skill</label>
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortNum alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Ranks</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
						<label class="medNum alignCenter lrBuffer">Error</label>
						<label class="medNum alignCenter lrBuffer">Threat</label>
					</div>
<?
	$skills = $mysql->query('SELECT spycraft_skills.skillID, skillsList.name, spycraft_skills.stat, spycraft_skills.ranks, spycraft_skills.misc FROM spycraft_skills INNER JOIN skillsList USING (skillID) WHERE spycraft_skills.characterID = '.$characterID.' ORDER BY skillsList.name');
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) skillFormFormat($skillInfo, $statBonus[$skillInfo['stat']]);
	} else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark">Weapons <a id="addWeapon" href="">[ Add Weapon ]</a></h2>
					<div>
<?
	$weapons = $mysql->query('SELECT * FROM spycraft_weapons WHERE characterID = '.$characterID);
	$weaponNum = 1;
	while (($weaponInfo = $weapons->fetch()) || $weaponNum <= 2) weaponFormFormat($weaponNum++, $weaponInfo);
?>
					</div>
				</div>
				<div id="armor" class="floatRight">
					<h2 class="headerbar hbDark">Armor <a id="addArmor" href="">[ Add Armor ]</a></h2>
					<div>
<?
	$armors = $mysql->query('SELECT * FROM spycraft_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	while (($armorInfo = $armors->fetch()) || $armorNum <= 1) armorFormFormat($armorNum++, $armorInfo);
?>
					</div>
				</div>
			</div>
			
			<div id="items">
				<h2 class="headerbar hbDark">Items</h2>
				<textarea name="items" class="hbdMargined"><?=$charInfo['items']?></textarea>
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