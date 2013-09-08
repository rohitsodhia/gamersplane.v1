	<link href="<?=SITEROOT?>/styles/reset.css" rel="stylesheet">
	<link href="<?=SITEROOT?>/styles/gamersPlane.css" rel="stylesheet">
<? if (in_array($action, array('forums', 'pms'))) { ?>
	<link href="<?=SITEROOT?>/javascript/markItUp/skins/gp/style.css" rel="stylesheet">
	<link href="<?=SITEROOT?>/javascript/markItUp/sets/bbcode/style.css" rel="stylesheet">
<?
	}
	if ($action == 'forums') {
?>
	<link href="<?=SITEROOT?>/styles/forums.css" rel="stylesheet">
<? } elseif ($action == 'tools') { ?>
	<link href="<?=SITEROOT?>/styles/tools.css" rel="stylesheet">
<? } elseif ($action == 'games') { ?>
	<link href="<?=SITEROOT?>/styles/games.css" rel="stylesheet">
<? } elseif ($action == 'characters') { ?>
	<link href="<?=SITEROOT?>/styles/characters.css" rel="stylesheet">
<?
//	foreach (glob('styles/characters/*.css') as $file) echo "\t<link href=\"".SITEROOT."/$file\" rel=\"stylesheet\">\n";
?>
	<link href="<?=SITEROOT?>/styles/characters/<?=$pathOptions[0]?>.css" rel="stylesheet">
<? } elseif ($action == 'chat') { ?>
	<link href="<?=SITEROOT?>/styles/chat.css" rel="stylesheet">
<? } elseif ($action == 'user' || $action == 'gamersList' || $action == 'ucp') { ?>
	<link href="<?=SITEROOT?>/styles/users.css" rel="stylesheet">
<? } ?>
	<link href="<?=SITEROOT?>/styles/colorbox.css" rel="stylesheet">
	
<? if ($mobileDetect->isMobile()) { ?>
	<link href="<?=SITEROOT?>/styles/mobile.css" rel="stylesheet">
<? } ?>
	
	<noscript><link href="<?=SITEROOT?>/styles/noJS.css" rel="stylesheet"></noscript>
	<!--[if IE]>
	<link href="<?=SITEROOT?>/styles/gamersPlane_ie.css" rel="stylesheet">
	<![endif]-->	
	<!--[if IE 7]>
	<link href="<?=SITEROOT?>/styles/gamersPlane_ie7.css" rel="stylesheet">
	<![endif]-->	
	<!--[if IE 8]>
	<link href="<?=SITEROOT?>/styles/gamersPlane_ie8.css" rel="stylesheet">
	<![endif]-->	
