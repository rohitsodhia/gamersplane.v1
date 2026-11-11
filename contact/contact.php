<?
	$captchaEnabled = getenv('ENVIRONMENT') !== 'dev';
	if ($captchaEnabled) { $addExternalJSFiles[] = 'https://www.google.com/recaptcha/api.js?render=explicit'; }
	require_once(FILEROOT.'/header.php');
?>
		<h1 class="headerbar">Contact Us</h1>
		<div class="animationFrame">
			<form ng-show="dispForm" ng-submit="send()" class="slideDown">
				<p ng-if="!loggedIn">All fields except "username" are required.</p>
				<p ng-if="loggedIn">All fields are required.</p>
				<div class="tr">
					<input type="text" ng-model="form.name" maxlength="50" placeholder="Name" ng-class="{ 'error': errors.empty.indexOf('name') > -1 }">
				</div>
				<div ng-if="!loggedIn" class="tr">
					<input type="text" ng-model="form.username" maxlength="50" placeholder="Username">
				</div>
				<div class="tr">
					<input type="text" ng-model="form.email" maxlength="100" placeholder="Email" ng-class="{ 'error': errors.empty.indexOf('email') > -1 }">
				</div>
				<div class="tr">
					<input type="text" ng-model="form.subject" maxlength="100" placeholder="Subject" ng-class="{ 'error': errors.empty.indexOf('subject') > -1 }">
				</div>
				<div class="tr">
					<textarea ng-model="form.comment" placeholder="Comments" ng-class="{ 'error': errors.empty.indexOf('comment') > -1 }"></textarea>
				</div>
	<?	if ($captchaEnabled) { ?>
				<div class="tr" ng-show="loggedIn === false">
					<div id="contactCaptcha" data-sitekey="<?=htmlspecialchars((string) getenv('RECAPTCHA_SITE_KEY'))?>"></div>
					<p ng-show="errors.captcha" style="color: #c00;">Please confirm you're not a robot, then submit again.</p>
				</div>
	<?	} ?>
				<div class="alignCenter"><button type="submit" class="fancyButton">Submit</button></div>
			</form>
			<div ng-hide="dispForm" class="slideUp">
				<p class="firstLine"><strong>Thanks for sending us your thoughts!</strong></p>
				<p>I can't promise you I'll respond, but I'll be sure to take whatever you say into due account.</p>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>