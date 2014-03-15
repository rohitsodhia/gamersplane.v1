<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$system = 'dnd3';
	$charInfo = getCharInfo($characterID, $system);
	if ($charInfo) {
		if ($viewerStatus = allowCharView($characterID, $userID)) {
			$noChar = FALSE;
			includeSystemInfo($system);

			if ($viewerStatus == 'library') $mysql->query("UPDATE characterLibrary SET viewed = viewed + 1 WHERE characterID = $characterID");
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
<? if (!$noChar) { ?>
		<div class="clearfix"><div id="sheetActions" class="wingDiv hbMargined floatRight">
			<div>
<?		if ($viewerStatus == 'edit') { ?>
				<a id="editCharacter" href="<?=SITEROOT?>/characters/<?=$system?>/<?=$characterID?>/edit" class="sprite pencil"></a>
<?		} else { ?>
				<a href="/" class="favoriteChar sprite tassel off" title="Favorite" alt="Favorite"></a>
<?		} ?>
			</div>
			<div class="wing ulWing"></div>
			<div class="wing urWing"></div>
		</div></div>
<? } ?>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/<?=$system?>.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

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
		
		<div class="clearfix">
			<div id="stats">
<?
	$statBonus = array();
	foreach ($stats as $short => $stat) {
		$bonus = showSign(floor(($charInfo[$short] - 10)/2));
?>
				<div class="tr dataTR">
					<label id="label_<?=$short?>" class="shortText leftLabel"><?=$stat?></label>
					<div class="stat"><?=$charInfo[$short]?></div>
					<span id="<?=$short?>Modifier"><?=$bonus?></span>
				</div>
<?
		$statBonus[$short] = $bonus;
	}
	
	$charInfo['size'] = showSign($charInfo['size']);
?>
			</div>
			
			<div id="savingThrows">
				<div class="tr labelTR">
					<div class="fillerBlock cell">&nbsp;</div>
					<label class="statCol shortNum lrBuffer">Total</label>
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
					<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_base'])?></div>
					<div class="shortNum lrBuffer statBonus_con"><?=$statBonus['con']?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_magic'])?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_race'])?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_misc'])?></div>
				</div>
				<div id="refRow" class="tr dataTR">
					<label class="leftLabel">Reflex</label>
					<div id="refTotal" class="shortNum lrBuffer"><?=$refBonus?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_base'])?></div>
					<div class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_magic'])?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_race'])?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_misc'])?></div>
				</div>
				<div id="willRow" class="tr dataTR">
					<label class="leftLabel">Will</label>
					<div id="willTotal" class="shortNum lrBuffer"><?=$willBonus?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['will_base'])?></div>
					<div class="shortNum lrBuffer statBonus_wis"><?=$statBonus['wis']?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['will_magic'])?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['will_race'])?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['will_misc'])?></div>
				</div>
			</div>
			
			<div id="hp" class="dataTR">
				<label class="leftLabel textLabel">Total HP</label>
				<div><?=$charInfo['hp']?></div>
				<label class="leftLabel textLabel">Damage Reduction</label>
				<div><?=$charInfo['dr']?></div>
			</div>
		</div>
		
		<div id="ac">
			<div class="tr labelTR">
				<label>Total AC</label>
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
			<div class="tr dataTR">
				<div class="cell"><?=$acTotal?></div>
				<div class="cell"> = 10 + </div>
				<div class="cell"><?=showSign($charInfo['ac_armor'])?></div>
				<div class="cell"><?=showSign($charInfo['ac_shield'])?></div>
				<div class="cell"><?=showSign($charInfo['ac_dex'])?></div>
				<div class="cell"><?=showSign($charInfo['ac_class'])?></div>
				<div class="cell"><?=$charInfo['size']?></div>
				<div class="cell"><?=showSign($charInfo['ac_natural'])?></div>
				<div class="cell"><?=showSign($charInfo['ac_deflection'])?></div>
				<div class="cell"><?=showSign($charInfo['ac_misc'])?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="combatBonuses">
				<div class="tr labelTR">
					<div class="fillerBlock cell shortText">&nbsp;</div>
					<label class="statCol shortNum lrBuffer">Total</label>
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
					<span id="initTotal" class="shortNum lrBuffer"><?=$initTotal?></span>
					<span class="lrBuffer">&nbsp;</span>
					<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
					<span class="lrBuffer">&nbsp;</span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['initiative_misc'])?></span>
				</div>
				<div id="melee" class="tr">
					<label class="leftLabel shortText">Melee</label>
					<span id="meleeTotal" class="shortNum lrBuffer"><?=$meleeTotal?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['bab'])?></span>
					<span class="shortNum lrBuffer statBonus_str"><?=$statBonus['str']?></span>
					<span class="shortNum lrBuffer sizeVal"><?=$charInfo['size']?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['melee_misc'])?></span>
				</div>
				<div id="ranged" class="tr">
					<label class="leftLabel shortText">Ranged</label>
					<span id="rangedTotal" class="shortNum lrBuffer"><?=$rangedTotal?></span>
					<span class="shortNum lrBuffer bab"><?=showSign($charInfo['bab'])?></span>
					<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
					<span class="shortNum lrBuffer sizeVal"><?=$charInfo['size']?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['ranged_misc'])?></span>
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
						<label class="statCol shortNum">Total</label>
						<label class="statCol shortNum">Base</label>
						<label class="statCol shortNum">Str</label>
						<label class="statCol shortNum">Size</label>
					</div>
					<div class="tr">
						<label class="leftLabel medNum">CMB</label>
						<div class="shortNum cell addStat_str subSize addBAB"><?=$cmb?></div>
						<div class="shortNum cell bab"><?=showSign($charInfo['bab'])?></div>
						<div class="shortNum cell statBonus_str"><?=$statBonus['str']?></div>
						<div class="shortNum cell nSizeVal"><?=showSign(0 - $charInfo['size'])?></div>
					</div>
				</div>
				
				<div id="cmd">
					<div class="tr labelTR">
						<div class="fillerBlock cell medNum">&nbsp;</div>
						<label class="statCol shortNum">Total</label>
						<label class="statCol shortNum">Base</label>
						<label class="statCol shortNum">Str</label>
						<label class="statCol shortNum">Dex</label>
						<label class="statCol shortNum">Size</label>
					</div>
					<div class="tr">
						<label class="leftLabel medNum">CMD</label>
						<div class="shortNum cell addStat_str addStat_dex subSize addBAB"><?=$cmd?></div>
						<div class="shortNum cell bab"><?=showSign($charInfo['bab'])?></div>
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
?>
					<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
						<span class="skill_total addStat_<?=$skillInfo['stat']?> shortNum lrBuffer"><?=showSign($statBonus[$skillInfo['stat']] + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skillInfo['stat'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skillInfo['ranks'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skillInfo['misc'])?></span>
					</div>
<?
	} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
				</div>
			</div>
			<div id="feats" class="floatRight">
				<h2 class="headerbar hbDark">Feats/Abilities</h2>
				<div class="hbdMargined">
<?
	$feats = $mysql->query('SELECT pathfinder_feats.featID, featsList.name, IF(LENGTH(pathfinder_feats.notes) = 0, 1, 0) hasNotes FROM pathfinder_feats INNER JOIN featsList USING (featID) WHERE pathfinder_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
?>
					<div id="feat_<?=$featInfo['featID']?>" class="feat tr clearfix">
						<span class="feat_name"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></span>
						<a href="<?=SITEROOT?>/characters/pathfinder/<?=$characterID?>/featNotes/<?=$feat['featID']?>" class="feat_notesLink">Notes</a>
					</div>
<?
	} } else echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
?>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="weapons" class="floatLeft">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div>
<?
	$weapons = $mysql->query('SELECT * FROM pathfinder_weapons WHERE characterID = '.$characterID);
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
	}
?>
				</div>
			</div>
			<div id="armor" class="floatRight">
				<h2 class="headerbar hbDark">Armor</h2>
				<div>
<?
	$armors = $mysql->query('SELECT * FROM pathfinder_armors WHERE characterID = '.$characterID);
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
	}
?>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="items">
				<h2 class="headerbar hbDark">Items</h2>
				<div class="hbdMargined"><?=$charInfo['items']?></div>
			</div>
			
			<div id="spells">
				<h2 class="headerbar hbDark">Spells</h2>
				<div class="hbdMargined"><?=$charInfo['spells']?></div>
			</div>
		</div>
		
		<div id="notes">
			<h2 class="headerbar hbDark">Notes</h2>
			<div class="hbdMargined"><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>