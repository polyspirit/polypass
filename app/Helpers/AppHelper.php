<?php

if (!function_exists('vdd')) {
    function vdd(mixed $var): void
    {
        var_dump($var);
        die();
    }
}

if (!function_exists('jvdd')) {
    function jvdd(mixed $var): void
    {
        echo json_encode($var, JSON_UNESCAPED_UNICODE);
        die();
    }
}

if (!function_exists('camelCaseToUnderscore')) {
    function camelCaseToUnderscore(string $input)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $input)), '_');
    }
}

if (!function_exists('getUnderscoreClassName')) {
    function getUnderscoreClassName(string $class)
    {
        $classNameParts = explode('\\', $class);
        return camelCaseToUnderscore($classNameParts[count($classNameParts) - 1]);
    }
}

if (!function_exists('isAssociative')) {
    function isAssociative(array $inptArr)
    {
        if ([] === $inptArr) {
            return true;
        }

        if (array_keys($inptArr) !== range(0, count($inptArr) - 1)) {
            return true;
        }

        return false;
    }
}

