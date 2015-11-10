<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Contact Us</h1>
		<div class="animationFrame">
			<form ng-show="dispForm" ng-submit="send()" class="slideDown">
				<p>All fields except "username" are required.</p>
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
				<div class="alignCenter"><button type="submit" class="fancyButton" skew-element>Submit</button></div>
			</form>
			<div ng-hide="dispForm" class="slideUp">
				<p class="firstLine"><strong>Thanks for sending us your thoughts!</strong></p>
				<p>I can't promise you I'll respond, but I'll be sure to take whatever you say into due account.</p>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>