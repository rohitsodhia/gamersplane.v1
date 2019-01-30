<script type="text/javascript">
	var API_HOST = 'https://<?=getenv('APP_API_URL')?>';
	var PAGINATE_PER_PAGE = <?=PAGINATE_PER_PAGE?>;
</script>
<script type="text/javascript" src="/javascript/html5shiv.js?v=<?=getJSVersion('/javascript/html5shiv.js')?>"></script>
<script type="text/javascript" src="/javascript/moment.js?v=<?=getJSVersion('/javascript/moment.js')?>"></script>
<script type="text/javascript" src="/javascript/jquery.min.js?v=<?=getJSVersion('/javascript/jquery.min.js')?>"></script>
<script type="text/javascript" src="/javascript/jquery-ui.min.js?v=<?=getJSVersion('/javascript/jquery-ui.min.js')?>"></script>
<script type="text/javascript" src="/javascript/jquery.colorbox-min.js?v=<?=getJSVersion('/javascript/jquery.colorbox-min.js')?>"></script>
<script type="text/javascript" src="/javascript/jquery.form.js?v=<?=getJSVersion('/javascript/jquery.form.js')?>"></script>
<script type="text/javascript" src="/javascript/angular.min.js?v=<?=getJSVersion('/javascript/angular.min.js')?>"></script>
<script type="text/javascript" src="/javascript/angular-route.min.js?v=<?=getJSVersion('/javascript/angular-route.min.js')?>"></script>
<script type="text/javascript" src="/javascript/angular-cookies.min.js?v=<?=getJSVersion('/javascript/angular-cookies.min.js')?>"></script>
<script type="text/javascript" src="/javascript/angular-sanitize.min.js?v=<?=getJSVersion('/javascript/angular-sanitize.min.js')?>"></script>
<script type="text/javascript" src="/javascript/angular-animate.min.js?v=<?=getJSVersion('/javascript/angular-animate.min.js')?>"></script>
<script type="text/javascript" src="/javascript/angular-file-upload-shim.min.js?v=<?=getJSVersion('/javascript/angular-file-upload-shim.min.js')?>"></script>
<script type="text/javascript" src="/javascript/angular-file-upload.min.js?v=<?=getJSVersion('/javascript/angular-file-upload.min.js')?>"></script>
<script type="text/javascript" src="/javascript/angular-moment.min.js?v=<?=getJSVersion('/javascript/angular-moment.min.js')?>"></script>
<script type="text/javascript" src="/node_modules/rx/dist/rx.all.min.js?v=<?=getJSVersion('/node_modules/rx/dist/rx.all.min.js')?>"></script>
<script type="text/javascript" src="/node_modules/rx-angular/dist/rx.angular.min.js?v=<?=getJSVersion('/node_modules/rx-angular/dist/rx.angular.min.js')?>"></script>
<script type="text/javascript" src="/javascript/combobox.angular.js?v=<?=getJSVersion('/javascript/combobox.angular.js')?>"></script>
<script type="text/javascript" src="/javascript/markItUp/jquery.markitup.js?v=<?=getJSVersion('/javascript/markItUp/jquery.markitup.js')?>"></script>
<script type="text/javascript" src="/javascript/markItUp/sets/bbcode/set.js?v=<?=getJSVersion('/javascript/markItUp/sets/bbcode/set.js')?>"></script>
<?	if ($pathAction == 'chat') { ?>
<script type="text/javascript" src="/javascript/jquery.scrollTo-min.js?v=<?=getJSVersion('/javascript/jquery.scrollTo-min.js')?>"></script>
<?	} elseif ($pathAction == 'tools') { ?>
<script type="text/javascript" src="/javascript/jquery.color.js?v=<?=getJSVersion('/javascript/jquery.color.js')?>"></script>
<script type="text/javascript" src="/javascript/jquery.qtip-1.0.0-rc3.min.js?v=<?=getJSVersion('/javascript/jquery.qtip-1.0.0-rc3.min.js')?>"></script>
<?	} ?>
<script type="text/javascript" src="/javascript/gamersplane.variables.js?v=<?=getJSVersion('/javascript/gamersplane.variables.js')?>"></script>
<script type="text/javascript" src="/javascript/gamersplane.functions.js?v=<?=getJSVersion('/javascript/gamersplane.functions.js')?>"></script>
<script type="text/javascript" src="/javascript/gamersplane.plugins.js?v=<?=getJSVersion('/javascript/gamersplane.plugins.js')?>"></script>
<script type="text/javascript" src="/javascript/gamersplane.js?v=<?=getJSVersion('/javascript/gamersplane.js')?>"></script>
<?	if (file_exists(FILEROOT.'/javascript/'.$pathAction.'/_section.js')) { ?>
<script type="text/javascript" src="/javascript/<?=$pathAction?>/_section.js?v=<?=getJSVersion('/javascript/'.$pathAction.'/_section.js')?>"></script>
<?	} ?>
<?	if (file_exists(FILEROOT.'/javascript/'.substr($requireLoc, 0, -4).'.js')) { ?>
<script type="text/javascript" src="/javascript/<?=substr($requireLoc, 0, -4)?>.js?v=<?=getJSVersion('/javascript/'.substr($requireLoc, 0, -4).'.js')?>"></script>
<?	} ?>
<?	if (($gameID || $pathAction == 'characters') && !isset($_GET['modal'])) { ?>
<script type="text/javascript" src="/javascript/tools/cards.js?v=<?=getJSVersion('/javascript/tools/cards.js')?>"></script>
<?	} ?>
<?	if (sizeof($addJSFiles)) { foreach ($addJSFiles as $file) { ?>
<script type="text/javascript" src="/javascript/<?=$file?>?v=<?=getJSVersion('/javascript/'.$file)?>"></script>
<?	} } ?>
<?	if (sizeof($addExternalJSFiles)) { foreach ($addExternalJSFiles as $file) { ?>
<script src="<?=$file?>"></script>
<?	} } ?>
