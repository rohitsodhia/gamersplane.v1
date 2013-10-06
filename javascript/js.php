<script type="text/javascript" src="<?=SITEROOT?>/javascript/html5shiv.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.min.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.form.js"></script>
<? if (in_array($pathAction, array('forums', 'pms'))) { ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/markItUp/jquery.markitup.pack.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/markItUp/sets/bbcode/set.js"></script>
<? } elseif ($pathAction == 'chat') { ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.scrollTo-min.js"></script>
<? } elseif ($pathAction == 'tools') { ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.color.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.qtip-1.0.0-rc3.min.js"></script>
<? } ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/gamersplane.variables.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/gamersplane.functions.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/gamersplane.plugins.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/gamersplane.js"></script>
<? if ($pathAction == 'register') { ?>
<script type="text/javascript" src="<?=SITEROOT?>/register/javascript/register.js"></script>
<? } ?>
<? if (file_exists(FILEROOT.'/javascript/'.$pathAction.'/_section.js')) { ?>
<script type="text/javascript" src="<?=SITEROOT.'/javascript/'.$pathAction?>/_section.js"></script>
<? } ?>
<? if (file_exists(FILEROOT.'/javascript/'.substr($requireLoc, 0, -4).'.js')) { ?>
<script type="text/javascript" src="<?=SITEROOT.'/javascript/'.substr($requireLoc, 0, -4)?>.js"></script>
<? } ?>
<? if (($gameID || $pathAction == 'characters') && !isset($_GET['modal'])) { ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/tools/cards.js"></script>
<? } ?>