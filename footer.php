<? if (!MODAL) { ?>
	</div>
</div></div>
<footer<?=$dispatchInfo['fixedGameMenu']?' class="withFixedMenu"':''?>><div class="bodyContainer">
	<a href="<?=SITEROOT?>/contact">Contact Us</a>
</div></footer>
<? if (FIXED_GAME_MENU) require(FILEROOT.'/fixedGameMenu.php'); ?>
<? } else { ?>
</div>
<? } ?>
</body>

<? if (SITEROOT == '') { ?>
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-5279175-8']);
	_gaq.push(['_trackPageview']);
	
	(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
<? } ?>
</html>