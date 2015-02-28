<?
	addPackage('pms');

	$box = $pathOptions[0] == 'outbox'?'outbox':'inbox';
	$boxManager = new PMBoxManager($box);
?>
<?	require_once(FILEROOT.'/header.php'); ?>
<?	if ($_GET['deleteSuc'] || $_GET['sent']) { ?>
		<div class="alertBox_success">
<?
	if ($_GET['deleteSuc']) { echo "\t\t\tPM successfully deleted.\n"; }
	if ($_GET['sent']) { echo "\t\t\tPM successfully sent.\n"; }
?>
		</div>
<?	} ?>
		<h1 class="headerbar">Private Messages - <?=ucwords($box)?></h1>
		
		<div id="controlsContainer" class="clearfix">
			<a id="newPM" href="/pms/send" class="fancyButton">New PM</a>
			<div id="controls" class="wingDiv sectionControls" data-ratio=".8">
				<div class="wingDivContent clearfix">
					<a href="/pms/" class="borderBox<?=$box == 'inbox'?' current':''?>">Inbox</a>
					<a href="/pms/outbox" class="borderBox<?=$box == 'outbox'?' current':''?>">Outbox</a>
				</div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</div>
		</div>
		<div id="pms">
			<div class="tr headerTR headerbar hbDark">
				<div class="delCol"></div>
				<div class="info">Message</div>
			</div>
<?	$boxManager->displayPMs($_GET['page']); ?>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>