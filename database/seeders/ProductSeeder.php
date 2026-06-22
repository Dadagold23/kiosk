<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()
            ->where('type', 'product')
            ->get()
            ->keyBy('slug');

        foreach ($this->products() as $product) {
            $category = $categories->get($product['category_slug']);

            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'category_id' => $category->id,
                    'source_type' => 'local',
                    'source_marketplace' => null,
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name'] . '-' . $product['sku']),
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'sale_price' => $product['sale_price'],
                    'quantity' => $product['quantity'],
                    'image' => null,
                    'external_url' => null,
                    'featured' => $product['featured'] ?? false,
                    'status' => true,
                ]
            );
        }
    }

    private function products(): array
    {
        return [
            ['category_slug' => 'electronics', 'sku' => 'KSK-SPK-001', 'name' => 'JBL Portable Bluetooth Speaker', 'description' => 'Portable local-market speaker with strong bass response, rechargeable battery, and Bluetooth pairing for home or outdoor use.', 'price' => 28500, 'sale_price' => 25900, 'quantity' => 24, 'featured' => true],
            ['category_slug' => 'office-equipment', 'sku' => 'KSK-TRP-002', 'name' => 'Thermal Receipt Printer 80mm', 'description' => 'Fast thermal printer for POS desks, supermarket counters, and kiosk checkout operations.', 'price' => 99000, 'sale_price' => 94500, 'quantity' => 10, 'featured' => true],
            ['category_slug' => 'bags-and-backpacks', 'sku' => 'KSK-BAG-003', 'name' => 'Laptop Backpack 15.6-inch', 'description' => 'Padded backpack with laptop compartment, side pockets, and reinforced shoulder support for work and travel.', 'price' => 28500, 'sale_price' => 25900, 'quantity' => 18, 'featured' => true],
            ['category_slug' => 'watches-and-accessories', 'sku' => 'KSK-SFW-004', 'name' => 'Smart Watch Series 8 Pro', 'description' => 'Bluetooth smart watch with step tracking, call alerts, heart-rate monitoring, and multi-sport modes.', 'price' => 36500, 'sale_price' => 33900, 'quantity' => 14, 'featured' => true],
            ['category_slug' => 'home-appliances', 'sku' => 'KSK-UT-003', 'name' => 'Kitchen Utensil Set 24pcs', 'description' => 'Complete kitchen utensil set with ladles, spoons, spatulas, and serving tools for everyday cooking.', 'price' => 17500, 'sale_price' => 15900, 'quantity' => 22],
            ['category_slug' => 'home-appliances', 'sku' => 'KSK-RFS-004', 'name' => 'Double Door Refrigerator 138L', 'description' => 'Compact double-door refrigerator for household food storage, drinks, and chilled essentials.', 'price' => 325000, 'sale_price' => 309000, 'quantity' => 4, 'featured' => true],

            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-001', 'name' => 'Premium Parboiled Rice 50kg', 'description' => 'Clean premium parboiled rice suitable for large family use, events, and household restocking.', 'price' => 83500, 'sale_price' => 79900, 'quantity' => 65, 'featured' => true],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-002', 'name' => 'Foreign Rice 25kg', 'description' => 'Well-sorted imported rice bag ideal for regular household meals and fast cooking.', 'price' => 46800, 'sale_price' => 44900, 'quantity' => 42],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-003', 'name' => 'Honey Beans 10kg', 'description' => 'Nutritious honey beans packed for weekly household cooking and bulk food preparation.', 'price' => 24200, 'sale_price' => 22950, 'quantity' => 36],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-004', 'name' => 'White Garri 10kg', 'description' => 'Dry white garri for eba, drinking, and pantry restocking.', 'price' => 15800, 'sale_price' => null, 'quantity' => 44],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-005', 'name' => 'Semovita 5kg', 'description' => 'Family-size semolina swallow pack for daily meals and quick food prep.', 'price' => 9650, 'sale_price' => 9200, 'quantity' => 58],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-006', 'name' => 'Golden Penny Spaghetti Carton 20 x 500g', 'description' => 'Carton of spaghetti suitable for bulk household storage, canteens, and mini-marts.', 'price' => 20500, 'sale_price' => 19400, 'quantity' => 28],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-007', 'name' => 'Groundnut Oil 5L', 'description' => 'Refined cooking oil for frying, meal prep, and home kitchen restocking.', 'price' => 18200, 'sale_price' => 17500, 'quantity' => 31],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-008', 'name' => 'Palm Oil 5L', 'description' => 'Fresh red palm oil for soups, stews, and traditional cooking.', 'price' => 13500, 'sale_price' => null, 'quantity' => 39],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-009', 'name' => 'Frozen Chicken 10kg', 'description' => 'Wholesale-size frozen chicken pack suited for homes, events, and restaurants.', 'price' => 46500, 'sale_price' => 44900, 'quantity' => 22],
            ['category_slug' => 'raw-food-items', 'sku' => 'LOC-FOOD-010', 'name' => 'Turkey Wings 5kg', 'description' => 'Frozen turkey wings pack for soups, grills, and catering operations.', 'price' => 27800, 'sale_price' => null, 'quantity' => 18],

            ['category_slug' => 'baby-care-and-diapers', 'sku' => 'LOC-BABY-001', 'name' => 'Molfix Air Dry Size 3 50pcs', 'description' => 'Comfort-fit baby diaper pack with leak guard and soft inner lining for active babies.', 'price' => 22500, 'sale_price' => 20900, 'quantity' => 40, 'featured' => true],
            ['category_slug' => 'baby-care-and-diapers', 'sku' => 'LOC-BABY-002', 'name' => 'Huggies Dry Comfort Size 4 62pcs', 'description' => 'Absorbent diaper pack for toddlers with strong elastic fit and day-to-night comfort.', 'price' => 24300, 'sale_price' => 22900, 'quantity' => 34],
            ['category_slug' => 'baby-care-and-diapers', 'sku' => 'LOC-BABY-003', 'name' => 'Pampers Baby Dry Size 5 64pcs', 'description' => 'Large diaper pack designed for overnight use and longer dryness performance.', 'price' => 26400, 'sale_price' => 24900, 'quantity' => 28],
            ['category_slug' => 'baby-care-and-diapers', 'sku' => 'LOC-BABY-004', 'name' => 'Cussons Baby Wipes 80pcs Triple Pack', 'description' => 'Three-pack gentle wipes for diaper changes, skin cleaning, and travel use.', 'price' => 6500, 'sale_price' => 5800, 'quantity' => 72],
            ['category_slug' => 'baby-care-and-diapers', 'sku' => 'LOC-BABY-005', 'name' => 'Cerelac Wheat with Milk 1kg', 'description' => 'Infant cereal blend for balanced baby feeding and nursery restocking.', 'price' => 9800, 'sale_price' => 9300, 'quantity' => 27],
            ['category_slug' => 'baby-care-and-diapers', 'sku' => 'LOC-BABY-006', 'name' => 'Aptamil Infant Formula 900g', 'description' => 'Infant formula milk tin for newborn feeding support and baby nutrition.', 'price' => 29800, 'sale_price' => 28900, 'quantity' => 16],

            ['category_slug' => 'baby-items-and-accessories', 'sku' => 'LOC-BACC-001', 'name' => 'Baby Feeding Bottle Set 3pcs', 'description' => 'Feeding bottle starter set with anti-colic design and cleaning brush support.', 'price' => 8500, 'sale_price' => null, 'quantity' => 30],
            ['category_slug' => 'baby-items-and-accessories', 'sku' => 'LOC-BACC-002', 'name' => 'Foldable Baby Stroller', 'description' => 'Compact stroller with sun shade, safety harness, and travel-friendly fold system.', 'price' => 68500, 'sale_price' => 64900, 'quantity' => 9, 'featured' => true],
            ['category_slug' => 'baby-items-and-accessories', 'sku' => 'LOC-BACC-003', 'name' => 'Musical Baby Walker', 'description' => 'Walker with safety support, play tray, and sound features for supervised movement.', 'price' => 34500, 'sale_price' => 32900, 'quantity' => 12],
            ['category_slug' => 'baby-items-and-accessories', 'sku' => 'LOC-BACC-004', 'name' => 'Newborn Bodysuit Set 5pcs', 'description' => 'Soft cotton newborn bodysuit set suitable for daily wear and gifting.', 'price' => 12500, 'sale_price' => 11600, 'quantity' => 26],
            ['category_slug' => 'baby-items-and-accessories', 'sku' => 'LOC-BACC-005', 'name' => 'Baby Bath Tub and Potty Set', 'description' => 'Convenient bath and potty combo set for early infant care routines.', 'price' => 18900, 'sale_price' => 17500, 'quantity' => 18],

            ['category_slug' => 'fashion', 'sku' => 'LOC-FASH-001', 'name' => 'Men\'s Senator Wear Navy', 'description' => 'Complete senator wear set in navy finish for events, office, and smart casual dressing.', 'price' => 42000, 'sale_price' => 38900, 'quantity' => 15, 'featured' => true],
            ['category_slug' => 'fashion', 'sku' => 'LOC-FASH-002', 'name' => 'Women\'s Ankara Maxi Gown Emerald', 'description' => 'Tailored Ankara maxi gown with a polished fit for casual and occasion wear.', 'price' => 36500, 'sale_price' => 34500, 'quantity' => 13],
            ['category_slug' => 'fashion', 'sku' => 'LOC-FASH-003', 'name' => 'Unisex Cotton T-Shirt White', 'description' => 'Soft round-neck t-shirt for layering, casual wear, and everyday comfort.', 'price' => 8500, 'sale_price' => 7500, 'quantity' => 48],
            ['category_slug' => 'fashion', 'sku' => 'LOC-FASH-004', 'name' => 'Straight Cut Denim Jeans Blue', 'description' => 'Classic denim jeans with everyday fit for casual wardrobe rotation.', 'price' => 18500, 'sale_price' => 16900, 'quantity' => 29],
            ['category_slug' => 'fashion', 'sku' => 'LOC-FASH-005', 'name' => 'Corporate Long Sleeve Shirt Sky Blue', 'description' => 'Formal long-sleeve shirt suited for work, business meetings, and presentations.', 'price' => 14500, 'sale_price' => 12900, 'quantity' => 32],
            ['category_slug' => 'fashion', 'sku' => 'LOC-FASH-006', 'name' => 'Women\'s Two-Piece Lounge Set Beige', 'description' => 'Comfortable home-and-out lounge set with matching top and trousers.', 'price' => 22000, 'sale_price' => 19800, 'quantity' => 17],

            ['category_slug' => 'watches-and-accessories', 'sku' => 'LOC-WAT-001', 'name' => 'Curren Leather Strap Watch', 'description' => 'Classic leather strap wristwatch for daily formal and smart casual use.', 'price' => 19500, 'sale_price' => 17900, 'quantity' => 21],
            ['category_slug' => 'watches-and-accessories', 'sku' => 'LOC-WAT-002', 'name' => 'Skmei Digital Sports Watch', 'description' => 'Water-resistant digital sports watch with alarm, stopwatch, and night light.', 'price' => 16500, 'sale_price' => 14900, 'quantity' => 24],
            ['category_slug' => 'watches-and-accessories', 'sku' => 'LOC-WAT-003', 'name' => 'Binbond Chronograph Watch', 'description' => 'Fashion chronograph watch with metallic finish and statement dial styling.', 'price' => 24800, 'sale_price' => 22500, 'quantity' => 14],
            ['category_slug' => 'watches-and-accessories', 'sku' => 'LOC-WAT-004', 'name' => 'Smart Watch Ultra Fit', 'description' => 'Bluetooth smart watch with call alerts, fitness tracking, and bright display.', 'price' => 39800, 'sale_price' => 36900, 'quantity' => 12],

            ['category_slug' => 'bags-and-backpacks', 'sku' => 'LOC-BAG-001', 'name' => 'Travel Duffel Bag 22-inch', 'description' => 'Large-capacity duffel bag suitable for road trips, weekend stays, and gym use.', 'price' => 24800, 'sale_price' => 22900, 'quantity' => 19],
            ['category_slug' => 'bags-and-backpacks', 'sku' => 'LOC-BAG-002', 'name' => 'Junior School Backpack', 'description' => 'Durable school bag with water-bottle pockets and reinforced zip compartments.', 'price' => 14500, 'sale_price' => 13200, 'quantity' => 36],
            ['category_slug' => 'bags-and-backpacks', 'sku' => 'LOC-BAG-003', 'name' => 'Ladies Tote Handbag', 'description' => 'Structured tote handbag for work, shopping, and everyday personal carry.', 'price' => 19800, 'sale_price' => 17900, 'quantity' => 20],
            ['category_slug' => 'bags-and-backpacks', 'sku' => 'LOC-BAG-004', 'name' => 'Anti-Theft USB Backpack', 'description' => 'Modern backpack with hidden zips, device compartments, and external USB port.', 'price' => 32500, 'sale_price' => 29900, 'quantity' => 14],

            ['category_slug' => 'shoes-and-footwear', 'sku' => 'LOC-SHOE-001', 'name' => 'Men\'s Leather Loafers', 'description' => 'Smart leather loafers for formal dressing, office wear, and events.', 'price' => 32500, 'sale_price' => 29900, 'quantity' => 16, 'featured' => true],
            ['category_slug' => 'shoes-and-footwear', 'sku' => 'LOC-SHOE-002', 'name' => 'Women\'s Block Heel Sandals', 'description' => 'Elegant block heel sandals for office, outings, and occasion styling.', 'price' => 22800, 'sale_price' => 20900, 'quantity' => 18],
            ['category_slug' => 'shoes-and-footwear', 'sku' => 'LOC-SHOE-003', 'name' => 'Canvas Sneakers Unisex', 'description' => 'Comfort-fit sneakers for campus, street wear, and weekend movement.', 'price' => 26500, 'sale_price' => 23900, 'quantity' => 27],
            ['category_slug' => 'shoes-and-footwear', 'sku' => 'LOC-SHOE-004', 'name' => 'Children\'s School Shoes Black', 'description' => 'Uniform-ready children\'s school shoes with durable sole and secure fit.', 'price' => 18500, 'sale_price' => 17200, 'quantity' => 22],
            ['category_slug' => 'shoes-and-footwear', 'sku' => 'LOC-SHOE-005', 'name' => 'Running Trainers Pro Mesh', 'description' => 'Breathable running shoes with cushioned sole for walking, gym, and fitness use.', 'price' => 34800, 'sale_price' => 31900, 'quantity' => 13],

            ['category_slug' => 'phones-and-tablets', 'sku' => 'LOC-PHONE-001', 'name' => 'Tecno Spark 20 128GB', 'description' => 'Affordable Android smartphone with 128GB storage, good battery life, and crisp display.', 'price' => 159500, 'sale_price' => 154500, 'quantity' => 11, 'featured' => true],
            ['category_slug' => 'phones-and-tablets', 'sku' => 'LOC-PHONE-002', 'name' => 'Infinix Hot 40i 256GB', 'description' => 'Large-storage smartphone suited for social media, apps, and everyday usage.', 'price' => 174500, 'sale_price' => 169500, 'quantity' => 9],
            ['category_slug' => 'phones-and-tablets', 'sku' => 'LOC-PHONE-003', 'name' => 'Samsung Galaxy A15 128GB', 'description' => 'Mid-range Samsung phone with bright AMOLED display and dependable daily performance.', 'price' => 265000, 'sale_price' => 254500, 'quantity' => 8, 'featured' => true],
            ['category_slug' => 'phones-and-tablets', 'sku' => 'LOC-PHONE-004', 'name' => 'Redmi 13C 128GB', 'description' => 'Value Android phone with smooth performance, long battery life, and practical camera setup.', 'price' => 189000, 'sale_price' => 179900, 'quantity' => 10],
            ['category_slug' => 'phones-and-tablets', 'sku' => 'LOC-PHONE-005', 'name' => 'itel A70 128GB', 'description' => 'Entry-level smartphone with large display and enough storage for everyday use.', 'price' => 129500, 'sale_price' => 124500, 'quantity' => 12],
            ['category_slug' => 'phones-and-tablets', 'sku' => 'LOC-PHONE-006', 'name' => 'Lenovo Tab M8 32GB', 'description' => 'Compact Android tablet ideal for reading, streaming, online classes, and light work.', 'price' => 149000, 'sale_price' => 142500, 'quantity' => 7],

            ['category_slug' => 'electronics', 'sku' => 'LOC-ELE-001', 'name' => 'Hisense 32-inch Smart TV', 'description' => 'Compact smart television with streaming support and sharp HD display for living spaces.', 'price' => 185000, 'sale_price' => 176500, 'quantity' => 6, 'featured' => true],
            ['category_slug' => 'electronics', 'sku' => 'LOC-ELE-002', 'name' => 'Oraimo FreePods 4', 'description' => 'Wireless earbuds with charging case, clear microphone pickup, and everyday portability.', 'price' => 26500, 'sale_price' => 23900, 'quantity' => 26],
            ['category_slug' => 'electronics', 'sku' => 'LOC-ELE-003', 'name' => 'Nexus 2.1 Home Theatre', 'description' => 'Home audio set with subwoofer and dual speakers for fuller in-room sound.', 'price' => 128000, 'sale_price' => 119500, 'quantity' => 8],
            ['category_slug' => 'electronics', 'sku' => 'LOC-ELE-004', 'name' => 'Oraimo Power Bank 20000mAh', 'description' => 'High-capacity power bank with dual output ports for phones and accessories.', 'price' => 22500, 'sale_price' => 19900, 'quantity' => 33],
            ['category_slug' => 'electronics', 'sku' => 'LOC-ELE-005', 'name' => '18-inch LED Ring Light Kit', 'description' => 'Studio-style ring light set for content creation, product shoots, and live sessions.', 'price' => 38500, 'sale_price' => 35900, 'quantity' => 12],
            ['category_slug' => 'electronics', 'sku' => 'LOC-ELE-006', 'name' => 'Wireless Game Pad Dual Pack', 'description' => 'Twin gamepad set for smart TV gaming, emulator setups, and family entertainment.', 'price' => 29500, 'sale_price' => 27900, 'quantity' => 11],

            ['category_slug' => 'home-appliances', 'sku' => 'LOC-HAP-001', 'name' => 'Scanfrost Microwave Oven 20L', 'description' => 'Countertop microwave for quick reheating, defrosting, and light cooking tasks.', 'price' => 102000, 'sale_price' => 96500, 'quantity' => 9, 'featured' => true],
            ['category_slug' => 'home-appliances', 'sku' => 'LOC-HAP-002', 'name' => 'Binatone Blender 1.5L', 'description' => 'Kitchen blender with durable jar and enough capacity for smoothies and sauces.', 'price' => 32500, 'sale_price' => 29900, 'quantity' => 17],
            ['category_slug' => 'home-appliances', 'sku' => 'LOC-HAP-003', 'name' => 'Qasa Standing Fan 18-inch', 'description' => 'Adjustable standing fan for cooling bedrooms, shops, and offices.', 'price' => 38500, 'sale_price' => 35900, 'quantity' => 16],
            ['category_slug' => 'home-appliances', 'sku' => 'LOC-HAP-004', 'name' => 'Century Electric Kettle 2L', 'description' => 'Fast-boil electric kettle for tea, coffee, and quick hot water preparation.', 'price' => 19800, 'sale_price' => 17900, 'quantity' => 23],
            ['category_slug' => 'home-appliances', 'sku' => 'LOC-HAP-005', 'name' => 'Philips Dry Iron 1200W', 'description' => 'Reliable dry iron for home pressing and garment finishing.', 'price' => 19500, 'sale_price' => 18200, 'quantity' => 20],
            ['category_slug' => 'home-appliances', 'sku' => 'LOC-HAP-006', 'name' => 'Hisense Refrigerator 93L', 'description' => 'Compact refrigerator for home, office, and mini-store cold storage needs.', 'price' => 248000, 'sale_price' => 235000, 'quantity' => 5],
            ['category_slug' => 'home-appliances', 'sku' => 'LOC-HAP-007', 'name' => 'Silver Crest Air Fryer 6L', 'description' => 'Healthy cooking air fryer with roomy basket for family portions.', 'price' => 89500, 'sale_price' => 84900, 'quantity' => 9],

            ['category_slug' => 'computing-and-accessories', 'sku' => 'LOC-COMP-001', 'name' => 'HP 240 G8 Laptop 8GB/256GB', 'description' => 'Business-ready laptop with SSD storage for office work, school, and online productivity.', 'price' => 445000, 'sale_price' => 429000, 'quantity' => 6, 'featured' => true],
            ['category_slug' => 'computing-and-accessories', 'sku' => 'LOC-COMP-002', 'name' => 'Lenovo IdeaPad 1 4GB/128GB', 'description' => 'Entry-level laptop suited for browsing, online classes, typing, and streaming.', 'price' => 318000, 'sale_price' => 305000, 'quantity' => 7],
            ['category_slug' => 'computing-and-accessories', 'sku' => 'LOC-COMP-003', 'name' => 'Logitech Wireless Mouse M185', 'description' => 'Compact wireless mouse for laptops, office desks, and portable work setups.', 'price' => 12500, 'sale_price' => 10900, 'quantity' => 34],
            ['category_slug' => 'computing-and-accessories', 'sku' => 'LOC-COMP-004', 'name' => 'USB-C Fast Charger 25W', 'description' => 'Fast charging adapter compatible with modern phones, tablets, and accessories.', 'price' => 11500, 'sale_price' => 9900, 'quantity' => 41],
            ['category_slug' => 'computing-and-accessories', 'sku' => 'LOC-COMP-005', 'name' => 'Mechanical Keyboard RGB', 'description' => 'Tactile keyboard with RGB lighting for gaming, coding, and office use.', 'price' => 36500, 'sale_price' => 33900, 'quantity' => 13],
            ['category_slug' => 'computing-and-accessories', 'sku' => 'LOC-COMP-006', 'name' => 'Laptop Sleeve 15.6-inch', 'description' => 'Protective laptop sleeve for daily commute, office carry, and travel packing.', 'price' => 9800, 'sale_price' => 8500, 'quantity' => 29],

            ['category_slug' => 'office-equipment', 'sku' => 'LOC-OFF-001', 'name' => 'Barcode Scanner USB', 'description' => 'Plug-and-play barcode scanner for stock control, supermarket checkout, and POS desks.', 'price' => 28500, 'sale_price' => 25900, 'quantity' => 18],
            ['category_slug' => 'office-equipment', 'sku' => 'LOC-OFF-002', 'name' => 'A4 Copy Paper Carton 5 Reams', 'description' => 'Office-use A4 paper carton for printing, photocopying, and administrative work.', 'price' => 27500, 'sale_price' => 25900, 'quantity' => 25],
            ['category_slug' => 'office-equipment', 'sku' => 'LOC-OFF-003', 'name' => 'Laminating Machine A4', 'description' => 'Desktop laminator for ID cards, certificates, menus, and office documents.', 'price' => 38500, 'sale_price' => 35900, 'quantity' => 11],
            ['category_slug' => 'office-equipment', 'sku' => 'LOC-OFF-004', 'name' => 'Cash Drawer Heavy Duty', 'description' => 'Durable cash drawer with multiple note compartments for retail checkout counters.', 'price' => 45500, 'sale_price' => 42900, 'quantity' => 9],
            ['category_slug' => 'office-equipment', 'sku' => 'LOC-OFF-005', 'name' => 'Thermal Receipt Paper Box 50 Rolls', 'description' => 'Bulk receipt paper box for supermarkets, kiosks, restaurants, and POS operations.', 'price' => 24800, 'sale_price' => 22900, 'quantity' => 22],
        ];
    }
}
