	<link href="<?=SITEROOT?>/styles/reset.css" rel="stylesheet">
	<link href="<?=SITEROOT?>/styles/gamersPlane.css" rel="stylesheet">
<? if (in_array($pathAction, array('forums', 'pms'))) { ?>
	<link href="<?=SITEROOT?>/javascript/markItUp/skins/gp/style.css" rel="stylesheet">
	<link href="<?=SITEROOT?>/javascript/markItUp/sets/bbcode/style.css" rel="stylesheet">
<?
	}
	if (file_exists(FILEROOT.'/styles/'.$pathAction.'.css')) {
?>
	<link href="<?=SITEROOT?>/styles/<?=$pathAction?>.css" rel="stylesheet">
<?
	}
	if ($pathOptions[1] == 'maps') {
?>
	<link href="<?=SITEROOT?>/styles/maps.css" rel="stylesheet">
<? } ?>
<? if ($pathAction == 'characters' && file_exists(FILEROOT."/styles/characters/{$pathOptions[0]}.css")) { ?>
	<link href="<?=SITEROOT?>/styles/characters/<?=$pathOptions[0]?>.css" rel="stylesheet">
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
