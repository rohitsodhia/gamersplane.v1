<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mapID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT maps.gameID FROM maps INNER JOIN gms USING (gameID) WHERE gms.userID = $userID AND maps.mapID = $mapID");
	if (!$gmCheck->rowCount()) { header('Location: '.SITEROOT.'/403'); exit; }
	$gameInfo = $gmCheck->fetch();
	$mapInfo = $mysql->query('SELECT gameID, name, columns, rows FROM maps WHERE mapID = '.$mapID);
	$mapInfo = $mapInfo->fetch();
	$mapData = $mysql->query("SELECT `column`, `row`, data FROM mapData WHERE mapID = $mapID");
	$bgData = array();
	while ($dataPiece = $mapData->fetch()) $bgData[$dataPiece['column']][$dataPiece['row']] = $dataPiece['data'];
	$fixedMenu = TRUE;
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
			<div id="staticMapOptions">
				<div id="addCR"><form method="post" action="<?=SITEROOT?>/tools/process/maps/addCR">
					<input id="mapID" type="hidden" name="mapID" value="<?=$mapID?>">
					Add <select id="addType" name="addType"><option value="c"<?=$_SESSION['lastSet'] == 'c'?' selected="selected"':''?>>column</option><option value="r"<?=$_SESSION['lastSet'] == 'r'?' selected="selected"':''?>>row</option></select> 
					<select id="addLoc" name="addLoc"><option value="a">after</option><option value="b">before</option></select> <select id="addPos" name="addPos"></select> 
					<button id="addCol" type="submit" name="addCR" class="btn_add"></button>
				</form></div>
<? unset($_SESSION['lastSet']); ?>
				<button id="saveMap" class="btn_save_disabled"></button>
			</div>
			<div id="tileOptions">
				<div class="colorOption">
					<div class="color" style="background-color: #383"></div>
					<div class="name">Grass</div>
				</div>
				<div class="colorOption">
					<div class="color" style="background-color: #040"></div>
					<div class="name">Forest</div>
				</div>
				<div class="colorOption">
					<div class="color" style="background-color: #39F"></div>
					<div class="name">Water</div>
				</div>
				<div class="colorOption">
					<div class="color" style="background-color: #33F"></div>
					<div class="name">Deep Water</div>
				</div>
				<div class="colorOption">
					<div class="color" style="background-color: #C96"></div>
					<div class="name">Desert</div>
				</div>
				<div class="colorOption">
					<div class="color" style="background-color: #AAA"></div>
					<div class="name">Road</div>
				</div>
				<div class="colorOption">
					<div class="color" style="background-color: #555"></div>
					<div class="name">Building</div>
				</div>
				<br class="clearL">
				<a id="selectAll">Select All</a>
				<a id="unselectAll">Unselect All</a>
				<a id="selectInverse">Select Inverse</a>
			</div>
			<br class="clear">
		</div>
		
		<div class="clearfix">
			<div id="mapSidebar" style="height: <?=$mapInfo['rows'] * 40 > 570?570:$mapInfo['rows'] * 40?>px;">
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
			</div>
			
			<div id="mapContainer" style="width: <?=$mapInfo['columns'] * 40 > 600?640:$mapInfo['columns'] * 40 + 40?>px; height: <?=$mapInfo['rows'] * 40 > 600?640:$mapInfo['columns'] * 40 + 40?>px">
				<div id="colHeaders" style="width: <?=$mapInfo['columns'] * 40 > 600?600:$mapInfo['columns'] * 40?>px;"><div style="width: <?=$mapInfo['columns'] * 40?>px;">
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
				<div id="rowHeaders" style="height: <?=$mapInfo['rows'] * 40 > 600?600:$mapInfo['rows'] * 40?>px;"><div style="height: <?=$mapInfo['rows'] * 40?>px;">
<?
	for ($rCount = 1; $rCount <= $mapInfo['rows']; $rCount++) {
		echo "\t\t\t\t\t<div class=\"rHeader rHeaderMin row_$rCount\">\n";
		echo "\t\t\t\t\t\t<a href=\"\">".$rCount."</a>\n";
		echo "\t\t\t\t\t\t<a href=\"".SITEROOT."/tools/process/maps/removeCR/$mapID/$rCount\" class=\"removeCol\">-</a>\n";
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