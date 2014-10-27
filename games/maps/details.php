<?
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = $userID");
	if (!$gmCheck->rowCount()) { header('Location: /tools/maps'); exit; }

	$action = $pathOptions[3];
	$mapDetails = array('name' => '', 'rows' => 10, 'columns' => 10, 'visible' => 0);

	if ($action == 'edit') {
		$mapID = intval($pathOptions[2]);
		$mapDetails = $mysql->query("SELECT name, columns, rows, visible FROM maps WHERE gameID = $gameID AND mapID = $mapID");
		$mapDetails = $mapDetails->fetch();
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=$action == 'edit'?'Edit':'New'?> Map</h1>
		
		<form method="post" action="/games/process/maps/details" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
<? if ($action == 'edit') {?>
			<input type="hidden" name="mapID" value="<?=$mapID?>">
<? } ?>
			<div class="tr clearfix">
				<label class="textLabel">Map Name:</label>
				<input type="text" name="name" value="<?=$mapDetails['name']?>">
			</div>
			<div id="mapSize" class="tr clearfix">
				<label class="textLabel">Map Size:</label>
				<input type="text" name="rows" value="<?=$mapDetails['rows']?>" class="alignCenter"> Rows x <input type="text" name="columns" value="<?=$mapDetails['columns']?>" class="alignCenter"> Columns
			</div>
			<div class="tr clearfix">
				<label>Visible?</label>
				<input type="checkbox" name="visible" value="visible"<?=$mapDetails['visible']?' checked="checked"':''?>>
			</div>
			<div class="alignCenter"><button type="submit" name="<?=$action == 'edit'?'edit':'create'?>" class="fancyButton"><?=$action == 'edit'?'Edit':'Create'?></button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>