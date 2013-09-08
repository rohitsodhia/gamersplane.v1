<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT spycraft.*, characters.gameID, characters.userID, gms.gameID IS NOT NULL isGM FROM spycraft_characters spycraft INNER JOIN characters ON spycraft.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE spycraft.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'speed', 'ac_armor', 'ac_dex', 'ac_size', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc', 'actionDie_total', 'inspiration_misc', 'education_misc');
			$textVals = array('name', 'codename', 'class', 'department', 'actionDie_dieType', 'items', 'notes');
			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = strlen($value)?printReady($value):'&nbsp';
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}
			$noChar = FALSE;
			$fixedMenu = TRUE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/spycraft.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div id="editCharLink"><a href="<?=SITEROOT?>/characters/spycraft/<?=$characterID?>/edit">Edit Character</a></div>
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
			<label id="label_alignment" class="shortText">Department</label>
		</div>
		<div class="tr dataTR">
			<div class="longText"><?=$charInfo['class']?></div>
			<div class="shortText"><?=$charInfo['department']?></div>
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
				<label class="leftLabel textLabel">Base Speed</label>
				<div><?=$charInfo['speed']?></div>
			</div>
		</div>
		
		<div id="ac">
			<div class="tr labelTR">
				<label class="first">Total AC</label>
				<label>Class/ Armor</label>
				<label>Dex</label>
				<label>Size</label>
				<label>Misc</label>
			</div>
<? $acTotal = 10 + $charInfo['ac_armor'] + $charInfo['ac_dex'] + $charInfo['ac_size'] + $charInfo['ac_misc']; ?>
			<div class="tr dataTR">
				<div class="first"><?=$acTotal?></div>
				<div> = 10 + </div>
				<div><?=showSign($charInfo['ac_armor'])?></div>
				<div><?=showSign($charInfo['ac_dex'])?></div>
				<div><?=showSign($charInfo['ac_size'])?></div>
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
				<label class="statCol shortNum first">Total</label>
				<label class="statCol shortNum">Stat</label>
				<label class="statCol shortNum">Misc</label>
			</div>
			<div class="tr">
				<label class="leftLabel shortText">Inspiration</label>
				<span id="inspiration_total" class="shortNum addStat_wis"><?=showSign($charInfo['inspiration_misc'] + $statBonus['wis'])?></span>
				<span class="shortNum statBonus_wis"><?=$statBonus['wis']?></span>
				<span class="shortNum"><?=$charInfo['inspiration_misc']?></span>
			</div>
			<div class="tr">
				<label class="leftLabel shortText">Education</label>
				<span id="education_total" class="shortNum addStat_int"><?=showSign($charInfo['education_misc'] + $statBonus['int'])?></span>
				<span class="shortNum statBonus_int"><?=$statBonus['int']?></span>
				<span class="shortNum"><?=$charInfo['education_misc']?></span>
			</div>
		</div>
		
		<br class="clear">
		<div class="clearfix">
			<div id="skills" class="floatLeft">
				<h2>Skills</h2>
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
		echo "\t\t\t\t<div id=\"skill_{$skillInfo['skillID']}\" class=\"skill tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"skill_name medText\">".mb_convert_case($skillInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_total addStat_{$skillInfo['stat']} shortNum lrBuffer\">".showSign($statBonus[$skillInfo['stat']] + $skillInfo['ranks'] + $skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_stat alignCenter shortNum lrBuffer\">".ucwords($skillInfo['stat'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_ranks alignCenter shortNum lrBuffer\">".showSign($skillInfo['ranks'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_ranks alignCenter shortNum lrBuffer\">".showSign($skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_ranks alignCenter medNum lrBuffer\">".$skillInfo['error']."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_ranks alignCenter medNum lrBuffer\">".$skillInfo['threat']."</span>\n";
		echo "\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
			</div>
			<div id="feats" class="floatRight">
				<h2>Feats/Abilities</h2>
<?
	$feats = $mysql->query('SELECT spycraft_feats.featID, featsList.name, spycraft_feats.notes FROM spycraft_feats INNER JOIN featsList USING (featID) WHERE spycraft_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		echo "\t\t\t\t<div id=\"feat_{$featInfo['featID']}\" class=\"feat tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"feat_name\">".mb_convert_case($featInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t<a href=\"".SITEROOT."/characters/spycraft/sheet/$characterID#featNotes_{$featInfo['featID']}\" id=\"featNotesLink_{$featInfo['featID']}\" class=\"feat_notesLink\">Notes</a>\n";
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
	$weapons = $mysql->query('SELECT * FROM spycraft_weapons WHERE characterID = '.$characterID);
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
							<label class="shortText alignCenter lrBuffer">Error</label>
							<label class="shortText alignCenter lrBuffer">Critical</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortNum alignCenter lrBuffer">Size</label>
						</div>
						<div class="tr weapon_secondRow">
							<span class="weapon_crit shortText lrBuffer alignCenter"><?=$weaponInfo['error']?></span>
							<span class="weapon_crit shortText lrBuffer alignCenter"><?=$weaponInfo['crit']?></span>
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
	$armors = $mysql->query('SELECT * FROM spycraft_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	foreach ($armors as $armorInfo) {
?>
					<div class="armor">
						<div class="tr labelTR armor_firstRow">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">Def Bonus</label>
							<label class="shortText alignCenter lrBuffer">Dam Resist</label>
						</div>
						<div class="tr armor_firstRow">
							<span class="armor_name medText lrBuffer"><?=$armorInfo['name']?></span>
							<span class="armors_ac shortText lrBuffer alignCenter"><?=$armorInfo['def']?></span>
							<span class="armor_maxDex shortText lrBuffer alignCenter"><?=$armorInfo['resist']?></span>
						</div>
						<div class="tr labelTR armor_secondRow">
							<label class="shortText alignCenter lrBuffer">Max Dex</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortText alignCenter lrBuffer">Check Penalty</label>
							<label class="shortNum alignCenter lrBuffer">Speed</label>
						</div>
						<div class="tr armor_secondRow">
							<span class="armor_maxDex shortText lrBuffer alignCenter"><?=$armorInfo['maxDex']?></span>
							<span class="armor_type shortText lrBuffer alignCenter"><?=$armorInfo['type']?></span>
							<span class="armor_check shortText lrBuffer alignCenter"><?=$armorInfo['check']?></span>
							<span class="armor_speed shortText lrBuffer alignCenter"><?=$armorInfo['speed']?></span>
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