<?php

namespace Flixon\Localization\Services;

use Flixon\Localization\Locale;

interface LocalizationService {
    public function getLocaleByFormat(string $format): ?Locale;
}