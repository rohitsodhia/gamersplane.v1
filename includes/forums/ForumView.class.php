<?
	class ForumView {
		public function __construct() {
		}

		public static function displayPagination($count = 0, $page = 1, $query = array()) {
			$page = intval($page) > 0?intval($page):1;
			if ($page > ceil($count / PAGINATE_PER_PAGE)) $page = ceil($count / PAGINATE_PER_PAGE);

			if ($count > PAGINATE_PER_PAGE) {
				$spread = 2;
				echo "\t\t\t<div class=\"paginateDiv\">";
				$numPages = ceil($count / PAGINATE_PER_PAGE);
				$firstPage = $page - $spread;
				if ($firstPage < 1) $firstPage = 1;
				$lastPage = $page + $spread;
				if ($lastPage > $numPages) $lastPage = $numPages;
				echo "\t\t\t\t<div class=\"currentPage\">{$page} of {$numPages}</div>\n";
				if (($page - $spread) > 1) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => 1)))."\">&lt;&lt; First</a>\n";
				if ($page > 1) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => $page - 1)))."\">&lt;</a>\n";
				for ($count = $firstPage; $count <= $lastPage; $count++) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => $count)))."\"".(($count == $page)?' class="page"':'').">$count</a>\n";
				if ($page < $numPages) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => $page + 1)))."\">&gt;</a>\n";
				if (($page + $spread) < $numPages) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => $numPages)))."\">Last &gt;&gt;</a>\n";
				echo "\t\t\t</div>\n";
			}
		}
	}
?>