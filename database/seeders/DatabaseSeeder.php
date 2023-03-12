<?php

namespace Database\Seeders;

use App\Models\ExamCentre;
use App\Models\Institution;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        User::create([
            'name' => 'Admin',
            'email' => 'dhniics@dhiu.in',
            'password' => Hash::make('Niics@175dhiu')
        ]);

        $exam_centres = [
            [
                'code' => 'CHA',
                'name' => 'Chatarpur, Jharkhand',
                'address' => '',
                'date_time' => '2023-03-26 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-03-26 17:00:00')->subDays(2)
            ],
            [
                'code' => 'ALH',
                'name' => 'Alahabad, Uttar Pradesh',
                'address' => '',
                'date_time' => '2023-03-28 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-03-28 17:00:00')->subDays(2)
            ],
            [
                'code' => 'GOR',
                'name' => 'Gorakhpur, Uttar Pradesh',
                'address' => '',
                'date_time' => '2023-03-30 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-03-30 17:00:00')->subDays(2)
            ],
            [
                'code' => 'SNG',
                'name' => 'Sangli, Maharashtra',
                'address' => '',
                'date_time' => '2023-03-30 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-03-30 17:00:00')->subDays(2)
            ],
            [
                'code' => 'SIT',
                'name' => 'Sitamarhi, Bihar',
                'address' => '',
                'date_time' => '2023-04-01 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-01 17:00:00')->subDays(2)
            ],
            [
                'code' => 'MUZ',
                'name' => 'Muzaffarpur, Bihar',
                'address' => '',
                'date_time' => '2023-04-02 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-02 17:00:00')->subDays(2)
            ],
            [
                'code' => 'BHI',
                'name' => 'Bhiwandi, Maharashtra',
                'address' => '',
                'date_time' => '2023-04-02 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-02 17:00:00')->subDays(2)
            ],
            [
                'code' => 'NAS',
                'name' => 'Nashik, Maharashtra',
                'address' => '',
                'date_time' => '2023-04-03 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-03 17:00:00')->subDays(2)
            ],
            [
                'code' => 'KIS',
                'name' => 'Kishanganj, Bihar',
                'address' => '',
                'date_time' => '2023-04-04 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-04 17:00:00')->subDays(2)
            ],
            [
                'code' => 'MAL',
                'name' => 'Malegaon, Maharashtra',
                'address' => '',
                'date_time' => '2023-04-04 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-04 17:00:00')->subDays(2)
            ],
            [
                'code' => 'AKO',
                'name' => 'Akola, Maharashtra',
                'address' => '',
                'date_time' => '2023-04-05 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-05 17:00:00')->subDays(2)
            ],
            [
                'code' => 'AMR',
                'name' => 'Amravati, Maharashtra',
                'address' => '',
                'date_time' => '2023-04-06 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-06 17:00:00')->subDays(2)
            ],
            [
                'code' => 'NAG',
                'name' => 'Nagpur, Maharashtra',
                'address' => '',
                'date_time' => '2023-04-08 09:00:00',
                'start_date' => today(),
                'end_date' => Carbon::parse('2023-04-08 17:00:00')->subDays(2)
            ],
            [
                'code' => 'UDY',
                'name' => 'Udaypur, Rajasthan',
                'address' => '',
                'date_time' => '2023-04-10 09:00:00',
                'start_date' => today(),
                'end_date' => CarboN::parse('2023-04-10 17:00:00')->subDays(2)
            ],
        ];

        foreach ($exam_centres as $exam_centre) {
            ExamCentre::create($exam_centre);
        }

        $institutions = [
            [
                'name' => 'DH Chemmad, Kerala',
                'address' => '',
                'code' => 'DH',
                'quota' => 100
            ],
            [
                'name' => 'DH Hangel, Karnataka',
                'address' => '',
                'code' => 'HA',
                'quota' => 100
            ],
            [
                'name' => 'DH Punganur, Andhra Pradesh',
                'address' => '',
                'code' => 'PU',
                'quota' => 100
            ],
            [
                'name' => 'Quwatul Islam, Mumbai, Maharashtra',
                'address' => '',
                'code' => 'QU',
                'quota' => 100
            ]
        ];

        foreach ($institutions as $institution) {
            Institution::create($institution);
        }
    }
}
