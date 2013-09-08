<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT spycraft2.*, characters.gameID, characters.userID, gms.gameID IS NOT NULL isGM FROM spycraft2_characters spycraft2 INNER JOIN characters ON spycraft2.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE spycraft2.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('characterID', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'subdual', 'stress', 'ac_class', 'ac_armor', 'ac_dex', 'ac_misc', 'initiative_class', 'initiative_misc', 'bab', 'unarmed_misc', 'melee_misc', 'ranged_misc', 'actionDie_total', 'knowledge_misc', 'request_misc', 'gear_misc');
			$textVals = array('name', 'codename', 'class', 'talent', 'specialty', 'actionDie_dieType', 'items', 'notes');
			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = strlen($value)?printReady($value):'&nbsp';
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}
			$charInfo['level'] = 0;
			preg_match_all('/\d+/', $charInfo['class'], $matches);
			foreach ($matches[0] as $level) $charInfo['level'] += $level;
			$noChar = FALSE;
			$fixedMenu = TRUE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/spycraft2.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div id="editCharLink"><a href="<?=SITEROOT?>/characters/spycraft2/<?=$characterID?>/edit">Edit Character</a></div>
		<div class="tr labelTR tr-noPadding">
			<label id="label_name" class="medText">Name</label>
			<label id="label_codename" class="medText">Codename</label>
		</div>
		<div class="tr dataTR">
			<div class="medText"><?=$charInfo['name']?></div>
			<div class="medText"><?=$charInfo['codename']?></div>
		</div>
		
		<div class="tr labelTR">
			<label id="label_classes" class="longText">Class(es)</label>
			<label id="label_talent" class="medText">Talent</label>
			<label id="label_specialty" class="medText">Specialty</label>
		</div>
		<div class="tr dataTR">
			<div class="longText"><?=$charInfo['class']?></div>
			<div class="medText"><?=$charInfo['talent']?></div>
			<div class="medText"><?=$charInfo['specialty']?></div>
		</div>
		
		<div id="stats">
<?
	$statBonus = array();
	foreach (array('Strength', 'Dexterity', 'Constitution', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = showSign(floor(($charInfo[$short] - 10)/2));
		echo "				<div class=\"tr dataTR\">
					<label id=\"label_{$short}\" class=\"textLabel shortText leftLabel\">{$stat}</label>
					<div class=\"stat\">{$charInfo[$short]}</div>
					<span id=\"{$short}Modifier\">{$bonus}</span>
				</div>
	";
		$statBonus[$short] = $bonus;
	}
	
	$charInfo['size'] = showSign($charInfo['size']);
?>
		</div>
		
		<div id="savingThrows">
			<div class="tr labelTR">
				<label class="statCol shortNum first">Total</label>
				<label class="statCol shortNum">Base</label>
				<label class="statCol shortNum">Ability</label>
				<label class="statCol shortNum">Misc</label>
			</div>
<?
	$fortBonus = showSign($charInfo['fort_base'] + $statBonus['con'] + $charInfo['fort_misc']);
	$refBonus = showSign($charInfo['ref_base'] + $statBonus['dex'] + $charInfo['ref_misc']);
	$willBonus = showSign($charInfo['will_base'] + $statBonus['wis'] + $charInfo['will_misc']);
?>
			<div id="fortRow" class="tr dataTR">
				<label class="leftLabel">Fortitude</label>
				<div id="fortTotal" class="shortNum"><?=$fortBonus?></div>
				<div class="shortNum"><?=showSign($charInfo['fort_base'])?></div>
				<div class="shortNum statBonus_con"><?=$statBonus['con']?></div>
				<div class="shortNum"><?=showSign($charInfo['fort_misc'])?></div>
			</div>
			<div id="refRow" class="tr dataTR">
				<label class="leftLabel">Reflex</label>
				<div id="refTotal" class="shortNum"><?=$refBonus?></div>
				<div class="shortNum"><?=showSign($charInfo['ref_base'])?></div>
				<div class="shortNum statBonus_dex"><?=$statBonus['dex']?></div>
				<div class="shortNum"><?=showSign($charInfo['ref_misc'])?></div>
			</div>
			<div id="willRow" class="tr dataTR">
				<label class="leftLabel">Will</label>
				<div id="willTotal" class="shortNum"><?=$willBonus?></div>
				<div class="shortNum"><?=showSign($charInfo['will_base'])?></div>
				<div class="shortNum statBonus_wis"><?=$statBonus['wis']?></div>
				<div class="shortNum"><?=showSign($charInfo['will_misc'])?></div>
			</div>
		</div>
		
		<div id="hp">
			<div class="tr">
				<label class="leftLabel textLabel">Vitality</label>
				<div><?=$charInfo['vitality']?></div>
			</div>
			<div class="tr">
				<label class="leftLabel textLabel">Wounds</label>
				<div><?=$charInfo['wounds']?></div>
			</div>
			<div class="tr">
				<label class="leftLabel textLabel">Subdual</label>
				<div><?=$charInfo['subdual']?></div>
			</div>
			<div class="tr">
				<label class="leftLabel textLabel">Stress</label>
				<div><?=$charInfo['stress']?></div>
			</div>
		</div>
		
		<div id="ac">
			<div class="tr labelTR">
				<label class="first">Total AC</label>
				<label>Class</label>
				<label>Armor</label>
				<label>Dex</label>
				<label>Misc</label>
			</div>
<? $acTotal = 10 + $charInfo['ac_class'] + $charInfo['ac_armor'] + $charInfo['ac_dex'] + $charInfo['ac_misc']; ?>
			<div class="tr dataTR">
				<div class="first"><?=$acTotal?></div>
				<div> = 10 + </div>
				<div><?=showSign($charInfo['ac_class'])?></div>
				<div><?=showSign($charInfo['ac_armor'])?></div>
				<div><?=showSign($charInfo['ac_dex'])?></div>
				<div><?=showSign($charInfo['ac_misc'])?></div>
			</div>
		</div>
		
		<br class="clear">
		<div id="combatBonuses" class="clearFix">
			<div class="tr labelTR">
				<label class="statCol shortNum first">Total</label>
				<label class="statCol shortNum">Base</label>
				<label class="statCol shortNum">Ability</label>
				<label class="statCol shortNum">Misc</label>
			</div>
<?
	$initTotal = showSign($statBonus['dex'] + $charInfo['initiative_misc']);
	$unarmedTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['unarmed_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['ranged_misc']);
?>
			<div id="init" class="tr dataTR">
				<label class="leftLabel shortText">Initiative</label>
				<span id="initTotal" class="shortNum"><?=$initTotal?></span>
				<span>&nbsp;</span>
				<span class="shortNum statBonus_dex"><?=$statBonus['dex']?></span>
				<div class="shortNum"><?=showSign($charInfo['initiative_misc'])?></div>
			</div>
			<div id="unarmed" class="tr dataTR">
				<label class="leftLabel shortText">Melee</label>
				<span id="unarmedTotal" class="shortNum"><?=$unarmedTotal?></span>
				<div class="shortNum"><?=showSign($charInfo['bab'])?></div>
				<span class="shortNum statBonus_str"><?=$statBonus['str']?></span>
				<div class="shortNum"><?=showSign($charInfo['unarmed_misc'])?></div>
			</div>
			<div id="melee" class="tr dataTR">
				<label class="leftLabel shortText">Melee</label>
				<span id="meleeTotal" class="shortNum"><?=$meleeTotal?></span>
				<div class="shortNum"><?=showSign($charInfo['bab'])?></div>
				<span class="shortNum statBonus_str"><?=$statBonus['str']?></span>
				<div class="shortNum"><?=showSign($charInfo['melee_misc'])?></div>
			</div>
			<div id="ranged" class="tr dataTR">
				<label class="leftLabel shortText">Ranged</label>
				<span id="rangedTotal" class="shortNum"><?=$rangedTotal?></span>
				<span class="shortNum bab"><?=showSign($charInfo['bab'])?></span>
				<span class="shortNum statBonus_dex"><?=$statBonus['dex']?></span>
				<div class="shortNum"><?=showSign($charInfo['ranged_misc'])?></div>
			</div>
		</div>
		
		<div id="actionDie">
			<div class="tr labelTR">
				<label class="statCol shortNum first">Total</label>
				<label class="statCol medNum">Dice Type</label>
			</div>
			<div class="tr">
				<label class="leftLabel shortText">Action Die</label>
				<span class="shortNum"><?=$charInfo['actionDie_total']?></span>
				<span class="medNum"><?=$charInfo['actionDie_dieType']?></span>
			</div>
		</div>
		
		<div id="extraStats">
			<div class="tr labelTR">
				<label class="shortText">Check Bonus</label>
				<label class="shortNum">Total</label>
				<label class="shortNum">Level</label>
				<label class="shortNum">Stat</label>
				<label class="shortNum">Misc</label>
			</div>
			<div class="tr">
				<label class="leftLabel shortText">Knowledge</label>
				<span class="shortNum"><?=showSign($charInfo['knowledge_misc'] + $charInfo['level'] + $statBonus['int'])?></span>
				<span class="shortNum"><?=showSign($charInfo['level'])?></span>
				<span class="shortNum"><?=$statBonus['int']?></span>
				<span class="shortNum"><?=showSign($charInfo['knowledge_misc'])?></span>
			</div>
			<div class="tr">
				<label class="leftLabel shortText">Request</label>
				<span class="shortNum"><?=showSign($charInfo['request_misc'] + $charInfo['level'] + $statBonus['cha'])?></span>
				<span class="shortNum"><?=showSign($charInfo['level'])?></span>
				<span class="shortNum"><?=$statBonus['cha']?></span>
				<span class="shortNum"><?=showSign($charInfo['request_misc'])?></span>
			</div>
			<div class="tr">
				<label class="leftLabel shortText">Gear</label>
				<span class="shortNum"><?=showSign($charInfo['gear_misc'] + $charInfo['level'] + $statBonus['wis'])?></span>
				<span class="shortNum"><?=showSign($charInfo['level'])?></span>
				<span class="shortNum"><?=$statBonus['wis']?></span>
				<span class="shortNum"><?=showSign($charInfo['gear_misc'])?></span>
			</div>
		</div>
		
		<br class="clear">
		<div id="skills" class="clearfix">
			<h2>Skills</h2>
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
		echo "\t\t\t<div id=\"skill_{$skillInfo['skillID']}\" class=\"skill tr clearfix\">\n";
		echo "\t\t\t\t<span class=\"skill_name medText\">".mb_convert_case($skillInfo['name'], MB_CASE_TITLE)."</span>\n";
		$total_1 = $statBonus[$skillInfo['stat_1']] + $skillInfo['ranks'] + $skillInfo['misc'];
		$total_2 = $statBonus[$skillInfo['stat_2']] + $skillInfo['ranks'] + $skillInfo['misc'];
		echo "\t\t\t\t<span class=\"skill_total textLabel lrBuffer shortNum\"><span class=\"skill_total_1 addStat_{$skillInfo['stat_1']}\">".showSign($total_1).'</span>'.($skillInfo['stat_2'] != '' ? "/<span class=\"skill_total_2 addStat_{$skillInfo['stat_2']}\">".showSign($total_2).'</span>' : '')."</span>\n";
		echo "\t\t\t\t<span class=\"skill_stat textLabel lrBuffer alignCenter shortText\">".ucwords($skillInfo['stat_1']).($skillInfo['stat_2'] != '' ? '/'.ucwords($skillInfo['stat_2']) : '')."</span>\n";
		echo "\t\t\t\t<span class=\"skill_ranks alignCenter shortNum lrBuffer\">".showSign($skillInfo['ranks'])."</span>\n";
		echo "\t\t\t\t<span class=\"skill_ranks alignCenter shortNum lrBuffer\">".showSign($skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t<span class=\"skill_ranks alignCenter medNum lrBuffer\">".$skillInfo['error']."</span>\n";
		echo "\t\t\t\t<span class=\"skill_ranks alignCenter medNum lrBuffer\">".$skillInfo['threat']."</span>\n";
		echo "\t\t\t</div>\n";
	} } else echo "\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
		</div>
		<div class="clearfix">
			<div id="focuses">
				<h2>Focuses/Fortes</h2>
<?
	$focuses = $mysql->query('SELECT cf.focusID, fl.name, cf.forte FROM spycraft2_focuses cf INNER JOIN spycraft2_focusesList fl USING (focusID) WHERE cf.characterID = '.$characterID.' ORDER BY fl.name');
	echo "\t\t\t\t<p id=\"noFocuses\"".($focuses->rowCount()?' style="display: none;"':'').">This character currently has no focuses.</p>\n";
	echo "\t\t\t\t<div class=\"labelTR\"".($focuses->rowCount() == 0?' style="display: none;"':'')."><label class=\"shortNum alignCenter\">Forte</label></div>\n";
	if ($focuses->rowCount()) { foreach ($focuses as $focusInfo) {
		echo "\t\t\t\t<div id=\"focus_{$focusInfo['focusID']}\" class=\"focus tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"shortNum alignCenter\"><img src=\"".SITEROOT."/images/".($focusInfo['forte']?'check':'cross_blank').".jpg\"></span>\n";
		echo "\t\t\t\t\t<span class=\"focus_name textLabel\">".mb_convert_case($focusInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t</div>\n";
	} }
?>
			</div>
			<div id="feats">
				<h2>Feats/Abilities</h2>
<?
	$feats = $mysql->query('SELECT spycraft2_feats.featID, featsList.name, spycraft2_feats.notes FROM spycraft2_feats INNER JOIN featsList USING (featID) WHERE spycraft2_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		echo "\t\t\t\t<div id=\"feat_{$featInfo['featID']}\" class=\"feat tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"feat_name\">".mb_convert_case($featInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t<a href=\"".SITEROOT."/characters/spycraft2/sheet/$characterID#featNotes_{$featInfo['featID']}\" id=\"featNotesLink_{$featInfo['featID']}\" class=\"feat_notesLink\">Notes</a>\n";
		echo "\t\t\t\t\t<div id=\"featNotes_{$featInfo['featID']}\" class=\"feat_notes\">".printReady($featInfo['notes'])."</div>\n";
		echo "\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
?>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="weapons" class="floatLeft">
				<h2>Weapons</h2>
				<div>
<?
	$weapons = $mysql->query('SELECT * FROM spycraft2_weapons WHERE characterID = '.$characterID);
	$weaponNum = 1;
	foreach ($weapons as $weaponInfo) {
?>
					<div class="weapon">
						<div class="tr labelTR">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
							<label class="shortText alignCenter lrBuffer">Damage</label>
						</div>
						<div class="tr">
							<span class="weapon_name medText lrBuffer"><?=$weaponInfo['name']?></span>
							<span class="weapons_ab shortText lrBuffer alignCenter"><?=$weaponInfo['ab']?></span>
							<span class="weapon_damage shortText lrBuffer alignCenter"><?=$weaponInfo['damage']?></span>
						</div>
						<div class="tr labelTR weapon_secondRow">
							<label class="shortText alignCenter lrBuffer">Recoil</label>
							<label class="shortText alignCenter lrBuffer">Error/Threat</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortNum alignCenter lrBuffer">Size</label>
						</div>
						<div class="tr weapon_secondRow">
							<span class="weapon_recoil shortText lrBuffer alignCenter"><?=$weaponInfo['recoil']?></span>
							<span class="weapon_et shortText lrBuffer alignCenter"><?=$weaponInfo['et']?></span>
							<span class="weapon_range shortText lrBuffer alignCenter"><?=$weaponInfo['range']?></span>
							<span class="weapon_type shortText lrBuffer alignCenter"><?=$weaponInfo['type']?></span>
							<span class="weapon_size shortText lrBuffer alignCenter"><?=$weaponInfo['size']?></span>
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer">Notes</label>
						</div>
						<div class="tr">
							<span class="weapon_notes lrBuffer"><?=$weaponInfo['notes']?></span>
						</div>
					</div>
<?
		$weaponNum++;
	}
?>
				</div>
			</div>
			<div id="armor" class="floatRight">
				<h2>Armor</h2>
				<div>
<?
	$armors = $mysql->query('SELECT * FROM spycraft2_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	foreach ($armors as $armorInfo) {
?>
					<div class="armor">
						<div class="tr labelTR armor_firstRow">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">Dam Reduct</label>
							<label class="shortText alignCenter lrBuffer">Dam Resist</label>
						</div>
						<div class="tr armor_firstRow">
							<span class="armor_name medText lrBuffer"><?=$armorInfo['name']?></span>
							<span class="armors_reduction shortText lrBuffer alignCenter"><?=$armorInfo['reduct']?></span>
							<span class="armor_resist shortText lrBuffer alignCenter"><?=$armorInfo['resist']?></span>
						</div>
						<div class="tr labelTR armor_secondRow">
							<label class="shortText alignCenter">Def Pen</label>
							<label class="shortText alignCenter">Check Penalty</label>
							<label class="shortText alignCenter">Speed</label>
							<label class="shortText alignCenter">Notice/Search DC</label>
						</div>
						<div class="tr armor_secondRow">
							<span class="armor_penalty shortText alignCenter"><?=$armorInfo['penalty']?></span>
							<span class="armor_check shortText alignCenter"><?=$armorInfo['check']?></span>
							<span class="armor_speed shortText alignCenter"><?=$armorInfo['speed']?></span>
							<span class="armor_dc shortText alignCenter"><?=$armorInfo['dc']?></span>
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer">Notes</label>
						</div>
						<div class="tr">
							<span class="armor_notes lrBuffer"><?=$armorInfo['notes']?></span>
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
			<div><?=$charInfo['items']?></div>
		</div>
		
		<div id="notes">
			<h2>Notes</h2>
			<div><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>