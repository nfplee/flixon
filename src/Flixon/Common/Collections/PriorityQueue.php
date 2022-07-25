<?php

namespace Flixon\Common\Collections;

use SplPriorityQueue;

/**
 * Fixes an issue where you have the same priority. See https://mwop.net/blog/253-Taming-SplPriorityQueue.html for more information.
 */
class PriorityQueue extends SplPriorityQueue {
    private $priority = PHP_INT_MAX;
    
    public function insert($value, $priority) {
        parent::insert($value, [(int)$priority, $this->priority--]);
    }
}