<?php
class Reviewpagination {
	public $total = 0;
	public $page = 1;
	public $limit = 20;
	public $num_links = 8;
	public $url = '';
	public $text_first = '|&lt;';
	public $text_last = '&gt;|';
	public $text_next = '&gt;';
	public $text_prev = '&lt;';

	public function render() {
		$total = $this->total;
		$product_id = $this->product_id;

		if ($this->page < 1) {
			$page = 1;
		} else {
			$page = $this->page;
		}

		if (!(int)$this->limit) {
			$limit = 10;
		} else {
			$limit = $this->limit;
		}
		
		$function_ajax = "load_review_pagination";
		
		$popup_all = "";
		if ($this->popup_all) {$function_ajax = "load_review_popup_pagination"; $popup_all = "&popup_all=1";}

		$num_links = $this->num_links;
		$num_pages = ceil($total / $limit);

		$this->url = str_replace('%7Bpage%7D', '{page}', $this->url);

		$output = '<ul class="pagination">';

		

			if ($page - 1 === 1) {
                $output .= '<li><a ' . ($page <= 1 ? "class='no-active'" : "") . 'onclick="' . $function_ajax . '(\'' . $product_id . '\', \'' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . $popup_all . '\')">' . $this->text_prev . '</a></li>';
			} else {
				$output .= '<li><a ' . ($page <= 1 ? "class='no-active'" : "") . 'onclick="' . $function_ajax . '(\'' . $product_id . '\', \'' . str_replace('{page}', $page - 1, $this->url) . $popup_all . '\')">' . $this->text_prev . '</a></li>';
			}
		

		if ($num_pages > 1) {
			if ($num_pages <= $num_links) {
				$start = 1;
				$end = $num_pages;
			} else {
				$start = $page - floor($num_links / 2);
				$end = $page + floor($num_links / 2);

				if ($start < 1) {
					$end += abs($start) + 1;
					$start = 1;
				}

				if ($end > $num_pages) {
					$start -= ($end - $num_pages);
					$end = $num_pages;
				}
			}

			for ($i = $start; $i <= $end; $i++) {
				if ($page == $i) {
					$output .= '<li class="active"><span>' . $i . '</span></li>';
				} else {
					if ($i === 1) {
                        $output .= '<li><a onclick="' . $function_ajax . '(\'' . $product_id . '\', \'' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $this->url) . $popup_all . '\')">' . $i . '</a></li>';
					} else {
						$output .= '<li><a onclick="' . $function_ajax . '(\'' . $product_id . '\', \'' . str_replace('{page}', $i, $this->url) . $popup_all . '\')">' . $i . '</a></li>';
					}
				}
			}
		}

		
		$output .= '<li><a ' . ($page >= $num_pages ? "class='no-active'" : 'onclick="' . $function_ajax . '(\'' . $product_id . '\', \'' . str_replace('{page}', $page + 1, $this->url) . $popup_all . '\')"') . '>' . $this->text_next . '</a></li>';
		

		$output .= '</ul>';

		if ($num_pages > 1) {
			return $output;
		} else {
			return '';
		}
	}
}