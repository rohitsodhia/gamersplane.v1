<script type="text/javascript" src="<?=SITEROOT?>/javascript/html5shiv.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/variables.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.min.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.form.js"></script>
<? if ($action == 'register') { ?>
<script type="text/javascript" src="<?=SITEROOT?>/register/javascript/register.js"></script>
<? } elseif (in_array($action, array('forums', 'pms'))) { ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/markItUp/jquery.markitup.pack.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/markItUp/sets/bbcode/set.js"></script>
<? } elseif ($action == 'chat') { ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.scrollTo-min.js"></script>
<? } elseif ($action == 'tools') { ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.color.js"></script>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/jquery.qtip-1.0.0-rc3.min.js"></script>
<? } ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/gamersplane.js"></script>
<? if (file_exists(FILEROOT.'/javascript/'.$action.'/_section.js')) { ?>
<script type="text/javascript" src="<?=SITEROOT.'/javascript/'.$action?>/_section.js"></script>
<? } ?>
<? if (file_exists(FILEROOT.'/javascript/'.substr($requireLoc, 0, -4).'.js')) { ?>
<script type="text/javascript" src="<?=SITEROOT.'/javascript/'.substr($requireLoc, 0, -4)?>.js"></script>
<? } ?>
<? if (($gameID || $action == 'characters') && !isset($_GET['modal'])) { ?>
<script type="text/javascript" src="<?=SITEROOT?>/javascript/tools/cards.js"></script>
<? } ?>