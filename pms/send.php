<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">{{headerTitle}}</h1>

		<form ng-submit="sendPM()">
			<input id="replyTo" type="hidden" name="pmID" value="{{replyTo}}">
			<div class="tr clearfix">
				<label for="username" class="textLabel">Username:</label>
				<input id="username" type="text" name="username" maxlength="24" ng-model="username" ng-blur="checkUser()">
				<div id="invalidUser" class="alert" ng-hide="formError.validUser">Invalid User</div>
			</div>
			<div class="tr">
				<label for="title" class="textLabel">Title:</label>
				<input id="title" type="text" name="title" maxlength="100" ng-model="title" ng-change="checkTitle()" ng-blur="checkTitle()">
			</div>
			<div id="titleRequired" class="tr alert" ng-hide="formError.validTitle">Title required!</div>
			<textarea id="messageTextArea" name="message" ng-model="message"></textarea>
			<div id="messageRequired" class="alert" ng-hide="formError.validMessage">Message required!</div>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="send" class="fancyButton">Send</button></div>
		</form>
<? if ($reply) { ?>
		
		<div id="history">
<?
		$first = true;
		foreach ($replyManager->history as $pm) {
?>
			<div class="historyPM<?=$first?' first':''?>">
<?			if ($pm->hasAccess()) { ?>
				<p class="title"><a href="/pms/view/<?=$pm->getPMID()?>/"><?=$pm->getTitle(true)?></a></p>
<?			} else { ?>
				<p class="title"><?=$pm->getTitle(true)?></p>
<?			} ?>
				<p class="user">from <a href="/user/<?=$pm->getSender('userID')?>/" class="username"><?=$pm->getSender('username')?></a> on <span class="convertTZ"><?=$pm->getDatestamp('F j, Y g:i a')?></span></p>
<?
			$recipients = array();
			foreach ($pm->getRecipients() as $recipient) 
				$recipients[] = "<a href=\"/user/{$recipient->userID}/\" class=\"username\">{$recipient->username}</a>";
?>
				<p class="user">to <?=implode(', ', $recipients)?></p>
				<div class="message">
<?=$pm->getMessage(true)?>
				</div>
			</div>
<?
			if ($first) $first = false;
		}
?>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>