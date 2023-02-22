<?php

namespace Flixon\Common;

class Inflector {
    /**
     * Works out whether a string is singular.
     *
     * @param string $word English noun to check is singular.
     *
     * @return bool Whether the string is singular.
     */
    public static function isSingular(string $word): bool {
        return self::singularize($word) == $word;
    }

    /**
     * Pluralizes English nouns.
     *
     * @param string $word English noun to pluralize.
     *
     * @return string Plural noun.
     */
    public static function pluralize(string $word): string {
        $pluralRules = [
            '/(quiz)$/i' => '\1zes',
            '/^(ox)$/i' => '\1en',
            '/([m|l])ouse$/i' => '\1ice',
            '/(matr|vert|ind)ix|ex$/i' => '\1ices',
            '/(x|ch|ss|sh)$/i' => '\1es',
            '/([^aeiouy]|qu)ies$/i' => '\1y',
            '/([^aeiouy]|qu)y$/i' => '\1ies',
            '/(hive)$/i' => '\1s',
            '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
            '/sis$/i' => 'ses',
            '/([ti])um$/i' => '\1a',
            '/(buffal|tomat)o$/i' => '\1oes',
            '/(bu)s$/i' => '\1ses',
            '/(alias|status)/i'=> '\1es',
            '/(octop|vir)us$/i'=> '\1i',
            '/(ax|test)is$/i'=> '\1es',
            '/s$/i'=> 's',
            '/$/'=> 's'
        ];

        $uncountableRules = ['equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep'];

        $irregularRules = [
            'person' => 'people',
            'man' => 'men',
            'child' => 'children',
            'sex' => 'sexes',
            'move' => 'moves'
        ];

        $lowercasedWord = strtolower($word);

        foreach ($uncountableRules as $uncountable) {
            if (substr($lowercasedWord, -1 * strlen($uncountable)) == $uncountable) {
                return $word;
            }
        }

        foreach ($irregularRules as $plural => $singular) {
            if (preg_match('/(' . $plural . ')$/i', $word, $arr)) {
                return preg_replace('/(' . $plural . ')$/i', substr($arr[0], 0, 1) . substr($singular, 1), $word);
            }
        }

        foreach ($pluralRules as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    /**
     * Singularizes English nouns.
     *
     * @param string $word English noun to singularize.
     *
     * @return string Singular noun.
     */
    public static function singularize(string $word): string {
        $singularRules = [
            '/(quiz)zes$/i' => '\1',
            '/(matr)ices$/i' => '\1ix',
            '/(vert|ind)ices$/i' => '\1ex',
            '/^(ox)en/i' => '\1',
            '/(alias|status)es$/i' => '\1',
            '/([octop|vir])i$/i' => '\1us',
            '/(cris|ax|test)es$/i' => '\1is',
            '/(shoe)s$/i' => '\1',
            '/(o)es$/i' => '\1',
            '/(bus)es$/i' => '\1',
            '/([m|l])ice$/i' => '\1ouse',
            '/(x|ch|ss|sh)es$/i' => '\1',
            '/(m)ovies$/i' => '\1ovie',
            '/(s)eries$/i' => '\1eries',
            '/([^aeiouy]|qu)ies$/i' => '\1y',
            '/([lr])ves$/i' => '\1f',
            '/(tive)s$/i' => '\1',
            '/(hive)s$/i' => '\1',
            '/([^f])ves$/i' => '\1fe',
            '/(^analy)ses$/i' => '\1sis',
            '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
            '/([ti])a$/i' => '\1um',
            '/(n)ews$/i' => '\1ews',
            '/s$/i' => ''
        ];

        $uncountableRules = ['equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep'];

        $irregularRules = [
            'person' => 'people',
            'man' => 'men',
            'child' => 'children',
            'sex' => 'sexes',
            'move' => 'moves'
        ];

        $lowercasedWord = strtolower($word);

        foreach ($uncountableRules as $uncountable) {
            if (substr($lowercasedWord, -1 * strlen($uncountable)) == $uncountable) {
                return $word;
            }
        }

        foreach ($irregularRules as $plural => $singular) {
            if (preg_match('/(' . $singular . ')$/i', $word, $arr)) {
                return preg_replace('/(' . $singular . ')$/i', substr($arr[0], 0, 1) . substr($plural, 1), $word);
            }
        }

        foreach ($singularRules as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }
}