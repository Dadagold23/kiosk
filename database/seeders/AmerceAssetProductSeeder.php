<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AmerceAssetProductSeeder extends Seeder
{
    private const IMPORT_LIMIT = 79;

    public function run(): void
    {
        $root = public_path('assets/images/product');

        if (! is_dir($root)) {
            $this->command?->warn('Amerce product image directory was not found. Skipping asset import.');

            return;
        }

        $categories = Category::query()
            ->where('type', 'product')
            ->get()
            ->keyBy('slug');

        $mappedAssets = $this->mappedAssets($root);
        $selectedAssets = $this->roundRobinAssets($mappedAssets, self::IMPORT_LIMIT);

        $created = 0;
        $updated = 0;

        foreach ($selectedAssets as $index => $asset) {
            $category = $categories->get($asset['category_slug']);

            if (! $category) {
                continue;
            }

            $payload = $this->payloadForAsset($asset, $category->id, $index);
            $exists = Product::query()->where('sku', $payload['sku'])->exists();

            Product::updateOrCreate(
                ['sku' => $payload['sku']],
                $payload
            );

            if ($exists) {
                $updated++;
            } else {
                $created++;
            }
        }

        $this->command?->info("Amerce asset catalog sync complete. Created {$created}, updated {$updated}.");
    }

    private function mappedAssets(string $root): array
    {
        $map = $this->folderMap();
        $grouped = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
            $parts = explode('/', $relative);
            $bucket = count($parts) > 1 ? $parts[0] : '_root';

            if (! isset($map[$bucket]) || ! $this->isCatalogCandidate($parts)) {
                continue;
            }

            $grouped[$bucket][] = [
                'relative_path' => $relative,
                'folder' => $bucket,
                'category_slug' => $map[$bucket]['category_slug'],
                'label' => $map[$bucket]['label'],
            ];
        }

        foreach ($grouped as &$items) {
            usort($items, fn (array $a, array $b) => strcmp($a['relative_path'], $b['relative_path']));
        }

        return $grouped;
    }

    private function roundRobinAssets(array $grouped, int $limit): array
    {
        $selected = [];
        $keys = array_values(array_keys($grouped));

        while (count($selected) < $limit) {
            $progressed = false;

            foreach ($keys as $key) {
                if (empty($grouped[$key])) {
                    continue;
                }

                $selected[] = array_shift($grouped[$key]);
                $progressed = true;

                if (count($selected) >= $limit) {
                    break;
                }
            }

            if (! $progressed) {
                break;
            }
        }

        return $selected;
    }

    private function payloadForAsset(array $asset, int $categoryId, int $index): array
    {
        $seed = abs(crc32($asset['relative_path']));
        $price = $this->priceForAsset($asset, $seed);
        $salePrice = $this->salePriceFor($price, $seed);
        $quantity = 6 + ($seed % 25);
        $name = $this->nameForAsset($asset, $seed, $index);
        $sku = 'AMERCE-' . strtoupper(substr(md5($asset['relative_path']), 0, 10));

        return [
            'category_id' => $categoryId,
            'source_type' => 'local',
            'source_marketplace' => 'Amerce Asset Catalog',
            'name' => $name,
            'slug' => Str::slug($name . '-' . strtolower($sku)),
            'description' => $this->descriptionFor($asset['label'], $asset['category_slug']),
            'sku' => $sku,
            'price' => $price,
            'sale_price' => $salePrice < $price ? $salePrice : null,
            'quantity' => $quantity,
            'image' => 'assets/images/product/' . $asset['relative_path'],
            'external_url' => null,
            'featured' => $index < 12,
            'status' => true,
        ];
    }

    private function extractOrdinal(string $relativePath, int $fallback): string
    {
        if (preg_match('/product-(\d+)/i', basename($relativePath), $matches)) {
            return str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        }

        return str_pad((string) $fallback, 2, '0', STR_PAD_LEFT);
    }

    private function nameForAsset(array $asset, int $seed, int $index): string
    {
        $ordinal = $this->extractOrdinal($asset['relative_path'], $index + 1);
        $descriptor = $this->descriptorFor($asset['category_slug'], $seed);
        $collection = $this->collectionFor($asset['folder'], $seed);

        return trim("{$descriptor} {$collection} {$ordinal}");
    }

    private function descriptorFor(string $categorySlug, int $seed): string
    {
        $sets = [
            'fashion' => ['Tailored', 'Signature', 'Modern', 'City', 'Weekend', 'Essential'],
            'bags-and-backpacks' => ['Transit', 'Carryall', 'Metro', 'Daily', 'Voyager', 'Structured'],
            'electronics' => ['Smart', 'Studio', 'Portable', 'Connected', 'Performance', 'Everyday'],
            'home-appliances' => ['Comfort', 'Living', 'Home', 'Utility', 'Domestic', 'Practical'],
            'watches-and-accessories' => ['Classic', 'Polished', 'Refined', 'Statement', 'Signature', 'Modern'],
            'office-equipment' => ['Desk', 'Workflow', 'Pro', 'Business', 'Office', 'Operations'],
            'raw-food-items' => ['Fresh', 'Pantry', 'Harvest', 'Kitchen', 'Daily', 'Household'],
            'shoes-and-footwear' => ['Stride', 'Active', 'Urban', 'Trail', 'Motion', 'Everyday'],
            'baby-care-and-diapers' => ['Gentle', 'Infant', 'Nursery', 'Daily', 'Soft', 'Care'],
            'baby-items-and-accessories' => ['Little', 'Nursery', 'Early', 'Growing', 'Care', 'Junior'],
            'computing-and-accessories' => ['Digital', 'Compute', 'Work', 'Power', 'Connected', 'Portable'],
            'phones-and-tablets' => ['Mobile', 'Touch', 'Smart', 'Pocket', 'Connected', 'Travel'],
        ];

        $options = $sets[$categorySlug] ?? ['Curated', 'Kiosk', 'Featured', 'Select', 'Modern', 'Essential'];

        return $options[$seed % count($options)];
    }

    private function collectionFor(string $folder, int $seed): string
    {
        $sets = [
            '_root' => ['Catalog Piece', 'Storefront Edit', 'Collection Piece'],
            'auto' => ['Drive Series', 'Motion Series', 'Auto Line'],
            'baby' => ['Baby Series', 'Nursery Line', 'Little Ones Edit'],
            'bag' => ['Carry Line', 'Transit Edit', 'Travel Series'],
            'construction' => ['Utility Series', 'Project Line', 'Build Edit'],
            'decor' => ['Home Edit', 'Interior Series', 'Living Line'],
            'electronics' => ['Tech Series', 'Device Edit', 'Digital Line'],
            'fashion-2' => ['Wardrobe Edit', 'Style Series', 'Closet Line'],
            'fashion-3' => ['Street Edit', 'Signature Series', 'Daily Line'],
            'furniture' => ['Living Series', 'Home Form Line', 'Comfort Edit'],
            'garden' => ['Outdoor Series', 'Garden Line', 'Patio Edit'],
            'headphone' => ['Audio Series', 'Sound Line', 'Studio Edit'],
            'jewellry' => ['Accessory Series', 'Accent Line', 'Finishing Edit'],
            'office' => ['Office Series', 'Desk Line', 'Workflow Edit'],
            'organic' => ['Pantry Series', 'Kitchen Line', 'Harvest Edit'],
            'sport' => ['Motion Series', 'Active Line', 'Performance Edit'],
        ];

        $options = $sets[$folder] ?? ['Collection', 'Series', 'Edit'];

        return $options[$seed % count($options)];
    }

    private function descriptionFor(string $label, string $categorySlug): string
    {
        $categoryContext = match ($categorySlug) {
            'fashion' => 'wardrobe rotation, casual styling, and everyday presentation',
            'bags-and-backpacks' => 'commute, storage, and travel movement',
            'electronics' => 'daily use, media, and device support',
            'home-appliances' => 'home setup, comfort, and practical living',
            'watches-and-accessories' => 'accessory styling and gift-ready presentation',
            'office-equipment' => 'workstation use, operations, and office support',
            'raw-food-items' => 'pantry restocking and household essentials',
            'shoes-and-footwear' => 'active use, outings, and regular movement',
            'baby-care-and-diapers' => 'infant care and daily baby needs',
            'baby-items-and-accessories' => 'nursery routines and child support',
            default => 'everyday catalog browsing and customer discovery',
        };

        return "{$label} curated from the Amerce image library and adapted into the Kiosk catalog for {$categoryContext}.";
    }

    private function priceForAsset(array $asset, int $seed): int
    {
        [$min, $max, $step] = match ($asset['category_slug']) {
            'fashion' => [16500, 38500, 1000],
            'bags-and-backpacks' => [21500, 46500, 1250],
            'electronics' => [28500, 98500, 2500],
            'home-appliances' => [32500, 145000, 2500],
            'watches-and-accessories' => [14500, 42500, 1000],
            'office-equipment' => [19500, 74500, 1500],
            'raw-food-items' => [8500, 36500, 1000],
            'shoes-and-footwear' => [18500, 42500, 1000],
            'baby-care-and-diapers' => [12500, 34500, 1000],
            'baby-items-and-accessories' => [14500, 52500, 1250],
            default => [15900, 45900, 1000],
        };

        $rangeSlots = max(1, intdiv($max - $min, $step));

        return $min + (($seed % ($rangeSlots + 1)) * $step);
    }

    private function salePriceFor(int $price, int $seed): ?int
    {
        if (($seed % 5) === 0) {
            return null;
        }

        $discount = match ($seed % 4) {
            0 => 500,
            1 => 1000,
            2 => 1500,
            default => 2000,
        };

        return max($price - $discount, 1000);
    }

    private function isCatalogCandidate(array $parts): bool
    {
        $ignoredDirectories = ['single', 'square', 'img_square', 'no-bg'];

        foreach ($parts as $part) {
            if (in_array(strtolower($part), $ignoredDirectories, true)) {
                return false;
            }
        }

        $filename = strtolower(end($parts));

        if (! preg_match('/^product-\d+\.(jpg|jpeg|png|webp)$/', $filename)) {
            return false;
        }

        return true;
    }

    private function folderMap(): array
    {
        return [
            '_root' => ['category_slug' => 'fashion', 'label' => 'Curated Catalog Piece'],
            'auto' => ['category_slug' => 'electronics', 'label' => 'Auto Tech Pick'],
            'baby' => ['category_slug' => 'baby-items-and-accessories', 'label' => 'Baby Essentials Pick'],
            'bag' => ['category_slug' => 'bags-and-backpacks', 'label' => 'Bag Collection Pick'],
            'construction' => ['category_slug' => 'home-appliances', 'label' => 'Utility Supply Pick'],
            'decor' => ['category_slug' => 'home-appliances', 'label' => 'Home Decor Pick'],
            'electronics' => ['category_slug' => 'electronics', 'label' => 'Electronics Select'],
            'fashion-2' => ['category_slug' => 'fashion', 'label' => 'Fashion Edit'],
            'fashion-3' => ['category_slug' => 'fashion', 'label' => 'Wardrobe Select'],
            'furniture' => ['category_slug' => 'home-appliances', 'label' => 'Home Living Pick'],
            'garden' => ['category_slug' => 'home-appliances', 'label' => 'Outdoor Living Pick'],
            'headphone' => ['category_slug' => 'electronics', 'label' => 'Audio Gear Pick'],
            'jewellry' => ['category_slug' => 'watches-and-accessories', 'label' => 'Accessory Edit'],
            'office' => ['category_slug' => 'office-equipment', 'label' => 'Office Supply Pick'],
            'organic' => ['category_slug' => 'raw-food-items', 'label' => 'Organic Pantry Pick'],
            'sport' => ['category_slug' => 'shoes-and-footwear', 'label' => 'Sport Motion Pick'],
        ];
    }
}
