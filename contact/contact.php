<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Contact Us</h1>
		<div class="animationFrame">
			<form ng-show="dispForm" ng-submit="send()">
				<p>All fields except "username" are required.</p>
				<div class="tr">
					<input type="text" ng-model="form.name" maxlength="50" placeholder="Name">
				</div>
				<div ng-if="!loggedIn" class="tr">
					<input type="text" ng-model="form.username" maxlength="50" placeholder="Username">
				</div>
				<div class="tr">
					<input type="text" ng-model="form.email" maxlength="100" placeholder="Email">
				</div>
				<div class="tr">
					<input type="text" ng-model="form.subject" maxlength="100" placeholder="Subject">
				</div>
				<div class="tr">
					<textarea ng-model="form.comment" placeholder="Comments"></textarea>
				</div>
				<div class="alignCenter"><button type="submit" class="fancyButton" skew-element>Submit</button></div>
			</form>
			<div ng-hide="dispForm">
				reveal!
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>