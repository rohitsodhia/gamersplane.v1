<?php
	class systems {
		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'get') {
				$this->get();
			} elseif ($pathOptions[0] == 'getGenres') {
				$this->getGenres();
			} elseif ($pathOptions[0] == 'save') {
				$this->save();
			} else {
				displayJSON(array('failed' => true));
			}
		}

		public function get() {
			$mysql = DB::conn('mysql');

			$search = [];
			$validFields = ['shortName', 'name', 'sortName', 'enabled', 'angular', 'genres', 'publisher', 'basics', 'hasCharSheet', 'lfg'];
			$fields = ['name'];
			$selectFields = '*';
			if (!isset($_POST['fields']) || $_POST['fields'] == 'all') {
				$fields = [];
				$selectFields = '*';
			} elseif (isset($_POST['fields']) && is_array($_POST['fields'])) {
				$reqFields = $_POST['fields'];
				if (!array_diff($reqFields, $validFields)) {
					$fields = $reqFields;
					$selectFields = implode(', ', str_repeat('?', count($fields)));
				}
			}
			if (isset($_POST['shortName']) && is_string($_POST['shortName']) && strlen($_POST['shortName'])) {
				$getSystems = $mysql->prepare("SELECT {$selectFields} FROM systems WHERE id = ?");
				$getSystems->execute(array_merge($fields, [$_POST['shortName']]));
				$numSystems = 1;
			} elseif (isset($_POST['getAll']) && $_POST['getAll']) {
				$getSystems = $mysql->prepare("SELECT {$selectFields} FROM systems ORDER BY sortName");
				$getSystems->execute($fields);
				$numSystems = $getSystems->rowCount();
			}
			$systems = [];
			$custom = [];
			$defaults = [
				'genres' => [],
				'publisher' => ['name' => '', 'site' => ''],
				'basics' => []
			];
			unset($fields['name']);
			$rSystems = $getSystems->fetchAll();
			foreach ($rSystems as $rSystem) {
				$system = array_merge($defaults, $rSystem, [
					'shortName' => $rSystem['id'],
					'fullName' => $rSystem['name']
				]);
				unset($system['id'], $system['name']);
				if ($system['shortName'] != 'custom') {
					$systems[] = $system;
				} else {
					$custom = $system;
				}
			}
			if ((!isset($_POST['excludeCustom']) || !$_POST['excludeCustom']) && sizeof($custom)) {
				array_splice($systems, 0, 0, array($custom));
			}
			displayJSON(array('numSystems' => $numSystems, 'systems' => $systems));
		}

		public function getGenres() {
			$mongo = DB::conn('mongo');

			$genres = [];
			$rSystem = $mongo->systems->find(
				['genres' => [
					'$not' => ['$size' => 0]
				]],
				['projection' => ['_id' => -1, 'genres' => 1]]
			);
			foreach ($rSystem as $system) {
				foreach ($system['genres'] as $genre) {
					$genres[] = $genre;
				}
			}
			displayJSON(array_unique($genres));
		}

		public function save() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			if ($currentUser->checkACP('systems', false)) {
				$genres = [];
				$systemData = $_POST['data'];
				if (isset($systemData->genres) && is_array($systemData->genres)) {
					foreach ($systemData->genres as $genre) {
						$genre = sanitizeString($genre);
						if (strlen($genre) && !array_search($genre, $genres)) {
							$genres[] = $genre;
						}
					}
				}
				$basics = [];
				if (isset($systemData->basics) && is_array($systemData->basics)) {
					foreach ($systemData->basics as $basic) {
						if (strlen($basic->text) && strlen($basic->site)) {
							$basics[] = (object) [
								'text' => sanitizeString($basic->text),
								'site' => sanitizeString($basic->site)
							];
						}
					}
				}
				$system = [
					'_id' => $systemData->shortName,
					'name' => sanitizeString($systemData->fullName),
					'sortName' => sanitizeString($systemData->fullName, 'lower'),
					'hasCharSheet' => $systemData->hasCharSheet ? true : false,
					'genres' => $genres,
					'publisher' => (object) [
						'name' => strlen($systemData->publisher->name) ? sanitizeString($systemData->publisher->name) : null,
						'site' => strlen($systemData->publisher->site) ? $systemData->publisher->site : null
					],
					'basics' => $basics
				];
				$system = $mongo->systems->findOneAndUpdate(
					['_id' => $systemData->shortName],
					['$set' => $system],
					['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER, 'upsert' => true]
				);
				$system['shortName'] = $system['_id'];
				$system['fullName'] = $system['name'];
				unset($system['_id'], $system['name']);
				displayJSON($system);
			}
		}
	}
?>
