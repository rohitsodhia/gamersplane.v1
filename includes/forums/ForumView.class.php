<?
	class ForumView {
		public function __construct() {
		}

		public static function displayPagination($count = 0, $page = 1, $query = array(), $pageSize=PAGINATE_PER_PAGE, $allowAll=false) {
			$page = intval($page) > 0?intval($page):1;
			if ($page > ceil($count / $pageSize)) $page = ceil($count / $pageSize);

			if ($count > $pageSize) {
				$spread = 2;
				echo "\t\t\t<div class=\"paginateDiv\">";
				$numPages = ceil($count / $pageSize);
				$firstPage = $page - $spread;
				if ($firstPage < 1) $firstPage = 1;
				$lastPage = $page + $spread;
				if ($lastPage > $numPages) $lastPage = $numPages;
				if($allowAll){
				echo "\t\t\t\t<div class=\"currentPage\"><a href='?pageSize=10000'>{$page}<span class='mob-hide'> of </span><span class='non-mob-hide'>/</span>{$numPages}</a></div>\n";
				}
				else{
					echo "\t\t\t\t<div class=\"currentPage\">{$page} of {$numPages}</div>\n";
				}
				if (($page - $spread) > 1) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => 1)))."\">&lt;&lt;<span class='mob-hide'> First</span></a>\n";
				if ($page > 1) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => $page - 1)))."\">&lt;</a>\n";
				for ($count = $firstPage; $count <= $lastPage; $count++) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => $count)))."\"".(($count == $page)?' class="page"':'').">$count</a>\n";
				if ($page < $numPages) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => $page + 1)))."\">&gt;</a>\n";
				if (($page + $spread) < $numPages) echo "\t\t\t\t<a href=\"?".http_build_query(array_merge($query, array('page' => $numPages)))."\"><span class='mob-hide'>Last </span>&gt;&gt;</a>\n";
				echo "\t\t\t</div>\n";
			}
		}
	}
?>