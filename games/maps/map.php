<?
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$mapID = intval($pathOptions[2]);
	$playerCheck = $mysql->query("SELECT p.isGM FROM maps m, players p WHERE m.gameID = $gameID AND m.gameID = p.gameID AND p.userID = $userID AND m.mapID = $mapID");
	if (!$playerCheck->rowCount()) { header('Location: /403'); exit; }
	else $isGM = $playerCheck->fetchColumn();
	$mapInfo = $mysql->query('SELECT m.gameID, m.name, m.cols, m.rows, m.info, g.title, g.systemID, s.fullName FROM maps m, games g, systems s WHERE g.systemID = s.systemID AND m.gameID = g.gameID AND m.mapID = '.$mapID);
	$mapInfo = $mapInfo->fetch();
	
	$mapIcons = $mysql->query("SELECT iconID, label, name, description, color, location FROM maps_icons WHERE mapID = $mapID AND deleted = 0");
	$iconsInBox = array();
	$iconsOnMap = array();
	foreach ($mapIcons as $info) {
		if ($info['location'] == '') $iconsInBox[] = $info;
		else {
			$locParts = explode('_', $info['location']);
			$iconsOnMap[$locParts[0]][$locParts[1]] = $info;
		}
	}
	$mapData = $mysql->query("SELECT col, row, data FROM mapData WHERE mapID = $mapID");
	$bgData = array();
	foreach ($mapData as $dataPiece) $bgData[$dataPiece['col']][$dataPiece['row']] = $dataPiece['data'];
	$mapSize['width'] = $mapInfo['cols'] * 40;
	$mapSize['height'] = $mapInfo['rows'] * 40;
	$maxMapWindow['width'] = $mapInfo['cols'] >= 15?600:$mapInfo['cols'] * 40;
	$maxMapWindow['height'] = $mapInfo['rows'] >= 15?600:$mapInfo['rows'] * 40;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar"><?=printReady($mapInfo['name'])?></h1>

		<div class="clearfix">
			<div id="mapSidebar" style="height: <?=$maxMapWindow['height'] - 32?>px;">
				<div id="mapControls">
					<img src="/images/maps/mapControls.png">
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
							<select class="prettySelect">
								<option value="info">Info</option>
								<option value="box">Box</option>
								<option value="history">History</option>
<? if ($isGM) { ?>
								<option value="mapOptions">Map Options</option>
<? } ?>
							</select>
						</div>
						<div class="wing dlWing"></div>
						<div class="wing drWing"></div>
					</div></div>
					<div id="sidebarIconHolder"></div>
					<div id="mapSidebar_contentContainer" style="height: <?=$maxMapWindow['height'] - 92?>px;">
						<div id="mapSidebar_content_info">
							<p><strong>Game:</strong> <?=$mapInfo['title']?> (<?=$mapInfo['fullName']?>)</p>
							<p><strong>Info:</strong> <span id="infoSpan"><? if (strlen($mapInfo['info'])) echo printReady($mapInfo['info']); elseif ($isGM) echo 'No info yet.'; ?></span> <sup><a id="infoEdit" href="/games/<?=$gameID?>/maps/<?=$mapID?>/editInfo">[ Edit ]</a></sup></p>
							<p class="reminder">Remember: you can see each icon's label by holding your mouse over it.</p>
						</div>
<? if ($isGM) { ?>
						<div id="mapSidebar_content_box">
							<div id="iconBox" class="clearfix">
<?		foreach ($iconsInBox as $icon) { $icon = new Icon($icon['iconID']); echo $icon; } ?>
							</div>
							<hr>
							
							<a id="addIcon" href="">Add Icon</a>
							
							<form id="iconForm" method="post" action="/games/process/maps/icons">
								<input id="iconID" type="hidden" name="iconID">
								<input type="hidden" name="mapID" value="<?=$mapID?>">
								<div class="tr">
									<label class="textLabel">Color:</label>
									<div><select id="iconColor" name="color">
										<option value="0000BB">Blue</option>
										<option value="00BB00">Green</option>
										<option value="555555">Grey</option>
										<option value="DD7722">Orange</option>
										<option value="BB0000">Red</option>
										<option value="FFFFFF">White</option>
									</select></div>
								</div>
								<div class="tr">
									<label class="textLabel">Label:</label>
									<div><input id="iconLabel" type="text" name="label" maxlength="2" class="alignCenter"></div>
								</div>
								<p class="small">The label must be 1 or 2 characters in length.</p>
								<div class="tr">
									<label class="textLabel">Name:</label>
									<div><input id="iconName" type="text" name="name"></div>
								</div>
								<div class="tr editDiv alignCenter">
									<button type="submit" name="save" class="fancyButton smallButton">Save</button>
									<button type="submit" name="delete" class="fancyButton smallButton">Delete</button>
								</div>
								<div class="tr addDiv alignCenter"><button type="submit" name="submit" class="fancyButton smallButton">Save</button></div>
							</form>
						</div>
<? } ?>
						<div id="mapSidebar_content_history">
<?
	$iconActions = $mysql->query("SELECT ih.iconID, i.label, i.name, i.mapID, ih.enactedBy, ih.enactedOn, u.username, ih.action, ih.origin, ih.destination FROM maps_iconHistory ih, maps_icons i, users u WHERE ih.iconID = i.iconID AND ih.enactedBy = u.userID ".($isGM?'':"AND ih.action = 'moved' ")."AND ih.mapID = $mapID ORDER BY ih.enactedOn DESC");
	foreach ($iconActions as $actionInfo) echo Icon::displayHistory($actionInfo);
?>
						</div>
<? if ($isGM) { ?>
						<div id="mapSidebar_content_mapOptions">
							<div id="staticMapOptions">
								<div id="addCR">
									<a href="">Add Row/Column</a>
									<form method="post" action="/games/process/maps/addCR">
										<input id="mapID" type="hidden" name="mapID" value="<?=$mapID?>">
										<div class="textLabel">Add</div>
										<div class="psWrapper"><select id="addType" name="addType"><option value="c"<?=$_SESSION['lastSet'] == 'c'?' selected="selected"':''?>>column</option><option value="r"<?=$_SESSION['lastSet'] == 'r'?' selected="selected"':''?>>row</option></select></div>
										<div class="psWrapper"><select id="addLoc" name="addLoc"><option value="a">after</option><option value="b">before</option></select></div>
										<div class="psWrapper"><select id="addPos" name="addPos"></select></div>
										<div class="alignCenter"><button id="addCol" type="submit" name="addCR" class="fancyButton">Add</button></div>
									</form>
								</div>
<? unset($_SESSION['lastSet']); ?>
							</div>
							<div id="tileOptions" class="clearfix">
								<h3>Tiles</h3>
								<div class="clearfix">
<?
	$count = 1;
	$first = TRUE;
	$tiles = array('Grass' => '338833', 'Forest' => '004400', 'Water' => '3399FF', 'Deep Water' => '3333FF', 'Desert' => 'CC9966', 'Road' => 'AAAAAA', 'Building' => '555555');
	foreach ($tiles as $tileName => $color) {
		if ($count % 3 == 1 && !$first) {
			echo "								</div>\n";
			echo "								<div class=\"clearfix\">\n";
		}
		if ($first) $first = FALSE;
?>
									<div class="colorOption">
										<div class="color" style="background-color: #<?=$color?>"></div>
										<div class="name"><?=$tileName?></div>
									</div>
<?
		$count++;
	}
?>
								</div>
								<p><a id="selectAll" href="">Select All</a></p>
								<p><a id="unselectAll" href="">Unselect All</a></p>
								<p><a id="selectInverse" href="">Select Inverse</a></p>
							</div>
							<div class="alignCenter"><button id="saveMap" class="fancyButton disabled">Save</button></div>
						</div>
<? } ?>
					</div>
				</div>
			</div>
			
			<div id="mapContainer" style="width: <?=$maxMapWindow['width'] + 40?>px; height: <?=$maxMapWindow['height'] + 40?>px">
				<div id="colHeaders" style="width: <?=$maxMapWindow['width']?>px;"><div style="width: <?=$mapSize['width']?>px;">
<?
	$curCol = 'a';
	for ($cCount = 1; $cCount <= $mapInfo['cols']; $cCount++) {
		echo "\t\t\t\t\t<div class=\"cHeader cHeaderMin col_$cCount\">\n";
		echo "\t\t\t\t\t\t<a href=\"\">".$curCol++."</a>\n";
		echo "\t\t\t\t\t\t<a href=\"/tools/process/maps/removeCR/$mapID/$cCount\" class=\"removeCol\">-</a>\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div></div>
				<div id="rowHeaders" style="height: <?=$maxMapWindow['height']?>px;"><div style="height: <?=$mapSize['height']?>px;">
<?
	for ($rCount = 1; $rCount <= $mapInfo['rows']; $rCount++) {
		echo "\t\t\t\t\t<div class=\"rHeader rHeaderMin row_$rCount\">\n";
		echo "\t\t\t\t\t\t<a href=\"\">".$rCount."</a>\n";
		echo "\t\t\t\t\t\t<a href=\"/tools/process/maps/removeCR/$mapID/$rCount\" class=\"removeCol\">-</a>\n";
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
		for ($cCount = 1; $cCount <= $mapInfo['cols']; $cCount++) {
			echo "\t\t\t\t\t\t<div id=\"{$cCount}_{$rCount}\" class=\"mapTile col_$cCount row_$rCount\"".(isset($bgData[$cCount][$rCount])?' style="background-color: '.$bgData[$cCount][$rCount].';"':'').">\n";
			if (isset($iconsOnMap[$cCount][$rCount])) { $icon = new Icon($iconsOnMap[$cCount][$rCount]['iconID']); echo $icon; }
			echo "</div>\n";
		}
	}
?>
					</div>
				</div>
			</div>
		</div>

		<div id="iconContextMenu"><ul>
			<li><a id="icm_edit" href="">Edit</a></li>
			<li class="boxHide"><a id="icm_stb" href="">Send to box</a></li>
		</ul></div>
<? require_once(FILEROOT.'/footer.php'); ?>