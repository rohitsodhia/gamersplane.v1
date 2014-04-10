<?	global $character; ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/dnd3/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div class="tr labelTR">
				<label id="label_name" class="medText lrBuffer borderBox shiftRight">Name</label>
				<label id="label_classes" class="medText lrBuffer borderBox shiftRight">Class(es)</label>
				<label id="label_levels" class="shortNum lrBuffer borderBox">Level(s)</label>
			</div>
			<div class="tr">
				<input type="text" name="name" value="<?=$charInfo['name']?>" class="medText alignTop lrBuffer">
				<div id="classWrapper">
					<a href="">[ Add Class ]</a>
					<div class="classSet">
						<input type="text" name="class[]" value="<?=$charInfo['class']?>" class="medText lrBuffer">
						<input type="text" name="level[]" value="<?=$charInfo['class']?>" class="shortNum lrBuffer">
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="stats">
<?
	$stats = d20Character_consts::getStatNames();
	foreach ($stats as $short => $stat) {
		$bonus = floor(($character->getStat($short) - 10)/2);
?>
					<div class="tr">
						<label id="label_<?=$short?>" class="textLabel shortText lrBuffer leftLabel"><?=$stat?></label>
						<input type="text" id="<?=$short?>" name="<?=$short?>" value="<?=$character->getStat($short)?>" maxlength="2" class="stat lrBuffer">
						<span id="<?=$short?>Modifier"><?=showSign($bonus)?></span>
					</div>
<?
		$statBonus[$short] = $bonus;
	}
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
					<div id="fortRow" class="tr">
						<label class="leftLabel">Fortitude</label>
						<span id="fortTotal" class="shortNum lrBuffer addStat_con"><?=$fortBonus?></span>
						<input type="text" name="fort_base"  value="<?=$charInfo['fort_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_con"><?=$statBonus['con']?></span>
						<input type="text" name="fort_magic"  value="<?=$charInfo['fort_magic']?>" class="lrBuffer">
						<input type="text" name="fort_race"  value="<?=$charInfo['fort_race']?>" class="lrBuffer">
						<input type="text" name="fort_misc"  value="<?=$charInfo['fort_misc']?>" class="lrBuffer">
					</div>
					<div id="refRow" class="tr">
						<label class="leftLabel">Reflex</label>
						<span id="refTotal" class="shortNum lrBuffer addStat_dex"><?=$refBonus?></span>
						<input type="text" name="ref_base"  value="<?=$charInfo['ref_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
						<input type="text" name="ref_magic"  value="<?=$charInfo['ref_magic']?>" class="lrBuffer">
						<input type="text" name="ref_race"  value="<?=$charInfo['ref_race']?>" class="lrBuffer">
						<input type="text" name="ref_misc"  value="<?=$charInfo['ref_misc']?>" class="lrBuffer">
					</div>
					<div id="willRow" class="tr">
						<label class="leftLabel">Will</label>
						<span id="willTotal" class="shortNum lrBuffer addStat_wis"><?=$willBonus?></span>
						<input type="text" name="will_base"  value="<?=$charInfo['will_base']?>" class="lrBuffer">
						<span class="shortNum lrBuffer statBonus_wis"><?=$statBonus['wis']?></span>
						<input type="text" name="will_magic"  value="<?=$charInfo['will_magic']?>" class="lrBuffer">
						<input type="text" name="will_race"  value="<?=$charInfo['will_race']?>" class="lrBuffer">
						<input type="text" name="will_misc"  value="<?=$charInfo['will_misc']?>" class="lrBuffer">
					</div>
				</div>
				
				<div id="hp">
					<label class="leftLabel textLabel">Total HP</label>
					<input type="text" name="hp" value="<?=$charInfo['hp']?>" class="medNum">
					<label class="leftLabel textLabel">Damage Reduction</label>
					<input id="damageReduction" type="text" name="dr" value="<?=$charInfo['dr']?>" class="medText">
				</div>
			</div>
			
			<div id="ac">
				<div class="tr labelTR">
					<label class="lrBuffer">Total AC</label>
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
				<div class="tr">
					<div id="ac_total" class="lrBuffer addSize"><?=$acTotal?></div>
					<div> = 10 + </div>
					<input type="text" name="ac_armor" value="<?=$charInfo['ac_armor']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_shield" value="<?=$charInfo['ac_shield']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_dex" value="<?=$charInfo['ac_dex']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_class" value="<?=$charInfo['ac_class']?>" class="acComponents lrBuffer">
					<div class="sizeVal lrBuffer"><?=showSign($charInfo['size'])?></div>
					<input type="text" name="ac_natural" value="<?=$charInfo['ac_natural']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_deflection" value="<?=$charInfo['ac_deflection']?>" class="acComponents lrBuffer">
					<input type="text" name="ac_misc" value="<?=$charInfo['ac_misc']?>" class="acComponents lrBuffer">
				</div>
			</div>
			
			<div id="combatBonuses" class="clearFix">
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
					<span id="initTotal" class="shortNum lrBuffer addStat_dex"><?=$initTotal?></span>
					<span class="lrBuffer">&nbsp;</span>
					<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
					<span class="lrBuffer">&nbsp;</span>
					<input type="text" name="initiative_misc" value="<?=$charInfo['initiative_misc']?>" class="lrBuffer">
				</div>
				<div id="melee" class="tr">
					<label class="leftLabel shortText">Melee</label>
					<span id="meleeTotal" class="shortNum lrBuffer addStat_str addSize"><?=$meleeTotal?></span>
					<input id="bab" type="text" name="bab" value="<?=$charInfo['bab']?>" class="lrBuffer">
					<span class="shortNum lrBuffer statBonus_str"><?=$statBonus['str']?></span>
					<span class="shortNum lrBuffer sizeVal"><?=showSign($charInfo['size'])?></span>
					<input id="melee_misc" type="text" name="melee_misc" value="<?=$charInfo['melee_misc']?>" class="lrBuffer">
				</div>
<? $charInfo['bab'] = showSign($charInfo['bab']); ?>
				<div id="ranged" class="tr">
					<label class="leftLabel shortText">Ranged</label>
					<span id="rangedTotal" class="shortNum lrBuffer addStat_dex addSize"><?=$rangedTotal?></span>
					<span class="shortNum lrBuffer bab"><?=$charInfo['bab']?></span>
					<span class="shortNum lrBuffer statBonus_dex"><?=$statBonus['dex']?></span>
					<span class="shortNum lrBuffer sizeVal"><?=showSign($charInfo['size'])?></span>
					<input id="ranged_misc" type="text" name="ranged_misc" value="<?=$charInfo['ranged_misc']?>" class="lrBuffer">
				</div>
			</div>
			
			<? /*
			<div class="clearfix">
				<div id="skills" class="floatLeft">
					<h2 class="headerbar hbDark">Skills</h2>
					<div class="hbdMargined">
						<div id="addSkillWrapper">
							<input id="skillName" type="text" name="newSkill[name]" class="medText placeholder" autocomplete="off" data-placeholder="Skill Name">
							<select id="skillStat" name="newSkill[stat]">
<?
	foreach ($stats as $short => $stat) echo "								<option value=\"$short\">".ucfirst($short)."</option>\n";
?>
							</select>
							<button id="addSkill" type="submit" name="newSkill_add" class="fancyButton">Add</button>
						</div>
						<div class="tr labelTR">
							<label class="medText">Skill</label>
							<label class="shortNum alignCenter lrBuffer">Total</label>
							<label class="shortNum alignCenter lrBuffer">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Ranks</label>
							<label class="shortNum alignCenter lrBuffer">Misc</label>
						</div>
<?
	$skills = $mysql->query("SELECT dnd3_skills.skillID, skillsList.name, dnd3_skills.stat, dnd3_skills.ranks, dnd3_skills.misc FROM dnd3_skills INNER JOIN skillsList USING (skillID) WHERE dnd3_skills.characterID = $characterID ORDER BY skillsList.name");
	if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
		skillFormFormat($skillInfo, $statBonus[$skillInfo['stat']]);
	} } else { ?>
						<p id="noSkills">This character currently has no skills.</p>
<?	} ?>
					</div>
				</div>
				<div id="feats" class="floatRight">
					<h2 class="headerbar hbDark">Feats/Abilities</h2>
					<div class="hbdMargined">
						<div id="addFeatWrapper">
							<input id="featName" type="text" name="newFeat_name" class="medText placeholder" autocomplete="off" data-placeholder="Feat Name">
							<button id="addFeat" type="submit" name="newFeat_add" class="fancyButton">Add</button>
						</div>
<?
	$feats = $mysql->query("SELECT dnd3_feats.featID, featsList.name FROM dnd3_feats INNER JOIN featsList USING (featID) WHERE dnd3_feats.characterID = $characterID ORDER BY featsList.name");
	if ($feats->rowCount()) { foreach ($feats as $featInfo) {
		featFormFormat($characterID, $featInfo);
	} } else { ?>
					<p id="noFeats">This character currently has no feats/abilities.</p>
<?	} ?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="weapons" class="floatLeft">
					<h2 class="headerbar hbDark">Weapons <a id="addWeapon" href="">[ Add Weapon ]</a></h2>
					<div>
<?
	$weapons = $mysql->query('SELECT * FROM dnd3_weapons WHERE characterID = '.$characterID);
	$weaponNum = 1;
	while (($weaponInfo = $weapons->fetch()) || $weaponNum <= 2) weaponFormFormat($weaponNum++, $weaponInfo);
?>
					</div>
				</div>
				<div id="armor" class="floatRight">
					<h2 class="headerbar hbDark">Armor <a id="addArmor" href="">[ Add Armor ]</a></h2>
					<div>
<?
	$armors = $mysql->query('SELECT * FROM dnd3_armors WHERE characterID = '.$characterID);
	$armorNum = 1;
	foreach ($armors as $armorInfo) {
		armorFormFormat($armorNum++, $armorInfo);
	}
	if ($armorNum == 1) armorFormFormat(1);
?>
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div id="items">
					<h2 class="headerbar hbDark">Items</h2>
					<textarea name="items" class="hbdMargined"><?=$charInfo['items']?></textarea>
				</div>
				
				<div id="spells">
					<h2 class="headerbar hbDark">Spells</h2>
					<textarea name="spells" class="hbdMargined"><?=$charInfo['spells']?></textarea>
				</div>
			</div>

			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<textarea name="notes" class="hbdMargined"><?=$charInfo['notes']?></textarea>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form> */ ?>
