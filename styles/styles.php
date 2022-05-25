	<link href="/styles/reset.css?v=<?=getStyleVersion('/styles/reset.css')?>" rel="stylesheet">
	<link href="/styles/rpgawesome/style.css?v=<?=getStyleVersion('/styles/rpgawesome/style.css')?>" rel="stylesheet">
	<link href="/styles/gamersPlane.css?v=<?=getStyleVersion('/styles/gamersPlane.css')?>" rel="stylesheet">
	<link href="/styles/markItUp/skins/gp/style.css?v=<?=getStyleVersion('/styles/markItUp/skins/gp/style.css')?>" rel="stylesheet">

<?	if($characterMarkitUp) {?>
	<link href="/styles/markItUp/sets/bbcode/character-style.css?v=<?=getStyleVersion('/styles/markItUp/sets/bbcode/character-style.css')?>" rel="stylesheet">
<?	} else {?>
	<link href="/styles/markItUp/sets/bbcode/style.css?v=<?=getStyleVersion('/styles/markItUp/sets/bbcode/style.css')?>" rel="stylesheet">
<?	} ?>
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

<?if($responsivePage){?>
	<link href="/styles/responsive.css?v=<?=getStyleVersion('/styles/responsive.css')?>" rel="stylesheet">
<?}else{?>
	<link href="/styles/nonResponsive.css?v=<?=getStyleVersion('/styles/nonResponsive.css')?>" rel="stylesheet">
<?}?>

<?	if ($addExternalCSSFiles && !empty($addExternalCSSFiles)) { foreach ($addExternalCSSFiles as $file) { ?>
	<link href="/styles/<?=$file?>.css?v=<?=getStyleVersion('/styles/'.$file.'.css')?>" rel="stylesheet">
<?	} } ?>

	<link href="/javascript/leaflet/leaflet.css?v=<?=getStyleVersion('/javascript/leaflet/leaflet')?>" rel="stylesheet">

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

<?=getUserThemeCss()?>