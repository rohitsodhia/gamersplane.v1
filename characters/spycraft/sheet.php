<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'spycraft');
	if ($charInfo) {
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/spycraft.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/spycraft/<?=$characterID?>/edit" class="button">Edit Character</a></div>
		<div class="tr labelTR tr-noPadding">
			<label id="label_name" class="medText">Name</label>
			<label id="label_codename" class="medText">Codename</label>
		</div>
		<div class="tr dataTR gapBelow">
			<div class="medText"><?=$charInfo['name']?></div>
			<div class="medText"><?=$charInfo['codename']?></div>
		</div>
		
		<div class="tr labelTR">
			<label id="label_classes" class="longText">Class(es)</label>
			<label id="label_alignment" class="shortText">Department</label>
		</div>
		<div class="tr dataTR gapBelow">
			<div class="longText"><?=$charInfo['class']?></div>
			<div class="shortText"><?=$charInfo['department']?></div>
		</div>
		
		<div class="clearfix">
			<div id="stats">
<?
	$statBonus = array();
	foreach (array('Strength', 'Dexterity', 'Constitution', 'Intelligence', 'Wisdom', 'Charisma') as $stat) {
		$short = strtolower(substr($stat, 0, 3));
		$bonus = showSign(floor(($charInfo[$short] - 10)/2));
?>
				<div class="tr">
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
					<label class="leftLabel">Vitality</label>
					<div><?=$charInfo['vitality']?></div>
				</div>
				<div class="tr">
					<label class="leftLabel">Wounds</label>
					<div><?=$charInfo['wounds']?></div>
				</div>
				<div class="tr">
					<label class="leftLabel">Base Speed</label>
					<div><?=$charInfo['speed']?></div>
				</div>
			</div>
			
			<div id="ac">
				<div class="tr labelTR">
					<label>Total AC</label>
					<div class="fillerBlock cell medNum">&nbsp;</div>
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
		</div>
		
		<div class="clearfix">
			<div class="col">
				<div id="combatBonuses" class="clearFix">
					<div class="tr labelTR">
						<div class="shortText">&nbsp;</div>
						<label class="shortNum lrBuffer">Total</label>
						<label class="shortNum lrBuffer">Base</label>
						<label class="shortNum lrBuffer">Ability</label>
						<label class="shortNum lrBuffer">Misc</label>
					</div>
<?
	$initTotal = showSign($statBonus['dex'] + $charInfo['initiative_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['ranged_misc']);
?>
					<div id="init" class="tr dataTR">
						<label class="leftLabel shortText">Initiative</label>
						<span id="initTotal" class="shortNum lrBuffer"><?=$initTotal?></span>
						<span class="lrBuffer">&nbsp;</span>
						<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
						<span class="shortNum lrBuffer"><?=showSign($charInfo['initiative_misc'])?></span>
					</div>
					<div id="melee" class="tr dataTR">
						<label class="leftLabel shortText">Melee</label>
						<span id="meleeTotal" class="shortNum lrBuffer"><?=$meleeTotal?></span>
						<span class="shortNum lrBuffer"><?=showSign($charInfo['bab'])?></span>
						<span class="shortNum lrBuffer statBonus_str"><?=$statBonus['str']?></span>
						<span class="shortNum lrBuffer"><?=showSign($charInfo['melee_misc'])?></span>
					</div>
					<div id="ranged" class="tr dataTR">
						<label class="leftLabel shortText">Ranged</label>
						<span id="rangedTotal" class="shortNum lrBuffer"><?=$rangedTotal?></span>
						<span class="shortNum lrBuffer"><?=showSign($charInfo['bab'])?></span>
						<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
						<span class="shortNum lrBuffer"><?=showSign($charInfo['ranged_misc'])?></span>
					</div>
				</div>
				
				<div id="actionDie">
					<div class="tr labelTR">
						<div class="shortText">&nbsp;</div>
						<label class="statCol lrBuffer shortNum">Total</label>
						<label class="statCol lrBuffer medNum">Dice Type</label>
					</div>
					<div class="tr">
						<label class="leftLabel shortText alignRight">Action Die</label>
						<span class="shortNum lrBuffer"><?=$charInfo['actionDie_total']?></span>
						<span class="medNum lrBuffer"><?=$charInfo['actionDie_dieType']?></span>
					</div>
				</div>
				
				<div id="extraStats">
					<div class="tr labelTR">
						<div class="shortText">&nbsp;</div>
						<label class="statCol shortNum lrBuffer">Total</label>
						<label class="statCol shortNum lrBuffer">Stat</label>
						<label class="statCol shortNum lrBuffer">Misc</label>
					</div>
					<div class="tr">
						<label class="leftLabel shortText alignRight">Inspiration</label>
						<span id="inspiration_total" class="shortNum lrBuffer"><?=showSign($charInfo['inspiration_misc'] + $statBonus['wis'])?></span>
						<span class="shortNum lrBuffer"><?=$statBonus['wis']?></span>
						<span class="shortNum lrBuffer"><?=$charInfo['inspiration_misc']?></span>
					</div>
					<div class="tr">
						<label class="leftLabel shortText alignRight">Education</label>
						<span id="education_total" class="shortNum lrBuffer"><?=showSign($charInfo['education_misc'] + $statBonus['int'])?></span>
						<span class="shortNum lrBuffer"><?=$statBonus['int']?></span>
						<span class="shortNum lrBuffer"><?=$charInfo['education_misc']?></span>
					</div>
				</div>
			</div>

			<div id="feats" class="floatRight">
				<h2 class="headerbar hbDark">Feats/Abilities</h2>
				<div class="hbdMargined">
<?
	$feats = $mysql->query('SELECT spycraft_feats.featID, featsList.name, IF(LENGTH(spycraft_feats.notes) = 0, 1, 0) hasNotes FROM spycraft_feats INNER JOIN featsList USING (featID) WHERE spycraft_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
?>
					<div id="feat_<?=$featInfo['featID']?>" class="feat tr clearfix">
						<span class="feat_name"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></span>
<?		if ($featInfo['hasNotes']) { ?>
						<a href="<?=SITEROOT?>/characters/spycraft/<?=$characterID?>/featNotes/<?=$featInfo['featID']?>" id="featNotesLink_<?=$featInfo['featID']?>" class="feat_notesLink">Notes</a>
<?		} ?>
					</div>
<?
	} } else echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
?>
				</div>
			</div>
		</div>
		
		<div id="skills" class="floatLeft">
			<h2 class="headerbar hbDark">Skills</h2>
			<div class="hbdMargined">
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
	$skills = $mysql->query('SELECT spycraft_skills.skillID, skillsList.name, spycraft_skills.stat, spycraft_skills.ranks, spycraft_skills.misc, spycraft_skills.error, spycraft_skills.threat FROM spycraft_skills INNER JOIN skillsList USING (skillID) WHERE spycraft_skills.characterID = '.$characterID.' ORDER BY skillsList.name');
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
?>
				<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
					<span class="skill_name medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
					<span class="skill_total addStat_<?=$skillInfo['stat']?> shortNum lrBuffer"><?=showSign($statBonus[$skillInfo['stat']] + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
					<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skillInfo['stat'])?></span>
					<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skillInfo['ranks'])?></span>
					<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skillInfo['misc'])?></span>
					<span class="skill_ranks alignCenter medNum lrBuffer"><?=$skillInfo['error']?></span>
					<span class="skill_ranks alignCenter medNum lrBuffer"><?=$skillInfo['threat']?></span>
				</div>
<?
	} } else echo "\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="weapons" class="floatLeft">
				<h2 class="headerbar hbDark">Weapons</h2>
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
				<h2 class="headerbar hbDark">Armor</h2>
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
		
		<div id="items">
			<h2 class="headerbar hbDark">Items</h2>
			<div><?=$charInfo['items']?></div>
		</div>
		
		<div id="notes">
			<h2 class="headerbar hbDark">Notes</h2>
			<div><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>