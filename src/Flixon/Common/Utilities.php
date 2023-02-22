<?php

namespace Flixon\Common;

class Utilities {
	/**
	 * Formats a string to a particular length.
	 *
	 * @param 	string 	$haystack	The string to format.
	 * @param 	int 	$maxLength 	The maximum length.
	 * @param 	bool 	$renderSpan Whether or not to render a span tag which shows the full string in the title.
     *
	 * @return string The formatted string.
	 */
    public static function formatLength(string $string, int $maxLength, bool $renderSpan = true): string {
        if ($renderSpan) {
		    return strlen($string) > $maxLength ? '<span title="' . htmlentities($string) . '">' . substr($string, 0, $maxLength) . '...</span>' : '<span>' . htmlentities($string) . '</span>';
        } else {
		    return strlen($string) > $maxLength ? substr($string, 0, $maxLength) . '...' : htmlentities($string);
        }
	}
	
	/**
	 * Generates a random string.
	 *
	 * @param int $length The length of the random string.
     *
	 * @return string The random string.
	 */
    public static function generateRandomString(int $length = 10): string {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';

	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    
	    return $randomString;
	}

    /**
	 * Splits a full name.
	 *
	 * @param string $name The name to split.
     *
	 * @return array The first and last name as an array.
	 */
    public static function splitName(string $name): array {
		return [
			strpos($name, ' ') ? substr($name, 0, strpos($name, ' ')) : $name,
			strpos($name, ' ') ? substr($name, strpos($name, ' ') + 1) : ''
		];
	}

	/**
	 * Splits a name by case and separates the changes in case with a particular character.
	 *
	 * @param string $string 	The string to split.
	 * @param string $with 		The string to separate by.
     *
	 * @return string The string split by case.
	 */
    public static function splitUpperCase(string $string, string $with = ' '): string {
        return preg_replace('/(?<=[A-Z])(?=[A-Z][a-z])|(?<=[^A-Z])(?=[A-Z])|(?<=[A-Za-z])(?=[^A-Za-z])/', $with, $string);
    }

    /**
	 * Strips the namespace from a class.
	 *
	 * @param string $class	The class to strip the namespace from.
     *
	 * @return string The name of the class.
	 */
    public static function stripNamespaceFromClass(string $class): string {
		return substr($class, (strrpos($class, '\\') ?: -1) + 1);
	}

	/**
	 * Looks for a pattern within a comma (default) separated string.
	 *
	 * @param 	string 	$pattern	The pattern to look for.
	 * @param 	string 	$search 	The string to search.
	 * @param 	string 	$separator 	The separator between each occurance.
     *
	 * @return bool Whether the pattern is matched.
	 */
    public static function wildCardMatch(string $pattern, string $search, string $separator = ','): bool {
        // If the pattern is empty then return true.
        if (empty($pattern)) {
            return true;
        }

        return count(array_filter(explode($separator, $pattern), function($item) use ($search) {
        	return preg_match('/^' . str_replace('\\*', '.*?', preg_quote(trim($item), '/')) . '$/i', $search);
        })) > 0;
    }
}