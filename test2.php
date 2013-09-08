<!DOCTYPE html>
<html>
<head>
</head>

<body>
<?php
	require_once('includes/requires.php');
$readData = array(1 => array(0 => array(
					'forums' => array(
					),
					'threads' => array(
					),
					'markedRead' => 0
				)),
				2 => array(0 => array(
					'forums' => array(
					),
					'threads' => array(
					),
					'markedRead' => 0
				))
			  );

echo setcookie('readData', serialize($readData), strtotime('+1 year'), COOKIE_ROOT);
?>
</body></html>