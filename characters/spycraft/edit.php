<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT spycraft.*, characters.userID, gms.gameID IS NOT NULL isGM FROM spycraft_characters spycraft INNER JOIN characters ON spycraft.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE spycraft.characterID = $characterID");
	$noChar = FALSE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'speed', 'ac_armor', 'ac_dex', 'ac_size', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc', 'actionDie_total', 'inspiration_misc', 'education_misc');
			$textVals = array('name', 'codename', 'class', 'department', 'actionDie_dieType', 'items', 'notes');
			foreach ($numVals as $key) $charInfo[$key] = intval($charInfo[$key]);
			$noChar = FALSE;
			$fixedMenu = TRUE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Edit Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/spycraft.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="jsHide">
			Javascript is disabled. This page won't show/work up properly.
		</div>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/spycraft/<?=$pathOptions[1]?>">
			<input id="characterID" type="hidden" name="characterID" value="<?=$pathOptions[2]?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="medText lrBuffer shiftRight">Name</label>
				<label id="label_codename" class="medText lrBuffer shiftRight">Codename</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="medText lrBuffer">
				<input type="text" name="codename" value="<?=$charInfo['codename']?>" class="medText lrBuffer">
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText lrBuffer shiftRight">Class(es)</label>
				<label id="label_department" class="shortText lrBuffer shiftRight">Department</label>
			</div>
			<div class="tr">
				<input id="classes" type="text" name="class" value="<?=$charInfo['class']?>" class="lrBuffer">
				<input id="department" type="text" name="department" value="<?=$charInfo['department']?>" class="shortText lrBuffer alignLeft">
			</div>
			
			<div id="stats">
<?
	$statBonus = array();
	foreach (array('Strength', 'Dexterity', 'Constitution', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = floor(($charInfo[$short] - 10)/2);
		if ($bonus >= 0) $bonus = '+'.$bonus;
		echo "				<div class=\"tr\">
					<label id=\"label_{$short}\" class=\"textLabel shortText lrBuffer leftLabel\">{$stat}</label>
					<input type=\"text\" id=\"{$short}\" name=\"{$short}\" value=\"".$charInfo[$short]."\" maxlength=\"2\" class=\"stat lrBuffer\">
					<span id=\"{$short}Modifier\">{$bonus}</span>
				</div>
";
		$statBonus[$short] = $bonus;
	}
	
	if ($charInfo['size'] > 0) $charInfo['size'] = '+'.$charInfo['size'];
?>
			</div>
			
			<div id="savingThrows">
				<div class="tr labelTR">
					<label class="statCol shortNum lrBuffer first">Total</label>
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
					<label class="lrBuffer first">Total Def</label>
					<label>Class/ Armor</label>
					<label>Dex</label>
					<label>Size</label>
					<label>Misc</label>
				</div>
<? $acTotal = 10 + $charInfo['ac_armor'] + $charInfo['ac_dex'] + $charInfo['ac_size'] + $charInfo['ac_misc']; ?>
				<div class="tr">
					<span id="ac_total" class="lrBuffer addSize"><?=$acTotal?></span>
					<span> = 10 + </span>
					<input type="text" name="ac_armor" value="<?=$charInfo['ac_armor']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_dex" value="<?=$charInfo['ac_dex']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_size" value="<?=$charInfo['ac_size']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_misc" value="<?=$charInfo['ac_misc']?>" class="acComponents lrBuffer">
				</div>
			</div>
			
			<br class="clear">
			<div id="combatBonuses">
				<div class="tr labelTR">
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
					<label class="statCol shortNum lrBuffer first">Total</label>
					<label class="statCol medNum lrBuffer">Dice Type</label>
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Action Die</label>
					<input type="text" name="actionDie_total" value="<?=$charInfo['actionDie_total']?>" class="lrBuffer">
					<input type="text" name="actionDie_dieType" value="<?=$charInfo['actionDie_dieType']?>" class="medNum lrBuffer">
				</div>
			</div>
			
			<div id="extraStats">
				<div class="tr labelTR">
					<label class="statCol shortNum lrBuffer first">Total</label>
					<label class="statCol shortNum lrBuffer">Stat</label>
					<label class="statCol shortNum lrBuffer">Misc</label>
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Inspiration</label>
					<span id="inspiration_total" class="shortNum lrBuffer addStat_wis"><?=showSign($charInfo['inspiration_misc'] + $statBonus['wis'])?></span>
					<span class="shortNum lrBuffer statBonus_wis"><?=$statBonus['wis']?></span>
					<input id="inspiration_misc" type="text" name="inspiration_misc" value="<?=$charInfo['inspiration_misc']?>" class="lrBuffer">
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Education</label>
					<span id="education_total" class="shortNum lrBuffer addStat_int"><?=showSign($charInfo['education_misc'] + $statBonus['int'])?></span>
					<span class="shortNum lrBuffer statBonus_int"><?=$statBonus['int']?></span>
					<input id="education_misc" type="text" name="education_misc" value="<?=$charInfo['education_misc']?>" class="lrBuffer">
				</div>
			</div>
			
			<br class="clear">
			<div class="clearfix">
				<div id="skills" class="floatLeft">
					<h2>Skills</h2>
					<div id="addSkillWrapper">
						<input id="skillName" type="text" name="newSkill[name]" value="Skill Name" class="medText" autocomplete="off">
						<div id="skillAjaxResults">
						</div>
						<select id="skillStat" name="newSkill[stat]">
							<option value="str">Str</option>
							<option value="dex">Dex</option>
							<option value="con">Con</option>
							<option value="int">Int</option>
							<option value="wis">Wis</option>
							<option value="cha">Cha</option>
						</select>
						<button id="addSkill" type="submit" name="newSkill_add" class="btn_add"></button>
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
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
		echo "\t\t\t\t\t<div id=\"skill_{$skillInfo['skillID']}\" class=\"skill tr clearfix\">\n";
		echo "\t\t\t\t\t\t<span class=\"skill_name textLabel medText\">".mb_convert_case($skillInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<span class=\"skill_total textLabel lrBuffer addStat_{$skillInfo['stat']} shortNum\">".showSign($statBonus[$skillInfo['stat']] + $skillInfo['ranks'] + $skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t\t\t<span class=\"skill_stat textLabel lrBuffer alignCenter shortNum\">".ucwords($skillInfo['stat'])."</span>\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][ranks]\" value=\"{$skillInfo['ranks']}\" class=\"skill_ranks shortNum lrBuffer\">\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][misc]\" value=\"{$skillInfo['misc']}\" class=\"skill_misc shortNum lrBuffer\">\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][error]\" value=\"{$skillInfo['error']}\" class=\"skill_error medNum lrBuffer\">\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][threat]\" value=\"{$skillInfo['threat']}\" class=\"skill_threat medNum lrBuffer\">\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"skill{$skillInfo['skillID']}_remove\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$skillInfo['skillID']}\" class=\"skill_remove\">\n";
		echo "\t\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
				</div>
				<div id="feats" class="floatRight">
					<h2>Feats/Abilities</h2>
					<div id="addFeatWrapper">
						<label class="textLabel leftLabel">Name</label>
						<input id="featName" type="text" name="newFeat_name" class="medText" autocomplete="off">
						<div id="featAjaxResults">
						</div>
						<button id="addFeat" type="submit" name="newFeat_add" class="btn_add"></button>
					</div>
<?
	$feats = $mysql->query('SELECT spycraft_feats.featID, featsList.name FROM spycraft_feats INNER JOIN featsList USING (featID) WHERE spycraft_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		echo "\t\t\t\t\t<div id=\"feat_{$featInfo['featID']}\" class=\"feat tr clearfix\">\n";
		echo "\t\t\t\t\t\t<span class=\"feat_name textLabel\">".mb_convert_case($featInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<a href=\"".SITEROOT."/characters/spycraft/featNotes/$characterID/{$featInfo['featID']}?modal=1\" id=\"featNotesLink_{$featInfo['featID']}\" class=\"feat_notesLink\">Notes</a>\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"featRemove_{$featInfo['featID']}\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$featInfo['featID']}\" class=\"feat_remove lrBuffer\">\n";
		echo "\t\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
?>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2>Weapons <a id="addWeapon" href="">Add Weapon</a></h2>
					<div>
<?
	$weapons = $mysql->query('SELECT * FROM spycraft_weapons WHERE characterID = '.$characterID);
	$weaponNum = 1;
	while (($weaponInfo = $weapons->fetch()) || $weaponNum <= 2) {
?>
						<div class="weapon<?=isset($weaponInfo['weaponID'])?'':''?>">
							<div class="tr labelTR">
								<label class="medText lrBuffer shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
								<label class="shortText alignCenter lrBuffer">Damage</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][name]" value="<?=$weaponInfo['name']?>" class="weapon_name medText lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][ab]" value="<?=$weaponInfo['ab']?>" class="weapons_ab shortText lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][damage]" value="<?=$weaponInfo['damage']?>" class="weapon_damage shortText lrBuffer">
							</div>
							<div class="tr labelTR weapon_secondRow">
								<label class="shortText alignCenter lrBuffer">Error</label>
								<label class="shortText alignCenter lrBuffer">Critical</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][error]" value="<?=$weaponInfo['error']?>" class="weapon_error shortText lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][crit]" value="<?=$weaponInfo['crit']?>" class="weapon_crit shortText lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][range]" value="<?=$weaponInfo['range']?>" class="weapon_range shortText lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][type]" value="<?=$weaponInfo['type']?>" class="weapon_type shortText lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][size]" value="<?=$weaponInfo['size']?>" class="weapon_size shortNum lrBuffer">
							</div>
							<div class="tr labelTR">
								<label class="lrBuffer shiftRight">Notes</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][notes]" value="<?=$weaponInfo['notes']?>" class="weapon_notes lrBuffer">
							</div>
						</div>
<?
		$weaponInfo = array();
		$weaponNum++;
		
	}
?>
					</div>
				</div>
				<div id="armor" class="floatRight">
					<h2>Armor <a id="addArmor" href="">Add Armor</a></h2>
					<div>
<?
	$armors = $mysql->query('SELECT * FROM spycraft_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	while (($armorInfo = $armors->fetch()) || $armorNum <= 1) {
?>
						<div class="armor">
							<div class="tr labelTR armor_firstRow">
								<label class="medText lrBuffer shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">Def Bonus</label>
								<label class="shortText alignCenter lrBuffer">Dam Resist</label>
							</div>
							<div class="tr armor_firstRow">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][name]" value="<?=$armorInfo['name']?>" class="armor_name medText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][def]" value="<?=$armorInfo['def']?>" class="armors_def shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][resist]" value="<?=$armorInfo['resist']?>" class="armors_resist shortText lrBuffer">
							</div>
							<div class="tr labelTR armor_secondRow">
								<label class="shortText alignCenter lrBuffer">Max Dex</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortText alignCenter lrBuffer">Check Penalty</label>
								<label class="shortNum alignCenter lrBuffer">Speed</label>
							</div>
							<div class="tr armor_secondRow">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][maxDex]" value="<?=$armorInfo['maxDex']?>" class="armor_maxDex shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][type]" value="<?=$armorInfo['type']?>" class="armor_type shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][check]" value="<?=$armorInfo['check']?>" class="armor_check shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][speed]" value="<?=$armorInfo['speed']?>" class="armor_speed shortNum lrBuffer">
							</div>
							<div class="tr labelTR">
								<label class="lrBuffer shiftRight">Notes</label>
							</div>
							<div class="tr">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][notes]" value="<?=$armorInfo['notes']?>" class="armor_notes lrBuffer">
							</div>
						</div>
<?
		$armorInfo = array();
		$armorNum++;
	}
?>
					</div>
				</div>
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