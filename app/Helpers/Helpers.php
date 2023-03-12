<?php

use App\Helpers\Settings;

if (!function_exists('settings')) {
    function settings($key = null, $value = null, $date_format = null)
    {
        if (is_null($key)) {
            return Settings::all();
        }
        return Settings::get($key, $value, $date_format);
    }
}

if (!function_exists('template_replace')) {
    function template_replace($subject, $replacements, $sub_item = null)
    {
        foreach ($replacements as $key => $value) {
            if ($key && $value) {
                if ($sub_item) {
                    $value = $value->{$sub_item};
                }
                $subject = str_replace($key, $value, $subject);
            } else {
                $subject = str_replace($key, '', $subject);
            }
        }
        return $subject;
    }
}
