<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

final class Availability extends Model
{
    use HasFactory;

    /**
     * Atribute asignabile în masă.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
        'available_spots',

        // Legacy (prețuri pe slot)
        'special_price',        // adult
        'per_children_price',   // child

        // Nou (prețuri și capacități pe grupe de vârstă pentru slot)
        // JSON: { adult|child|baby|infant: { enabled, price, count, min_age, max_age } }
        'age_categories',

        'notes',
    ];

    /**
     * Casturi de tip.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date'               => 'date:Y-m-d',
        'start_time'         => 'datetime:H:i:s',
        'end_time'           => 'datetime:H:i:s',
        'is_available'       => 'boolean',
        'available_spots'    => 'integer',
        'special_price'      => 'decimal:2',
        'per_children_price' => 'decimal:2',
        'age_categories'     => 'array',
    ];

    /* =========================================================
     |  Relații
     * =======================================================*/

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /* =========================================================
     |  Scopes
     * =======================================================*/

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('is_available', false);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeFuture($query)
    {
        return $query->whereDate('date', '>=', now()->toDateString());
    }

    public function scopeHasSpecialPrice($query)
    {
        return $query->whereNotNull('special_price');
    }

    public function scopeOfService($query, int $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /* =========================================================
     |  Helpers age-categories (pe slotul curent)
     * =======================================================*/

    /**
     * Ordinea canonică a categoriilor.
     *
     * @return array<int,string>
     */
    public static function ageCategoryKeys(): array
    {
        return ['adult', 'child', 'baby', 'infant'];
    }

    /**
     * Normalizează structura age_categories de pe availability.
     *
     * @return array<string,array<string,mixed>>
     */
    public function normalizedAgeCategories(): array
    {
        $raw = is_array($this->age_categories) ? $this->age_categories : [];
        $out = [];

        foreach (self::ageCategoryKeys() as $key) {
            $cfg = $raw[$key] ?? [];
            $out[$key] = [
                'enabled' => (bool)($cfg['enabled'] ?? false),
                'price'   => isset($cfg['price']) ? (float)$cfg['price'] : null,
                'count'   => isset($cfg['count']) ? (int)$cfg['count'] : 0,
                'min_age' => $cfg['min_age'] ?? null,
                'max_age' => $cfg['max_age'] ?? null,
            ];
        }

        return $out;
    }

    /**
     * True dacă pe availability există cel puțin o categorie marcată enabled.
     */
    public function hasAnyAgeCategoryEnabled(): bool
    {
        foreach ($this->normalizedAgeCategories() as $cfg) {
            if (!empty($cfg['enabled'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * True dacă există cel puțin o categorie enabled cu preț numeric setat.
     * (util când vrei „active cu preț concret”)
     */
    public function hasActiveAgeCategoryWithPrice(): bool
    {
        foreach ($this->normalizedAgeCategories() as $cfg) {
            if (!empty($cfg['enabled']) && $cfg['price'] !== null && is_numeric($cfg['price'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Prețul specific pentru o categorie în acest availability (doar dacă e enabled).
     */
    public function priceForCategory(string $key): ?float
    {
        $key  = strtolower($key);
        $cats = $this->normalizedAgeCategories();

        if (!isset($cats[$key]) || empty($cats[$key]['enabled'])) {
            return null;
        }

        return $cats[$key]['price'] ?? null;
    }

    /**
     * Capacitatea (count) pentru o categorie în acest availability (doar dacă e enabled).
     * Returnează null dacă nu este setată sau categoria nu e activă.
     */
    public function capacityForCategory(string $key): ?int
    {
        $key  = strtolower($key);
        $cats = $this->normalizedAgeCategories();

        if (!isset($cats[$key]) || empty($cats[$key]['enabled'])) {
            return null;
        }

        $count = $cats[$key]['count'] ?? null;
        return is_numeric($count) ? (int)$count : null;
    }

    /**
     * Preț legacy la nivel de availability:
     * - adult → special_price
     * - child → per_children_price
     * - baby/infant → nu au legacy (returnează null)
     */
    public function legacyPriceForCategory(string $key): ?float
    {
        return match (strtolower($key)) {
            'adult' => $this->special_price      !== null ? (float)$this->special_price      : null,
            'child' => $this->per_children_price !== null ? (float)$this->per_children_price : null,
            default => null,
        };
    }
}
