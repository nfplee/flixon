<?php

namespace App\Services;

use App\Models\Locale;
use Flixon\Localization\Services\LocalizationService as LocalizationServiceInterface;
use Flixon\Localization\Locale as LocaleInterface;

class LocalizationService implements LocalizationServiceInterface {
    public function getLocaleByFormat(string $format): ?LocaleInterface {
		return new Locale($format);
	}
}