<?php

namespace App\Models;

use Flixon\Localization\Locale as LocaleInterface;

class Locale implements LocaleInterface {
    public string $format;

    public function __construct(string $format) {
        $this->format = $format;
    }
}