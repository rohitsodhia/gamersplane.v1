<?
	class marvelCharacter extends Character {
		const SYSTEM = 'marvel';

		protected $superName = '';
		protected $health = 0;
		protected $energy = 0;
		protected $unusedStones = 0;
		protected $stats = array('int' => 0, 'str' => 0, 'agi' => 0, 'spd' => 0, 'dur' => 0);
		protected $challenges = array();

		protected $linkedTables = array('actions', 'modifiers');

		public function getRedStones($stones) {
			if ($stones - intval($stones) == 0) return 0;
			else {
				$redStones = intval(($stones - intval($stones)) * 10 / 3);
				if ($redStones == 3) $redStones = 0;
			}
			
			return $redStones;
		}
		
		public function getWhiteStones($stones) {
			if ($this->getRedStones($stones) == 0 || $stones > 0) return intval($stones);
			else return '-'.intval(abs($stones));
		}

		public function setSuperName($superName) {
			$this->superName = $superName;
		}

		public function getSuperName() {
			return $this->superName;
		}

		public function setHealth($health) {
			$this->health = intval($health) > 0?intval($health):0;
		}

		public function getHealth() {
			return $this->health;
		}

		public function setEnergy($energy) {
			$this->energy = intval($energy) > 0?intval($energy):0;
		}

		public function getEnergy() {
			return $this->energy;
		}

		public function setUnusedStones($white, $red) {
			$this->unusedStones = number_format(intval($white) + intval($red) / 3, 1);
		}

		public function getUnusedStones($color = NULL) {
			if ($color == 'white') return $this->getWhiteStones($this->unusedStones);
			elseif ($color == 'red') return $this->getRedStones($this->unusedStones);
			else return $this->unusedStones;
		}

		public function setStat($stat, $value) {
			if (array_key_exists($stat, $this->stats)) {
				$value = intval($value);
				if ($value > 0) $this->stats[$stat] = $value;
			} else return FALSE;
		}
		
		public function getStat($stat = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (array_key_exists($stat, $this->stats)) return $this->stats[$stat];
			else return FALSE;
		}

		public function addAction($actionName) {
			global $mysql;

			$actionID = $mysql->prepare('SELECT actionID FROM marvel_actionsList WHERE searchName = :name');
			$actionID->execute(array(':name' => sanitizeString($actionName, 'search_format')));
			if ($actionID->rowCount()) $actionID = $actionID->fetchColumn();
			else {
				$addNewAction = $mysql->prepare("INSERT INTO marvel_actionsList (name, searchName, userDefined) VALUES (:name, :searchName, {$this->userID})");
				$addNewAction->bindValue(':name', sanitizeString($actionName, 'rem_dup_spaces'));
				$addNewAction->bindValue(':searchName', sanitizeString($actionName, 'search_format'));
				$addNewAction->execute();
				$actionID = $mysql->lastInsertId();
			}
			$addAction = $mysql->query("INSERT INTO marvel_actions (characterID, actionID) VALUES ({$this->characterID}, $actionID)");
			$actionInfo = array('actionID' => $actionID, 'name' => $actionName);
			if ($addAction->rowCount()) $this->actionEditFormat($actionInfo);
		}

		public function updateAction($actionID, $actionInfo) {
			global $mysql;

			$addAction = $mysql->prepare("UPDATE marvel_actions SET level = :level, details = :details, cost = :cost WHERE characterID = :characterID AND actionID = :actionID");
			$addAction->bindValue(':characterID', $this->characterID);
			$addAction->bindValue(':actionID', $actionID);
			$addAction->bindValue(':level', $actionInfo['level']);
			$addAction->bindValue(':cost', number_format(floatval($actionInfo['cost']), 1));
			$addAction->bindValue(':details', sanitizeString($actionInfo['details']));
			$addAction->execute();
		}

		public function actionEditFormat($actionInfo) {
			if (!is_array($actionInfo) || sizeof($actionInfo) == 0) $actionInfo = array();
			$defaults = array('cost' => 0, 'level' => 0);
			foreach ($defaults as $key => $value) if (!isset($actionInfo[$key])) $actionInfo[$key] = $value;
?>
					<div id="action_<?=$actionInfo['actionID']?>" class="action borderBox">
						<div class="tr labelTR clearfix">
							<span class="spacer name">&nbsp;</span>
							<label class="cost borderBox">Cost</label>
							<label class="level borderBox">Level</label>
						</div>
						<div class="clearfix">
							<span class="name"><?=$actionInfo['name']?></span>
							<input type="text" name="actions[<?=$actionInfo['actionID']?>][cost]" value="<?=$actionInfo['cost']?>" class="cost borderBox">
							<input type="text" name="actions[<?=$actionInfo['actionID']?>][level]" value="<?=$actionInfo['level']?>" class="level borderBox">
						</div>
						<textarea name="actions[<?=$actionInfo['actionID']?>][details]"><?=$actionInfo['details']?></textarea>
						<div class="removeDiv alignRight"><a href="" class="remove">[ Remove ]</a></div>
					</div>
<?
		}

		public function showActionsEdit() {
			global $mysql;

			$actions = $mysql->query('SELECT pa.actionID, al.name, pa.level, pa.offset, pa.cost, pa.details FROM marvel_actions pa INNER JOIN marvel_actionsList al USING (actionID) WHERE characterID = '.$this->characterID);
			if ($actions->rowCount()) { foreach ($actions as $actionInfo) {
				$this->actionEditFormat($actionInfo);
			} }
		}

		public function removeAction($actionID) {
			global $mysql;

			$mysql->query("DELETE FROM marvel_actions WHERE characterID = {$this->characterID} AND actionID = {$actionID}");
		}

		public function displayActions() {
			global $mysql;

			$actions = $mysql->query('SELECT pa.actionID, al.name, pa.level, pa.offset, pa.cost, pa.details FROM marvel_actions pa INNER JOIN marvel_actionsList al USING (actionID) WHERE characterID = '.$this->characterID);
			if ($actions->rowCount()) { foreach ($actions as $action) {
?>
				<div class="action">
					<div class="tr labelTR clearfix">
						<span class="spacer name">&nbsp;</span>
						<label class="level">Level</label>
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="name"><?=$action['name']?></span>
						<span class="level"><?=$action['level']?></span>
						<span class="cost"><?=$action['cost']?></span>
					</div>
					<div class="details borderBox"><?=$action['details']?></div>
				</div>
<?
			} }
		}

		public function addModifier($modifierName) {
			global $mysql;

			$modifierID = $mysql->prepare('SELECT modifierID FROM marvel_modifiersList WHERE searchName = :name');	
			$modifierID->execute(array(':name' => sanitizeString($modifierName, 'search_format')));
			if ($modifierID->rowCount()) $modifierID = $modifierID->fetchColumn();
			else {
				$addNewModifier = $mysql->prepare("INSERT INTO marvel_modifiersList (name, searchName, userDefined) VALUES (:name, :searchName, {$this->userID})");
				$addNewModifier->bindValue(':name', sanitizeString($modifierName, 'rem_dup_spaces'));
				$addNewModifier->bindValue(':searchName', sanitizeString($modifierName, 'search_format'));
				$addNewModifier->execute();
				$modifierID = $mysql->lastInsertId();
			}
			$addModifier = $mysql->query("INSERT INTO marvel_modifiers (characterID, modifierID) VALUES ({$this->characterID}, $modifierID)");
			$modifierInfo = array('modifierID' => $modifierID, 'name' => $modifierName);
			if ($addModifier->rowCount()) $this->modifierEditFormat($modifierInfo);
		}

		public function updateModifier($modifierID, $modifierInfo) {
			global $mysql;

			$addModifier = $mysql->prepare("UPDATE marvel_modifiers SET level = :level, details = :details, cost = :cost WHERE characterID = :characterID AND modifierID = :modifierID");
			$addModifier->bindValue(':characterID', $this->characterID);
			$addModifier->bindValue(':modifierID', $modifierID);
			$addModifier->bindValue(':level', $modifierInfo['level']);
			$addModifier->bindValue(':cost', number_format(floatval($modifierInfo['cost']), 1));
			$addModifier->bindValue(':details', sanitizeString($modifierInfo['details']));
			$addModifier->execute();
		}

		public function modifierEditFormat($modifierInfo) {
		if (!is_array($modifierInfo) || sizeof($modifierInfo) == 0) $modifierInfo = array();
		$defaults = array('cost' => 0, 'level' => 0);
		foreach ($defaults as $key => $value) if (!isset($modifierInfo[$key])) $modifierInfo[$key] = $value;
?>
					<div id="modifier_<?=$modifierInfo['modifierID']?>" class="modifier borderBox">
						<div class="tr labelTR clearfix">
							<span class="name">&nbsp;</span>
							<label class="cost borderBox">Cost</label>
							<label class="level borderBox">Level</label>
						</div>
						<div class="clearfix">
							<span class="name"><?=$modifierInfo['name']?></span>
							<input type="text" name="modifiers[<?=$modifierInfo['modifierID']?>][cost]" value="<?=$modifierInfo['cost']?>" class="cost borderBox">
							<input type="text" name="modifiers[<?=$modifierInfo['modifierID']?>][level]" value="<?=$modifierInfo['level']?>" class="level borderBox">
						</div>
						<textarea name="modifiers[<?=$modifierInfo['modifierID']?>][details]"><?=$modifierInfo['details']?></textarea>
						<div class="removeDiv alignRight"><a href="" class="remove">[ Remove ]</a></div>
					</div>
<?
		}

		public function showModifiersEdit() {
			global $mysql;

			$modifiers = $mysql->query('SELECT pm.modifierID, ml.name, pm.level, pm.offset, pm.cost, pm.details FROM marvel_modifiers pm INNER JOIN marvel_modifiersList ml USING (modifierID) WHERE characterID = '.$this->characterID);
			if ($modifiers->rowCount()) { foreach ($modifiers as $modifierInfo) {
				$this->modifierEditFormat($modifierInfo);
			} }
		}

		public function removeModifier($modifierID) {
			global $mysql;

			$mysql->query("DELETE FROM marvel_modifiers WHERE characterID = {$this->characterID} AND modifierID = {$modifierID}");
		}

		public function displayModifiers() {
			global $mysql;

			$modifiers = $mysql->query('SELECT pm.modifierID, ml.name, pm.level, pm.offset, pm.cost, pm.details FROM marvel_modifiers pm INNER JOIN marvel_modifiersList ml USING (modifierID) WHERE characterID = '.$this->characterID);
			if ($modifiers->rowCount()) { foreach ($modifiers as $modifier) {
?>
				<div class="modifier">
					<div class="tr labelTR">
						<span class="spacer name">&nbsp;</span>
						<label class="level">Level</label>
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="name"><?=$modifier['name']?></span>
						<span class="level"><?=$modifier['level']?></span>
						<span class="cost"><?=$modifier['cost']?></span>
					</div>
					<div class="details"><?=$modifier['details']?></div>
				</div>
<?
			} }
		}

		public function addChallenge($challenge) {
			if (strlen($challenge['name']) && strlen($challenge['stones']) && intval($challenge['stones']) >= 0) {
				$cleanChallenge['name'] = $challenge['name'];
				$cleanChallenge['stones'] = intval($challenge['stones']);
				$this->challenges[] = $cleanChallenge;
			}
		}

		public function showChallengesEdit($min) {
			$challengeNum = 0;
			if (!is_array($this->challenges)) $this->challenges = (array) $this->challenges;
			foreach ($this->challenges as $challengeInfo) $this->challengeEditFormat($challengeNum++, $challengeInfo);
			if ($challengeNum < $min) while ($challengeNum < $min) $this->challengeEditFormat($challengeNum++);
		}

		public function challengeEditFormat($challengeNum, $challengeInfo = NULL) {
			if (!is_array($challengeInfo) || sizeof($challengeInfo) == 0) $challengeInfo = array();
?>
					<div class="tr challenge">
						<input type="text" name="challenges[<?=$challengeNum?>][name]" value="<?=$challengeInfo['name']?>" class="name">
						<input type="text" name="challenges[<?=$challengeNum?>][stones]" value="<?=$challengeInfo['stones']?>" class="stones">
						<a href="" class="remove">[ Remove ]</a>
					</div>
<?
		}

		public function displayChallenges() {
			if (sizeof($this->challenges)) { foreach ($this->challenges as $challengeInfo) {
?>
				<div class="challenge tr clearfix">
					<span class="name"><?=$challengeInfo['name']?></span>
					<span class="stones"><?=$challengeInfo['stones']?></span>
				</div>
<?
			} }
		}

		public function save() {
			$data = $_POST;

			$this->setName($data['normName']);
			$this->setSuperName($data['superName']);
			$this->setHealth($data['health']);
			$this->setEnergy($data['energy']);
			$this->setUnusedStones($data['unusedStones']['white'], $data['unusedStones']['red']);
			foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);

			if (sizeof($data['actions'])) { foreach ($data['actions'] as $actionID => $actionInfo) {
				$this->updateAction($actionID, $actionInfo);
			} }
			if (sizeof($data['modifiers'])) { foreach ($data['modifiers'] as $modifierID => $modifierInfo) {
				$this->updateModifier($modifierID, $modifierInfo);
			} }
			$this->clearVar('challenges');
			foreach ($data['challenges'] as $challenge) $this->addChallenge($challenge);

			$this->setNotes($data['notes']);

			parent::save();
		}
	}
?>