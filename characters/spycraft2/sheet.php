<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'spycraft2');
	if ($charInfo) {
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$noChar = FALSE;
			$charInfo['level'] = 0;
			preg_match_all('/\d+/', $charInfo['class'], $matches);
			foreach ($matches[0] as $level) $charInfo['level'] += $level;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/spycraft2.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/spycraft2/<?=$characterID?>/edit" class="button">Edit Character</a></div>
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
			<label id="label_talent" class="medText">Talent</label>
			<label id="label_specialty" class="medText">Specialty</label>
		</div>
		<div class="tr dataTR gapBelow">
			<div class="longText"><?=$charInfo['class']?></div>
			<div class="medText"><?=$charInfo['talent']?></div>
			<div class="medText"><?=$charInfo['specialty']?></div>
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
						<label id="label_<?=$short?>" class="shortText leftLabel lrBuffer"><?=$stat?></label>
						<div class="stat lrBuffer"><?=$charInfo[$short]?></div>
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
					<div class="">&nbsp;</div>
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
				<div id="fortRow" class="tr dataTR">
					<label class="leftLabel">Fortitude</label>
					<div id="fortTotal" class="shortNum lrBuffer"><?=$fortBonus?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_base'])?></div>
					<div class="shortNum statBonus_con lrBuffer"><?=$statBonus['con']?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['fort_misc'])?></div>
				</div>
				<div id="refRow" class="tr dataTR">
					<label class="leftLabel">Reflex</label>
					<div id="refTotal" class="shortNum lrBuffer"><?=$refBonus?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_base'])?></div>
					<div class="shortNum statBonus_dex lrBuffer"><?=$statBonus['dex']?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['ref_misc'])?></div>
				</div>
				<div id="willRow" class="tr dataTR">
					<label class="leftLabel">Will</label>
					<div id="willTotal" class="shortNum lrBuffer"><?=$willBonus?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['will_base'])?></div>
					<div class="shortNum statBonus_wis lrBuffer"><?=$statBonus['wis']?></div>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['will_misc'])?></div>
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
					<label class="leftLabel">Subdual</label>
					<div><?=$charInfo['subdual']?></div>
				</div>
				<div class="tr">
					<label class="leftLabel">Stress</label>
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
		</div>
		
		<div class="clearfix">
			<div id="combatBonuses">
				<div class="tr labelTR">
					<div class="shortText">&nbsp;</div>
					<label class="statCol shortNum lrBuffer">Total</label>
					<label class="statCol shortNum lrBuffer">Base</label>
					<label class="statCol shortNum lrBuffer">Ability</label>
					<label class="statCol shortNum lrBuffer">Misc</label>
				</div>
<?
	$initTotal = showSign($statBonus['dex'] + $charInfo['initiative_misc']);
	$unarmedTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['unarmed_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['ranged_misc']);
?>
				<div id="init" class="tr dataTR">
					<label class="leftLabel shortText">Initiative</label>
					<span id="initTotal" class="shortNum lrBuffer"><?=$initTotal?></span>
					<span class="lrBuffer">&nbsp;</span>
					<span class="shortNum statBonus_dex lrBuffer"><?=$statBonus['dex']?></span>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['initiative_misc'])?></div>
				</div>
				<div id="unarmed" class="tr dataTR">
					<label class="leftLabel shortText">Melee</label>
					<span id="unarmedTotal" class="shortNum lrBuffer"><?=$unarmedTotal?></span>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['bab'])?></div>
					<span class="shortNum statBonus_str lrBuffer"><?=$statBonus['str']?></span>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['unarmed_misc'])?></div>
				</div>
				<div id="melee" class="tr dataTR">
					<label class="leftLabel shortText">Melee</label>
					<span id="meleeTotal" class="shortNum lrBuffer"><?=$meleeTotal?></span>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['bab'])?></div>
					<span class="shortNum statBonus_str lrBuffer"><?=$statBonus['str']?></span>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['melee_misc'])?></div>
				</div>
				<div id="ranged" class="tr dataTR">
					<label class="leftLabel shortText">Ranged</label>
					<span id="rangedTotal" class="shortNum lrBuffer"><?=$rangedTotal?></span>
					<span class="shortNum bab lrBuffer"><?=showSign($charInfo['bab'])?></span>
					<span class="shortNum statBonus_dex lrBuffer"><?=$statBonus['dex']?></span>
					<div class="shortNum lrBuffer"><?=showSign($charInfo['ranged_misc'])?></div>
				</div>
			</div>
			
			<div id="actionDie">
				<div class="tr labelTR">
					<div class="shortText">&nbsp;</div>
					<label class="statCol shortNum lrBuffer">Total</label>
					<label class="statCol medNum lrBuffer">Dice Type</label>
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Action Die</label>
					<span class="shortNum lrBuffer"><?=$charInfo['actionDie_total']?></span>
					<span class="medNum lrBuffer"><?=$charInfo['actionDie_dieType']?></span>
				</div>
			</div>
			
			<div id="extraStats">
				<div class="tr labelTR">
					<label class="shortText">Check Bonus</label>
					<label class="shortNum lrBuffer">Total</label>
					<label class="shortNum lrBuffer">Level</label>
					<label class="shortNum lrBuffer">Stat</label>
					<label class="shortNum lrBuffer">Misc</label>
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Knowledge</label>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['knowledge_misc'] + $charInfo['level'] + $statBonus['int'])?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['level'])?></span>
					<span class="shortNum lrBuffer"><?=$statBonus['int']?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['knowledge_misc'])?></span>
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Request</label>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['request_misc'] + $charInfo['level'] + $statBonus['cha'])?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['level'])?></span>
					<span class="shortNum lrBuffer"><?=$statBonus['cha']?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['request_misc'])?></span>
				</div>
				<div class="tr">
					<label class="leftLabel shortText">Gear</label>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['gear_misc'] + $charInfo['level'] + $statBonus['wis'])?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['level'])?></span>
					<span class="shortNum lrBuffer"><?=$statBonus['wis']?></span>
					<span class="shortNum lrBuffer"><?=showSign($charInfo['gear_misc'])?></span>
				</div>
			</div>
		</div>
		
		<div id="skills">
			<h2 class="headerbar hbDark">Skills</h2>
			<div class="hbMargined">
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
		$total_1 = $statBonus[$skillInfo['stat_1']] + $skillInfo['ranks'] + $skillInfo['misc'];
		$total_2 = $statBonus[$skillInfo['stat_2']] + $skillInfo['ranks'] + $skillInfo['misc'];
?>
				<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
					<span class="skill_name medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
					<span class="skill_total lrBuffer shortNum"><span class="skill_total_1 addStat_<?=$skillInfo['stat_1']?>"><?=showSign($total_1).'</span>'.($skillInfo['stat_2'] != '' ? "/<span class=\"skill_total_2 addStat_".$skillInfo['stat_2']."\">".showSign($total_2).'</span>' : '')?></span>
					<span class="skill_stat lrBuffer alignCenter shortText"><?=ucwords($skillInfo['stat_1']).($skillInfo['stat_2'] != '' ? '/'.ucwords($skillInfo['stat_2']) : '')?></span>
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
			<div id="focuses">
				<h2 class="headerbar hbDark">Focuses/Fortes</h2>
				<div class="hbMargined">
<?
	$focuses = $mysql->query('SELECT cf.focusID, fl.name, cf.forte FROM spycraft2_focuses cf INNER JOIN spycraft2_focusesList fl USING (focusID) WHERE cf.characterID = '.$characterID.' ORDER BY fl.name');
	if ($focuses->rowCount()) {
?>
					<div class="labelTR"><label class="shortNum alignCenter">Forte</label></div>
<?	} else { ?>
					<p id="noFocuses">This character currently has no focuses.</p>
<?
	}
	if ($focuses->rowCount()) { foreach ($focuses as $focusInfo) {
?>
					<div id="focus_<?=$focusInfo['focusID']?>" class="focus tr clearfix">
						<span class="shortNum alignCenter"><?=$focusInfo['forte']?'<img src="'.SITEROOT.'/images/check.png">':''?></span>
						<span class="focus_name"><?=mb_convert_case($focusInfo['name'], MB_CASE_TITLE)?></span>
					</div>
<?
	} }
?>
				</div>
			</div>
			<div id="feats">
				<h2 class="headerbar hbDark">Feats/Abilities</h2>
				<div class="hbMargined">
<?
	$feats = $mysql->query('SELECT spycraft2_feats.featID, featsList.name, IF(LENGTH(spycraft2_feats.notes) = 0, 1, 0) hasNotes FROM spycraft2_feats INNER JOIN featsList USING (featID) WHERE spycraft2_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
?>
					<div id="feat_<?=$featInfo['featID']?>" class="feat tr clearfix">
						<span class="feat_name"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></span>
<?		if ($featInfo['hasNotes']) { ?>
						<a href="<?=SITEROOT?>/characters/spycraft2/<?=$characterID?>/featNotes/<?=$featInfo['featID']?>" id="featNotesLink_<?=$featInfo['featID']?>" class="feat_notesLink">Notes</a>
<?		} ?>
					</div>
<?
	} } else echo "\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
?>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="weapons" class="floatLeft">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div>
<?
	$weapons = $mysql->query('SELECT * FROM spycraft2_weapons WHERE characterID = '.$characterID);

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
	}
?>
				</div>
			</div>
			<div id="armor" class="floatRight">
				<h2 class="headerbar hbDark">Armor</h2>
				<div>
<?
	$armors = $mysql->query('SELECT * FROM spycraft2_armors WHERE characterID = '.$characterID);
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
	}
?>
				</div>
			</div>
		</div>
		
		<br class="clear">
		<div id="items">
			<h2 class="headerbar hbDark">Items</h2>
			<div class="hbMargined"><?=$charInfo['items']?></div>
		</div>
		
		<div id="notes">
			<h2 class="headerbar hbDark">Notes</h2>
			<div class="hbMargined"><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>