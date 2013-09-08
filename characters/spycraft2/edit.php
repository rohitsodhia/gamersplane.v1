<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT spycraft2.*, characters.userID, gms.gameID IS NOT NULL isGM FROM spycraft2_characters spycraft2 INNER JOIN characters ON spycraft2.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE spycraft2.characterID = $characterID");
	$noChar = FALSE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('characterID', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'subdual', 'stress', 'ac_class', 'ac_armor', 'ac_dex', 'ac_misc', 'initiative_class', 'initiative_misc', 'bab', 'unarmed_misc', 'melee_misc', 'ranged_misc', 'actionDie_total', 'knowledge_misc', 'request_misc', 'gear_misc');
			$textVals = array('name', 'codename', 'class', 'talent', 'specialty', 'actionDie_dieType', 'items', 'notes');
			foreach ($numVals as $key) $charInfo[$key] = intval($charInfo[$key]);
			$charInfo['level'] = 0;
			preg_match_all('/\d+/', $charInfo['class'], $matches);
			foreach ($matches[0] as $level) $charInfo['level'] += $level;
			$noChar = FALSE;
			$fixedMenu = TRUE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Edit Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/spycraft2.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="jsHide">
			Javascript is disabled. This page won't show/work up properly.
		</div>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/spycraft2/<?=$pathOptions[1]?>">
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
				<label id="label_talent" class="medText lrBuffer shiftRight">Talent</label>
				<label id="label_specialty" class="medText lrBuffer shiftRight">Specialty</label>
			</div>
			<div class="tr">
				<input id="classes" type="text" name="class" value="<?=$charInfo['class']?>" class="lrBuffer">
				<input id="talent" type="text" name="talent" value="<?=$charInfo['talent']?>" class="medText lrBuffer alignLeft">
				<input id="specialty" type="text" name="specialty" value="<?=$charInfo['specialty']?>" class="medText lrBuffer alignLeft">
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
					<label class="leftLabel textLabel">Subdual</label>
					<input type="text" name="subdual" value="<?=$charInfo['subdual']?>" class="medNum">
				</div>
				<div class="tr">
					<label class="leftLabel textLabel">Stress</label>
					<input type="text" name="stress" value="<?=$charInfo['stress']?>" class="medNum">
				</div>
			</div>
			
			<div id="ac">
				<div class="tr labelTR">
					<label class="lrBuffer first">Total Def</label>
					<label>Class</label>
					<label>Armor</label>
					<label>Dex</label>
					<label>Misc</label>
				</div>
<? $acTotal = 10 + $charInfo['ac_class'] + $charInfo['ac_armor'] + $charInfo['ac_dex'] + $charInfo['ac_misc']; ?>
				<div class="tr">
					<span id="ac_total" class="lrBuffer"><?=$acTotal?></span>
					<span> = 10 + </span>
					<input type="text" name="ac_class" value="<?=$charInfo['ac_class']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_armor" value="<?=$charInfo['ac_armor']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_dex" value="<?=$charInfo['ac_dex']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_misc" value="<?=$charInfo['ac_misc']?>" class="acComponents lrBuffer">
				</div>
			</div>
			
			<br class="clear">
			<div id="combatBonuses">
				<div class="tr labelTR">
					<label class="statCol shortNum lrBuffer first">Total</label>
					<label class="statCol shortNum lrBuffer">Base/ Class</label>
					<label class="statCol shortNum lrBuffer">Ability</label>
					<label class="statCol shortNum lrBuffer">Misc</label>
				</div>
<?
	$initTotal = showSign($charInfo['initiative_class'] + $statBonus['dex'] + $charInfo['initiative_misc']);
	$unarmedTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['unarmed_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['ranged_misc']);
?>
				<div id="init" class="tr">
					<label class="leftLabel shortText">Initiative</label>
					<span id="initTotal" class="shortNum lrBuffer addStat_dex"><?=$initTotal?></span>
					<input id="initiative_class" type="text" name="initiative_class" value="<?=$charInfo['initiative_class']?>" class="lrBuffer">
					<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
					<input type="text" name="initiative_misc" value="<?=$charInfo['initiative_misc']?>" class="lrBuffer">
				</div>
				<div id="unarmed" class="tr">
					<label class="leftLabel shortText">Unarmed</label>
					<span id="unarmedTotal" class="shortNum lrBuffer addStat_str"><?=$unarmedTotal?></span>
					<input id="bab" type="text" name="bab" value="<?=$charInfo['bab']?>" class="lrBuffer">
					<span class="shortNum lrBuffer statBonus_str"><?=$statBonus['str']?></span>
					<input id="unarmed_misc" type="text" name="unarmed_misc" value="<?=$charInfo['unarmed_misc']?>" class="lrBuffer">
				</div>
<? $charInfo['bab'] = showSign($charInfo['bab']); ?>
				<div id="melee" class="tr">
					<label class="leftLabel shortText">Melee</label>
					<span id="meleeTotal" class="shortNum lrBuffer addStat_str"><?=$meleeTotal?></span>
					<span class="shortNum lrBuffer bab"><?=$charInfo['bab']?></span>
					<span class="shortNum lrBuffer statBonus_str"><?=$statBonus['str']?></span>
					<input id="melee_misc" type="text" name="melee_misc" value="<?=$charInfo['melee_misc']?>" class="lrBuffer">
				</div>
				<div id="ranged" class="tr">
					<label class="leftLabel shortText">Ranged</label>
					<span id="rangedTotal" class="shortNum lrBuffer addStat_dex"><?=$rangedTotal?></span>
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
					<label class="shortText">Check Bonus</label>
					<label class="statCol shortNum lrBuffer">Total</label>
					<label class="statCol shortNum lrBuffer">Level</label>
					<label class="statCol shortNum lrBuffer">Stat</label>
					<label class="statCol shortNum lrBuffer">Misc</label>
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Knowledge</label>
					<span id="knowledge_total" class="shortNum lrBuffer addLevel addStat_int"><?=showSign($charInfo['knowledge_misc'] + $charInfo['level'] + $statBonus['int'])?></span>
					<span class="shortNum lrBuffer level"><?=showSign($charInfo['level'])?></span>
					<span class="shortNum lrBuffer statBonus_int"><?=$statBonus['int']?></span>
					<input id="knowledge_misc" type="text" name="knowledge_misc" value="<?=$charInfo['knowledge_misc']?>" class="lrBuffer">
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Request</label>
					<span id="request_total" class="shortNum lrBuffer addLevel addStat_cha"><?=showSign($charInfo['request_misc'] + $charInfo['level'] + $statBonus['cha'])?></span>
					<span class="shortNum lrBuffer level"><?=showSign($charInfo['level'])?></span>
					<span class="shortNum lrBuffer statBonus_cha"><?=$statBonus['cha']?></span>
					<input id="request_misc" type="text" name="request_misc" value="<?=$charInfo['request_misc']?>" class="lrBuffer">
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Gear</label>
					<span id="gear_total" class="shortNum lrBuffer addLevel addStat_wis"><?=showSign($charInfo['gear_misc'] + $charInfo['level'] + $statBonus['wis'])?></span>
					<span class="shortNum lrBuffer level"><?=showSign($charInfo['level'])?></span>
					<span class="shortNum lrBuffer statBonus_wis"><?=$statBonus['wis']?></span>
					<input id="gear_misc" type="text" name="gear_misc" value="<?=$charInfo['gear_misc']?>" class="lrBuffer">
				</div>
			</div>
			
			<br class="clear">
			<div id="skills" class="clearfix">
				<h2>Skills</h2>
				<div id="addSkillWrapper">
					<input id="skillName" type="text" name="newSkill[name]" value="Skill Name" class="medText" autocomplete="off">
					<div id="skillAjaxResults">
					</div>
					<select id="skillStat_1" name="newSkill[stat_1]">
						<option value="str">Str</option>
						<option value="dex">Dex</option>
						<option value="con">Con</option>
						<option value="int">Int</option>
						<option value="wis">Wis</option>
						<option value="cha">Cha</option>
					</select>
					<select id="skillStat_2" name="newSkill[stat_2]">
						<option value=""></option>
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
					<label class="shortText alignCenter lrBuffer">Stat(s)</label>
					<label class="shortNum alignCenter lrBuffer">Ranks</label>
					<label class="shortNum alignCenter lrBuffer">Misc</label>
					<label class="medNum alignCenter lrBuffer">Error</label>
					<label class="medNum alignCenter lrBuffer">Threat</label>
				</div>
<?
	$skills = $mysql->query('SELECT spycraft2_skills.skillID, skillsList.name, spycraft2_skills.stat_1, spycraft2_skills.stat_2, spycraft2_skills.ranks, spycraft2_skills.misc, spycraft2_skills.error, spycraft2_skills.threat FROM spycraft2_skills INNER JOIN skillsList USING (skillID) WHERE spycraft2_skills.characterID = '.$characterID.' ORDER BY skillsList.name');
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
		echo "\t\t\t\t<div id=\"skill_{$skillInfo['skillID']}\" class=\"skill tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"skill_name textLabel medText\">".mb_convert_case($skillInfo['name'], MB_CASE_TITLE)."</span>\n";
		$total_1 = $statBonus[$skillInfo['stat_1']] + $skillInfo['ranks'] + $skillInfo['misc'];
		$total_2 = $statBonus[$skillInfo['stat_2']] + $skillInfo['ranks'] + $skillInfo['misc'];
		echo "\t\t\t\t\t<span class=\"skill_total textLabel lrBuffer shortNum\"><span class=\"skill_total_1 addStat_{$skillInfo['stat_1']}\">".showSign($total_1).'</span>'.($skillInfo['stat_2'] != '' ? "/<span class=\"skill_total_2 addStat_{$skillInfo['stat_2']}\">".showSign($total_2).'</span>' : '')."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_stat textLabel lrBuffer alignCenter shortText\">".ucwords($skillInfo['stat_1']).($skillInfo['stat_2'] != '' ? '/'.ucwords($skillInfo['stat_2']) : '')."</span>\n";
		echo "\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][ranks]\" value=\"{$skillInfo['ranks']}\" class=\"skill_ranks shortNum lrBuffer\">\n";
		echo "\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][misc]\" value=\"{$skillInfo['misc']}\" class=\"skill_misc shortNum lrBuffer\">\n";
		echo "\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][error]\" value=\"{$skillInfo['error']}\" class=\"skill_error medNum lrBuffer\">\n";
		echo "\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][threat]\" value=\"{$skillInfo['threat']}\" class=\"skill_threat medNum lrBuffer\">\n";
		echo "\t\t\t\t\t<input type=\"image\" name=\"skill{$skillInfo['skillID']}_remove\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$skillInfo['skillID']}\" class=\"skill_remove\">\n";
		echo "\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
			</div>

			<div class="clearfix">
				<div id="focuses">
					<h2>Focuses/Fortes</h2>
					<div id="addFocusWrapper">
						<label class="textLabel leftLabel">Name</label>
						<input id="focusName" type="text" name="newFocus_name" class="medText" autocomplete="off">
						<div id="focusAjaxResults">
						</div>
						<button id="addFocus" type="submit" name="newFocus_add" class="btn_add"></button>
					</div>
<?
	$focuses = $mysql->query('SELECT cf.focusID, fl.name, cf.forte FROM spycraft2_focuses cf INNER JOIN spycraft2_focusesList fl USING (focusID) WHERE cf.characterID = '.$characterID.' ORDER BY fl.name');
	echo "\t\t\t\t\t<p id=\"noFocuses\"".($focuses->rowCount()?' style="display: none;"':'').">This character currently has no focuses.</p>\n";
	echo "\t\t\t\t\t<div class=\"labelTR\"".($focuses->rowCount() == 0?' style="display: none;"':'')."><label class=\"shortNum alignCenter\">Forte</label></div>\n";
	if ($focuses->rowCount()) {
		foreach ($focuses as $focusInfo) {
			echo "\t\t\t\t\t<div id=\"focus_{$focusInfo['focusID']}\" class=\"focus tr clearfix\">\n";
			echo "\t\t\t\t\t\t<input type=\"checkbox\" name=\"focusForte[{$focusInfo['focusID']}]\"".($focusInfo['forte']?' checked="checked"':'')." class=\"shortNum\">\n";
			echo "\t\t\t\t\t\t<span class=\"focus_name textLabel\">".mb_convert_case($focusInfo['name'], MB_CASE_TITLE)."</span>\n";
			echo "\t\t\t\t\t\t<input type=\"image\" name=\"focusRemove_{$focusInfo['focusID']}\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$focusInfo['focusID']}\" class=\"focus_remove lrBuffer\">\n";
			echo "\t\t\t\t\t</div>\n";
		}
	}
?>
				</div>
				<div id="feats">
					<h2>Feats/Abilities</h2>
					<div id="addFeatWrapper">
						<label class="textLabel leftLabel">Name</label>
						<input id="featName" type="text" name="newFeat_name" class="medText" autocomplete="off">
						<div id="featAjaxResults">
						</div>
						<button id="addFeat" type="submit" name="newFeat_add" class="btn_add"></button>
					</div>
<?
	$feats = $mysql->query('SELECT spycraft2_feats.featID, featsList.name FROM spycraft2_feats INNER JOIN featsList USING (featID) WHERE spycraft2_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	echo "\t\t\t\t\t<p id=\"noFeats\"".($feats->rowCount()?' style="display: none;"':'').">This character currently has no feats/abilities.</p>\n";
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		echo "\t\t\t\t\t<div id=\"feat_{$featInfo['featID']}\" class=\"feat tr clearfix\">\n";
		echo "\t\t\t\t\t\t<span class=\"feat_name textLabel\">".mb_convert_case($featInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<a href=\"".SITEROOT."/characters/spycraft2/featNotes/$characterID/{$featInfo['featID']}?modal=1\" id=\"featNotesLink_{$featInfo['featID']}\" class=\"feat_notesLink\">Notes</a>\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"featRemove_{$featInfo['featID']}\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$featInfo['featID']}\" class=\"feat_remove lrBuffer\">\n";
		echo "\t\t\t\t\t</div>\n";
	} }
?>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2>Weapons <a id="addWeapon" href="">Add Weapon</a></h2>
					<div>
<?
	$weapons = $mysql->query('SELECT * FROM spycraft2_weapons WHERE characterID = '.$characterID);
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
								<label class="shortText alignCenter lrBuffer">Recoil</label>
								<label class="shortText alignCenter lrBuffer">Error/Threat</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][recoil]" value="<?=$weaponInfo['recoil']?>" class="weapon_recoil shortNum lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][et]" value="<?=$weaponInfo['et']?>" class="weapon_et shortNum lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][range]" value="<?=$weaponInfo['range']?>" class="weapon_range shortNum lrBuffer">
								<input type="text" name="weapons[<?=isset($weaponInfo['weaponID'])?$weaponInfo['weaponID']:'new_'.$weaponNum?>][type]" value="<?=$weaponInfo['type']?>" class="weapon_type shortNum lrBuffer">
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
	$armors = $mysql->query('SELECT * FROM spycraft2_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	while (($armorInfo = $armors->fetch()) || $armorNum <= 1) {
?>
						<div class="armor">
							<div class="tr labelTR armor_firstRow">
								<label class="medText lrBuffer shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">Dam Reduct</label>
								<label class="shortText alignCenter lrBuffer">Dam Resist</label>
							</div>
							<div class="tr armor_firstRow">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][name]" value="<?=$armorInfo['name']?>" class="armor_name medText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][reduction]" value="<?=$armorInfo['reduction']?>" class="armors_reduction shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][resist]" value="<?=$armorInfo['resist']?>" class="armors_resist shortText lrBuffer">
							</div>
							<div class="tr labelTR armor_secondRow">
								<label class="shortText alignCenter">Def Pen</label>
								<label class="shortText alignCenter">Check Penalty</label>
								<label class="shortText alignCenter">Speed</label>
								<label class="shortText alignCenter">Notice/Search DC</label>
							</div>
							<div class="tr armor_secondRow">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][penalty]" value="<?=$armorInfo['penalty']?>" class="armor_penalty shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][check]" value="<?=$armorInfo['check']?>" class="armor_check shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][speed]" value="<?=$armorInfo['speed']?>" class="armor_speed shortText lrBuffer">
								<input type="text" name="armors[<?=isset($armorInfo['armorID'])?$armorInfo['armorID']:'new_'.$armorNum?>][dc]" value="<?=$armorInfo['dc']?>" class="armor_dc shortText lrBuffer">
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