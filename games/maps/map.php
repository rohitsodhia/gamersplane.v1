<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$mapID = intval($pathOptions[2]);
	$playerCheck = $mysql->query("SELECT p.isGM FROM maps m, players p WHERE m.gameID = $gameID AND m.gameID = p.gameID AND p.userID = $userID AND m.mapID = $mapID");
	if (!$playerCheck->rowCount()) { header('Location: '.SITEROOT.'/403'); exit; }
	else list($isGM) = $playerCheck->fetch();
	$mapInfo = $mysql->query('SELECT m.gameID, m.name, m.columns, m.rows, m.info, g.title, g.systemID, s.fullName FROM maps m, games g, systems s WHERE g.systemID = s.systemID AND m.gameID = g.gameID AND m.mapID = '.$mapID);
	$mapInfo = $mapInfo->fetch();
	
	$mapIcons = $mysql->query("SELECT iconID, label, name, description, color, location FROM maps_icons WHERE mapID = $mapID");
	$iconsInBox = array();
	$iconsOnMap = array();
	foreach ($mapIcons as $info) {
		if ($info['location'] == '') $iconsInBox[] = $info;
		else {
			$locParts = explode('_', $info['location']);
			$iconsOnMap[$locParts[0]][$locParts[1]] = $info;
		}
	}
	$mapData = $mysql->query("SELECT `column`, row, data FROM mapData WHERE mapID = $mapID");
	$bgData = array();
	foreach ($mapData as $dataPiece) $bgData[$dataPiece['column']][$dataPiece['row']] = $dataPiece['data'];
	$mapSize['width'] = $mapInfo['columns'] * 40;
	$mapSize['height'] = $mapInfo['rows'] * 40;
	$maxMapWindow['width'] = $mapInfo['columns'] >= 15?600:$mapInfo['columns'] * 40;
	$maxMapWindow['height'] = $mapInfo['rows'] >= 15?600:$mapInfo['rows'] * 40;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=printReady($mapInfo['name'])?></h1>
		
<? if ($isGM) { ?>
		<div id="iconBox">
			<div id="iconBox_icons" class="clearfix">
<?		foreach ($iconsInBox as $icon) echo "\t\t\t\t<div id=\"icon_{$icon['iconID']}\" class=\"mapIcon {$icon['color']}Icon\" title=\"{$icon['name']}\">{$icon['label']}</div>\n"; ?>
			</div>
			<hr>
			
			<a id="addIcon" href="">Add Icon</a>
			
			<form id="iconForm" method="post" action="<?=SITEROOT?>/games/process/maps/icons">
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
					<img src="<?=SITEROOT?>/images/maps/mapControls.png">
					<a id="mapControls_up_top" href="" class="mapControls_up">&nbsp;</a>
					<a id="mapControls_up_body" href="" class="mapControls_up">&nbsp;</a>
					<a id="mapControls_right_top" href="" class="mapControls_right">&nbsp;</a>
					<a id="mapControls_right_body" href="" class="mapControls_right">&nbsp;</a>
					<a id="mapControls_down_top" href="" class="mapControls_down">&nbsp;</a>
					<a id="mapControls_down_body" href="" class="mapControls_down">&nbsp;</a>
					<a id="mapControls_left_top" href="" class="mapControls_left">&nbsp;</a>
					<a id="mapControls_left_body" href="" class="mapControls_left">&nbsp;</a>
				</div>
				<div id="mapSidebar_content" class="clearfix">
					<div class="clearfix"><div id="mapSidebar_contentControls" class="wingDiv sectionControls" data-ratio=".8">
						<div class="wingDivContent">
							<a id="mapSidebar_contentControls_info" href="" class="current">Info</a>
							<a id="mapSidebar_contentControls_box" href="">Box</a>
							<a id="mapSidebar_contentControls_history" href="">History</a>
						</div>
						<div class="wing dlWing"></div>
						<div class="wing drWing"></div>
					</div></div>
					<div id="mapSidebar_contentContainer" style="height: <?=$maxMapWindow['height'] - 92?>px;">
						<div id="mapInfo">
							<p><strong>Game:</strong> <?=$mapInfo['title']?> (<?=$mapInfo['fullName']?>)</p>
							<p><strong>Info:</strong> <span id="infoSpan"><? if (strlen($mapInfo['info'])) echo printReady($mapInfo['info']); elseif ($isGM) echo 'No info yet.'; ?></span> <sup><a id="infoEdit" href="<?=SITEROOT?>/games/<?=$gameID?>/maps/<?=$mapID?>/editInfo">[ Edit ]</a></sup></p>
							<p class="reminder">Remember: you can see each icon's label by holding your mouse over it.</p>
						</div>
<?
	$iconActions = $mysql->query("SELECT ic.iconID, icons.label, icons.name, ic.mapID, ic.enactedBy, users.username, ic.action, ic.origin, ic.destination FROM maps_iconHistory ic, maps_icons icons, users WHERE ic.iconID = icons.iconID AND ic.enactedBy = users.userID ".($isGM?'':"AND ic.action = 'moved' ")."AND ic.mapID = $mapID ORDER BY ic.actionID");
	foreach ($iconActions as $actionInfo) {
		$locParts = explode('_', $actionInfo['origin']);
		$actionInfo['origin'] = decToB26($locParts[0]).$locParts[1];
		$locParts = explode('_', $actionInfo['destination']);
		$actionInfo['destination'] = decToB26($locParts[0]).$locParts[1];
		if ($actionInfo['action'] == 'moved') echo "\t\t\t\t\t<p><a href=\"".SITEROOT."/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> moved <strong>{$actionInfo['name']}</strong> ({$actionInfo['label']}) from ".(strlen($actionInfo['origin'])?strtoupper($actionInfo['origin']):'Box')." to ".(strlen($actionInfo['destination'])?strtoupper($actionInfo['destination']):'Box')."</p>\n";
		elseif ($actionInfo['action'] == 'created') echo "\t\t\t\t\t<p><a href=\"".SITEROOT."/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> created <strong>{$actionInfo['name']}</strong> ({$actionInfo['label']})</p>\n";
		elseif ($actionInfo['action'] == 'edited') echo "\t\t\t\t\t<p><a href=\"".SITEROOT."/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> edited <strong>{$actionInfo['name']}</strong> ({$actionInfo['label']})</p>\n";
		elseif ($actionInfo['action'] == 'deleted') echo "\t\t\t\t\t<p><a href=\"".SITEROOT."/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> deleted <strong>{$actionInfo['name']}</strong> ({$actionInfo['label']})</p>\n";
	}
?>
					</div>
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
<? require_once(FILEROOT.'/footer.php'); ?>