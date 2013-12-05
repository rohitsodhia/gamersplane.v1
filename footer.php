<? if (!MODAL) { ?>
	</div>
</div></div>
<footer<?=$fixedMenu?' class="withFixedMenu"':''?>><div class="bodyContainer">
	<a href="<?=SITEROOT?>/contact">Contact Us</a>
</div></footer>
<? if (FIXED_GAME_MENU) require(FILEROOT.'/fixedGameMenu.php'); ?>
<? } else { ?>
</div>
<? } ?>

<? require_once(FILEROOT.'/javascript/js.php'); ?>

<? if (SITEROOT == '') { ?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-46238092-1', 'gamersplane.com');
  ga('send', 'pageview');

</script>
<? } ?>
</body>
</html>