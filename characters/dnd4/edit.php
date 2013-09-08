<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT dnd4.*, characters.userID, gms.gameID IS NOT NULL isGM FROM dnd4_characters dnd4 INNER JOIN characters ON dnd4.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE dnd4.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('str', 'con', 'dex', 'int', 'wis', 'cha', 'ac_armor', 'ac_class', 'ac_feats', 'ac_enh', 'ac_misc', 'fort_class', 'fort_feats', 'fort_enh', 'fort_misc', 'ref_class', 'ref_feats', 'ref_enh', 'ref_misc', 'will_class', 'will_feats', 'will_enh', 'will_misc', 'init_misc', 'hp', 'surges', 'speed_base', 'speed_armor', 'speed_item', 'speed_misc', 'ap', 'piSkill', 'ppSkill', 'ab1_stat', 'ab1_class', 'ab1_prof', 'ab1_feat', 'ab1_enh', 'ab1_misc', 'ab2_stat', 'ab2_class', 'ab2_prof', 'ab2_feat', 'ab2_enh', 'ab2_misc', 'ab3_stat', 'ab3_class', 'ab3_prof', 'ab3_feat', 'ab3_enh', 'ab3_misc');
			$textVals = array('name', 'race', 'alignment', 'class', 'paragon', 'epic', 'ab1_ability', 'ab2_ability', 'ab3_ability', 'skills', 'feats', 'spells', 'weapons', 'armor', 'items', 'notes');
/*			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = printReady($value);
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}*/
			foreach ($numVals as $key) $charInfo[$key] = intval($charInfo[$key]);
			$charInfo['level'] = 0;
			preg_match_all('/\d+/', $charInfo['class'], $matches);
			foreach ($matches[0] as $level) $charInfo['level'] += $level;
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/dnd4.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="jsHide">
			Javascript is disabled. This page won't show/work up properly.
		</div>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/dnd4/<?=$pathOptions[1]?>">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="medText lrBuffer shiftRight">Name</label>
				<label id="label_race" class="medText lrBuffer shiftRight">Race</label>
				<label id="label_alignment" class="shortText lrBuffer shiftRight">Alignment</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="medText lrBuffer">
				<input type="text" name="race" value="<?=$charInfo['race']?>" class="medText lrBuffer">
				<select name="alignment" class="lrBuffer">
					<option value="g"<?=$charInfo['alignment'] == 'g'?' selected="selected"':''?>>Good</option>
					<option value="lg"<?=$charInfo['alignment'] == 'lg'?' selected="selected"':''?>>Lawful Good</option>
					<option value="e"<?=$charInfo['alignment'] == 'e'?' selected="selected"':''?>>Evil</option>
					<option value="ce"<?=$charInfo['alignment'] == 'ce'?' selected="selected"':''?>>Chaotic Evil</option>
					<option value="u"<?=$charInfo['alignment'] == 'u'?' selected="selected"':''?>>Unaligned</option>
				</select>
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText lrBuffer shiftRight">Class(es)/Level(s)</label>
				<label id="label_paragon" class="medText lrBuffer shiftRight">Paragon Path</label>
				<label id="label_epic" class="medText lrBuffer shiftRight">Epic Destinies</label>
			</div>
			<div class="tr">
				<input id="classes" type="text" name="class" value="<?=$charInfo['class']?>" class="longText lrBuffer">
				<input id="paragon" type="text" name="paragon" value="<?=$charInfo['paragon']?>" class="medText lrBuffer">
				<input id="epic" type="text" name="epic" value="<?=$charInfo['epic']?>" class="medText lrBuffer">
			</div>
			
			<div id="stats">
				<div class="tr labelTR">
					<label class="shortText lrBuffer">Stat</label>
					<label class="shortNum alignCenter lrBuffer">Score</label>
					<label class="shortNum alignCenter">Mod</label>
					<label class="shortNum alignCenter">Mod + 1/2 Lvl</label>
				</div>
<?
	$statBonus = array();
	foreach (array('Strength', 'Constitution', 'Dexterity', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = floor(($charInfo[$short] - 10) / 2);
		echo "				<div class=\"tr\">
					<label id=\"label_{$short}\" class=\"shortText lrBuffer leftLabel\">{$stat}</label>
					<input id=\"{$short}\" type=\"text\" name=\"{$short}\" value=\"".$charInfo[$short]."\" maxlength=\"2\" class=\"statInput stat lrBuffer\">
					<div id=\"{$short}Modifier\" class=\"shortNum alignCenter statBonus_{$short}\">".showSign($bonus)."</div>
					<div id=\"{$short}ModifierPL\" class=\"shortNum alignCenter addHL\">".showSign($bonus + floor($charInfo['level'] / 2))."</div>
				</div>
";
		$statBonus[$short] = $bonus;
	}
?>
			</div>
			
			<div id="saves">
				<div class="tr labelTR">
					<label class="shortNum lrBuffer first">Total</label>
					<label class="shortNum lrBuffer">10 + 1/2 Lvl</label>
					<label class="shortNum lrBuffer">Armor/ Ability</label>
					<label class="shortNum lrBuffer">Class</label>
					<label class="shortNum lrBuffer">Feats</label>
					<label class="shortNum lrBuffer">Enh</label>
					<label class="shortNum lrBuffer">Misc</label>
				</div>
<?
	$ac = 10 + floor($charInfo['level'] / 2) + $charInfo['ac_armor'] + $charInfo['ac_class'] + $charInfo['ac_feats'] + $charInfo['ac_enh'] + $charInfo['ac_misc'];
	$fortBonus = 10 + floor($charInfo['level'] / 2) + $charInfo['fort_armor'] + $charInfo['fort_class'] + $charInfo['fort_feats'] + $charInfo['fort_enh'] + $charInfo['fort_misc'] + ($statBonus['con'] > $statBonus['str']?$statBonus['con']:$statBonus['str']);
	$refBonus = 10 + floor($charInfo['level'] / 2) + $charInfo['ref_armor'] + $charInfo['ref_class'] + $charInfo['ref_feats'] + $charInfo['ref_enh'] + $charInfo['ref_misc'] + ($statBonus['dex'] > $statBonus['int']?$statBonus['dex']:$statBonus['int']);
	$willBonus = 10 + floor($charInfo['level'] / 2) + $charInfo['will_armor'] + $charInfo['will_class'] + $charInfo['will_feats'] + $charInfo['will_enh'] + $charInfo['will_misc'] + ($statBonus['wis'] > $statBonus['cha']?$statBonus['wis']:$statBonus['cha']);
?>
				<div id="acRow" class="tr">
					<label class="leftLabel">AC</label>
					<span id="acTotal" class="medNum lrBuffer addHL"><?=showSign($ac)?></span>
					<span class="medNum lrBuffer addHL"><?=showSign(10 + floor($charInfo['level'] / 2))?></span>
					<input type="text" name="ac_armor"  value="<?=$charInfo['ac_armor']?>" class="statInput lrBuffer">
					<input type="text" name="ac_class"  value="<?=$charInfo['ac_class']?>" class="statInput lrBuffer">
					<input type="text" name="ac_feats"  value="<?=$charInfo['ac_feats']?>" class="statInput lrBuffer">
					<input type="text" name="ac_enh"  value="<?=$charInfo['ac_enh']?>" class="statInput lrBuffer">
					<input type="text" name="ac_misc"  value="<?=$charInfo['ac_misc']?>" class="statInput lrBuffer">
				</div>
				<div id="fortRow" class="tr ">
					<label class="leftLabel">Fortitude</label>
					<span id="fortTotal" class="medNum lrBuffer addHL"><?=showSign($fortBonus)?></span>
					<span class="medNum lrBuffer addHL"><?=showSign(10 + floor($charInfo['level'] / 2))?></span>
					<span id="fortStatBonus" class="medNum lrBuffer"><?=showSign($statBonus['con'] > $statBonus['str']?$statBonus['con']:$statBonus['str'])?></span>
					<input type="text" name="fort_class"  value="<?=$charInfo['fort_class']?>" class="statInput lrBuffer">
					<input type="text" name="fort_feats"  value="<?=$charInfo['fort_feats']?>" class="statInput lrBuffer">
					<input type="text" name="fort_enh"  value="<?=$charInfo['fort_enh']?>" class="statInput lrBuffer">
					<input type="text" name="fort_misc"  value="<?=$charInfo['fort_misc']?>" class="statInput lrBuffer">
				</div>
				<div id="refRow" class="tr">
					<label class="leftLabel">Reflex</label>
					<span id="refTotal" class="medNum lrBuffer addHL"><?=showSign($refBonus)?></span>
					<span class="medNum lrBuffer addHL"><?=showSign(10 + floor($charInfo['level'] / 2))?></span>
					<span id="refStatBonus" class="medNum lrBuffer"><?=showSign($statBonus['dex'] > $statBonus['int']?$statBonus['dex']:$statBonus['int'])?></span>
					<input type="text" name="ref_class"  value="<?=$charInfo['ref_class']?>" class="statInput lrBuffer">
					<input type="text" name="ref_feats"  value="<?=$charInfo['ref_feats']?>" class="statInput lrBuffer">
					<input type="text" name="ref_enh"  value="<?=$charInfo['ref_enh']?>" class="statInput lrBuffer">
					<input type="text" name="ref_misc"  value="<?=$charInfo['ref_misc']?>" class="statInput lrBuffer">
				</div>
				<div id="willRow" class="tr">
					<label class="leftLabel">Will</label>
					<span id="willTotal" class="medNum lrBuffer addHL"><?=showSign($willBonus)?></span>
					<span class="medNum lrBuffer addHL"><?=showSign(10 + floor($charInfo['level'] / 2))?></span>
					<span id="willStatBonus" class="medNum lrBuffer"><?=showSign($statBonus['wis'] > $statBonus['cha']?$statBonus['wis']:$statBonus['cha'])?></span>
					<input type="text" name="will_class"  value="<?=$charInfo['will_class']?>" class="statInput lrBuffer">
					<input type="text" name="will_feats"  value="<?=$charInfo['will_feats']?>" class="statInput lrBuffer">
					<input type="text" name="will_enh"  value="<?=$charInfo['will_enh']?>" class="statInput lrBuffer">
					<input type="text" name="will_misc"  value="<?=$charInfo['will_misc']?>" class="statInput lrBuffer">
				</div>
			</div>
			
			<div id="init">
				<div class="tr labelTR">
					<label class="shortNum alignCenter lrBuffer first">Total</label>
					<label class="shortNum alignCenter lrBuffer">Dex</label>
					<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
					<label class="shortNum alignCenter lrBuffer">Misc</label>
				</div>
				<div class="tr">
					<label class="shortText alighRight leftLabel">Initiative</label>
					<div id="init_total" class="shortNum alignCenter lrBuffer addHL"><?=showSign($statBonus['dex'] + floor($charInfo['level'] / 2) + $charInfo['init_misc'])?></div>
					<div class="shortNum alignCenter statBonus_dex lrBuffer"><?=showSign($statBonus['dex'])?></div>
					<div class="shortNum alignCenter addHL lrBuffer">+<?=floor($charInfo['level'] / 2)?></div>
					<input id="init_misc" type="text" name="init_misc"  value="<?=$charInfo['init_misc']?>" class="statInput lrBuffer">
				</div>
			</div>
			
			<br class="clear">
			<div id="hpCol">
				<div id="hp">
					<div class="tr labelTR">
						<label class="medNum alignCenter lrBuffer">Total HP</label>
						<label class="medNum alignCenter lrBuffer">Bloodied</label>
						<label class="medNum alignCenter lrBuffer">Surge Value</label>
						<label class="medNum alignCenter lrBuffer">Surges/ Day</label>
					</div>
					<div class="tr">
						<input id="hpInput" type="text" name="hp" value="<?=$charInfo['hp']?>" class="medNum lrBuffer">
						<div id="bloodiedVal" class="medNum alignCenter lrBuffer cell"><?=floor($charInfo['hp'] / 2)?></div>
						<div id="surgeVal" class="medNum alignCenter lrBuffer cell"><?=floor($charInfo['hp'] / 4)?></div>
						<input type="text" name="surges" value="<?=$charInfo['surges']?>" class="medNum lrBuffer">
					</div>
				</div>
				
				<div id="movement">
					<div class="tr labelTR">
						<label class="shortNum alignCenter lrBuffer first">Total</label>
						<label class="shortNum alignCenter lrBuffer">Base</label>
						<label class="shortNum alignCenter lrBuffer">Armor</label>
						<label class="shortNum alignCenter lrBuffer">Item</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
					<div class="tr">
						<label class="medNum leftLabel">Speed</label>
						<div class="shortNum alignCenter lrBuffer cell total"><?=$charInfo['speed_base'] + $charInfo['speed_armor'] + $charInfo['speed_item'] + $charInfo['speed_misc']?></div>
						<input type="text" name="speed_base"  value="<?=$charInfo['speed_base']?>" class="statInput lrBuffer">
						<input type="text" name="speed_armor"  value="<?=$charInfo['speed_armor']?>" class="statInput lrBuffer">
						<input type="text" name="speed_item"  value="<?=$charInfo['speed_item']?>" class="statInput lrBuffer">
						<input type="text" name="speed_misc"  value="<?=$charInfo['speed_misc']?>" class="statInput lrBuffer">
					</div>
				</div>
				
				<div id="actionPoints">
					<label class="shortText leftLabel">Action Points</label>
					<input type="text" name="ap" value="<?=$charInfo['ap']?>" class="statInput">
				</div>
				
				<div id="passiveSenses">
					<div class="tr labelTR">
						<label class="medNum alignCenter">Total</label>
						<label class="medNum alignCenter">Skill</label>
					</div>
					<div class="tr">
						<label class="leftLabel">Passive Insight</label>
						<div class="medNum alignCenter cell total"><?=$charInfo['piSkill'] + 10?></div>
						<div class="shortNum alignCenter cell">10 + </div>
						<input type="text" name="piSkill" value="<?=$charInfo['piSkill']?>" class="statInput">
					</div>
					<div class="tr">
						<label class="leftLabel">Passive Perception</label>
						<div class="medNum alignCenter cell total"><?=$charInfo['ppSkill'] + 10?></div>
						<div class="shortNum alignCenter cell">10 + </div>
						<input type="text" name="ppSkill" value="<?=$charInfo['ppSkill']?>" class="statInput">
					</div>
				</div>
			</div>
			
			<div id="combatBonuses">
				<h2>Attack Bonuses <a id="addAttack" href="">Add Attack</a></h2>
<?
	$attacks = $mysql->query('SELECT attackID, ability, stat, class, prof, feat, enh, misc FROM dnd4_attacks WHERE characterID = '.$characterID);
	foreach ($attacks as $attackInfo) {
		$total = showSign(floor($charInfo['level'] / 2) + $attackInfo['stat'] + $attackInfo['class'] + $attackInfo['prof'] + $attackInfo['feat'] + $attackInfo['enh'] + $attackInfo['misc']);
?>
				<div class="attackBonusSet">
					<div class="tr">
						<label class="medNum leftLabel">Ability</label>
						<input type="text" name="attacks[<?=$attackInfo['attackID']?>][ability]" value="<?=$attackInfo['ability']?>" class="ability">
					</div>
					<div class="tr labelTR">
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
						<label class="shortNum alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Class</label>
						<label class="shortNum alignCenter lrBuffer">Prof</label>
						<label class="shortNum alignCenter lrBuffer">Feat</label>
						<label class="shortNum alignCenter lrBuffer">Enh</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
					<div class="tr">
						<span class="shortNum lrBuffer addHL total"><?=$total?></span>
						<span class="shortNum lrBuffer addHL">+<?=floor($charInfo['level'] / 2)?></span>
						<input type="text" name="attacks[<?=$attackInfo['attackID']?>][stat]" value="<?=$attackInfo['stat']?>" class="statInput lrBuffer">
						<input type="text" name="attacks[<?=$attackInfo['attackID']?>][class]" value="<?=$attackInfo['class']?>" class="statInput lrBuffer">
						<input type="text" name="attacks[<?=$attackInfo['attackID']?>][prof]" value="<?=$attackInfo['prof']?>" class="statInput lrBuffer">
						<input type="text" name="attacks[<?=$attackInfo['attackID']?>][feat]" value="<?=$attackInfo['feat']?>" class="statInput lrBuffer">
						<input type="text" name="attacks[<?=$attackInfo['attackID']?>][enh]" value="<?=$attackInfo['enh']?>" class="statInput lrBuffer">
						<input type="text" name="attacks[<?=$attackInfo['attackID']?>][misc]" value="<?=$attackInfo['misc']?>" class="statInput lrBuffer">
					</div>
				</div>
<?
	}
	for ($count = 1; $count + $attacks->rowCount() <= 3; $count++) {
?>
				<div class="attackBonusSet">
					<div class="tr">
						<label class="medNum leftLabel">Ability</label>
						<input type="text" name="attacks[new_<?=$count?>][ability]" class="ability">
					</div>
					<div class="tr labelTR">
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
						<label class="shortNum alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Class</label>
						<label class="shortNum alignCenter lrBuffer">Prof</label>
						<label class="shortNum alignCenter lrBuffer">Feat</label>
						<label class="shortNum alignCenter lrBuffer">Enh</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
					<div class="tr">
						<span class="shortNum lrBuffer addHL total">+<?=floor($charInfo['level'] / 2)?></span>
						<span class="shortNum lrBuffer addHL">+<?=floor($charInfo['level'] / 2)?></span>
						<input type="text" name="attacks[new_<?=$count?>][stat]" value="0" class="statInput lrBuffer">
						<input type="text" name="attacks[new_<?=$count?>][class]" value="0" class="statInput lrBuffer">
						<input type="text" name="attacks[new_<?=$count?>][prof]" value="0" class="statInput lrBuffer">
						<input type="text" name="attacks[new_<?=$count?>][feat]" value="0" class="statInput lrBuffer">
						<input type="text" name="attacks[new_<?=$count?>][enh]" value="0" class="statInput lrBuffer">
						<input type="text" name="attacks[new_<?=$count?>][misc]" value="0" class="statInput lrBuffer">
					</div>
				</div>
<? } ?>
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
						<label class="medText">Skill</label>
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortNum alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Ranks</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
<?
	$skills = $mysql->query('SELECT dnd4_skills.skillID, skillsList.name, dnd4_skills.stat, dnd4_skills.ranks, dnd4_skills.misc FROM dnd4_skills INNER JOIN skillsList USING (skillID) ORDER BY skillsList.name');
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
		echo "\t\t\t\t\t<div id=\"skill_{$skillInfo['skillID']}\" class=\"skill tr clearfix\">\n";
		echo "\t\t\t\t\t\t<span class=\"skill_name textLabel medText\">".mb_convert_case($skillInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<span class=\"skill_total textLabel lrBuffer addStat_{$skillInfo['stat']} addHL shortNum\">".showSign($statBonus[$skillInfo['stat']] + $skillInfo['ranks'] + $skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t\t\t<span class=\"skill_stat textLabel lrBuffer alignCenter shortNum\">".ucwords($skillInfo['stat'])."</span>\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][ranks]\" value=\"{$skillInfo['ranks']}\" class=\"skill_ranks shortNum lrBuffer\">\n";
		echo "\t\t\t\t\t\t<input type=\"text\" name=\"skills[{$skillInfo['skillID']}][misc]\" value=\"{$skillInfo['misc']}\" class=\"skill_misc shortNum lrBuffer\">\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"skill{$skillInfo['skillID']}_remove\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$skillInfo['skillID']}\" class=\"skill_remove lrBuffer\">\n";
		echo "\t\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
				</div>
				<div id="feats" class="floatRight">
					<h2>Feats/Features</h2>
					<div id="addFeatWrapper">
						<label class="textLabel leftLabel">Name</label>
						<input id="featName" type="text" name="newFeat_name" class="medText" autocomplete="off">
						<div id="featAjaxResults">
						</div>
						<button id="addFeat" type="submit" name="newFeat_add" class="btn_add"></button>
					</div>
<?
	$feats = $mysql->query('SELECT dnd4_feats.featID, featsList.name FROM dnd4_feats INNER JOIN featsList USING (featID) ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		echo "\t\t\t\t\t<div id=\"feat_{$featInfo['featID']}\" class=\"feat tr clearfix\">\n";
		echo "\t\t\t\t\t\t<span class=\"feat_name textLabel\">".mb_convert_case($featInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<a href=\"".SITEROOT."/characters/dnd4/featNotes/$characterID/{$featInfo['featID']}?modal=1\" id=\"featNotesLink_{$featInfo['featID']}\" class=\"feat_notesLink\">Notes</a>\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"featRemove_{$featInfo['featID']}\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$featInfo['featID']}\" class=\"feat_remove lrBuffer\">\n";
		echo "\t\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/features.</p>\n";
?>
				</div>
			</div>
			
			<div id="powers" class="clearfix">
				<h2>Powers</h2>
				<div id="addPowerWrapper">
					<input id="powerName" type="text" name="newPower[name]" value="Power" class="medText" autocomplete="off">
					<div id="powerAjaxResults">
					</div>
					<select id="powerType" name="newPower[type]">
						<option value="a">At-will</option>
						<option value="e">Encounter</option>
						<option value="d">Daily</option>
					</select>
					<button id="addPower" type="submit" name="newPower_add" class="btn_add"></button>
				</div>
				<div id="powers_atwill" class="powerCol first">
					<h3>At-Will</h3>
<?
	$powers = $mysql->query('SELECT name FROM dnd4_powers WHERE type = "a" AND characterID = '.$characterID);
	foreach ($powers as $power) {
		echo "\t\t\t\t\t<div id=\"power_".str_replace(' ', '_', $power['name'])."\" class=\"power\">\n";
		echo "\t\t\t\t\t\t<span class=\"power_name\">".mb_convert_case($power['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"removePower_".str_replace(' ', '_', $power['name'])."\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$power['name']}\" class=\"power_remove lrBuffer\">\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div>
				<div id="powers_encounter" class="powerCol">
					<h3>Encounter</h3>
<?
	$powers = $mysql->query('SELECT name FROM dnd4_powers WHERE type = "e" AND characterID = '.$characterID);
	foreach ($powers as $power) {
		echo "\t\t\t\t\t<div id=\"power_".str_replace(' ', '_', $power['name'])."\" class=\"power\">\n";
		echo "\t\t\t\t\t\t<span class=\"power_name\">".mb_convert_case($power['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"removePower_".str_replace(' ', '_', $power['name'])."\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$power['name']}\" class=\"power_remove lrBuffer\">\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div>
				<div id="powers_daily" class="powerCol">
					<h3>Daily</h3>
<?
	$powers = $mysql->query('SELECT name FROM dnd4_powers WHERE type = "d" AND characterID = '.$characterID);
	foreach ($powers as $power) {
		echo "\t\t\t\t\t<div id=\"power_".str_replace(' ', '_', $power['name'])."\" class=\"power\">\n";
		echo "\t\t\t\t\t\t<span class=\"power_name\">".mb_convert_case($power['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t\t<input type=\"image\" name=\"removePower_".str_replace(' ', '_', $power['name'])."\" src=\"".SITEROOT."/images/cross.jpg\" value=\"{$power['name']}\" class=\"power_remove lrBuffer\">\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div>
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