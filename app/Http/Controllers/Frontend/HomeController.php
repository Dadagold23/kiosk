<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\ModuleReviewService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function index(ModuleReviewService $moduleReviewService)
    {
        $featuredProducts = Product::where('featured', true)
            ->where('status', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $serviceCategories = Category::where('type', 'service')
            ->where('status', true)
            ->take(6)
            ->get();

        $consultancyCategories = Category::where('type', 'consultancy')
            ->where('status', true)
            ->take(6)
            ->get();

        $productCategories = Category::where('type', 'product')
            ->where('status', true)
            ->withCount(['products' => function ($query) {
                $query->where('status', true);
            }])
            ->take(6)
            ->get();

        $curatedFeaturedItems = $this->curatedFeaturedItems();
        $productCategories = $this->attachCategoryImages($productCategories, $curatedFeaturedItems);
        $heroSlides = $this->heroSlides($curatedFeaturedItems)
            ->concat($this->displayLibrarySlides())
            ->take(8)
            ->values();
        $featuredGridItems = $this->featuredGridItems($curatedFeaturedItems);
        $testimonials = $moduleReviewService->testimonialsFor('order', 4);
        $catalogHighlights = $this->catalogHighlights();
        $servicePathVisuals = $this->servicePathVisuals();
        $collectionShowcase = $this->collectionShowcase();
        $lookbookFrames = $this->lookbookFrames();
        $journalEntries = $this->journalEntries();
        $experienceGallery = $this->assetImages('gallery', 6);
        $videoFeature = $this->videoFeature();

        return view('frontend.home', compact(
            'featuredProducts',
            'curatedFeaturedItems',
            'heroSlides',
            'featuredGridItems',
            'serviceCategories',
            'consultancyCategories',
            'productCategories',
            'testimonials',
            'catalogHighlights',
            'servicePathVisuals',
            'collectionShowcase',
            'lookbookFrames',
            'journalEntries',
            'experienceGallery',
            'videoFeature'
        ));
    }

    protected function curatedFeaturedItems(): Collection
    {
        return collect(config('curated_featured.items', []))
            ->map(function (array $item) {
                $title = $item['title'] ?? str($item['file'])
                    ->beforeLast('.')
                    ->replace(['_', '-'], ' ')
                    ->title()
                    ->value();

                return [
                    'slug' => $item['slug'],
                    'title' => $title,
                    'category' => $item['category'] ?? 'Featured',
                    'summary' => $item['summary'] ?? 'Curated featured selection now available in the storefront.',
                    'badge' => $item['badge'] ?? 'Curated',
                    'availability' => $item['availability'] ?? 'Available now',
                    'price_label' => $item['price_label'] ?? null,
                    'price_source_name' => $item['price_source_name'] ?? null,
                    'price_source_url' => $item['price_source_url'] ?? null,
                    'image_url' => asset('imports/epjornal/' . $item['file']),
                    'href' => route('shop.index', ['search' => $title]),
                ];
            });
    }

    protected function featuredGridItems(Collection $curatedFeaturedItems): Collection
    {
        $featuredSlugs = config('curated_featured.featured', []);

        return collect($featuredSlugs)
            ->map(fn (string $slug) => $curatedFeaturedItems->firstWhere('slug', $slug))
            ->filter()
            ->values();
    }

    protected function heroSlides(Collection $curatedFeaturedItems): Collection
    {
        $heroSlugs = config('curated_featured.hero', []);

        return collect($heroSlugs)
            ->map(fn (string $slug) => $curatedFeaturedItems->firstWhere('slug', $slug))
            ->filter()
            ->values();
    }

    protected function displayLibrarySlides(): Collection
    {
        $folders = [
            'slider' => ['label' => 'Store Highlights', 'title' => 'New highlights from across the store', 'summary' => 'A wider look at products, collections, and current picks across Kiosk.'],
            'section' => ['label' => 'Featured', 'title' => 'More to explore in the catalog', 'summary' => 'Extra visuals from the display library to give the storefront more range.'],
            'gallery' => ['label' => 'Gallery', 'title' => 'Browse more of what is available', 'summary' => 'A quick look at the kinds of items and categories you can explore in the shop.'],
            'collection' => ['label' => 'Collections', 'title' => 'Collections worth a closer look', 'summary' => 'Grouped visuals that help you jump into the store a little faster.'],
            'item' => ['label' => 'Featured Items', 'title' => 'A few extra store picks', 'summary' => 'More product imagery from the display library to keep the storefront feeling full and active.'],
        ];

        return collect($folders)
            ->flatMap(function (array $meta, string $folder) {
                return $this->assetImages($folder, 2)
                    ->values()
                    ->map(function (array $image, int $index) use ($meta, $folder) {
                        return [
                            'slug' => "{$folder}-slide-{$index}",
                            'title' => $meta['title'],
                            'category' => $meta['label'],
                            'summary' => $meta['summary'],
                            'badge' => $meta['label'],
                            'availability' => 'In store now',
                            'price_label' => null,
                            'price_source_name' => null,
                            'price_source_url' => null,
                            'image_url' => $image['url'],
                            'href' => route('shop.index'),
                        ];
                    });
            })
            ->values();
    }

    protected function attachCategoryImages(Collection $categories, Collection $curatedFeaturedItems): Collection
    {
        $imageMap = [
            'raw-food-items' => 'spaghetti-bulk-stock',
            'baby-care-and-diapers' => 'molfix-newborn-pack',
            'baby-items-and-accessories' => 'foldable-baby-stroller',
            'phones-and-tablets' => 'smart-watch-series',
            'electronics' => 'smart-watch-series',
            'home-appliances' => 'mobile-power-box-stack',
            'computing-and-accessories' => 'thermal-receipt-printer',
            'fashion' => 'wrap-two-piece-set',
            'watches-and-accessories' => 'smart-watch-series',
            'bags-and-backpacks' => 'travel-stroller-blue',
            'shoes-and-footwear' => 'moncler-trail-slides',
            'office-equipment' => 'thermal-printer-unit',
        ];

        return $categories->map(function ($category) use ($curatedFeaturedItems, $imageMap) {
            $mapped = $curatedFeaturedItems->firstWhere('slug', $imageMap[$category->slug] ?? null);

            if (! $mapped) {
                $fallbackByName = $curatedFeaturedItems->first(function (array $item) use ($category) {
                    return str($item['category'])->lower()->contains(str($category->name)->lower())
                        || str($category->name)->lower()->contains(str($item['category'])->lower());
                });

                $mapped = $fallbackByName ?: $curatedFeaturedItems->first();
            }

            $category->category_image_url = $mapped['image_url'] ?? null;

            return $category;
        });
    }

    protected function assetImages(string $folder, ?int $limit = null, array $extensions = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif', 'mp4']): Collection
    {
        $directory = public_path("assets/images/{$folder}");

        if (! is_dir($directory)) {
            return collect();
        }

        return collect(File::files($directory))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), $extensions, true))
            ->filter(fn ($file) => $this->isDisplayAssetCandidate($folder, $file->getFilename()))
            ->sortBy(fn ($file) => strtolower($file->getFilename()), SORT_NATURAL)
            ->when($limit, fn (Collection $items) => $items->take($limit))
            ->values()
            ->map(function ($file) use ($folder) {
                $path = "assets/images/{$folder}/" . $file->getFilename();

                return [
                    'name' => $file->getFilenameWithoutExtension(),
                    'label' => str($file->getFilenameWithoutExtension())
                        ->replace(['-', '_'], ' ')
                        ->title()
                        ->value(),
                    'path' => $path,
                    'url' => asset($path),
                ];
            });
    }

    protected function isDisplayAssetCandidate(string $folder, string $filename): bool
    {
        $name = strtolower(pathinfo($filename, PATHINFO_FILENAME));

        if ($folder === 'section' && in_array($name, ['404', 'accordion-cls', 'amerce-html'], true)) {
            return false;
        }

        return true;
    }

    protected function catalogHighlights(): Collection
    {
        $titles = [
            'baby-care' => ['title' => 'Baby care', 'copy' => 'Diapers, essentials, and easier family restocks.'],
            'bag-luggage' => ['title' => 'Bags and travel', 'copy' => 'Carry goods, luggage sets, and travel-ready storage.'],
            'computing-tools' => ['title' => 'Computing tools', 'copy' => 'Practical office and setup hardware for everyday work.'],
            'fashion-apparel' => ['title' => 'Fashion picks', 'copy' => 'Wearable drops with cleaner styling and broader choice.'],
            'footwear-style' => ['title' => 'Footwear', 'copy' => 'Slides, sneakers, and comfort-led movement pieces.'],
            'grocery-staples' => ['title' => 'Groceries', 'copy' => 'Fast-moving staples and pantry support essentials.'],
            'home-appliance' => ['title' => 'Home appliances', 'copy' => 'Household helpers that fit daily routines better.'],
            'office-tools' => ['title' => 'Office tools', 'copy' => 'Operational basics for workspaces and counters.'],
            'tech-device' => ['title' => 'Tech devices', 'copy' => 'Portable electronics and everyday digital support.'],
            'tech-phone' => ['title' => 'Phones and tablets', 'copy' => 'Mobile-first devices and accessory-friendly upgrades.'],
            'watch-accessory' => ['title' => 'Watches and accessories', 'copy' => 'Everyday finishing pieces with utility and style.'],
        ];

        return $this->assetImages('catalog', 6, ['svg'])
            ->map(function (array $item) use ($titles) {
                $meta = $titles[$item['name']] ?? ['title' => $item['label'], 'copy' => 'Catalog lane now active inside the Kiosk storefront.'];

                return $item + $meta;
            });
    }

    protected function servicePathVisuals(): array
    {
        $categoryVisuals = $this->assetImages('category', 3)->values();
        $itemVisuals = $this->assetImages('item', 3)->values();

        return [
            'services' => [
                'image_url' => $categoryVisuals->get(0)['url'] ?? null,
                'accent_url' => $itemVisuals->get(0)['url'] ?? null,
            ],
            'consultancy' => [
                'image_url' => $categoryVisuals->get(1)['url'] ?? $categoryVisuals->get(0)['url'] ?? null,
                'accent_url' => $itemVisuals->get(1)['url'] ?? null,
            ],
            'booking' => [
                'image_url' => $categoryVisuals->get(2)['url'] ?? $categoryVisuals->get(0)['url'] ?? null,
                'accent_url' => $itemVisuals->get(2)['url'] ?? null,
            ],
        ];
    }

    protected function collectionShowcase(): Collection
    {
        $copy = [
            ['title' => 'Household refresh', 'copy' => 'Collection-led product groupings for calmer browsing.'],
            ['title' => 'Seasonal wardrobe', 'copy' => 'Fashion clusters that feel more editorial than raw inventory.'],
            ['title' => 'Giftable essentials', 'copy' => 'Ready-to-send picks for lifestyle, travel, and daily use.'],
        ];

        return $this->assetImages('collection', 3)
            ->values()
            ->map(fn (array $item, int $index) => $item + ($copy[$index] ?? ['title' => $item['label'], 'copy' => 'Curated collection now available in the Kiosk storefront.']));
    }

    protected function lookbookFrames(): Collection
    {
        $captions = [
            'Style-led product groupings',
            'A softer, editorial view of the catalog',
            'Visual browsing beyond plain product grids',
            'Reusable imagery now folded into the live storefront',
        ];

        return $this->assetImages('lookbook', 4)
            ->values()
            ->map(fn (array $item, int $index) => $item + ['caption' => $captions[$index] ?? 'Lookbook imagery now active in Kiosk.']);
    }

    protected function journalEntries(): Collection
    {
        $avatars = $this->assetImages('avatar', 3)->values();
        $headlines = [
            ['title' => 'How to shop sourced items with less friction', 'author' => 'Kiosk Desk', 'category' => 'Sourcing notes'],
            ['title' => 'What makes service requests easier to track', 'author' => 'Support Team', 'category' => 'Customer flow'],
            ['title' => 'Why one account view improves follow-up', 'author' => 'Operations Team', 'category' => 'Platform updates'],
        ];

        $journalImages = collect([
            [
                'name' => 'cate-48',
                'label' => 'Cate 48',
                'path' => 'assets/images/category/cate-48.jpg',
                'url' => asset('assets/images/category/cate-48.jpg'),
            ],
            [
                'name' => 'cate-50',
                'label' => 'Cate 50',
                'path' => 'assets/images/category/cate-50.jpg',
                'url' => asset('assets/images/category/cate-50.jpg'),
            ],
            [
                'name' => 'cate-45',
                'label' => 'Cate 45',
                'path' => 'assets/images/category/cate-45.jpg',
                'url' => asset('assets/images/category/cate-45.jpg'),
            ],
        ]);

        return $journalImages
            ->map(function (array $item, int $index) use ($avatars, $headlines) {
                $meta = $headlines[$index] ?? ['title' => $item['label'], 'author' => 'Kiosk Journal', 'category' => 'Inside Kiosk'];
                $avatar = $avatars->get($index);

                return $item + $meta + [
                    'avatar_url' => $avatar['url'] ?? null,
                ];
            });
    }

    protected function videoFeature(): ?array
    {
        $video = $this->assetImages('video', null, ['mp4'])->first();
        $poster = $this->assetImages('slider', 1)->first()
            ?? $this->assetImages('section', 1)->first();

        if (! $video) {
            return null;
        }

        return [
            'video_url' => $video['url'],
            'poster_url' => $poster['url'] ?? null,
            'title' => 'A closer look at what you can explore',
            'copy' => 'We added more of our image library here to make the homepage feel clearer, warmer, and easier to browse.',
        ];
    }
}
