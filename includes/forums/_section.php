<?
	function rollTR($count) {
?>
					<tr>
						<td class="reason"><input type="text" name="roll_reason_<?=$count?>" maxlength="100" class="borderBox"></td>
						<td class="roll"><input type="text" name="roll_roll_<?=$count?>" maxlength="50" class="borderBox"></td>
						<td class="reroll"><input type="checkbox" name="roll_ra_<?=$count?>"></td>
						<td class="visibility"><select name="roll_visibility_<?=$count?>">
							<option value="0">Hide Nothing</option>
							<option value="1">Hide Roll/Result</option>
							<option value="2">Hide Dice &amp; Roll</option>
							<option value="3">Hide Everything</option>
						</select></td>
					</tr>
<?
	}

	function permissionSet($type, $label, $permissions, $forumID = NULL, $typeID = NULL, $gameForum = FALSE) {
		global $permissionTypes;
?>
					<div class="permissionSet tr">
						<div class="clearfix">
							<div class="permission_label"><?=$label?></div>
							<a href="" class="permission_edit">[ Edit ]</a>
<?	if (!$gameForum && $type != 'general') { ?>
							<a href="<?=SITEROOT?>/forums/acp/<?=$forumID?>/deletePermission/<?=$type?>/<?=$typeID?>/" class="permission_delete">[ Delete ]</a>
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