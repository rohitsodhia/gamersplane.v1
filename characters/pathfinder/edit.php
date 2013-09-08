<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT pathfinder.*, characters.userID, gms.gameID IS NOT NULL isGM FROM pathfinder_characters pathfinder INNER JOIN characters ON pathfinder.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE pathfinder.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
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
		<h2><img src="<?=SITEROOT?>/images/logos/pathfinder.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="jsHide">
			Javascript is disabled. This page won't show/work up properly.
		</div>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/pathfinder/<?=$pathOptions[1]?>">
			<input id="characterID" type="hidden" name="characterID" value="<?=$pathOptions[2]?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="medText lrBuffer shiftRight">Name</label>
				<label id="label_race" class="medText lrBuffer shiftRight">Race</label>
				<label id="label_size" class="medText lrBuffer">Size Modifier</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="medText lrBuffer">
				<input type="text" name="race" value="<?=$charInfo['race']?>" class="medText lrBuffer">
				<input id="size" type="text" name="size" value="<?=$charInfo['size']?>" class="lrBuffer">
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText lrBuffer shiftRight">Class(es)</label>
<!--					<label id="label_levels" class="shortText lrBuffer shiftRight">Level(s)</label>-->
				<label id="label_alignment" class="medText lrBuffer shiftRight">Alignment</label>
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
					<label id=\"label_{$short}\" class=\"textLabel shortText lrBuffer leftLabel\">{$stat}</label>
					<input type=\"text\" id=\"{$short}\" name=\"{$short}\" value=\"".$charInfo[$short]."\" maxlength=\"2\" class=\"stat lrBuffer\">
					<span id=\"{$short}Modifier\">{$bonus}</span>
				</div>
";
		$statBonus[$short] = $bonus;
	}
	
	if ($charInfo['size'] > 0) $charInfo['size'] = '+'.$charInfo['size'];
/*				<div class="tr">
					<label id="label_str" class="textLabel shortText lrBuffer leftLabel">Strength</label>
					<input type="text" id="str" name="str" class="stat lrBuffer">
					<span id="strModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_dex" class="textLabel shortText lrBuffer leftLabel">Dexterity</label>
					<input type="text" id="dex" name="dex" class="stat lrBuffer">
					<span id="dexModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_con" class="textLabel shortText lrBuffer leftLabel">Constitution</label>
					<input type="text" id="con" name="con" class="stat lrBuffer">
					<span id="conModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_int" class="textLabel shortText lrBuffer leftLabel">Intelligence</label>
					<input type="text" id="int" name="int" class="stat lrBuffer">
					<span id="intModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_wis" class="textLabel shortText lrBuffer leftLabel">Wisdom</label>
					<input type="text" id="wis" name="wis" class="stat lrBuffer">
					<span id="wisModifier">0</span>
				</div>
				<div class="tr">
					<label id="label_cha" class="textLabel shortText lrBuffer leftLabel">Charisma</label>
					<input type="text" id="cha" name="cha" class="stat lrBuffer">
					<span id="chaModifier">0</span>
				</div>*/
?>
			</div>
			
			<div id="savingThrows">
				<div class="tr labelTR">
					<label class="statCol shortNum lrBuffer first">Total</label>
					<label class="statCol shortNum lrBuffer">Base</label>
					<label class="statCol shortNum lrBuffer">Ability</label>
					<label class="statCol shortNum lrBuffer">Magic</label>
					<label class="statCol shortNum lrBuffer">Race</label>
					<label class="statCol shortNum lrBuffer">Misc</label>
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
			
			<div id="ac">
				<div class="tr labelTR">
					<label class="medNum lrBuffer first">Total AC</label>
					<label class="medNum">Armor</label>
					<label class="medNum">Shield</label>
					<label class="medNum">Dex</label>
					<label class="medNum">Class</label>
					<label class="medNum">Size</label>
					<label class="medNum">Natural</label>
					<label class="medNum">Deflection</label>
					<label class="medNum">Misc</label>
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
			<div id="combatBonuses">
				<div class="tr labelTR">
					<label class="statCol shortNum lrBuffer first">Total</label>
					<label class="statCol shortNum lrBuffer">Base</label>
					<label class="statCol shortNum lrBuffer">Ability</label>
					<label class="statCol shortNum lrBuffer">Size</label>
					<label class="statCol shortNum lrBuffer">Misc</label>
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
						<label class="statCol shortNum first">Total</label>
						<label class="statCol shortNum">Base</label>
						<label class="statCol shortNum">Str</label>
						<label class="statCol shortNum">Size</label>
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
						<label class="statCol shortNum first">Total</label>
						<label class="statCol shortNum">Base</label>
						<label class="statCol shortNum">Str</label>
						<label class="statCol shortNum">Dex</label>
						<label class="statCol shortNum">Size</label>
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
						<label class="medText">Skill</label>
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortNum alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Ranks</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
<?
	$skills = $mysql->query('SELECT pathfinder_skills.skillID, skillsList.name, pathfinder_skills.stat, pathfinder_skills.ranks, pathfinder_skills.misc FROM pathfinder_skills INNER JOIN skillsList USING (skillID) WHERE pathfinder_skills.characterID = '.$characterID.' ORDER BY skillsList.name');
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
		echo "\t\t\t\t\t<div id=\"skill_{$skillInfo['skillID']}\" class=\"skill tr clearfix\">\n";
		echo "\t\t\t\t\t\t<span class=\"skill_name textLabel medText\">".mb_convert_case($skillInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<span class=\"skill_total textLabel lrBuffer addStat_{$skillInfo['stat']} shortNum\">".showSign($statBonus[$skillInfo['stat']] + $skillInfo['ranks'] + $skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t\t\t<span class=\"skill_stat textLabel lrBuffer alignCenter shortNum\">".ucwords($skillInfo['stat'])."</span>\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][ranks]\" value=\"{$skillInfo['ranks']}\" class=\"skill_ranks shortNum lrBuffer\">\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][misc]\" value=\"{$skillInfo['misc']}\" class=\"skill_misc shortNum lrBuffer\">\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"skill{$skillInfo['skillID']}_remove\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$skillInfo['skillID']}\" class=\"skill_remove lrBuffer\">\n";
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
	$feats = $mysql->query('SELECT pathfinder_feats.featID, featsList.name FROM pathfinder_feats INNER JOIN featsList USING (featID) WHERE pathfinder_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		echo "\t\t\t\t\t<div id=\"feat_{$featInfo['featID']}\" class=\"feat tr clearfix\">\n";
		echo "\t\t\t\t\t\t<span class=\"feat_name textLabel\">".mb_convert_case($featInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<a href=\"".SITEROOT."/characters/pathfinder/featNotes/$characterID/{$featInfo['featID']}?modal=1\" id=\"featNotesLink_{$featInfo['featID']}\" class=\"feat_notesLink\">Notes</a>\n";
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
	$weapons = $mysql->query('SELECT * FROM pathfinder_weapons WHERE characterID = '.$characterID);
	$weaponNum = 1;
	while (($weaponInfo = $weapons->fetch()) || $weaponNum <= 2) {
?>
						<div class="weapon">
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
								<label class="shortText alignCenter lrBuffer">Critical</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
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
		$weaponNum++;
	}
?>
					</div>
				</div>
				<div id="armor" class="floatRight">
					<h2>Armor <a id="addArmor" href="">Add Armor</a></h2>
					<div>
<?
	$armors = $mysql->query('SELECT * FROM pathfinder_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	while (($armorInfo = $armors->fetch()) || $armorNum <= 1) {
?>
						<div class="armor">
							<div class="tr labelTR armor_firstRow">
								<label class="medText lrBuffer shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">AC Bonus</label>
								<label class="shortText alignCenter lrBuffer">Max Dex</label>
							</div>
							<div class="tr armor_firstRow">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][name]" value="<?=$armorInfo['name']?>" class="armor_name medText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][ac]" value="<?=$armorInfo['ac']?>" class="armors_ac shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][maxDex]" value="<?=$armorInfo['maxDex']?>" class="armor_maxDex shortText lrBuffer">
							</div>
							<div class="tr labelTR armor_secondRow">
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortText alignCenter lrBuffer">Check Penalty</label>
								<label class="shortText alignCenter lrBuffer">Spell Failure</label>
								<label class="shortNum alignCenter lrBuffer">Speed</label>
							</div>
							<div class="tr armor_secondRow">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][type]" value="<?=$armorInfo['type']?>" class="armor_type shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][check]" value="<?=$armorInfo['check']?>" class="armor_check shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][spellFailure]" value="<?=$armorInfo['spellFailure']?>" class="armor_spellFailure shortText lrBuffer">
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