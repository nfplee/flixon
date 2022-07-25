<?php

namespace Flixon\Common\Collections;

class PagedEnumerable extends Enumerable {
	public $page, $pageSize, $totalCount, $totalPages, $hasPreviousPage, $hasNextPage;

	public function getUrl(string $url, int $page): string {
        // Make sure the page placeholder has not been encoded.
        $url = str_replace('%25d', '%d', $url);

    	if ($page == 1) {
    		return str_replace('?&', '?', str_replace('?page=%d', (strpos($url, '&') ? '?' : ''), str_replace('&page=%d', '', $url)));
    	} else {
    		return str_replace('%d', $page, $url);
    	}
    }

	public function page(int $page, int $pageSize, int $totalCount = null): PagedEnumerable {
		if ($totalCount === null) {
			$totalCount = $this->count();
			$enumerable = $this->skip(($page - 1) * $pageSize)->take($pageSize);
		} else {
			$enumerable = $this;
		}

		$enumerable->page = $page;
		$enumerable->pageSize = $pageSize;
		$enumerable->totalCount = $totalCount;
		$enumerable->totalPages = ceil($totalCount / $pageSize);
		$enumerable->hasPreviousPage = $page > 1;
		$enumerable->hasNextPage = $page < $enumerable->totalPages;

		return $enumerable;
	}

	public function renderPager(string $url): string {
        $pager = '';

		if ($this->hasPreviousPage) {
		    $pager .= '<a href="' . $this->getUrl($url, 1) . '" data-page="1">&laquo;</a>';
		    $pager .= '<a href="' . $this->getUrl($url, $this->page - 1) . '" data-page="' . ($this->page - 1) . '">&#8249;</a>';
		}

		if ($this->totalCount > $this->pageSize) {
		    for ($i = $this->page <= 2 ? 1 : $this->page - 2; $i <= ($this->page >= $this->totalPages - 2 ? $this->totalPages : $this->page + 2); $i++) {
		        if ($this->page == $i) {
		            $pager .= '<span class="current">' . $i . '</span>';
		        } else {
		            $pager .= '<a href="' . $this->getUrl($url, $i) . '" data-page="' . $i . '">' . $i . '</a>';
		        }
		    }

		    if ($this->page < $this->totalPages - 2) {
		        $pager .= ' ... ';
		        $pager .= '<a href="' . $this->getUrl($url, $this->totalPages) . '" data-page="' . $this->totalPages . '">' . $this->totalPages . '</a>';
		    }
		}

		if ($this->hasNextPage) {
		    $pager .= '<a href="' . $this->getUrl($url, $this->page + 1) . '" data-page="' . ($this->page + 1) . '">&#8250;</a>';
		    $pager .= '<a href="' . $this->getUrl($url, $this->totalPages) . '" data-page="' . $this->totalPages . '">&raquo;</a>';
		}

        return $pager;
    }

    public function skip(int $count): Enumerable {
        return $this->slice($count);
    }

    public function take(int $count): Enumerable {
        return $this->slice(0, $count);
    }
}