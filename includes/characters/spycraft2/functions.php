<?
	function weaponFormFormat($weaponNum = 1, $weaponInfo = array()) {
		if (!is_numeric($weaponNum)) $weaponNum = 1;
		if (!is_array($weaponInfo) || sizeof($weaponInfo) == 0) $weaponInfo = array();
?>
						<div class="weapon<?=$weaponNum == 1?' first':''?>">
							<div class="tr labelTR">
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
								<label class="shortText alignCenter lrBuffer">Recoil</label>
								<label class="shortText alignCenter lrBuffer">Error/Threat</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
								<input type="text" name="weapons[<?=$weaponNum?>][recoil]" value="<?=$weaponInfo['recoil']?>" class="weapon_recoil shortNum lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][et]" value="<?=$weaponInfo['et']?>" class="weapon_et shortNum lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][range]" value="<?=$weaponInfo['range']?>" class="weapon_range shortNum lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][type]" value="<?=$weaponInfo['type']?>" class="weapon_type shortNum lrBuffer">
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
								<label class="shortText alignCenter lrBuffer">Dam Reduct</label>
								<label class="shortText alignCenter lrBuffer">Dam Resist</label>
							</div>
							<div class="tr armor_firstRow">
								<input type="text" name="armors[<?=$armorNum?>][name]" value="<?=$armorInfo['name']?>" class="armor_name medText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][reduction]" value="<?=$armorInfo['reduction']?>" class="armors_reduction shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][resist]" value="<?=$armorInfo['resist']?>" class="armors_resist shortText lrBuffer">
							</div>
							<div class="tr labelTR armor_secondRow">
								<label class="shortText alignCenter lrBuffer">Def Pen</label>
								<label class="shortText alignCenter lrBuffer">Check Penalty</label>
								<label class="shortText alignCenter lrBuffer">Speed</label>
								<label class="shortText alignCenter lrBuffer">Notice/Search DC</label>
							</div>
							<div class="tr armor_secondRow">
								<input type="text" name="armors[<?=$armorNum?>][penalty]" value="<?=$armorInfo['penalty']?>" class="armor_penalty shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][check]" value="<?=$armorInfo['check']?>" class="armor_check shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][speed]" value="<?=$armorInfo['speed']?>" class="armor_speed shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][dc]" value="<?=$armorInfo['dc']?>" class="armor_dc shortText lrBuffer">
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

	function skillFormFormat($skillInfo, $statBonus1 = 0, $statBonus2 = 0) {
		if (is_array($skillInfo)) {
			$total_1 = $statBonus1 + $skillInfo['ranks'] + $skillInfo['misc'];
			$total_2 = $statBonus2 + $skillInfo['ranks'] + $skillInfo['misc'];
?>
					<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
						<span class="skill_total lrBuffer shortNum"><span class="skill_total_1 addStat_<?=$skillInfo['stat_1']?>"><?=showSign($total_1).'</span>'.($skillInfo['stat_2'] != '' ? "/<span class=\"skill_total_2 addStat_{$skillInfo['stat_2']}\">".showSign($total_2).'</span>' : '')?></span>
						<span class="skill_stat lrBuffer alignCenter shortText"><?=ucwords($skillInfo['stat_1']).($skillInfo['stat_2'] != '' ? '/'.ucwords($skillInfo['stat_2']) : '')?></span>
						<input type="text" name="skills[<?=$skillInfo['skillID']?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
						<input type="text" name="skills[<?=$skillInfo['skillID']?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
						<input type="text" name="skills[<?=$skillInfo['skillID']?>][error]" value="<?=$skillInfo['error']?>" class="skill_error medNum lrBuffer">
						<input type="text" name="skills[<?=$skillInfo['skillID']?>][threat]" value="<?=$skillInfo['threat']?>" class="skill_threat medNum lrBuffer">
						<input type="image" name="skill<?=$skillInfo['skillID']?>_remove" src="/images/cross.png" value="<?=$skillInfo['skillID']?>" class="skill_remove">
					</div>
<?
		}
	}

	function focusFormFormat($characterID, $focusInfo) {
		if (is_array($focusInfo)) {
?>
						<div id="focus_<?=$focusInfo['focusID']?>" class="focus tr clearfix">
							<input type="checkbox" name="focus_forte[<?=$focusInfo['focusID']?>]"<?=$focusInfo['forte']?' checked="checked"':''?> class="shortNum">
							<span class="focus_name"><?=mb_convert_case($focusInfo['name'], MB_CASE_TITLE)?></span>
							<input type="image" name="focusRemove_<?=$focusInfo['focusID']?>" src="/images/cross.png" value="<?=$focusInfo['focusID']?>" class="focus_remove lrBuffer">
						</div>
<?
		}
	}

	function featFormFormat($characterID, $featInfo) {
		if (is_array($featInfo)) {
?>
						<div id="feat_<?=$featInfo['featID']?>" class="feat tr clearfix">
							<span class="feat_name"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></span>
							<a href="/characters/spycraft2/<?=$characterID?>/editFeatNotes/<?=$featInfo['featID']?>" id="featNotesLink_<?=$featInfo['featID']?>" class="feat_notesLink">Notes</a>
							<input type="image" name="featRemove_<?=$featInfo['featID']?>" src="/images/cross.png" value="<?=$featInfo['featID']?>" class="feat_remove lrBuffer">
						</div>
<?
		}
	}
?>