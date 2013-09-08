<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM dnd3_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$noChar = FALSE;
		}
	}
	
	$alignments = array('lg' => 'Lawful Good', 'ng' => 'Neutral Good', 'cg' => 'Chaotic Good', 'ln' => 'Lawful Neutral', 'tn' => 'True Neutral', 'cn' => 'Chaotic Neutral', 'le' => 'Lawful Evil', 'ne' => 'Neutral Evil', 'ce' => 'Chaotic Evil'); 
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/dnd3.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/dnd3/<?=$characterID?>/edit" class="button">Edit Character</a></div>
		<div class="tr labelTR tr-noPadding">
			<label id="label_name" class="medText">Name</label>
			<label id="label_race" class="medText">Race</label>
		</div>
		<div class="tr dataTR">
			<div class="medText"><?=$charInfo['name']?></div>
			<div class="medText"><?=$charInfo['race']?></div>
		</div>
		
		<div class="tr labelTR">
			<label id="label_classes" class="longText">Class(es)/Level(s)</label>
			<label id="label_alignment" class="medText">Alignment</label>
		</div>
		<div class="tr dataTR">
			<div class="longText"><?=$charInfo['class']?></div>
			<div class="longText"><?=$alignments[$charInfo['alignment']]?></div>
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
					<label id="label_<?=$short?>" class="textLabel shortText leftLabel"><?=$stat?></label>
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
				<label class="first">Total AC</label>
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
				<div class="first"><?=$acTotal?></div>
				<div> = 10 + </div>
				<div><?=showSign($charInfo['ac_armor'])?></div>
				<div><?=showSign($charInfo['ac_shield'])?></div>
				<div><?=$charInfo['ac_dex']?></div>
				<div><?=showSign($charInfo['ac_class'])?></div>
				<div><?=$charInfo['size']?></div>
				<div><?=showSign($charInfo['ac_natural'])?></div>
				<div><?=showSign($charInfo['ac_deflection'])?></div>
				<div><?=showSign($charInfo['ac_misc'])?></div>
			</div>
		</div>
		
		<div id="combatBonuses" class="clearFix">
			<div class="tr labelTR">
				<label class="statCol shortNum first">Total</label>
				<label class="statCol shortNum">Base</label>
				<label class="statCol shortNum">Ability</label>
				<label class="statCol shortNum">Size</label>
				<label class="statCol shortNum">Misc</label>
			</div>
<?
	$initTotal = showSign($statBonus['dex'] + $charInfo['initiative_misc']);
	$meleeTotal = showSign($charInfo['bab'] + $statBonus['str'] + $charInfo['size'] + $charInfo['melee_misc']);
	$rangedTotal = showSign($charInfo['bab'] + $statBonus['dex'] + $charInfo['size'] + $charInfo['ranged_misc']);
?>
			<div id="init" class="tr dataTR">
				<label class="leftLabel shortText">Initiative</label>
				<span id="initTotal" class="shortNum"><?=$initTotal?></span>
				<span>&nbsp;</span>
				<span class="shortNum statBonus_dex"><?=$statBonus['dex']?></span>
				<span>&nbsp;</span>
				<div class="shortNum"><?=showSign($charInfo['initiative_misc'])?></div>
			</div>
			<div id="melee" class="tr dataTR">
				<label class="leftLabel shortText">Melee</label>
				<span id="meleeTotal" class="shortNum"><?=$meleeTotal?></span>
				<div class="shortNum"><?=showSign($charInfo['bab'])?></div>
				<span class="shortNum statBonus_str"><?=$statBonus['str']?></span>
				<span class="shortNum sizeVal"><?=$charInfo['size']?></span>
				<div class="shortNum"><?=showSign($charInfo['melee_misc'])?></div>
			</div>
			<div id="ranged" class="tr dataTR">
				<label class="leftLabel shortText">Ranged</label>
				<span id="rangedTotal" class="shortNum"><?=$rangedTotal?></span>
				<span class="shortNum bab"><?=showSign($charInfo['bab'])?></span>
				<span class="shortNum statBonus_dex"><?=$statBonus['dex']?></span>
				<span class="shortNum sizeVal"><?=$charInfo['size']?></span>
				<div class="shortNum"><?=showSign($charInfo['ranged_misc'])?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="skills" class="floatLeft">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbMargined">
					<div class="tr labelTR">
						<label class="medText">Skill</label>
						<label class="shortNum alignCenter lrBuffer">Total</label>
						<label class="shortNum alignCenter lrBuffer">Stat</label>
						<label class="shortNum alignCenter lrBuffer">Ranks</label>
						<label class="shortNum alignCenter lrBuffer">Misc</label>
					</div>
<?
	$skills = $mysql->query('SELECT dnd3_skills.skillID, skillsList.name, dnd3_skills.stat, dnd3_skills.ranks, dnd3_skills.misc FROM dnd3_skills INNER JOIN skillsList USING (skillID) WHERE dnd3_skills.characterID = '.$characterID.' ORDER BY skillsList.name');
	if ($skills->rowCount()) { foreach ($skills as $skill) { ?>
					<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
						<span class="skill_total addStat_<?=$skill['stat']?> shortNum lrBuffer"><?=showSign($statBonus[$skill['stat']] + $skill['ranks'] + $skill['misc'])?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skill['stat'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['ranks'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['misc'])?></span>
					</div>
<?	} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n"; ?>
				</div>
			</div>
			<div id="feats" class="floatRight">
				<h2 class="headerbar hbDark">Feats/Abilities</h2>
				<div class="hbMargined">
<?
	$feats = $mysql->query('SELECT dnd3_feats.featID, featsList.name, dnd3_feats.notes FROM dnd3_feats INNER JOIN featsList USING (featID) WHERE dnd3_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	if ($feats->rowCount()) { foreach ($feats as $feat) { ?>
					<div id="feat_<?=$feat['featID']?>" class="feat tr clearfix">
						<span class="feat_name"><?=mb_convert_case($feat['name'], MB_CASE_TITLE)?></span>
						<a href="<?=SITEROOT?>/characters/dnd3/<?=$characterID?>/featNotes/<?=$feat['featID']?>" class="feat_notesLink">Notes</a>
					</div>
<?	} } else echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n"; ?>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div id="weapons" class="floatLeft">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div class="hbMargined">
<?
	$weapons = $mysql->query('SELECT * FROM dnd3_weapons WHERE characterID = '.$characterID);
	foreach ($weapons as $weapon) {
	?>
					<div class="weapon">
						<div class="tr labelTR">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
							<label class="shortText alignCenter lrBuffer">Damage</label>
						</div>
						<div class="tr">
							<span class="weapon_name medText lrBuffer"><?=$weapon['name']?></span>
							<span class="weapons_ab shortText lrBuffer alignCenter"><?=$weapon['ab']?></span>
							<span class="weapon_damage shortText lrBuffer alignCenter"><?=$weapon['damage']?></span>
						</div>
						<div class="tr labelTR weapon_secondRow">
							<label class="shortText alignCenter lrBuffer">Critical</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortNum alignCenter lrBuffer">Size</label>
						</div>
						<div class="tr weapon_secondRow">
							<span class="weapon_crit shortText lrBuffer alignCenter"><?=$weapon['critical']?></span>
							<span class="weapon_range shortText lrBuffer alignCenter"><?=$weapon['range']?></span>
							<span class="weapon_type shortText lrBuffer alignCenter"><?=$weapon['type']?></span>
							<span class="weapon_size shortText lrBuffer alignCenter"><?=$weapon['size']?></span>
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer">Notes</label>
						</div>
						<div class="tr">
							<span class="weapon_notes lrBuffer"><?=$weapon['notes']?></span>
						</div>
					</div>
<?
	}
?>
				</div>
			</div>
			<div id="armor" class="floatRight">
				<h2 class="headerbar hbDark">Armor</h2>
				<div class="hbMargined">
<?
	$armors = $mysql->query('SELECT * FROM dnd3_armors WHERE characterID = '.$characterID);
	foreach ($armors as $armor) {
?>
					<div class="armor">
						<div class="tr labelTR armor_firstRow">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">AC Bonus</label>
							<label class="shortText alignCenter lrBuffer">Max Dex</label>
						</div>
						<div class="tr armor_firstRow">
							<span class="armor_name medText lrBuffer"><?=$armor['name']?></span>
							<span class="armors_ac shortText lrBuffer alignCenter"><?=$armor['ac']?></span>
							<span class="armor_maxDex shortText lrBuffer alignCenter"><?=$armor['maxDex']?></span>
						</div>
						<div class="tr labelTR armor_secondRow">
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortText alignCenter lrBuffer">Check Penalty</label>
							<label class="shortText alignCenter lrBuffer">Spell Failure</label>
							<label class="shortNum alignCenter lrBuffer">Speed</label>
						</div>
						<div class="tr armor_secondRow">
							<span class="armor_type shortText lrBuffer alignCenter"><?=$armor['type']?></span>
							<span class="armor_check shortText lrBuffer alignCenter"><?=$armor['check']?></span>
							<span class="armor_spellFailure shortText lrBuffer alignCenter"><?=$armor['spellFailure']?></span>
							<span class="armor_speed shortText lrBuffer alignCenter"><?=$armor['speed']?></span>
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer">Notes</label>
						</div>
						<div class="tr">
							<span class="armor_notes lrBuffer"><?=$armor['notes']?></span>
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
				<div class="hbMargined"><?=$charInfo['items']?></div>
			</div>
			
			<div id="spells">
				<h2 class="headerbar hbDark">Spells</h2>
				<div class="hbMargined"><?=$charInfo['spells']?></div>
			</div>
		</div>

		<div id="notes">
			<h2 class="headerbar hbDark">Notes</h2>
			<div class="hbMargined"><?=$charInfo['notes']?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>