	<link href="/styles/reset.css?v=<?=getStyleVersion('/styles/reset.css')?>" rel="stylesheet">
	<link href="/styles/gamersPlane.css?v=<?=getStyleVersion('/styles/gamersPlane.css')?>" rel="stylesheet">
	<link href="/javascript/markItUp/skins/gp/style.css?v=<?=getStyleVersion('/javascript/markItUp/skins/gp/style.css')?>" rel="stylesheet">
	<link href="/javascript/markItUp/sets/bbcode/style.css?v=<?=getStyleVersion('/javascript/markItUp/sets/bbcode/style.css')?>" rel="stylesheet">
<?
	if (file_exists(FILEROOT.'/styles/'.$pathAction.'.css')) {
?>
	<link href="/styles/<?=$pathAction?>.css?v=<?=getStyleVersion('/styles/'.$pathAction.'.css')?>" rel="stylesheet">
<?
	}
	if ($pathOptions[1] == 'maps') {
?>
	<link href="/styles/maps.css?v=<?=getStyleVersion('/styles/maps.css')?>" rel="stylesheet">
<? } ?>
<? if ($pathAction == 'characters' && file_exists(FILEROOT."/styles/characters/{$pathOptions[0]}.css")) { ?>
	<link href="/styles/characters/<?=$pathOptions[0]?>.css?v=<?=getStyleVersion('/styles/characters/'.$pathOptions[0].'.css')?>" rel="stylesheet">
<? } ?>
	<link href="/styles/colorbox.css?v=<?=getStyleVersion('/styles/colorbox.css')?>" rel="stylesheet">

<? // if ($mobileDetect->isMobile()) { ?>
<!--	<link href="/styles/mobile.css?v=<?=getStyleVersion('/styles/mobile.css')?>" rel="stylesheet"> -->
<? // } ?>

	<noscript><link href="/styles/noJS.css?v=<?=getStyleVersion('/styles/noJS.css')?>" rel="stylesheet"></noscript>
	<!--[if IE]>
	<link href="/styles/gamersPlane_ie.css?v=<?=getStyleVersion('/styles/gamersPlane_ie.css')?>" rel="stylesheet">
	<![endif]-->
	<!--[if IE 7]>
	<link href="/styles/gamersPlane_ie7.css?v=<?=getStyleVersion('/styles/gamersPlane_ie7.css')?>" rel="stylesheet">
	<![endif]-->
	<!--[if IE 8]>
	<link href="/styles/gamersPlane_ie8.css?v=<?=getStyleVersion('/styles/gamersPlane_ie8.css')?>" rel="stylesheet">
	<![endif]-->

<?=getUserTheme()?>