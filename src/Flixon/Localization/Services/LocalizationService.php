<?php

namespace Flixon\Localization\Services;

interface LocalizationService {
	public function getLocaleByFormat(string $format);
}