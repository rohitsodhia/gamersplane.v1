<script type="text/javascript">
	var API_HOST = 'http://api.<?=$_SERVER['HTTP_HOST']?>';
</script>
<script type="text/javascript" src="/javascript/html5shiv.js"></script>
<script type="text/javascript" src="/javascript/moment.js"></script>
<script type="text/javascript" src="/javascript/jquery.min.js"></script>
<script type="text/javascript" src="/javascript/jquery-ui.min.js"></script>
<script type="text/javascript" src="/javascript/jquery.colorbox-min.js"></script>
<script type="text/javascript" src="/javascript/jquery.form.js"></script>
<script type="text/javascript" src="/javascript/angular.min.js"></script>
<script type="text/javascript" src="/javascript/angular-route.min.js"></script>
<script type="text/javascript" src="/javascript/angular-cookies.min.js"></script>
<?	if (in_array($pathAction, array('forums', 'pms'))) { ?>
<script type="text/javascript" src="/javascript/markItUp/jquery.markitup.js"></script>
<script type="text/javascript" src="/javascript/markItUp/sets/bbcode/set.js"></script>
<?	} elseif ($pathAction == 'chat') { ?>
<script type="text/javascript" src="/javascript/jquery.scrollTo-min.js"></script>
<?	} elseif ($pathAction == 'tools') { ?>
<script type="text/javascript" src="/javascript/jquery.color.js"></script>
<script type="text/javascript" src="/javascript/jquery.qtip-1.0.0-rc3.min.js"></script>
<?	} ?>
<script type="text/javascript" src="/javascript/gamersplane.variables.js"></script>
<script type="text/javascript" src="/javascript/gamersplane.functions.js"></script>
<script type="text/javascript" src="/javascript/gamersplane.plugins.js"></script>
<script type="text/javascript" src="/javascript/gamersplane.js"></script>
<?	if ($pathAction == 'register') { ?>
<script type="text/javascript" src="/register/javascript/register.js"></script>
<?	} ?>
<?	if (file_exists(FILEROOT.'/javascript/'.$pathAction.'/_section.js')) { ?>
<script type="text/javascript" src="/javascript/<?=$pathAction?>/_section.js"></script>
<?	} ?>
<?	if (file_exists(FILEROOT.'/javascript/'.substr($requireLoc, 0, -4).'.js')) { ?>
<script type="text/javascript" src="/javascript/<?=substr($requireLoc, 0, -4)?>.js"></script>
<?	} ?>
<?	if (($gameID || $pathAction == 'characters') && !isset($_GET['modal'])) { ?>
<script type="text/javascript" src="/javascript/tools/cards.js"></script>
<?	} ?>
<?	if (sizeof($addJSFiles)) { foreach ($addJSFiles as $file) { ?>
<script type="text/javascript" src="/javascript/<?=$file?>"></script>
<?	} } ?>