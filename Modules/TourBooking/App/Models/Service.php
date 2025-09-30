<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Wishlist\App\Models\Wishlist;

final class Service extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'title',
        'description',
        'short_description',
        'slug',
        'location',
        'latitude',
        'longitude',
        'service_type_id',
        'destination_id',

        // Pricing
        'price_per_person',
        'full_price',
        'discount_price',
        'age_categories',   // JSON: infant, baby, child, adult (enabled, price, min_age, max_age, count)
        'child_price',      // legacy
        'infant_price',     // legacy (fallback pt infant/baby dacă nu există în JSON)

        // Booking / rules / info
        'security_deposit',
        'deposit_required',
        'deposit_percentage',
        'included',
        'excluded',
        'duration',
        'group_size',
        'languages',
        'check_in_time',
        'check_out_time',
        'ticket',
        'amenities',
        'facilities',
        'rules',
        'safety',
        'cancellation_policy',
        'meta',

        // Flags
        'is_featured',
        'is_popular',
        'show_on_homepage',
        'status',
        'is_new',
        'is_per_person',

        // SEO / Contact / Map
        'video_url',
        'address',
        'email',
        'phone',
        'website',
        'social_links',
        'google_map_sub_title',
        'google_map_url',

        // Ownership
        'user_id',

        // Date range (pentru listări)
        'check_in_date',
        'check_out_date',

        // UI
        'tour_plan_sub_title',
        'room_count',
        'adult_count',
        'children_count',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'included'            => 'json',
        'excluded'            => 'json',
        'languages'           => 'array',
        'amenities'           => 'array',
        'facilities'          => 'json',
        'rules'               => 'json',
        'safety'              => 'json',
        'cancellation_policy' => 'json',
        'meta'                => 'json',
        'social_links'        => 'json',

        'price_per_person'    => 'decimal:2',
        'full_price'          => 'decimal:2',
        'discount_price'      => 'decimal:2',
        'age_categories'      => 'array',
        'child_price'         => 'decimal:2',
        'infant_price'        => 'decimal:2',
        'security_deposit'    => 'decimal:2',

        'deposit_required'    => 'boolean',
        'is_featured'         => 'boolean',
        'is_popular'          => 'boolean',
        'show_on_homepage'    => 'boolean',
        'status'              => 'boolean',
        'is_new'              => 'boolean',
        'is_per_person'       => 'boolean',

        'check_in_date'       => 'date',
        'check_out_date'      => 'date',
    ];

    /* =========================================================
     |  Relații
     * =======================================================*/

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ServiceMedia::class);
    }

    public function thumbnail(): HasOne
    {
        return $this->hasOne(ServiceMedia::class)
            ->where('is_thumbnail', true)
            ->withDefault();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function activeReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 1);
    }

    public function extraCharges(): HasMany
    {
        return $this->hasMany(ExtraCharge::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function availabilitieByDate(): HasOne
    {
        return $this->hasOne(Availability::class)
            ->where('is_available', true)
            ->where('date', now()->toDateString());
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(TourItinerary::class)->orderBy('day_number');
    }

    public function translation(): HasOne
    {
        return $this->hasOne(ServiceTranslation::class)
            ->where('locale', app()->getLocale())
            ->withDefault();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceTranslation::class);
    }

    public function wishlists()
    {
        return $this->morphMany(Wishlist::class, 'wishable');
    }

    public function myWishlist()
    {
        return $this->hasOne(Wishlist::class, 'wishable_id', 'id')->where('user_id', auth()->id());
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name', 'email', 'image', 'username');
    }

    public function tripTypes()
    {
        return $this->belongsToMany(TripType::class, 'service_trip_type');
    }

    /* =========================================================
     |  Scopes
     * =======================================================*/

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeShowOnHomepage($query)
    {
        return $query->where('show_on_homepage', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOfType($query, $typeId)
    {
        return $query->where('service_type_id', $typeId);
    }

    /**
     * NU poți filtra după un accessor în SQL; folosim COALESCE(discount_price, full_price, price_per_person).
     */
    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereRaw(
            'COALESCE(discount_price, full_price, price_per_person) BETWEEN ? AND ?',
            [$min, $max]
        );
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    /* =========================================================
     |  Atribute calculate
     * =======================================================*/

    protected function discountedPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->discount_price !== null && $this->discount_price !== '') {
                    return $this->discount_price;
                }
                if ($this->full_price !== null && $this->full_price !== '') {
                    return $this->full_price;
                }
                return $this->price_per_person;
            }
        );
    }

    protected function discountPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->full_price) || empty($this->discount_price)) {
                    return 0;
                }
                return (int) round((($this->full_price - $this->discount_price) / $this->full_price) * 100);
            }
        );
    }

    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->reviews()->where('status', true)->avg('rating') ?? 0
        );
    }

    protected function reviewCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->reviews()->where('status', true)->count()
        );
    }

    public function getPriceDisplayAttribute(): string
    {
        if ($this->is_per_person) {
            return currency($this->price_per_person);
        }

        return $this->discount_price
            ? '<del>' . currency($this->full_price) . '</del> ' . currency($this->discount_price)
            : currency($this->full_price);
    }

    /* =========================================================
     |  Age Categories – helpers & pricing logic
     * =======================================================*/

    /** Ordinea canonică a categoriilor. */
    public static function ageCategoryKeys(): array
    {
        return ['infant', 'baby', 'child', 'adult'];
    }

    /** Default-uri min/max pentru categorii. */
    protected function defaultMinFor(string $key): int
    {
        return match ($key) {
            'infant' => 0,
            'baby'   => 0,
            'child'  => 2,
            'adult'  => 18,
            default  => 0,
        };
    }

    protected function defaultMaxFor(string $key): int
    {
        return match ($key) {
            'infant' => 1,
            'baby'   => 2,
            'child'  => 12,
            'adult'  => 120,
            default  => 0,
        };
    }

    /** Normalizează JSON-ul age_categories și aplică defaulturi. */
    public function normalizedAgeCategories(): array
    {
        $raw = is_array($this->age_categories) ? $this->age_categories : [];
        $out = [];

        foreach (self::ageCategoryKeys() as $key) {
            $cfg = $raw[$key] ?? [];
            $out[$key] = [
                'enabled' => (bool)($cfg['enabled'] ?? false),
                'count'   => (int)($cfg['count']   ?? 0),
                'price'   => array_key_exists('price', $cfg)
                    ? (is_null($cfg['price']) ? null : (float)$cfg['price'])
                    : null,
                'min_age' => isset($cfg['min_age']) ? (int)$cfg['min_age'] : $this->defaultMinFor($key),
                'max_age' => isset($cfg['max_age']) ? (int)$cfg['max_age'] : $this->defaultMaxFor($key),
            ];
        }

        return $out;
    }

    /** Categoria pentru o vârstă, ținând cont doar de categoriile enabled=true. */
    public function ageCategoryFor(int $age): ?string
    {
        foreach ($this->normalizedAgeCategories() as $key => $cfg) {
            if (!$cfg['enabled']) {
                continue;
            }
            if ($age >= (int)$cfg['min_age'] && $age <= (int)$cfg['max_age']) {
                return $key;
            }
        }
        return null;
    }

    /** Prețul „de bază” din serviciu (nu din availability) pentru o categorie activă. */
    public function basePriceForCategory(string $key): ?float
    {
        $key  = strtolower($key);
        $cats = $this->normalizedAgeCategories();

        if (!isset($cats[$key]) || !$cats[$key]['enabled']) {
            return null;
        }
        return $cats[$key]['price'] ?? null;
    }

    /** Alias compatibil (în unele locuri se caută priceForCategory). */
    public function priceForCategory(string $key): ?float
    {
        return $this->basePriceForCategory($key);
    }

    /** Prețul de bază global al serviciului (ultimul fallback). */
    public function baseUnitPrice(): float
    {
        if ($this->discount_price !== null && $this->discount_price !== '') {
            return (float)$this->discount_price;
        }
        if ($this->full_price !== null && $this->full_price !== '') {
            return (float)$this->full_price;
        }
        if ($this->price_per_person !== null && $this->price_per_person !== '') {
            return (float)$this->price_per_person;
        }
        return 0.0;
    }

    /** Availability (indiferent de status) pentru o dată; util în calcule. */
    public function availabilityForDate($date): ?Availability
    {
        $d = $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : (string)$date;

        /** @var Availability|null $av */
        return $this->availabilities()
            ->whereDate('date', $d)
            ->first();
    }

    /** Age-categories de pe availability (dacă există), normalizate. */
    public function availabilityAgeCategoriesForDate($date): array
    {
        $av = $this->availabilityForDate($date);
        if (!$av) {
            return [];
        }

        $raw = is_array($av->age_categories ?? null) ? $av->age_categories : [];
        $out = [];
        foreach (self::ageCategoryKeys() as $key) {
            $cfg = $raw[$key] ?? [];
            $out[$key] = [
                'enabled' => (bool)($cfg['enabled'] ?? false),
                'count'   => (int)($cfg['count']   ?? 0),
                'price'   => array_key_exists('price', $cfg)
                    ? (is_null($cfg['price']) ? null : (float)$cfg['price'])
                    : null,
                // min/max de pe availability sunt opționale; încadrăm pe min/max din service
                'min_age' => $cfg['min_age'] ?? null,
                'max_age' => $cfg['max_age'] ?? null,
            ];
        }
        return $out;
    }

    /**
     * Prețul efectiv pentru o categorie la data $date (Availability → Legacy → Service → Fallback).
     */
    public function effectivePriceForCategoryOnDate(string $key, $date): ?float
    {
        $key = strtolower($key);

        // 1) Override din Availability: dacă e enabled și are price setat, îl folosim
        $avCats = $this->availabilityAgeCategoriesForDate($date);
        if (!empty($avCats[$key]) && ($avCats[$key]['enabled'] ?? false)) {
            $p = $avCats[$key]['price'] ?? null;
            if ($p !== null) {
                return (float)$p;
            }
        }

        // 2) Legacy Availability (special_price / per_children_price)
        $av = $this->availabilityForDate($date);
        if ($av) {
            if ($key === 'adult' && $av->special_price !== null) {
                return (float)$av->special_price;
            }
            if ($key === 'child' && $av->per_children_price !== null) {
                return (float)$av->per_children_price;
            }
            // infant/baby nu au câmp legacy — continuăm
        }

        // 3) Baza din serviciu, dacă categoria e activă și are price
        $base = $this->basePriceForCategory($key);
        if ($base !== null) {
            return $base;
        }

        // 4) Fallback final
        if ($key === 'adult') {
            $dp = $this->discounted_price; // accessor 'discountedPrice' expus ca snake_case
            if ($dp !== null) {
                return (float)$dp;
            }
            if ($this->price_per_person !== null) {
                return (float)$this->price_per_person;
            }
            return $this->baseUnitPrice();
        }

        // pentru child/baby/infant nu forțăm un preț generic
        return null;
    }

    /** Setul de prețuri efective pentru toate categoriile la o dată. */
    public function effectivePriceSetForDate($date): array
    {
        $out = [];
        foreach (self::ageCategoryKeys() as $key) {
            $out[$key] = $this->effectivePriceForCategoryOnDate($key, $date);
        }
        return $out;
    }

    /** Prețul efectiv pentru ADULT la o dată (util pentru „From”). */
    public function effectiveAdultPriceForDate($date): ?float
    {
        return $this->effectivePriceForCategoryOnDate('adult', $date);
    }

    /**
     * Prețul pentru o vârstă concretă la data $date:
     * mapează vârsta la o categorie activă în service (min/max) și aplică regulile de mai sus.
     */
    public function priceForAgeOnDate(int $age, $date): ?float
    {
        $cat = $this->ageCategoryFor($age) ?? 'adult';
        return $this->effectivePriceForCategoryOnDate($cat, $date);
    }
}
