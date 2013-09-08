<!DOCTYPE html>
<html>
<head>
</head>

<body>
<?
	function rndStr() {
		$validChars = "ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789";
		$randomStr = "";
		for ($count = 0; $count < 20; $count++) $randomStr .= $validChars[rand(0, strlen($validChars) - 1)];
		
		return $randomStr;
	}
	
	$regKey = '';
	do {
		$regKey = rndStr();
		$numCount = 0;
		$numCount = strlen(preg_replace('/[A-Z]/', '', $regKey));
	} while ($numCount < 8 || $numCount > 12);
	
	echo $regKey;
?>
</body></html>