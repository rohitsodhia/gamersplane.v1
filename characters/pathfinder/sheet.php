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
			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = strlen($value)?printReady($value):'&nbsp';
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}
			$noChar = FALSE;
		}
	}

	$alignments = array('lg' => 'Lawful Good', 'ng' => 'Neutral Good', 'cg' => 'Chaotic Good', 'ln' => 'Lawful Neutral', 'tn' => 'True Neutral', 'cn' => 'Chaotic Neutral', 'le' => 'Lawful Evil', 'ne' => 'Neutral Evil', 'ce' => 'Chaotic Evil'); 
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/pathfinder.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div id="editCharLink"><a href="<?=SITEROOT?>/characters/pathfinder/<?=$characterID?>/edit">Edit Character</a></div>
		<div class="tr labelTR tr-noPadding">
			<label id="label_name" class="medText lrBuffer">Name</label>
			<label id="label_race" class="medText lrBuffer">Race</label>
		</div>
		<div class="tr dataTR">
			<div class="medText"><?=$charInfo['name']?></div>
			<div class="medText"><?=$charInfo['race']?></div>
		</div>
		
		<div class="tr labelTR">
			<label id="label_classes" class="longText lrBuffer">Class(es)</label>
			<label id="label_alignment" class="medText lrBuffer">Alignment</label>
		</div>
		<div class="tr dataTR">
			<div class="longText"><?=$charInfo['class']?></div>
			<div class="longText"><?=$alignments[$charInfo['alignment']]?></div>
		</div>
		
		<div id="stats">
<?
	$statBonus = array();
	foreach (array('Strength', 'Dexterity', 'Constitution', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = showSign(floor(($charInfo[$short] - 10)/2));
		echo "				<div class=\"tr dataTR\">
					<label id=\"label_{$short}\" class=\"textLabel shortText lrBuffer leftLabel\">{$stat}</label>
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
				<label class="statCol shortNum lrBuffer first">Total</label>
				<label class="statCol shortNum lrBuffer">Base</label>
				<label class="statCol shortNum lrBuffer">Ability</label>
				<label class="statCol shortNum lrBuffer">Magic</label>
				<label class="statCol shortNum lrBuffer">Race</label>
				<label class="statCol shortNum lrBuffer">Misc</label>
			</div>
<?
	$fortBonus = showSign($charInfo['fort_base'] + $statBonus['con'] + $charInfo['fort_magic'] + $charInfo['fort_race'] + $charInfo['fort_misc']);
	$refBonus = showSign($charInfo['ref_base'] + $statBonus['dex'] + $charInfo['ref_magic'] + $charInfo['ref_race'] + $charInfo['ref_misc']);
	$willBonus = showSign($charInfo['will_base'] + $statBonus['wis'] + $charInfo['will_magic'] + $charInfo['will_race'] + $charInfo['will_misc']);
?>
			<div id="fortRow" class="tr dataTR">
				<label class="leftLabel">Fortitude</label>
				<div id="fortTotal" class="shortNum lrBuffer"><?=$fortBonus?></div>
				<div class="shortNum"><?=showSign($charInfo['fort_base'])?></div>
				<div class="shortNum lrBuffer statBonus_con"><?=$statBonus['con']?></div>
				<div class="shortNum"><?=showSign($charInfo['fort_magic'])?></div>
				<div class="shortNum"><?=showSign($charInfo['fort_race'])?></div>
				<div class="shortNum"><?=showSign($charInfo['fort_misc'])?></div>
			</div>
			<div id="refRow" class="tr dataTR">
				<label class="leftLabel">Reflex</label>
				<div id="refTotal" class="shortNum lrBuffer"><?=$refBonus?></div>
				<div class="shortNum"><?=showSign($charInfo['ref_base'])?></div>
				<div class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></div>
				<div class="shortNum"><?=showSign($charInfo['ref_magic'])?></div>
				<div class="shortNum"><?=showSign($charInfo['ref_race'])?></div>
				<div class="shortNum"><?=showSign($charInfo['ref_misc'])?></div>
			</div>
			<div id="willRow" class="tr dataTR">
				<label class="leftLabel">Will</label>
				<div id="willTotal" class="shortNum lrBuffer"><?=$willBonus?></div>
				<div class="shortNum"><?=showSign($charInfo['will_base'])?></div>
				<div class="shortNum lrBuffer statBonus_wis"><?=$statBonus['wis']?></div>
				<div class="shortNum"><?=showSign($charInfo['will_magic'])?></div>
				<div class="shortNum"><?=showSign($charInfo['will_race'])?></div>
				<div class="shortNum"><?=showSign($charInfo['will_misc'])?></div>
			</div>
		</div>
		
		<div id="hp" class="dataTR">
			<label class="leftLabel textLabel">Total HP</label>
			<div><?=$charInfo['hp']?></div>
			<label class="leftLabel textLabel">Damage Reduction</label>
			<div><?=$charInfo['dr']?></div>
		</div>
		
		<br class="clear">
		<div id="ac">
			<div class="tr labelTR">
				<label class="medNum lrBuffer first">Total AC</label>
				<label class="medNum lrBuffer">Armor</label>
				<label class="medNum lrBuffer">Shield</label>
				<label class="medNum lrBuffer">Dex</label>
				<label class="medNum lrBuffer">Class</label>
				<label class="medNum lrBuffer">Size</label>
				<label class="medNum lrBuffer">Natural</label>
				<label class="medNum lrBuffer">Deflection</label>
				<label class="medNum lrBuffer">Misc</label>
			</div>
<? $acTotal = 10 + $charInfo['ac_armor'] + $charInfo['ac_shield'] + $charInfo['ac_dex'] + $charInfo['ac_class'] + $charInfo['size'] + $charInfo['ac_natural'] + $charInfo['ac_deflection'] + $charInfo['ac_misc']; ?>
			<div class="tr dataTR">
				<div class="medNum first"><?=$acTotal?></div>
				<div class="medNum"> = 10 + </div>
				<div class="medNum"><?=showSign($charInfo['ac_armor'])?></div>
				<div class="medNum"><?=showSign($charInfo['ac_shield'])?></div>
				<div class="medNum"><?=$charInfo['ac_dex']?></div>
				<div class="medNum"><?=showSign($charInfo['ac_class'])?></div>
				<div class="medNum"><?=$charInfo['size']?></div>
				<div class="medNum"><?=showSign($charInfo['ac_natural'])?></div>
				<div class="medNum"><?=showSign($charInfo['ac_deflection'])?></div>
				<div class="medNum"><?=showSign($charInfo['ac_misc'])?></div>
			</div>
		</div>
		
		<div id="combatBonuses" class="clearFix">
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
			<div id="init" class="tr dataTR">
				<label class="leftLabel shortText">Initiative</label>
				<span id="initTotal" class="shortNum lrBuffer"><?=$initTotal?></span>
				<span class="lrBuffer">&nbsp;</span>
				<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
				<span class="lrBuffer">&nbsp;</span>
				<div class="shortNum"><?=showSign($charInfo['initiative_misc'])?></div>
			</div>
			<div id="melee" class="tr dataTR">
				<label class="leftLabel shortText">Melee</label>
				<span id="meleeTotal" class="shortNum lrBuffer"><?=$meleeTotal?></span>
				<div class="shortNum"><?=showSign($charInfo['bab'])?></div>
				<span class="shortNum lrBuffer statBonus_str"><?=$statBonus['str']?></span>
				<span class="shortNum lrBuffer sizeVal"><?=$charInfo['size']?></span>
				<div class="shortNum"><?=showSign($charInfo['melee_misc'])?></div>
			</div>
			<div id="ranged" class="tr dataTR">
				<label class="leftLabel shortText">Ranged</label>
				<span id="rangedTotal" class="shortNum lrBuffer"><?=$rangedTotal?></span>
				<span class="shortNum lrBuffer bab"><?=showSign($charInfo['bab'])?></span>
				<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
				<span class="shortNum lrBuffer sizeVal"><?=$charInfo['size']?></span>
				<div class="shortNum"><?=showSign($charInfo['ranged_misc'])?></div>
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
		
		<br class="clear">
		<div class="clearfix">
			<div id="skills" class="floatLeft">
				<h2>Skills</h2>
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
		echo "\t\t\t\t<div id=\"skill_{$skillInfo['skillID']}\" class=\"skill tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"skill_name medText\">".mb_convert_case($skillInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_total addStat_{$skillInfo['stat']} shortNum lrBuffer\">".showSign($statBonus[$skillInfo['stat']] + $skillInfo['ranks'] + $skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_stat alignCenter shortNum lrBuffer\">".ucwords($skillInfo['stat'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_ranks alignCenter shortNum lrBuffer\">".showSign($skillInfo['ranks'])."</span>\n";
		echo "\t\t\t\t\t<span class=\"skill_ranks alignCenter shortNum lrBuffer\">".showSign($skillInfo['misc'])."</span>\n";
		echo "\t\t\t\t</div>\n";
	} } else echo "\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
			</div>
			<div id="feats" class="floatRight">
				<h2>Feats/Abilities</h2>
<?
	$feats = $mysql->query('SELECT pathfinder_feats.featID, featsList.name, pathfinder_feats.notes FROM pathfinder_feats INNER JOIN featsList USING (featID) WHERE pathfinder_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		echo "\t\t\t\t<div id=\"feat_{$featInfo['featID']}\" class=\"feat tr clearfix\">\n";
		echo "\t\t\t\t\t<span class=\"feat_name\">".mb_convert_case($featInfo['name'], MB_CASE_TITLE)."</span>\n";
		echo "\t\t\t\t\t<a href=\"".SITEROOT."/characters/pathfinder/sheet/$characterID#featNotes_{$featInfo['featID']}\" id=\"featNotesLink_{$featInfo['featID']}\" class=\"feat_notesLink\">Notes</a>\n";
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
	$weapons = $mysql->query('SELECT * FROM pathfinder_weapons WHERE characterID = '.$characterID);
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
							<label class="shortText alignCenter lrBuffer">Critical</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortNum alignCenter lrBuffer">Size</label>
						</div>
						<div class="tr weapon_secondRow">
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
	$armors = $mysql->query('SELECT * FROM pathfinder_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	foreach ($armors as $armorInfo) {
?>
					<div class="armor">
						<div class="tr labelTR armor_firstRow">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">AC Bonus</label>
							<label class="shortText alignCenter lrBuffer">Max Dex</label>
						</div>
						<div class="tr armor_firstRow">
							<span class="armor_name medText lrBuffer"><?=$armorInfo['name']?></span>
							<span class="armors_ac shortText lrBuffer alignCenter"><?=$armorInfo['ac']?></span>
							<span class="armor_maxDex shortText lrBuffer alignCenter"><?=$armorInfo['maxDex']?></span>
						</div>
						<div class="tr labelTR armor_secondRow">
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortText alignCenter lrBuffer">Check Penalty</label>
							<label class="shortText alignCenter lrBuffer">Spell Failure</label>
							<label class="shortNum alignCenter lrBuffer">Speed</label>
						</div>
						<div class="tr armor_secondRow">
							<span class="armor_type shortText lrBuffer alignCenter"><?=$armorInfo['type']?></span>
							<span class="armor_check shortText lrBuffer alignCenter"><?=$armorInfo['check']?></span>
							<span class="armor_spellFailure shortText lrBuffer alignCenter"><?=$armorInfo['spellFailure']?></span>
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
		
		<div id="spells">
			<h2>Spells</h2>
			<div><?=$charInfo['spells']?></div>
		</div>
		
		<div id="notes">
			<h2>Notes</h2>
			<div><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>