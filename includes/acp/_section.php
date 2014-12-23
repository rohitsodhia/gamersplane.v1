<?
	function linkFormat($link = array()) {
		if (sizeof($link) == 0) $link['_id'] = 'new';
		if ($link['_id'] != 'new') {
?>
				<li<?=!$link['active']?' class="inactive"':''?>><form method="post" action="/acp/process/manageLink/" enctype="multipart/form-data">
					<input type="hidden" name="mongoID" value="<?=$link['_id']?>">
<?		} ?>
					<div class="preview">
<?php if (file_exists(FILEROOT.'/images/links/'.$link['_id'].'.png')) { ?>
					<img src="/images/links/<?=$link['_id']?>.png">
<? } ?>
					</div>
					<div class="link">
						<input type="text" name="title" value="<?=$link['title']?>" class="title placeholder" data-placeholder="Title">
						<input type="text" name="url" value="<?=$link['url']?>" class="url placeholder" data-placeholder="URL">
						<input type="file" name="image" class="image">
					</div>
					<select name="level">
						<option name="link">Link</option>
						<option name="affiliate">Affiliate</option>
					</select>
<?		if ($link['_id'] != 'new') { ?>
					<div class="actions">
						<div>
							<button type="submit" name="action" value="edit" class="action_edit sprite pencil"></button>
							<div class="confirmEdit">
								<button type="submit" name="action" value="edit" class="action_edit_save sprite check green"></button>
								<button type="submit" name="action" value="edit" class="action_edit_cancel sprite cross"></button>
							</div>
						</div>
						<div>
							<button type="submit" name="action" value="edit" class="action_delete sprite cross"></button>
							<div class="confirmDelete">
								<button type="submit" name="action" value="edit" class="action_delete_confirm sprite check"></button>
								<button type="submit" name="action" value="edit" class="action_delete_cancel sprite cross"></button>
							</div>
						</div>
					</div>
				</form></li>
<?
		}
	}
?>