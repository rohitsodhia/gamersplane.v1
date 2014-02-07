<?
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" ?>';
	
	echo '<rolls>';
	preg_match_all('/\d+d\d+([+-]\d+)?/', $_POST['dice'], $rolls);
	$rerollAces = $_POST['rerollAces']?1:0;
	foreach($rolls[0] as $roll) {
		$results = rollDice($roll, $rerollAces);
		
		echo '<roll>';
		echo '<dice>'.$roll.'</dice>';
		echo '<indivRolls>'.displayIndivDice($results['indivRolls']).'</indivRolls>';
		echo '<total>'.$results['total'].'</total>';
		echo '</roll>';
//		echo "\t\t\t<p>".$roll."<br>\n\t\t\t".$results['indivRolls'].' = '.$results['total']."</p>\n";
	}
	echo '</rolls>';
?>