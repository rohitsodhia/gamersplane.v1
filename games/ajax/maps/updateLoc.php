<?
	checkLogin();
	
	$iconID = intval($_POST['iconID']);
	$location = preg_match('/^[0-9]{1,2}_[0-9]{1,2}$/', $_POST['location'])?$_POST['location']:'';
	$icon = new Icon($iconID);
	echo $icon->saveLocation($location);
?>