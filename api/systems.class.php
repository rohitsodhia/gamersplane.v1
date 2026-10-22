<?php
	class systems {
		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'getGenres') {
				$this->getGenres();
			} elseif ($pathOptions[0] == 'save') {
				$this->save();
			} else {
				displayJSON(array('failed' => true));
			}
		}


		public function getGenres() {
			$mysql = DB::conn('mysql');

			$genres = [];
			$getSystem = $mysql->query("SELECT genres FROM systems");
			foreach ($getSystem->fetchAll() as $system) {
				$genres = json_decode($system['genres']);
				foreach ($genres as $genre) {
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
