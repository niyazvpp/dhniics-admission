<?php

namespace App\Helpers;

use App\Models\Institution;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Settings
{

    protected static $settings;

    protected static $cast_as_dates = [
        'starting_at' => 'Y-m-d',
        'ending_at' => 'Y-m-d',
        'results_starting_at' => 'Y-m-d',
        'results_ending_at' => 'Y-m-d',
        'date_of_birth_starting_at' => 'Y-m-d',
        'date_of_birth_ending_at' => 'Y-m-d',
    ];

    public static function get($key, $value = null, $date_format = null)
    {
        static::init();
        $value = static::$settings->{$key} ?? $value;
        if ($date_format) {
            $value = Carbon::parse($value)->format($date_format);
        }
        return $value;
    }

    public static function all()
    {
        static::init();
        return static::$settings;
    }

    public static function getFillables()
    {
        return array_keys((array) static::all());
    }

    public static function init()
    {
        if (!empty(static::$settings)) {
            return;
        }
        $institutions_count = 0;
        try {
            $institutions_count = Institution::count('id');
        } catch (\Throwable $th) {
        }
        $defaults = [
            'header' => 'NIICS Darul Huda',
            'site_name' => 'NIICS || Darul Huda Islamic University Kerala',
            'academic_year' =>  date('Y') . ' - ' . (date('Y') - 1999),
            'header_first_line' => 'DARUL HUDA ISLAMIC UNIVERSITY KERALA',
            'header_second_line' => 'NIICS KERALA',
            'address_and_contact' => "BHIMPUR, PAIKAR PS, BIBHUM DIST. WEST BENGAL, PIN 731219\nCONTACT NO: +919382641384, +917034015019, +919605536676",
            'starting_at' => today()->format('Y-m-d'),
            'ending_at' => today()->addDays(15)->format('Y-m-d'),
            'results_starting_at' => today()->addDays(16)->format('Y-m-d'),
            'results_ending_at' => today()->addDays(31)->format('Y-m-d'),
            'dob_starting_at' => today()->subYears(12)->format('Y-m-d'),
            'dob_ending_at' => today()->subYears(9)->format('Y-m-d'),
            'selectable_max' => min([5, $institutions_count]),
            'selectable_min' => 1,
            'admission_result_selected_template' => 'Congratulations, you are selected',
            'admission_result_not_selected_template' => 'Sorry, you are not selected',
        ];
        static::$settings = Cache::rememberForever('settings', function () use ($defaults, $institutions_count) {
            $settings = new \stdClass();
            try {
                $settings = (object) Setting::pluck('value', 'name')->toArray();
            } catch (\Throwable $th) {
            }
            // use from default if not found in database
            foreach ($defaults as $key => $value) {
                if (!isset($settings->{$key})) {
                    $settings->{$key} = $value;
                }
                if ($key == 'selectable_max' || $key == 'selectable_min') {
                    $settings->{$key} = min([$settings->{$key}, $institutions_count]);
                }
                if (!empty(static::$cast_as_dates[$key]) && $settings->{$key}) {
                    // convert to the castable item
                    $settings->{$key} = Carbon::parse($settings->{$key})->format(static::$cast_as_dates[$key]);
                }
            }
            return $settings;
        });
    }
}
