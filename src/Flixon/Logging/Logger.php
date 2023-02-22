<?php

namespace Flixon\Logging;

interface Logger {
    public function info(string $message): Logger;
    public function log(string $level, string $message): Logger;
    public function write(): Logger;
}