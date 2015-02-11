<?	if (!MODAL) { ?>
	</div>
</div></div>
<footer class="clearfix<?=$fixedGameMenu?' withFixedMenu':''?>"><div class="bodyContainer">
	<a href="/contact/">Contact Us</a>
	<div class="floatRight">
		<p><a href="http://amazon.gamersplane.com" target="_blank">Amazon referral link</a></p>
		<p><a href="http://dtrpg.gamersplane.com" target="_blank">DTRPG referral link</a></p>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="6VHQ2BP4AS7L6">
			<input type="image" src="/images/support_us.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
<?		if ($currentUser->checkACP()) { ?>
		<div>
			<a href="/acp/">ACP</a>
		</div>
<?		} ?>
	</div>
</div></footer>
<?		if ($fixedGameMenu) require(FILEROOT.'/fixedGameMenu.php'); ?>
<?	} else { ?>
</div>
<?	} ?>

<?	require_once(FILEROOT.'/javascript/js.php'); ?>

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-46259019-1', 'gamersplane.com');
	ga('send', 'pageview');
</script>
</body>
</html>