<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'source_type',
        'source_marketplace',
        'name',
        'slug',
        'description',
        'sku',
        'price',
        'sale_price',
        'quantity',
        'image',
        'external_url',
        'featured',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name . '-' . Str::random(5));
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->sale_price ?: $this->price);
    }

    public function getUploadedImageUrlAttribute(): ?string
    {
        if (is_string($this->image) && $this->image !== '' && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        return null;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->uploaded_image_url) {
            return $this->uploaded_image_url;
        }

        if ($this->hasUsableRemoteImage()) {
            return $this->image;
        }

        if ($mappedPublicAsset = $this->publicAssetImageUrl()) {
            if (!Str::endsWith($mappedPublicAsset, '.svg')) {
                return $mappedPublicAsset;
            }
        }

        if ($mapped = $this->mappedCatalogImageUrl()) {
            if (!Str::endsWith($mapped, '.svg')) {
                return $mapped;
            }
        }

        if ($dynamicMatch = $this->findDynamicEpjornalImage()) {
            return $dynamicMatch;
        }

        return $this->getGenericProductImage();
    }

    protected static ?array $epjornalFilesCache = null;

    protected function getEpjornalFiles(): array
    {
        if (self::$epjornalFilesCache === null) {
            $dir = public_path('imports/epjornal');
            if (is_dir($dir)) {
                $files = scandir($dir);
                self::$epjornalFilesCache = array_filter($files, function ($file) use ($dir) {
                    return $file !== '.' && $file !== '..' && is_file($dir . '/' . $file);
                });
            } else {
                self::$epjornalFilesCache = [];
            }
        }
        return self::$epjornalFilesCache;
    }

    protected function findDynamicEpjornalImage(): ?string
    {
        $words = preg_split('/[^a-zA-Z0-9]+/', Str::lower($this->name . ' ' . $this->slug));
        if (!$words) {
            return null;
        }

        $ignoreWords = ['and', 'the', 'for', 'with', 'pcs', 'pack', 'set', 'size', 'pro', 'new', 'ksk', 'loc'];
        $keywords = array_filter($words, function ($word) use ($ignoreWords) {
            return strlen($word) >= 3 && !in_array($word, $ignoreWords);
        });

        if (empty($keywords)) {
            return null;
        }

        $files = $this->getEpjornalFiles();
        $bestFile = null;
        $bestScore = 0;

        foreach ($files as $file) {
            $filenameLower = Str::lower(pathinfo($file, PATHINFO_FILENAME));
            $score = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($filenameLower, $keyword)) {
                    $score++;
                }
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestFile = $file;
            }
        }

        return $bestFile ? asset('imports/epjornal/' . $bestFile) : null;
    }

    protected function getGenericProductImage(): string
    {
        $genericImages = [
            '01.png', '02.png', '03.png', '04.png', '05.png',
            '06.png', '07.png', '08.png', '09.png', '10.png',
            '11.png', '12.png', '13.png', '14.png', '15.png',
            '16.png', '17.png', '19.png'
        ];

        $index = ($this->id ?? 0) % count($genericImages);
        return asset('admin-assets/images/products/' . $genericImages[$index]);
    }

    protected function hasUsableRemoteImage(): bool
    {
        return is_string($this->image)
            && $this->image !== ''
            && filter_var($this->image, FILTER_VALIDATE_URL)
            && ! str_contains($this->image, 'marketplace.example');
    }

    protected function publicAssetImageUrl(): ?string
    {
        if (! is_string($this->image) || $this->image === '' || filter_var($this->image, FILTER_VALIDATE_URL)) {
            return null;
        }

        $relative = ltrim(str_replace('\\', '/', $this->image), '/');

        return is_file(public_path($relative))
            ? asset($relative)
            : null;
    }

    protected function mappedCatalogImageUrl(): ?string
    {
        $config = config('product_media');
        $exact = $config['exact'][$this->slug] ?? null;

        if ($exact) {
            return asset('imports/epjornal/' . $exact);
        }

        $haystack = Str::lower(trim($this->name . ' ' . $this->slug . ' ' . ($this->category?->name ?? '')));

        foreach ($config['keyword'] ?? [] as $rule) {
            foreach ($rule['keywords'] as $keyword) {
                if (str_contains($haystack, Str::lower($keyword))) {
                    return asset('imports/epjornal/' . $rule['file']);
                }
            }
        }

        $categorySlug = $this->category?->slug;
        if ($categorySlug && isset($config['category_defaults'][$categorySlug])) {
            return asset($config['category_defaults'][$categorySlug]);
        }

        return null;
    }

    protected function generatedCatalogArtUrl(): string
    {
        $theme = $this->catalogArtTheme();
        $palette = [
            'baby' => ['#f7e7f3', '#e8c9dd', '#8d5b79'],
            'grocery' => ['#f5edd7', '#e3d2a5', '#7b5f28'],
            'fashion' => ['#f4e5df', '#dfb7a5', '#7f4d3d'],
            'footwear' => ['#efe8e2', '#d2b49d', '#6d4e40'],
            'bag' => ['#ece6de', '#c9b59d', '#5c4a3a'],
            'phone' => ['#e5edf8', '#b6c9e9', '#3f5f8b'],
            'electronics' => ['#e7eef0', '#b6cad0', '#36505b'],
            'appliance' => ['#eef1ea', '#c6d1be', '#4e5b46'],
            'computing' => ['#e8ebf4', '#bcc6e1', '#46557f'],
            'office' => ['#efe7f7', '#cabbe0', '#5a437b'],
            'watch' => ['#ececf1', '#c0c1cf', '#484a5c'],
            'default' => ['#f2ede7', '#d7c8b9', '#665447'],
        ][$theme];

        $title = e(Str::limit($this->name, 28, ''));
        $subtitle = e($this->category?->name ?: 'Catalog item');
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 720 920" role="img" aria-label="{$title}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$palette[0]}"/>
      <stop offset="100%" stop-color="{$palette[1]}"/>
    </linearGradient>
  </defs>
  <rect width="720" height="920" fill="url(#bg)"/>
  <circle cx="570" cy="160" r="110" fill="rgba(255,255,255,0.35)"/>
  <circle cx="160" cy="760" r="140" fill="rgba(255,255,255,0.28)"/>
  <rect x="74" y="96" width="572" height="728" rx="44" fill="rgba(255,255,255,0.42)"/>
  <rect x="112" y="142" width="496" height="420" rx="28" fill="rgba(255,255,255,0.52)"/>
  <rect x="152" y="192" width="416" height="14" rx="7" fill="{$palette[2]}" opacity="0.18"/>
  <rect x="152" y="232" width="352" height="14" rx="7" fill="{$palette[2]}" opacity="0.18"/>
  <rect x="152" y="272" width="388" height="14" rx="7" fill="{$palette[2]}" opacity="0.18"/>
  <rect x="152" y="524" width="200" height="18" rx="9" fill="{$palette[2]}" opacity="0.16"/>
  <text x="112" y="640" fill="{$palette[2]}" font-family="Arial, sans-serif" font-size="44" font-weight="700">{$title}</text>
  <text x="112" y="692" fill="{$palette[2]}" font-family="Arial, sans-serif" font-size="24" opacity="0.72">{$subtitle}</text>
</svg>
SVG;

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    protected function catalogArtTheme(): string
    {
        $haystack = Str::lower(trim($this->name . ' ' . $this->slug . ' ' . ($this->category?->name ?? '')));

        return match (true) {
            str_contains($haystack, 'baby'),
            str_contains($haystack, 'molfix'),
            str_contains($haystack, 'huggies'),
            str_contains($haystack, 'pampers'),
            str_contains($haystack, 'aptamil'),
            str_contains($haystack, 'cerelac') => 'baby',
            str_contains($haystack, 'rice'),
            str_contains($haystack, 'garri'),
            str_contains($haystack, 'semovita'),
            str_contains($haystack, 'spaghetti'),
            str_contains($haystack, 'beans'),
            str_contains($haystack, 'oil'),
            str_contains($haystack, 'chicken'),
            str_contains($haystack, 'turkey') => 'grocery',
            str_contains($haystack, 'watch') => 'watch',
            str_contains($haystack, 'shoe'),
            str_contains($haystack, 'sneaker'),
            str_contains($haystack, 'trainer'),
            str_contains($haystack, 'heel'),
            str_contains($haystack, 'sandal'),
            str_contains($haystack, 'loafer'),
            str_contains($haystack, 'derby') => 'footwear',
            str_contains($haystack, 'bag'),
            str_contains($haystack, 'backpack'),
            str_contains($haystack, 'briefcase'),
            str_contains($haystack, 'suitcase'),
            str_contains($haystack, 'duffel'),
            str_contains($haystack, 'sleeve') => 'bag',
            str_contains($haystack, 'phone'),
            str_contains($haystack, 'iphone'),
            str_contains($haystack, 'samsung'),
            str_contains($haystack, 'tecno'),
            str_contains($haystack, 'infinix'),
            str_contains($haystack, 'redmi'),
            str_contains($haystack, 'itel'),
            str_contains($haystack, 'tablet'),
            str_contains($haystack, 'tab ') => 'phone',
            str_contains($haystack, 'laptop'),
            str_contains($haystack, 'mouse'),
            str_contains($haystack, 'keyboard'),
            str_contains($haystack, 'charger'),
            str_contains($haystack, 'docking') => 'computing',
            str_contains($haystack, 'printer'),
            str_contains($haystack, 'scanner'),
            str_contains($haystack, 'copy paper'),
            str_contains($haystack, 'laminating'),
            str_contains($haystack, 'cash drawer'),
            str_contains($haystack, 'pos'),
            str_contains($haystack, 'ups') => 'office',
            str_contains($haystack, 'fridge'),
            str_contains($haystack, 'refrigerator'),
            str_contains($haystack, 'blender'),
            str_contains($haystack, 'fan'),
            str_contains($haystack, 'kettle'),
            str_contains($haystack, 'iron'),
            str_contains($haystack, 'washer'),
            str_contains($haystack, 'air fryer'),
            str_contains($haystack, 'air purifier'),
            str_contains($haystack, 'vacuum'),
            str_contains($haystack, 'microwave'),
            str_contains($haystack, 'utensil') => 'appliance',
            str_contains($haystack, 'speaker'),
            str_contains($haystack, 'sound'),
            str_contains($haystack, 'earbud'),
            str_contains($haystack, 'freepods'),
            str_contains($haystack, 'power bank'),
            str_contains($haystack, 'ring light'),
            str_contains($haystack, 'game pad'),
            str_contains($haystack, 'tv'),
            str_contains($haystack, 'display') => 'electronics',
            str_contains($haystack, 'shirt'),
            str_contains($haystack, 't-shirt'),
            str_contains($haystack, 'blazer'),
            str_contains($haystack, 'gown'),
            str_contains($haystack, 'ankara'),
            str_contains($haystack, 'jeans'),
            str_contains($haystack, 'lounge'),
            str_contains($haystack, 'senator') => 'fashion',
            default => 'default',
        };
    }
}
