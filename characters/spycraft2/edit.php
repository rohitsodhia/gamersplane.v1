<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'spycraft2');
	if ($charInfo) {
		if (allowCharView($characterID, $userID)) {
			$charInfo['level'] = 0;
			preg_match_all('/\d+/', $charInfo['class'], $matches);
			foreach ($matches[0] as $level) $charInfo['level'] += $level;
			$noChar = FALSE;
			includeSystemInfo('spycraft2');
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="/images/logos/spycraft2.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="/characters/process/spycraft2/<?=$pathOptions[1]?>">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
				<label id="label_codename" class="medText lrBuffer borderBox shiftRight">Codename</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="medText lrBuffer">
				<input type="text" name="codename" value="<?=$charInfo['codename']?>" class="medText lrBuffer">
			</div>
			
			<div class="tr labelTR">
				<label id="label_classes" class="longText lrBuffer borderBox shiftRight">Class(es)/Level(s)</label>
				<label id="label_talent" class="medText lrBuffer borderBox shiftRight">Talent</label>
				<label id="label_specialty" class="medText lrBuffer borderBox shiftRight">Specialty</label>
			</div>
			<div class="tr">
				<input id="classes" type="text" name="class" value="<?=$charInfo['class']?>" class="lrBuffer">
				<input id="talent" type="text" name="talent" value="<?=$charInfo['talent']?>" class="medText lrBuffer alignLeft">
				<input id="specialty" type="text" name="specialty" value="<?=$charInfo['specialty']?>" class="medText lrBuffer alignLeft">
			</div>
			
			<div class="clearfix">
				<div id="stats">
<?
	$statBonus = array();
	foreach ($stats as $short => $stat) {
		$bonus = floor(($charInfo[$short] - 10)/2);
		if ($bonus >= 0) $bonus = '+'.$bonus;
?>
					<div class=tr>
						<label id="label_<?=$short?>" class="shortText lrBuffer leftLabel"><?=$stat?></label>
						<input type="text" id="<?=$short?>" name="<?=$short?>" value="<?=$charInfo[$short]?>" maxlength="2" class="stat lrBuffer">
						<span id="<?=$short?>Modifier"><?=$bonus?></span>
					</div>
<?
		$statBonus[$short] = $bonus;
	}
	
	if ($charInfo['size'] > 0) $charInfo['size'] = '+'.$charInfo['size'];
?>
				</div>
				
				<div id="savingThrows">
					<div class="tr labelTR">
						<div class="">&nbsp;</div>
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
						<label class="leftLabel">Vitality</label>
						<input type="text" name="vitality" value="<?=$charInfo['vitality']?>" class="medNum">
					</div>
					<div class="tr">
						<label class="leftLabel">Wounds</label>
						<input type="text" name="wounds" value="<?=$charInfo['wounds']?>" class="medNum">
					</div>
					<div class="tr">
						<label class="leftLabel">Subdual</label>
						<input type="text" name="subdual" value="<?=$charInfo['subdual']?>" class="medNum">
					</div>
					<div class="tr">
						<label class="leftLabel">Stress</label>
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
			</div>
			
			<div class="clearfix">
				<div id="combatBonuses">
					<div class="tr labelTR">
						<div class="shortText">&nbsp;</div>
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
						<div class="shortText"></div>
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
			</div>
			
			<div id="skills">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined">
					<div id="addSkillWrapper">
						<input id="skillName" type="text" name="newSkill[name]" class="medText placeholder" autocomplete="off" data-placeholder="Skill Name">
						<select id="skillStat_1" name="newSkill[stat_1]">
<?
	foreach ($stats as $short => $stat) echo "								<option value=\"$short\">".ucfirst($short)."</option>\n";
?>
						</select>
						<select id="skillStat_2" name="newSkill[stat_2]">
							<option value=""></option>
<?
	foreach ($stats as $short => $stat) echo "								<option value=\"$short\">".ucfirst($short)."</option>\n";
?>
						</select>
						<button id="addSkill" type="submit" name="newSkill_add" class="fancyButton">Add</button>
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
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) skillFormFormat($skillInfo, $statBonus[$skillInfo['stat_1']], $statBonus[$skillInfo['stat_2']]);
	} else echo "\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
?>
				</div>
			</div>

			<div class="clearfix">
				<div id="focuses">
					<h2 class="headerbar hbDark">Focuses/Fortes</h2>
					<div class="hbdMargined">
						<div id="addFocusWrapper">
							<input id="focusName" type="text" name="newFocus_name" class="medText placeholder" autocomplete="off" data-placeholder="Focus Name">
							<button id="addFocus" type="submit" name="newFocus_add" class="fancyButton">Add</button>
						</div>
<?
	$focuses = $mysql->query('SELECT cf.focusID, fl.name, cf.forte FROM spycraft2_focuses cf INNER JOIN spycraft2_focusesList fl USING (focusID) WHERE cf.characterID = '.$characterID.' ORDER BY fl.name');
	echo "\t\t\t\t\t\t<p id=\"noFocuses\"".($focuses->rowCount()?' style="display: none;"':'').">This character currently has no focuses.</p>\n";
	echo "\t\t\t\t\t\t<div class=\"labelTR\"".($focuses->rowCount() == 0?' style="display: none;"':'')."><label class=\"shortNum alignCenter\">Forte</label></div>\n";
	if ($focuses->rowCount()) { foreach ($focuses as $focusInfo) focusFormFormat($characterID, $focusInfo);
	}
?>
					</div>
				</div>
				<div id="feats">
					<h2 class="headerbar hbDark">Feats/Abilities</h2>
					<div class="hbdMargined">
						<div id="addFeatWrapper">
							<input id="featName" type="text" name="newFeat_name" class="medText placeholder" autocomplete="off" data-placeholder="Feat Name">
							<button id="addFeat" type="submit" name="newFeat_add" class="fancyButton">Add</button>
						</div>
<?
	$feats = $mysql->query('SELECT spycraft2_feats.featID, featsList.name FROM spycraft2_feats INNER JOIN featsList USING (featID) WHERE spycraft2_feats.characterID = '.$characterID.' ORDER BY featsList.name');
	echo "\t\t\t\t\t\t<p id=\"noFeats\"".($feats->rowCount()?' style="display: none;"':'').">This character currently has no feats/abilities.</p>\n";
	if ($feats->rowCount()) { foreach ($feats as $featInfo) featFormFormat($characterID, $featInfo); }
?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark">Weapons <a id="addWeapon" href="">[ Add Weapon ]</a></h2>
					<div>
<?
	$weapons = $mysql->query('SELECT * FROM spycraft2_weapons WHERE characterID = '.$characterID);
	$weaponNum = 1;
	while (($weaponInfo = $weapons->fetch()) || $weaponNum <= 2) weaponFormFormat($weaponNum++, $weaponInfo);
?>
					</div>
				</div>
				<div id="armor" class="floatRight">
					<h2 class="headerbar hbDark">Armor <a id="addArmor" href="">[ Add Armor ]</a></h2>
					<div>
<?
	$armors = $mysql->query('SELECT * FROM spycraft2_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	while (($armorInfo = $armors->fetch()) || $armorNum <= 1) armorFormFormat($armorNum++, $armorInfo);
?>
					</div>
				</div>
			</div>
			
			<div id="items">
				<h2 class="headerbar hbDark">Items</h2>
				<textarea name="items" class="hbdMargined"><?=$charInfo['items']?></textarea>
			</div>
			
			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<textarea name="notes" class="hbdMargined"><?=$charInfo['notes']?></textarea>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>