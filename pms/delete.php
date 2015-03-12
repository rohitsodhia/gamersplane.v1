<? require_once(FILEROOT.'/header.php'); ?>
			<h1 class="headerbar">Delete Message</h1>
			
			<p class="alignCenter">
				Are you sure you wanna delete this PM?
			</p>
			<form class="alignCenter">
				<button type="submit" name="delete" class="fancyButton" ng-click="deletePM($event)">Delete</button>
				<button type="submit" name="cancel" class="fancyButton" ng-click="cancel($event)">Cancel</button>
			</form>
<? require_once(FILEROOT.'/footer.php'); ?>