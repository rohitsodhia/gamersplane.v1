<?
	function weaponFormFormat($weaponNum = 1, $weaponInfo = array()) {
		if (!is_numeric($weaponNum)) $weaponNum = 1;
		if (!is_array($weaponInfo) || sizeof($weaponInfo) == 0) $weaponInfo = array();
?>
						<div class="weapon<?=$weaponNum == 1?' first':''?>">
							<div class="tr labelTR weapon_firstRow">
								<label class="medText lrBuffer borderBox shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
								<label class="shortText alignCenter lrBuffer">Damage</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=$weaponNum?>][name]" value="<?=$weaponInfo['name']?>" class="weapon_name medText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][ab]" value="<?=$weaponInfo['ab']?>" class="weapons_ab shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][damage]" value="<?=$weaponInfo['damage']?>" class="weapon_damage shortText lrBuffer">
							</div>
							<div class="tr labelTR weapon_secondRow">
								<label class="shortText alignCenter lrBuffer">Error</label>
								<label class="shortText alignCenter lrBuffer">Threat</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
								<input type="text" name="weapons[<?=$weaponNum?>][error]" value="<?=$weaponInfo['error']?>" class="weapon_error shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][threat]" value="<?=$weaponInfo['threat']?>" class="weapon_crit shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][range]" value="<?=$weaponInfo['range']?>" class="weapon_range shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][type]" value="<?=$weaponInfo['type']?>" class="weapon_type shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][size]" value="<?=$weaponInfo['size']?>" class="weapon_size shortNum lrBuffer">
							</div>
							<div class="tr labelTR">
								<label class="lrBuffer shiftRight">Notes</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=$weaponNum?>][notes]" value="<?=$weaponInfo['notes']?>" class="weapon_notes lrBuffer">
							</div>
							<div class="tr alignRight lrBuffer"><a href="" class="remove">[ Remove ]</a></div>
						</div>
<?
	}

	function armorFormFormat($armorNum = 1, $armorInfo = array()) {
		if (!is_numeric($armorNum)) $armorNum = 1;
		if (!is_array($armorInfo) || sizeof($armorInfo) == 0) $armorInfo = array();
?>
						<div class="armor<?=$armorNum == 1?' first':''?>">
							<div class="tr labelTR armor_firstRow">
								<label class="medText lrBuffer borderBox shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">Def Bonus</label>
								<label class="shortText alignCenter lrBuffer">Dam Resist</label>
							</div>
							<div class="tr armor_firstRow">
								<input type="text" name="armors[<?=$armorNum?>][name]" value="<?=$armorInfo['name']?>" class="armor_name medText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][def]" value="<?=$armorInfo['def']?>" class="armors_def shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][resist]" value="<?=$armorInfo['resist']?>" class="armors_resist shortText lrBuffer">
							</div>
							<div class="tr labelTR armor_secondRow">
								<label class="shortText alignCenter lrBuffer">Max Dex</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortText alignCenter lrBuffer">Check Penalty</label>
								<label class="shortNum alignCenter lrBuffer">Speed</label>
							</div>
							<div class="tr armor_secondRow">
								<input type="text" name="armors[<?=$armorNum?>][maxDex]" value="<?=$armorInfo['maxDex']?>" class="armor_maxDex shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][type]" value="<?=$armorInfo['type']?>" class="armor_type shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][check]" value="<?=$armorInfo['check']?>" class="armor_check shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][speed]" value="<?=$armorInfo['speed']?>" class="armor_speed shortNum lrBuffer">
							</div>
							<div class="tr labelTR">
								<label class="lrBuffer shiftRight">Notes</label>
							</div>
							<div class="tr">
								<input type="text" name="armors[<?=$armorNum?>][notes]" value="<?=$armorInfo['notes']?>" class="armor_notes lrBuffer">
							</div>
							<div class="tr alignRight lrBuffer"><a href="" class="remove">[ Remove ]</a></div>
						</div>
<?
	}

	function skillFormFormat($skillInfo, $statBonus = 0) {
		if (is_array($skillInfo)) {
?>
						<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
							<span class="skill_name textLabel medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
							<span class="skill_total textLabel lrBuffer addStat_<?=$skillInfo['stat']?> shortNum"><?=showSign($statBonus + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
							<span class="skill_stat textLabel lrBuffer alignCenter shortNum"><?=ucwords($skillInfo['stat'])?></span>
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][error]" value="<?=$skillInfo['error']?>" class="skill_error medNum lrBuffer">
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][threat]" value="<?=$skillInfo['threat']?>" class="skill_threat medNum lrBuffer">
							<input type="image" name="skill<?=$skillInfo['skillID']?>_remove" src="<?=SITEROOT?>/images/cross.png" value="<?=$skillInfo['skillID']?>" class="skill_remove lrBuffer">
						</div>
<?
		}
	}

	function featFormFormat($characterID, $featInfo) {
		if (is_array($featInfo)) {
?>
						<div id="feat_<?=$featInfo['featID']?>" class="feat tr clearfix">
							<span class="feat_name textLabel"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></span>
							<a href="<?=SITEROOT?>/characters/spycraft/<?=$characterID?>/editFeatNotes/<?=$featInfo['featID']?>" id="featNotesLink_<?=$featInfo['featID']?>" class="feat_notesLink">Notes</a>
							<input type="image" name="featRemove_<?=$featInfo['featID']?>" src="<?=SITEROOT?>/images/cross.png" value="<?=$featInfo['featID']?>" class="feat_remove lrBuffer">
						</div>
<?
		}
	}
?>