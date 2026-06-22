<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AmerceDisplayAssetProductSeeder extends Seeder
{
    private const IMPORT_LIMIT = 60;

    private const SOURCE = 'Amerce Display Library';

    public function run(): void
    {
        $root = public_path('assets/images');

        if (! is_dir($root)) {
            $this->command?->warn('Display image directory was not found. Skipping display asset import.');

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

        $this->command?->info("Display asset catalog sync complete. Created {$created}, updated {$updated}.");
    }

    private function mappedAssets(string $root): array
    {
        $folders = ['catalog', 'collection', 'gallery', 'item', 'section', 'slider'];
        $grouped = [];

        foreach ($folders as $folder) {
            $directory = $root . DIRECTORY_SEPARATOR . $folder;

            if (! is_dir($directory)) {
                continue;
            }

            $files = collect(File::files($directory))
                ->filter(fn ($file) => $this->isSupportedImage($file->getExtension()))
                ->filter(fn ($file) => $this->isDisplayAssetCandidate($folder, $file->getFilename()))
                ->sortBy(fn ($file) => strtolower($file->getFilename()), SORT_NATURAL)
                ->values();

            foreach ($files as $index => $file) {
                $filename = $file->getFilename();
                $categorySlug = $this->categorySlugFor($folder, $filename, $index);

                if (! $categorySlug) {
                    continue;
                }

                $grouped[$folder][] = [
                    'folder' => $folder,
                    'relative_path' => $folder . '/' . $filename,
                    'filename' => $filename,
                    'label' => $this->labelFor($filename),
                    'category_slug' => $categorySlug,
                ];
            }
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
        $price = $this->priceForAsset($asset['category_slug'], $seed);
        $salePrice = $this->salePriceFor($price, $seed);
        $name = $this->nameForAsset($asset, $index, $seed);
        $sku = 'DISPLAY-' . strtoupper(substr(md5($asset['relative_path']), 0, 10));

        return [
            'category_id' => $categoryId,
            'source_type' => 'local',
            'source_marketplace' => self::SOURCE,
            'name' => $name,
            'slug' => Str::slug($name . '-' . strtolower($sku)),
            'description' => $this->descriptionFor($asset),
            'sku' => $sku,
            'price' => $price,
            'sale_price' => $salePrice < $price ? $salePrice : null,
            'quantity' => 5 + ($seed % 18),
            'image' => 'assets/images/' . $asset['relative_path'],
            'external_url' => null,
            'featured' => $index < 10,
            'status' => true,
        ];
    }

    private function categorySlugFor(string $folder, string $filename, int $index): ?string
    {
        $name = strtolower(pathinfo($filename, PATHINFO_FILENAME));

        if ($folder === 'catalog') {
            return [
                'baby-care' => 'baby-care-and-diapers',
                'bag-luggage' => 'bags-and-backpacks',
                'computing-tools' => 'computing-and-accessories',
                'fashion-apparel' => 'fashion',
                'footwear-style' => 'shoes-and-footwear',
                'grocery-staples' => 'raw-food-items',
                'home-appliance' => 'home-appliances',
                'office-tools' => 'office-equipment',
                'tech-device' => 'electronics',
                'tech-phone' => 'phones-and-tablets',
                'watch-accessory' => 'watches-and-accessories',
            ][$name] ?? 'fashion';
        }

        if ($folder === 'item') {
            return match (true) {
                str_contains($name, 'shoe') => 'shoes-and-footwear',
                str_contains($name, 'garden') => 'home-appliances',
                str_contains($name, 'graphic') => 'office-equipment',
                str_contains($name, 'pet') => 'home-appliances',
                default => 'fashion',
            };
        }

        $rotations = [
            'collection' => ['fashion', 'bags-and-backpacks', 'home-appliances', 'watches-and-accessories'],
            'gallery' => ['fashion', 'electronics', 'home-appliances', 'bags-and-backpacks', 'shoes-and-footwear'],
            'section' => ['fashion', 'home-appliances', 'electronics', 'office-equipment'],
            'slider' => ['fashion', 'bags-and-backpacks', 'electronics', 'home-appliances'],
        ];

        $options = $rotations[$folder] ?? ['fashion'];

        return $options[$index % count($options)];
    }

    private function labelFor(string $filename): string
    {
        return Str::of(pathinfo($filename, PATHINFO_FILENAME))
            ->replace(['-', '_'], ' ')
            ->title()
            ->value();
    }

    private function nameForAsset(array $asset, int $index, int $seed): string
    {
        $ordinal = str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);

        $prefix = match ($asset['folder']) {
            'catalog' => 'Category Pick',
            'collection' => 'Collection Select',
            'gallery' => 'Gallery Choice',
            'item' => 'Featured Item',
            'section' => 'Store Highlight',
            'slider' => 'Slider Pick',
            default => 'Catalog Pick',
        };

        $suffixes = [
            'fashion' => ['Style Edit', 'Wardrobe Pick', 'Daily Select'],
            'bags-and-backpacks' => ['Carry Pick', 'Travel Select', 'Transit Edit'],
            'electronics' => ['Tech Select', 'Device Pick', 'Digital Edit'],
            'home-appliances' => ['Home Pick', 'Living Select', 'Comfort Edit'],
            'office-equipment' => ['Desk Pick', 'Office Select', 'Work Edit'],
            'phones-and-tablets' => ['Mobile Pick', 'Phone Select', 'Pocket Edit'],
            'shoes-and-footwear' => ['Footwear Pick', 'Stride Select', 'Daily Pair'],
            'watches-and-accessories' => ['Accessory Pick', 'Timepiece Select', 'Finishing Edit'],
            'raw-food-items' => ['Pantry Pick', 'Kitchen Select', 'Household Edit'],
            'computing-and-accessories' => ['Compute Pick', 'Workstation Select', 'Digital Gear'],
            'baby-care-and-diapers' => ['Care Pick', 'Baby Select', 'Nursery Edit'],
        ];

        $categorySuffixes = $suffixes[$asset['category_slug']] ?? ['Store Select', 'Catalog Pick', 'Display Edit'];
        $suffix = $categorySuffixes[$seed % count($categorySuffixes)];

        return "{$prefix} {$suffix} {$ordinal}";
    }

    private function descriptionFor(array $asset): string
    {
        $folderCopy = match ($asset['folder']) {
            'catalog' => 'category icon set',
            'collection' => 'collection image set',
            'gallery' => 'gallery image set',
            'item' => 'featured item set',
            'section' => 'section banner library',
            'slider' => 'slider library',
            default => 'display library',
        };

        return "{$asset['label']} adapted from the Kiosk {$folderCopy} and added to the catalog to give the storefront more product variety.";
    }

    private function priceForAsset(string $categorySlug, int $seed): int
    {
        [$min, $max, $step] = match ($categorySlug) {
            'fashion' => [14500, 36500, 1000],
            'bags-and-backpacks' => [18500, 49500, 1250],
            'electronics' => [25500, 92500, 2500],
            'home-appliances' => [29500, 135000, 2500],
            'office-equipment' => [18500, 64500, 1500],
            'phones-and-tablets' => [42500, 118000, 2500],
            'shoes-and-footwear' => [16500, 39500, 1000],
            'watches-and-accessories' => [12500, 32500, 1000],
            'raw-food-items' => [7500, 28500, 1000],
            'computing-and-accessories' => [22500, 84500, 2000],
            'baby-care-and-diapers' => [11500, 29500, 1000],
            default => [15900, 45900, 1000],
        };

        $rangeSlots = max(1, intdiv($max - $min, $step));

        return $min + (($seed % ($rangeSlots + 1)) * $step);
    }

    private function salePriceFor(int $price, int $seed): ?int
    {
        if (($seed % 4) === 0) {
            return null;
        }

        $discount = [1000, 1500, 2000, 2500][$seed % 4];

        return max($price - $discount, 1000);
    }

    private function isSupportedImage(string $extension): bool
    {
        return in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'svg'], true);
    }

    private function isDisplayAssetCandidate(string $folder, string $filename): bool
    {
        $name = strtolower(pathinfo($filename, PATHINFO_FILENAME));

        if ($folder === 'section' && in_array($name, ['404', 'accordion-cls', 'amerce-html'], true)) {
            return false;
        }

        return true;
    }
}
