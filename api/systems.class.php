<?
	class systems {
		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'get') 
				$this->get();
			elseif ($pathOptions[0] == 'getGenres') 
				$this->getGenres();
			elseif ($pathOptions[0] == 'save') 
				$this->save();
			else 
				displayJSON(array('failed' => true));
		}

		public function get() {
			global $mongo;

			$search = array();
			if (isset($_POST['excludeCustom']) && $_POST['excludeCustom']) 
				$search['_id'] = array('$ne' => 'custom');
			if (isset($_POST['shortName']) && is_string($_POST['shortName']) && strlen($_POST['shortName'])) {
				$rSystems = $mongo->systems->findOne(array('_id' => $_POST['shortName']));
				$rSystems = array($rSystems);
				$numSystems = 1;
			} elseif (isset($_POST['getAll']) && $_POST['getAll']) {
				$numSystems = $mongo->systems->find(array(), array('_id' => 1))->count();
				$rSystems = $mongo->systems->find()->sort(array('sortName' => 1));
			} else {
				$numSystems = $mongo->systems->find($search, array('_id' => 1))->count();
				$page = isset($_POST['page']) && intval($_POST['page'])?intval($_POST['page']):1;
				$rSystems = $mongo->systems->find($search)->sort(array('sortName' => 1))->skip(10 * ($page - 1))->limit(10);
			}
			$systems = array();
			$custom = array();
			foreach ($rSystems as $system) {
				$system = array(
					'shortName' => $system['_id'],
					'fullName' => $system['name']
				);
				if (!isset($_POST['basic']) || !$_POST['basic'])
					$system = array_merge($system, array(
						'genres' => $system['genres']?$system['genres']:array(),
						'publisher' => $system['publisher']?$system['publisher']:array('name' => '', 'site' => ''),
						'basics' => $system['basics']?$system['basics']:array()
					));
				if ($system['shortName'] != 'custom') 
					$systems[] = $system;
				else 
					$custom = $system;
			}
			if ((!isset($_POST['excludeCustom']) || !$_POST['excludeCustom']) && sizeof($custom)) 
				$systems[] = $custom;
			displayJSON(array('numSystems' => $numSystems, 'systems' => $systems));
		}

		public function getGenres() {
			global $mongo;

			$genres = array();
			$rSystem = $mongo->systems->find(array('genres' => array('$not' => array('$size' => 0))), array('_id' => -1, 'genres' => 1));
			foreach ($rSystem as $system) 
				foreach ($system['genres'] as $genre)
					$genres[] = $genre;
			displayJSON(array_unique($genres));
		}

		public function save() {
			global $mongo, $currentUser;

			if ($currentUser->checkACP('systems', false)) {
				$genres = array();
				$systemData = $_POST['system'];
				if (isset($systemData->genres) && is_array($systemData->genres)) { foreach ($systemData->genres as $genre) {
					$genre = sanitizeString($genre);
					if (strlen($genre) && !array_search($genre, $genres)) 
						$genres[] = $genre;
				} }
				$basics = array();
				if (isset($systemData->basics) && is_array($systemData->basics)) { foreach ($systemData->basics as $basic) {
					if (strlen($basic->text) && strlen($basic->site)) {
						$basics[] = (object) array(
							'text' => sanitizeString($basic->text),
							'site' => sanitizeString($basic->site)
						);
					}
				} }
				$system = array(
					'name' => sanitizeString($systemData->fullName),
					'sortName' => sanitizeString($systemData->fullName, 'lower'),
					'genres' => $genres,
					'publisher' => (object) array(
						'name' => strlen($systemData->publisher->name)?sanitizeString($systemData->publisher->name):null,
						'site' => strlen($systemData->publisher->site)?$systemData->publisher->site:null
					),
					'basics' => $basics
				);
				$system = $mongo->systems->findAndModify(array('_id' => $systemData->shortName), array('$set' => $system), null, array('new' => true));
				$system['shortName'] = $system['_id'];
				$system['fullName'] = $system['name'];
				unset($system['_id'], $system['name']);
				displayJSON($system);
			}
		}
	}
?>