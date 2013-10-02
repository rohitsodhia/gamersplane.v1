<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mapID = intval($pathOptions[2]);
	$playerCheck = $mysql->query("SELECT maps.gameID, IF(gms.userID, 1, 0) isGM FROM maps LEFT JOIN gms USING (gameID) WHERE gms.userID = $userID AND maps.mapID = $mapID");
	if (!$playerCheck->rowCount()) { header('Location: '.SITEROOT.'/403'); exit; }
	else {
		list($gameID, $isGM) = $playerCheck->fetch();
		$isGM = $isGM?TRUE:FALSE;
	}
	$mapInfo = $mysql->query('SELECT maps.gameID, maps.name, maps.columns, maps.rows, maps.details, games.title, games.systemID, systems.fullName FROM maps, games, systems WHERE games.systemID = systems.systemID AND maps.gameID = games.gameID AND maps.mapID = '.$mapID);
	$mapInfo = $mapInfo->fetch();
	
	$mapIcons = $mysql->query("SELECT * FROM maps_icons WHERE mapID = $mapID");
	$iconsInBox = array();
	$iconsOnMap = array();
	foreach ($mapIcons as $info) {
		if ($info['location'] == '') $iconsInBox[] = $info;
		else {
			$locParts = explode('_', $info['location']);
			$iconsOnMap[$locParts[0]][$locParts[1]] = $info;
		}
	}
	$mapData = $mysql->query("SELECT `column`, `row`, data FROM mapData WHERE mapID = $mapID");
	$bgData = array();
	foreach ($mapData as $dataPiece) $bgData[$dataPiece['column']][$dataPiece['row']] = $dataPiece['data'];
	$fixedMenu = TRUE;
	$mapSize['width'] = $mapInfo['columns'] * 40;
	$mapSize['height'] = $mapInfo['rows'] * 40;
	$maxMapWindow['width'] = $mapInfo['columns'] >= 15?600:$mapInfo['columns'] * 40;
	$maxMapWindow['height'] = $mapInfo['rows'] >= 15?600:$mapInfo['rows'] * 40;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<noscript><div class="alertBox_error">
			<p>This page requires javascript to run.</p>
			<p>Please make sure your browser is running javascript to use the GP mapping system.</p>
		</div></noscript>
		
		<h1><?=printReady($mapInfo['name'])?></h1>
		
		<div id="mapInfo">
			<h2>Map Info</h2>
			<p><b>Game:</b> <?=$mapInfo['title']?> (<?=$mapInfo['fullName']?>)</p>
			<p><b>Details:</b> <span id="detailsSpan"><? if (strlen($mapInfo['details'])) echo printReady($mapInfo['details']); elseif ($isGM) echo 'No details yet.'; ?></span> <sup><a id="detailsEdit" href="">[ Edit ]</a></sup></p>
			<p class="reminder">Remember: you can see each icon's label by holding your mouse over it.</p>
		</div>
		
<? if ($isGM) { ?>
		<div id="iconBox">
			<div id="iconBox_icons" class="clearfix">
<?		foreach ($iconsInBox as $icon) echo "\t\t\t\t<div id=\"icon_{$icon['iconID']}\" class=\"mapIcon {$icon['color']}Icon\" title=\"{$icon['name']}\">{$icon['label']}</div>\n"; ?>
			</div>
			<hr>
			
			<a id="addIcon" href="">Add Icon</a>
			
			<form id="iconForm" method="post" action="<?=SITEROOT?>/tools/process/maps/icons">
				<input id="iconID" type="hidden" name="iconID">
				<div class="tr">
					<label class="textLabel">Color:</label>
					<div><select id="iconColor" name="color">
						<option value="blue">Blue</option>
						<option value="green">Green</option>
						<option value="grey">Grey</option>
						<option value="orange">Orange</option>
						<option value="red">Red</option>
						<option value="white">White</option>
					</select></div>
				</div>
				<div class="tr">
					<label class="textLabel">Label:</label>
					<div><input id="iconLabel" type="text" name="label" maxlength="2"></div>
				</div>
				<p class="small">The label must be 1 or 2 characters in length.</p>
				<div class="tr">
					<label class="textLabel">Name:</label>
					<div><input id="iconName" type="text" name="name"></div>
				</div>
<!--				<div class="tr">
					<label class="textLabel">Description:</label>
					<div><textarea id="desc" name="desc"></textarea></div>
				</div>-->
				<div class="tr editDiv">
					<button type="submit" name="save" class="btn_save"></button>
					<button type="submit" name="delete" class="btn_delete"></button>
				</div>
				<div class="tr addDiv"><button type="submit" name="submit" class="btn_submit"></button></div>
			</form>
		</div>
		
<? } ?>
		
		<div class="clearfix">
			<div id="mapSidebar" style="height: <?=$maxMapWindow['height'] - 32?>px;">
				<div id="mapControls">
					<img src="<?=SITEROOT?>/images/mapControls.png">
					<a id="mapControls_up_top" href="" class="mapControls_up">&nbsp;</a>
					<a id="mapControls_up_body" href="" class="mapControls_up">&nbsp;</a>
					<a id="mapControls_right_top" href="" class="mapControls_right">&nbsp;</a>
					<a id="mapControls_right_body" href="" class="mapControls_right">&nbsp;</a>
					<a id="mapControls_down_top" href="" class="mapControls_down">&nbsp;</a>
					<a id="mapControls_down_body" href="" class="mapControls_down">&nbsp;</a>
					<a id="mapControls_left_top" href="" class="mapControls_left">&nbsp;</a>
					<a id="mapControls_left_body" href="" class="mapControls_left">&nbsp;</a>
				</div>
				<div id="textPane" style="height: <?=$maxMapWindow['height'] - 102?>px;">
<?
	$iconActions = $mysql->query("SELECT ic.iconID, icons.label, icons.name, ic.mapID, ic.enactedBy, users.username, ic.action, ic.origin, ic.destination FROM maps_iconHistory ic, maps_icons icons, users WHERE ic.iconID = icons.iconID AND ic.enactedBy = users.userID ".($isGM?'':"AND ic.action = 'moved' ")."AND ic.mapID = $mapID ORDER BY ic.actionID");
	foreach ($iconActions as $actionInfo) {
		$locParts = explode('_', $actionInfo['origin']);
		$actionInfo['origin'] = decToB26($locParts[0]).$locParts[1];
		$locParts = explode('_', $actionInfo['destination']);
		$actionInfo['destination'] = decToB26($locParts[0]).$locParts[1];
		if ($actionInfo['action'] == 'moved') echo "\t\t\t\t\t<p><a href=\"".SITEROOT."/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> moved <b>{$actionInfo['name']}</b> ({$actionInfo['label']}) from ".(strlen($actionInfo['origin'])?strtoupper($actionInfo['origin']):'Box')." to ".(strlen($actionInfo['destination'])?strtoupper($actionInfo['destination']):'Box')."</p>\n";
		elseif ($actionInfo['action'] == 'created') echo "\t\t\t\t\t<p><a href=\"".SITEROOT."/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> created <b>{$actionInfo['name']}</b> ({$actionInfo['label']})</p>\n";
		elseif ($actionInfo['action'] == 'edited') echo "\t\t\t\t\t<p><a href=\"".SITEROOT."/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> edited <b>{$actionInfo['name']}</b> ({$actionInfo['label']})</p>\n";
		elseif ($actionInfo['action'] == 'deleted') echo "\t\t\t\t\t<p><a href=\"".SITEROOT."/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> deleted <b>{$actionInfo['name']}</b> ({$actionInfo['label']})</p>\n";
	}
?>
				</div>
			</div>
			
			<div id="mapContainer" style="width: <?=$maxMapWindow['width'] + 40?>px; height: <?=$maxMapWindow['height'] + 40?>px">
				<div id="colHeaders" style="width: <?=$maxMapWindow['width']?>px;"><div style="width: <?=$mapSize['width']?>px;">
<?
	$curCol = 'a';
	for ($cCount = 1; $cCount <= $mapInfo['columns']; $cCount++) {
		echo "\t\t\t\t\t<div class=\"cHeader cHeaderMin col_$cCount\">\n";
		echo "\t\t\t\t\t\t<a href=\"\">".$curCol++."</a>\n";
		echo "\t\t\t\t\t\t<a href=\"".SITEROOT."/tools/process/maps/removeCR/$mapID/$cCount\" class=\"removeCol\">-</a>\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div></div>
				<div id="rowHeaders" style="height: <?=$maxMapWindow['height']?>px;"><div style="height: <?=$mapSize['height']?>px;">
<?
	for ($rCount = 1; $rCount <= $mapInfo['rows']; $rCount++) {
		echo "\t\t\t\t\t<div class=\"rHeader rHeaderMin row_$rCount\">\n";
		echo "\t\t\t\t\t\t<a href=\"\">".$rCount."</a>\n";
		echo "\t\t\t\t\t\t<a href=\"".SITEROOT."/tools/process/maps/removeCR/$mapID/$rCount\" class=\"removeCol\">-</a>\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div></div>
				<div id="mapIconHolder"></div>
				<div id="mapWindow" style="width: <?=$maxMapWindow['width']?>px; height: <?=$maxMapWindow['height']?>px">
					<div id="map" style="width: <?=$mapSize['width']?>px; height: <?=$mapSize['height']?>px">
<?
	$count = 0;
	$bg = '';
	for ($rCount = 1; $rCount <= $mapInfo['rows']; $rCount++) {
		for ($cCount = 1; $cCount <= $mapInfo['columns']; $cCount++) {
			echo "\t\t\t\t\t\t<div id=\"{$cCount}_{$rCount}\" class=\"mapTile col_$cCount row_$rCount\"".(isset($bgData[$cCount][$rCount])?' style="background-color: '.$bgData[$cCount][$rCount].';"':'').">\n";
			if (isset($iconsOnMap[$cCount][$rCount])) echo "<div id=\"icon_{$iconsOnMap[$cCount][$rCount]['iconID']}\" class=\"mapIcon {$iconsOnMap[$cCount][$rCount]['color']}Icon\" title=\"{$iconsOnMap[$cCount][$rCount]['name']}\">{$iconsOnMap[$cCount][$rCount]['label']}</div>";
			echo "</div>\n";
		}
	}
?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="hideDiv"><form id="saveDetails" method="post" action="<?=SITEROOT?>/tools/process/maps/saveDetails">
			<input type="hidden" name="mapID" value="<?=$mapID?>">
			<textarea name="details"><?=$mapInfo['details']?></textarea>
			<button type="submit" name="save" class="btn_save"></button>
		</form></div>
<? require_once(FILEROOT.'/footer.php'); ?>