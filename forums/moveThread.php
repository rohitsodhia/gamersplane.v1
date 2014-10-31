<?
	$threadID = intval($pathOptions[1]);
	
	$forumInfo = $mysql->query("SELECT forums.forumID, forums.heritage FROM threads, forums WHERE forums.forumID = threads.forumID AND threads.threadID = {$threadID}");
	list($forumID, $heritage) = $forumInfo->fetch(PDO::FETCH_NUM);
	$heritage = explode('-', $heritage);
	foreach ($heritage as $key => $value) $heritage[$key] = intval($value);
	$adminCheck = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = {$currentUser->userID} AND forumID IN (0, 2, {$heritage[1]})");
	if (!$adminCheck->rowCount() || !in_array(2, $heritage)) { header('Location: /forums'); exit; }
	$forumInfos = $mysql->query('SELECT forumID, title, parentID, heritage, `order` FROM forums WHERE heritage LIKE "002-'.sql_forumIDPad($heritage[1]).'%" ORDER BY LEFT(heritage, LENGTH(heritage) - 3), `order`');
	$temp = array();
	$forumOrder = array();
	foreach ($forumInfos as $forumInfo) {
		$temp[$forumInfo['forumID']] = $forumInfo;
		if (array_search($forumInfo['parentID'], $forumOrder) !== FALSE) {
			array_splice($forumOrder, array_search($forumInfo['parentID'], $forumOrder) + $forumInfo['order'], 0, $forumInfo['forumID']);
			$forumOrder = array_values($forumOrder);
		} else $forumOrder[] = $forumInfo['forumID'];
	}
	$forumInfos = $temp;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Move Thread</h1>
		
		<p>Where would you like to move the thread to?</p>
		
		<form method="post" action="/forums/process/moveThread/" class="alignCenter">
			<input type="hidden" name="threadID" value="<?=$threadID?>">
			<div>
<? foreach ($forumOrder as $oForumID) echo "\t\t\t\t<div class=\"tr\"><input type=\"radio\" name=\"destinationID\" value=\"$oForumID\"".($oForumID == $forumID?' checked="checked"':'')."><span style=\"margin-left: ".((sizeof(explode('-', $forumInfos[$oForumID]['heritage'])) - 1) * 20)."px\">".printReady($forumInfos[$oForumID]['title'])."</span>".($oForumID == $forumID?' <i>[ Currently Here ]</i>':'')."</div>\n"; ?>
			</div>
			<button type="submit" name="add" class="btn_add"></button>
			<button type="submit" name="cancel" class="btn_cancel"></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>