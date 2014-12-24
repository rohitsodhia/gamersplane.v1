<?
	global $levels;
	$levels = array(1 => 'Link', 'Affiliate');
	function linkFormat($link = array()) {
		global $levels;
		if (sizeof($link) == 0) {
			$link['_id'] = 'new';
			$link['level'] = 'link';
		}
		if ($link['_id'] != 'new') {
?>
				<li<?=!$link['active']?' class="inactive"':''?>><form method="post" action="/acp/process/manageLink/" enctype="multipart/form-data">
					<input type="hidden" name="modal" value="1">
					<input type="hidden" name="mongoID" value="<?=$link['_id']?>">
<?		} ?>
					<div class="preview">
<?		if (file_exists(FILEROOT.'/images/links/'.$link['_id'].'.'.$link['image'])) { ?>
						<img src="/images/links/<?=$link['_id']?>.<?=$link['image']?>">
<?		} else { ?>
						<img src="/images/spacer.gif">
<?		} ?>
<?		if ($link['_id'] != 'new') { ?>
						<button type="submit" name="action" value="deleteImage" class="action_deleteImage sprite cross small"></button>
<?		} ?>
					</div>
					<div class="link">
						<input type="text" name="title" value="<?=$link['title']?>"<?=$link['_id'] != 'new'?' disabled="disabled"':''?> class="title placeholder" data-placeholder="Title">
						<input type="text" name="url" value="<?=$link['url']?>"<?=$link['_id'] != 'new'?' disabled="disabled"':''?> class="url placeholder" data-placeholder="URL">
						<input type="file" name="image"<?=$link['_id'] != 'new'?' disabled="disabled"':''?> class="image">
					</div>
					<div class="level">
						<div class="display"><?=$levels[$link['level']]?></div>
						<select name="level">
<?		foreach ($levels as $levelNum => $level) { ?>
							<option value="<?=$levelNum?>"<?=$link['level'] == $levelNum?' selected="selected"':''?>><?=$level?></option>
<?		} ?>
						</select>
					</div>
<?		if ($link['_id'] != 'new') { ?>
					<div class="actions">
						<div>
							<button type="submit" name="action" value="edit" class="action_edit sprite pencil"></button>
							<div class="confirmEdit hideDiv">
								<button type="submit" name="action" value="save" class="action_edit_save sprite check green"></button>
								<button type="submit" name="action" value="cancelEdit" class="action_edit_cancel sprite cross"></button>
							</div>
						</div>
						<div>
							<button type="submit" name="action" value="deleteCheck" class="action_delete sprite cross"></button>
							<div class="confirmDelete hideDiv">
								<button type="submit" name="action" value="delete" class="action_delete_confirm sprite check"></button>
								<button type="submit" name="action" value="cancelDelete" class="action_delete_cancel sprite cross"></button>
							</div>
						</div>
					</div>
				</form></li>
<?
		}
	}
?>