<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['type' => 'product', 'name' => 'Raw Food Items', 'description' => 'Local staples, grains, oils, frozen foods, and household food stock.'],
            ['type' => 'product', 'name' => 'Baby Care and Diapers', 'description' => 'Baby diapers, wipes, formula, cereals, and infant care essentials.'],
            ['type' => 'product', 'name' => 'Baby Items and Accessories', 'description' => 'Baby strollers, walkers, feeding sets, clothing, and nursery essentials.'],
            ['type' => 'product', 'name' => 'Phones and Tablets', 'description' => 'Smartphones, tablets, and everyday mobile devices for local purchase.'],
            ['type' => 'product', 'name' => 'Electronics', 'description' => 'Speakers, earbuds, TVs, power banks, and general electronic gadgets.'],
            ['type' => 'product', 'name' => 'Home Appliances', 'description' => 'Kitchen, cooling, and home convenience appliances for daily use.'],
            ['type' => 'product', 'name' => 'Computing and Accessories', 'description' => 'Laptops, chargers, keyboards, mice, and computing accessories.'],
            ['type' => 'product', 'name' => 'Fashion', 'description' => 'Clothing, ready-to-wear items, and everyday fashion pieces.'],
            ['type' => 'product', 'name' => 'Watches and Accessories', 'description' => 'Classic watches, smart watches, and wrist accessories.'],
            ['type' => 'product', 'name' => 'Bags and Backpacks', 'description' => 'Travel bags, handbags, school bags, and laptop backpacks.'],
            ['type' => 'product', 'name' => 'Shoes and Footwear', 'description' => 'Sneakers, sandals, school shoes, loafers, and everyday footwear.'],
            ['type' => 'product', 'name' => 'Office Equipment', 'description' => 'Thermal printers, scanners, office paper, and business workstation items.'],

            ['type' => 'service', 'name' => 'Mechanical Engineering', 'description' => 'Mechanical installation, repairs, and maintenance services.'],
            ['type' => 'service', 'name' => 'Technical Engineering', 'description' => 'Technical field support, diagnostics, and engineering operations.'],
            ['type' => 'service', 'name' => 'Plumbing', 'description' => 'Pipework, water systems, fittings, and plumbing repairs.'],
            ['type' => 'service', 'name' => 'Painting', 'description' => 'Residential and commercial surface finishing and painting services.'],
            ['type' => 'service', 'name' => 'Bricklaying', 'description' => 'Block work, masonry finishing, and structural repair support.'],
            ['type' => 'service', 'name' => 'Electrical Installation', 'description' => 'Electrical setup, rewiring, and power system maintenance.'],
            ['type' => 'service', 'name' => 'General Repairs', 'description' => 'Broad handyman, maintenance, and facility repair services.'],

            ['type' => 'consultancy', 'name' => 'Legal Consultancy', 'description' => 'Legal guidance, compliance support, and case advisory services.'],
            ['type' => 'consultancy', 'name' => 'Educational Consultancy', 'description' => 'Academic placement, admissions, and training guidance.'],
            ['type' => 'consultancy', 'name' => 'Business Consultancy', 'description' => 'Business setup, operations planning, and growth advisory services.'],
            ['type' => 'consultancy', 'name' => 'Travel Guidance', 'description' => 'Travel planning, visa support guidance, and logistics advisory.'],
            ['type' => 'consultancy', 'name' => 'Police Liaison', 'description' => 'Guided liaison support for documentation and lawful process navigation.'],
            ['type' => 'consultancy', 'name' => 'Legal Adviser', 'description' => 'Ongoing professional legal advice and document review support.'],
            ['type' => 'consultancy', 'name' => 'Lawyer', 'description' => 'Direct access channel for managed legal professional consultation.'],

            ['type' => 'booking', 'name' => 'Hotels', 'description' => 'Managed hotel reservation requests and stay support.'],
            ['type' => 'booking', 'name' => 'Resorts', 'description' => 'Resort booking coordination and leisure stay planning.'],
            ['type' => 'booking', 'name' => 'Lounges', 'description' => 'Lounge reservation assistance and premium access handling.'],
            ['type' => 'booking', 'name' => 'Parks', 'description' => 'Park booking support for family, group, and event visits.'],
            ['type' => 'booking', 'name' => 'Flights', 'description' => 'Flight reservation requests, routing support, and travel booking assistance.'],
        ];

        foreach ($categories as $row) {
            Category::updateOrCreate(
                ['slug' => Str::slug($row['name'])],
                [
                    'type' => $row['type'],
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'status' => true,
                ]
            );
        }
    }
}
