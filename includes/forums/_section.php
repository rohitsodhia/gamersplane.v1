<?
	function rollTR($count, $type = 'basic', $data = array()) {
		echo "						<div class=\"rollWrapper\">\n";
		echo "							<button class=\"close\"><img src=\"/images/cross.png\"></button>\n";
		if ($type == 'sweote') {
?>
							<div class="table newRoll sweoteRoll">
								<div class="headers">
									<div class="reason">Reason</div>
									<div class="roll">Roll</div>
									<div class="visibility">Visibility</div>
								</div>
								<div>
									<input type="hidden" name="rolls[<?=$count?>][type]" value="sweote">
									<div class="reason"><input type="text" name="rolls[<?=$count?>][reason]" maxlength="100"<?=isset($data['reason'])?" value=\"{$data['reason']}\"":NULL?> class="borderBox"></div>
									<div class="roll">
										<div class="dicePool">
											<div class="add"><img src="/images/plus.png"></div>
											<div class="selectedDice">
<?
			if (isset($data['roll']) && strlen($data['roll'])) {
				$diceTypes = array('a' => 'ability', 'p' => 'proficiency', 'b' => 'boost', 'd' => 'difficulty', 'c' => 'challenge', 's' => 'setback', 'f' => 'force');
				foreach (explode(',', $data['roll']) as $dice) {
?>
												<div class="diceIcon sweote_<?=$diceTypes[$dice]?>" title="<?=ucwords($diceTypes[$dice])?>"></div>
<?
				}
			}
?>
											</div>
											<div class="diceOptions">
												<div class="do_pointer"><div class="do_pointer_inner"></div></div>
												<div class="dice">
													<div class="diceIcon sweote_ability" title="Ability"></div>
													<div class="diceIcon sweote_proficiency" title="Proficiency"></div>
													<div class="diceIcon sweote_boost" title="Boost"></div>
													<div class="diceIcon sweote_difficulty" title="Difficulty"></div>
													<div class="diceIcon sweote_challenge" title="Challenge"></div>
													<div class="diceIcon sweote_setback" title="Setback"></div>
													<div class="diceIcon sweote_force" title="Force"></div>
												</div>
											</div>
											<input type="hidden" name="rolls[<?=$count?>][roll]" value="<?=isset($data['roll'])?$data['roll']:NULL?>">
										</div>
									</div>
									<div class="visibility"><select name="rolls[<?=$count?>][visibility]">
										<option value="0"<?=isset($data['visibility']) && $data['visibility'] == 0?' selected="selected"':NULL?>>Hide Nothing</option>
										<option value="1"<?=isset($data['visibility']) && $data['visibility'] == 1?' selected="selected"':NULL?>>Hide Roll/Result</option>
										<option value="2"<?=isset($data['visibility']) && $data['visibility'] == 2?' selected="selected"':NULL?>>Hide Dice &amp; Roll</option>
										<option value="3"<?=isset($data['visibility']) && $data['visibility'] == 3?' selected="selected"':NULL?>>Hide Everything</option>
									</select></div>
								</div>
							</div>
<?
		} else {
?>
							<div class="table newRoll basicRoll">
								<div class="headers">
									<div class="reason">Reason</div>
									<div class="roll">Roll</div>
									<div class="visibility">Visibility</div>
									<div class="reroll">Reroll Aces</div>
								</div>
								<div>
									<input type="hidden" name="rolls[<?=$count?>][type]" value="basic">
									<div class="reason"><input type="text" name="rolls[<?=$count?>][reason]" maxlength="100"<?=isset($data['reason'])?" value=\"{$data['reason']}\"":NULL?> class="borderBox"></div>
									<div class="roll"><input type="text" name="rolls[<?=$count?>][roll]" maxlength="50"<?=isset($data['roll'])?" value=\"{$data['roll']}\"":NULL?> class="borderBox"></div>
									<div class="visibility"><select name="rolls[<?=$count?>][visibility]">
										<option value="0"<?=isset($data['visibility']) && $data['visibility'] == 0?' selected="selected"':NULL?>>Hide Nothing</option>
										<option value="1"<?=isset($data['visibility']) && $data['visibility'] == 1?' selected="selected"':NULL?>>Hide Roll/Result</option>
										<option value="2"<?=isset($data['visibility']) && $data['visibility'] == 2?' selected="selected"':NULL?>>Hide Dice &amp; Roll</option>
										<option value="3"<?=isset($data['visibility']) && $data['visibility'] == 3?' selected="selected"':NULL?>>Hide Everything</option>
									</select></div>
									<div class="reroll"><input type="checkbox" name="rolls[<?=$count?>][options][rerollAces]"<?=isset($data['options']['rerollAces'])?' checked="checked"':NULL?>></div>
								</div>
							</div>
<?
		}
		echo "						</div>\n";
	}

	function permissionSet($type, $label, $permissions, $forumID = NULL, $typeID = NULL, $gameForum = FALSE) {
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
							<div class="tr clearfix">
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