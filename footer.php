<?php	if (!MODAL) { ?>
	</div>
</div></div>
<footer class="clearfix<?=$fixedGameMenu ? ' withFixedMenu' : ''?><?=isset($contentClasses) && array_search('fullWidthBody', $contentClasses) >= 0 ? ' fullWidthBody' : ''?>"><div class="bodyContainer">
	<ul>
		<li><a href="/tools/">Tools</a></li>
		<li><a href="/systems/">Systems</a></li>
		<li><a href="/characters/">Characters</a></li>
		<li><a href="/games/">Games</a></li>
		<li><a href="/forums/">Forums</a></li>
		<li><a href="/gamersList/">The Gamers</a></li>
		<li><a href="/links/">Links</a></li>
	</ul>
	<ul>
		<li><a href="/faqs/">FAQs</a></li>
		<li><a href="/about/">About GP</a></li>
		<li><a href="/contact/">Contact Us</a></li>
		<li class="non-mob-hide"><a href="/forums/rules/">Forum rules</a></li>
	</ul>
	<ul id="followLinks">
		<li><a id="fl_twitter" href="http://twitter.com/GamersPlane" target="_blank" title="Twitter"></a></li>
		<li><a id="fl_facebook" href="https://www.facebook.com/pages/Gamers-Plane/245904792107862" target="_blank" title="Facebook"></a></li>
		<!-- <li><a id="fl_stumbleupon" href="http://www.stumbleupon.com/submit?url=http://gamersplane.com" target="_blank" title="StumbleUpon"></a></li> -->
		<li><a id="fl_twitch" href="http://www.twitch.tv/gamersplane" target="_blank" title="Twitch"></a></li>
	</ul>

	<ul class="floatRight">
<?php
	$refLinks = $mysql->query("SELECT title, link FROM referralLinks ORDER BY `order` ASC");
	foreach ($refLinks as $link) {
?>
		<li><a href="<?=$link['link']?>" target="_blank"><?=$link['title']?> referral link</a></li>
<?php
	}
?>
		<li><form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="6VHQ2BP4AS7L6">
			<input type="image" src="/images/support_us.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form></li>
<?php		if ($currentUser->checkACP('any', false)) { ?>
		<li><a href="/acp/">ACP</a>
		</li>
<?php		} ?>
	</ul>
</div></footer>
<?php		if ($fixedGameMenu) { require(FILEROOT . '/fixedGameMenu.php'); } ?>
<?php	} else { ?>
</div>
<?php	} ?>

<?php	require_once(FILEROOT . '/javascript/js.php'); ?>

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
