<?
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" ?>';
	
	echo '<rolls type="'.$_POST['rollType'].'">';
	if ($_POST['rollType'] == 'basic') {
		$rolls = array();
		if (isset($_POST['roll'])) {
			preg_match_all('/\d+d\d+([+-]\d+)?/', $_POST['dice'], $rolls);
			$rolls = $rolls[0];
			$rerollAces = $_POST['rerollAces']?1:0;
		} else { foreach ($_POST as $key => $value) {
			if (preg_match('/d[\d]+/', $key)) {
				$rolls = array('1'.$key);
				$rerollAces = 0;
				break;
			}
		} }
		foreach($rolls as $roll) {
			$results = rollDice($roll, $rerollAces);
			
			echo '<roll>';
			echo '<dice>'.$roll.'</dice>';
			echo '<indivRolls>'.$results['indivRolls'].'</indivRolls>';
			echo '<total>'.$results['total'].'</total>';
			echo '</roll>';
		}
	} elseif ($_POST['rollType'] == 'sweote') {
		$rolls = $_POST['dice'];
		foreach($rolls as $roll) {
			$result = sweote_rollDice($roll, $rerollAces);
			
			echo '<roll>';
			echo '<dice>'.$roll.'</dice>';
			echo '<result>'.$result['result'].'</result>';
			echo '<values>'.implode(',', $result['values']).'</values>';
			echo '</roll>';
		}
	}
	echo '</rolls>';
?>