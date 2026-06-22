<?php

namespace Database\Seeders;

use App\Models\EmergencyServiceUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class EmergencyServiceUnitSeeder extends Seeder
{
    public function run(): void
    {
        $path = (string) config('kiosk.emergency.directory_data_path');

        if (! File::exists($path)) {
            return;
        }

        $units = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);

        foreach ($units as $unit) {
            EmergencyServiceUnit::updateOrCreate(
                ['unit_code' => $unit['unit_code']],
                $unit
            );
        }
    }
}
