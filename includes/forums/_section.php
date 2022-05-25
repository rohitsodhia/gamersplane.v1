<?
	function rollTR($count, $data) {
		echo "						<div class=\"rollWrapper\">\n";
		echo "							<button class=\"sprite cross small\"></button>\n";
		if ($data->type == 'basic') {
?>
							<div class="newRoll basicRoll">
								<div class="headers">
									<div class="reason">Reason</div>
									<div class="roll">Roll</div>
									<div class="visibility">Visibility</div>
									<div class="reroll">Reroll Aces</div>
								</div>
								<div>
									<input type="hidden" name="rolls[<?=$count?>][type]" value="basic">
									<div class="reason"><input type="text" name="rolls[<?=$count?>][reason]" maxlength="100"<?=isset($data->reason)?" value=\"{$data->reason}\"":null?> class="borderBox"></div>
									<div class="roll"><input type="text" name="rolls[<?=$count?>][roll]" maxlength="50"<?=isset($data->roll)?" value=\"{$data->roll}\"":null?> class="borderBox"></div>
									<div class="visibility"><select name="rolls[<?=$count?>][visibility]">
										<option value="0"<?=isset($data->visibility) && $data->visibility == 0?' selected="selected"':null?>>Hide Nothing</option>
										<option value="1"<?=isset($data->visibility) && $data->visibility == 1?' selected="selected"':null?>>Hide Roll/Result</option>
										<option value="2"<?=isset($data->visibility) && $data->visibility == 2?' selected="selected"':null?>>Hide Dice &amp; Roll</option>
										<option value="3"<?=isset($data->visibility) && $data->visibility == 3?' selected="selected"':null?>>Hide Everything</option>
										<option value="4"<?=isset($data->visibility) && $data->visibility == 4?' selected="selected"':null?>>Hide Reason</option>
									</select></div>
									<div class="reroll"><input type="checkbox" name="rolls[<?=$count?>][options][rerollAces]"<?=isset($data->options['rerollAces'])?' checked="checked"':null?>></div>
								</div>
							</div>
<?
		} elseif ($data->type == 'starwarsffg') {
?>
							<div class="newRoll starwarsffgRoll">
								<div class="headers">
									<div class="reason">Reason</div>
									<div class="roll">Roll</div>
									<div class="visibility">Visibility</div>
								</div>
								<div>
									<input type="hidden" name="rolls[<?=$count?>][type]" value="starwarsffg">
									<div class="reason"><input type="text" name="rolls[<?=$count?>][reason]" maxlength="100"<?=isset($data->reason)?" value=\"{$data->reason}\"":null?> class="borderBox"></div>
									<div class="roll">
										<div class="dicePool">
											<div class="add"><img src="/images/plus.png"></div>
											<div class="selectedDice">
<?
			if (isset($data->roll) && strlen($data->roll)) {
				$diceTypes = array('a' => 'ability', 'p' => 'proficiency', 'b' => 'boost', 'd' => 'difficulty', 'c' => 'challenge', 's' => 'setback', 'f' => 'force');
				foreach (explode(',', $data->roll) as $dice) {
?>
												<div class="diceIcon starwarsffg_<?=$diceTypes[$dice]?>" title="<?=ucwords($diceTypes[$dice])?>"></div>
<?
				}
			}
?>
											</div>
											<div class="diceOptions">
												<div class="do_pointer"><div class="do_pointer_inner"></div></div>
												<div class="dice">
													<div class="diceIcon starwarsffg_ability" title="Ability"></div>
													<div class="diceIcon starwarsffg_proficiency" title="Proficiency"></div>
													<div class="diceIcon starwarsffg_boost" title="Boost"></div>
													<div class="diceIcon starwarsffg_difficulty" title="Difficulty"></div>
													<div class="diceIcon starwarsffg_challenge" title="Challenge"></div>
													<div class="diceIcon starwarsffg_setback" title="Setback"></div>
													<div class="diceIcon starwarsffg_force" title="Force"></div>
												</div>
											</div>
											<input type="hidden" name="rolls[<?=$count?>][roll]" value="<?=isset($data->roll)?$data->roll:null?>">
										</div>
									</div>
									<div class="visibility"><select name="rolls[<?=$count?>][visibility]">
										<option value="0"<?=isset($data->visibility) && $data->visibility == 0?' selected="selected"':null?>>Hide Nothing</option>
										<option value="1"<?=isset($data->visibility) && $data->visibility == 1?' selected="selected"':null?>>Hide Roll/Result</option>
										<option value="2"<?=isset($data->visibility) && $data->visibility == 2?' selected="selected"':null?>>Hide Dice &amp; Roll</option>
										<option value="3"<?=isset($data->visibility) && $data->visibility == 3?' selected="selected"':null?>>Hide Everything</option>
										<option value="4"<?=isset($data->visibility) && $data->visibility == 4?' selected="selected"':null?>>Hide Reason</option>

									</select></div>
								</div>
							</div>
<?
		} elseif ($data->type == 'fate') {
?>
							<div class="newRoll fateRoll">
								<div class="headers">
									<div class="reason">Reason</div>
									<div class="roll"># of Dice</div>
									<div class="modifier">Modifier</div>
									<div class="visibility">Visibility</div>
								</div>
								<div>
									<input type="hidden" name="rolls[<?=$count?>][type]" value="fate">
									<div class="reason"><input type="text" name="rolls[<?=$count?>][reason]" maxlength="100"<?=isset($data->reason)?" value=\"{$data->reason}\"":null?> class="borderBox"></div>
									<div class="roll">
										<input type="text" name="rolls[<?=$count?>][roll]" value="<?=isset($data->roll)?$data->roll:4?>">
									</div>
									<div class="modifier">
										<input type="text" name="rolls[<?=$count?>][modifier]" value="<?=isset($data->modifier)?$data->modifier:0?>">
									</div>
									<div class="visibility"><select name="rolls[<?=$count?>][visibility]">
										<option value="0"<?=isset($data->visibility) && $data->visibility == 0?' selected="selected"':null?>>Hide Nothing</option>
										<option value="1"<?=isset($data->visibility) && $data->visibility == 1?' selected="selected"':null?>>Hide Roll/Result</option>
										<option value="2"<?=isset($data->visibility) && $data->visibility == 2?' selected="selected"':null?>>Hide Dice &amp; Roll</option>
										<option value="3"<?=isset($data->visibility) && $data->visibility == 3?' selected="selected"':null?>>Hide Everything</option>
										<option value="4"<?=isset($data->visibility) && $data->visibility == 4?' selected="selected"':null?>>Hide Reason</option>
									</select></div>
								</div>
							</div>
<?
		} elseif ($data->type == 'fengshui') {
?>
							<div class="newRoll fengShuiRoll">
								<div class="headers">
									<div class="reason">Reason</div>
									<div class="av">AV</div>
									<div class="modifier">Modifier</div>
									<div class="visibility">Visibility</div>
								</div>
								<div>
									<input type="hidden" name="rolls[<?=$count?>][type]" value="fengshui">
									<div class="reason"><input type="text" name="rolls[<?=$count?>][reason]" maxlength="100"<?=isset($data->reason)?" value=\"{$data->reason}\"":null?> class="borderBox"></div>
									<div class="av">
										<input type="text" name="rolls[<?=$count?>][roll]" value="<?=isset($data->roll)?$data->roll:0?>">
									</div>
									<div class="modifier"><select name="rolls[<?=$count?>][options][]">
<?			foreach (array('standard', 'fortune', 'closed') as $modifier) { ?>
										<option value="<?=$modifier?>"<?=isset($data->modifier) && $data->modifier == $modifier?' selected="selected"':null?>><?=ucwords($modifier)?></option>
<?			} ?>
									</select></div>
									<div class="visibility"><select name="rolls[<?=$count?>][visibility]">
										<option value="0"<?=isset($data->visibility) && $data->visibility == 0?' selected="selected"':null?>>Hide Nothing</option>
										<option value="1"<?=isset($data->visibility) && $data->visibility == 1?' selected="selected"':null?>>Hide Roll/Result</option>
										<option value="2"<?=isset($data->visibility) && $data->visibility == 2?' selected="selected"':null?>>Hide Dice &amp; Roll</option>
										<option value="3"<?=isset($data->visibility) && $data->visibility == 3?' selected="selected"':null?>>Hide Everything</option>
										<option value="4"<?=isset($data->visibility) && $data->visibility == 4?' selected="selected"':null?>>Hide Reason</option>
									</select></div>
								</div>
							</div>
<?
		}
		echo "						</div>\n";
	}

	function permissionSet($type, $label, $permissions, $forumID = null, $typeID = null, $gameForum = false) {
		global $permissionTypes;
?>
					<div class="permissionSet tr">
						<div class="clearfix">
							<div class="permission_label"><?=$label?></div>
							<a href="" class="permission_edit">[ Edit ]</a>
<?	if (!$gameForum && $type != 'general') { ?>
							<a href="/forums/acp/<?=$forumID?>/deletePermission/<?=$type?>/<?=$typeID?>/" class="permission_delete">[ Delete ]</a>
<?	} ?>
						</div>
						<div class="permissions">
<?	foreach ($permissionTypes as $pType => $title) { if ($title != 'Moderate' || ($title == 'Moderate' && $type != 'general')) { ?>
							<div class="tr clearfix permission_type_repeater">
								<div class="permission_type textLabel"><?=$title?></div>
								<select name="permissions[<?=$type?>]<?=$type != 'general'?"[$typeID]":''?>[<?=$pType?>]">
									<option value="1"<?=$permissions[$pType] >= 1?' selected="selected"':''?>>Yes</option>
									<option value="0"<?=$permissions[$pType] == 0?' selected="selected"':''?>>Don't Care</option>
									<option value="-1"<?=$permissions[$pType] <= -1?' selected="selected"':''?>>No</option>
								</select>
							</div>
<?	} } ?>
						</div>
					</div>
<?
	}
?>
