<?php
if (! function_exists('array_remove_empty')) {
    function array_remove_empty(array $haystack) : array {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = array_remove_empty($haystack[$key]);
            }

            if (empty($haystack[$key])) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }
}
