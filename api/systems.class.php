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
			$fields = array('name' => true);
			if (isset($_POST['fields']) && $_POST['fields'] == 'all') 
				$fields = array();
			elseif (isset($_POST['fields']) && is_array($_POST['fields'])) 
				foreach ($_POST['fields'] as $field) 
					$fields[$field] = true;
			if (isset($_POST['excludeCustom']) && $_POST['excludeCustom']) 
				$search['_id'] = array('$ne' => 'custom');
			if (isset($_POST['shortName']) && is_string($_POST['shortName']) && strlen($_POST['shortName'])) {
				$rSystems = $mongo->systems->findOne(array('_id' => $_POST['shortName']), $fields);
				$rSystems = array($rSystems);
				$numSystems = 1;
			} elseif (isset($_POST['getAll']) && $_POST['getAll']) {
				$numSystems = $mongo->systems->find(array(), array('_id' => 1))->count();
				$rSystems = $mongo->systems->find(array(), $fields)->sort(array('sortName' => 1));
			} else {
				$numSystems = $mongo->systems->find($search, array('_id' => 1))->count();
				$page = isset($_POST['page']) && intval($_POST['page'])?intval($_POST['page']):1;
				$rSystems = $mongo->systems->find($search, $fields)->sort(array('sortName' => 1))->skip(10 * ($page - 1))->limit(10);
			}
			$systems = array();
			$custom = array();
			$defaults = array(
				'genres' => array(),
				'publisher' => array('name' => '', 'site' => ''),
				'basics' => array()
			);
			unset($fields['name']);
			foreach ($rSystems as $rSystem) {
				$system = array(
					'shortName' => $rSystem['_id'],
					'fullName' => $rSystem['name']
				);
				if (sizeof($fields) > 0) {
					foreach ($fields as $field => $nothing) 
						$system[$field] = isset($rSystem[$field])?$rSystem[$field]:(isset($defaults[$field])?$defaults[$field]:null);
				}
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
				$systemData = $_POST['data'];
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
					'_id' => $systemData->shortName,
					'name' => sanitizeString($systemData->fullName),
					'sortName' => sanitizeString($systemData->fullName, 'lower'),
					'hasCharSheet' => $systemData->hasCharSheet?true:false,
					'genres' => $genres,
					'publisher' => (object) array(
						'name' => strlen($systemData->publisher->name)?sanitizeString($systemData->publisher->name):null,
						'site' => strlen($systemData->publisher->site)?$systemData->publisher->site:null
					),
					'basics' => $basics
				);
				$system = $mongo->systems->findAndModify(array('_id' => $systemData->shortName), array('$set' => $system), null, array('upsert' => true, 'new' => true));
				$system['shortName'] = $system['_id'];
				$system['fullName'] = $system['name'];
				unset($system['_id'], $system['name']);
				displayJSON($system);
			}
		}
	}
?>