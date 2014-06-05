<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mapID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT maps.gameID FROM maps INNER JOIN gms USING (gameID) WHERE gms.userID = $userID AND maps.mapID = $mapID");
	if (!$gmCheck->rowCount()) { header('Location: /403'); exit; }
	$gameInfo = $gmCheck->fetch();
	$mapInfo = $mysql->query('SELECT gameID, name, columns, rows FROM maps WHERE mapID = '.$mapID);
	$mapInfo = $mapInfo->fetch();
	$mapData = $mysql->query("SELECT `column`, `row`, data FROM mapData WHERE mapID = $mapID");
	$bgData = array();
	while ($dataPiece = $mapData->fetch()) $bgData[$dataPiece['column']][$dataPiece['row']] = $dataPiece['data'];
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if ($_GET['exceededSize'] == 1) { ?>
		<div class="alertBox_error">
			<p>The map cannot exceed 20x20.</p>
		</div>
<? } ?>
		<noscript><div class="alertBox_error">
			<p>This page requires javascript to run.</p>
			<p>Please make sure your browser is running javascript to use the GP mapping system.</p>
		</div></noscript>
		
		<h1>Maps</h1>
		<h2><?=printReady($mapInfo['name'])?></h2>
		
		<div id="mapOptions">
			<br class="clear">
		</div>
		
		<div class="clearfix">
			<div id="mapSidebar" style="height: <?=$mapInfo['rows'] * 40 > 570?570:$mapInfo['rows'] * 40?>px;">
				<div id="mapControls">
					<img src="/images/mapControls.png">
					<a id="mapControls_up_top" href="" class="mapControls_up">&nbsp;</a>
					<a id="mapControls_up_body" href="" class="mapControls_up">&nbsp;</a>
					<a id="mapControls_right_top" href="" class="mapControls_right">&nbsp;</a>
					<a id="mapControls_right_body" href="" class="mapControls_right">&nbsp;</a>
					<a id="mapControls_down_top" href="" class="mapControls_down">&nbsp;</a>
					<a id="mapControls_down_body" href="" class="mapControls_down">&nbsp;</a>
					<a id="mapControls_left_top" href="" class="mapControls_left">&nbsp;</a>
					<a id="mapControls_left_body" href="" class="mapControls_left">&nbsp;</a>
				</div>
			</div>
			
			<div id="mapContainer" style="width: <?=$mapInfo['columns'] * 40 > 600?640:$mapInfo['columns'] * 40 + 40?>px; height: <?=$mapInfo['rows'] * 40 > 600?640:$mapInfo['columns'] * 40 + 40?>px">
				<div id="colHeaders" style="width: <?=$mapInfo['columns'] * 40 > 600?600:$mapInfo['columns'] * 40?>px;"><div style="width: <?=$mapInfo['columns'] * 40?>px;">
<?
	$curCol = 'a';
	for ($cCount = 1; $cCount <= $mapInfo['columns']; $cCount++) {
		echo "\t\t\t\t\t<div class=\"cHeader cHeaderMin col_$cCount\">\n";
		echo "\t\t\t\t\t\t<a href=\"\">".$curCol++."</a>\n";
		echo "\t\t\t\t\t\t<a href=\"/tools/process/maps/removeCR/$mapID/$cCount\" class=\"removeCol\">-</a>\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div></div>
				<div id="rowHeaders" style="height: <?=$mapInfo['rows'] * 40 > 600?600:$mapInfo['rows'] * 40?>px;"><div style="height: <?=$mapInfo['rows'] * 40?>px;">
<?
	for ($rCount = 1; $rCount <= $mapInfo['rows']; $rCount++) {
		echo "\t\t\t\t\t<div class=\"rHeader rHeaderMin row_$rCount\">\n";
		echo "\t\t\t\t\t\t<a href=\"\">".$rCount."</a>\n";
		echo "\t\t\t\t\t\t<a href=\"/tools/process/maps/removeCR/$mapID/$rCount\" class=\"removeCol\">-</a>\n";
		echo "\t\t\t\t\t</div>\n";
	}
?>
				</div></div>
				<div id="mapWindow" style="width: <?=$mapInfo['columns'] * 40 > 600?600:$mapInfo['columns'] * 40?>px; height: <?=$mapInfo['rows'] * 40 > 600?600:$mapInfo['columns'] * 40?>px">
					<div id="map" style="width: <?=$mapInfo['columns'] * 40?>px; height: <?=$mapInfo['rows'] * 40?>px">
<?
	$count = 0;
	$bg = '';
	for ($rCount = 1; $rCount <= $mapInfo['rows']; $rCount++) {
		for ($cCount = 1; $cCount <= $mapInfo['columns']; $cCount++) {
			echo "\t\t\t\t\t\t<div id=\"{$cCount}_{$rCount}\" class=\"mapTile col_$cCount row_$rCount\"".(isset($bgData[$cCount][$rCount])?' style="background-color: '.$bgData[$cCount][$rCount].';"':'')."></div>\n";
		}
	}
?>
					</div>
				</div>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>