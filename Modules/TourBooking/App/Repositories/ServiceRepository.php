<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\TourBooking\App\Models\Service;
use Modules\TourBooking\App\Models\ServiceTranslation;

final class ServiceRepository
{
    /**
     * Get all services.
     */
    public function getAll(): Collection
    {
        return Service::with(['translation', 'serviceType', 'thumbnail'])->get();
    }

    /**
     * Get active services.
     */
    public function getActive(): Collection
    {
        return Service::with(['translation', 'serviceType', 'thumbnail'])
            ->active()
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get featured services.
     */
    public function getFeatured(): Collection
    {
        return Service::with(['translation', 'serviceType', 'thumbnail'])
            ->active()
            ->featured()
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get popular services.
     */
    public function getPopular(): Collection
    {
        return Service::with(['translation', 'serviceType', 'thumbnail'])
            ->active()
            ->popular()
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get homepage services.
     */
    public function getHomepage(): Collection
    {
        return Service::with(['translation', 'serviceType', 'thumbnail'])
            ->active()
            ->showOnHomepage()
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Get services by type.
     */
    public function getByType(int $typeId, int $limit = 0): Collection
    {
        $query = Service::with(['translation', 'serviceType', 'thumbnail'])
            ->active()
            ->ofType($typeId)
            ->orderBy('id', 'desc');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getAllFilters(array $filters = [])
    {
        $query = Service::with(['translation', 'serviceType', 'thumbnail']);

        // Apply filters

         if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['type_id'])) {
            $query->where('service_type_id', $filters['type_id']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        if (!empty($filters['price_min']) && !empty($filters['price_max'])) {
            $query->whereBetween('full_price', [$filters['price_min'], $filters['price_max']]);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', true);
        }

        if (!empty($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        if (!empty($filters['popular'])) {
            $query->where('is_popular', $filters['popular']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('translation', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->get();
    }

    /**
     * Get paginated services.
     */
    public function getPaginated(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Service::with(['translation', 'serviceType', 'thumbnail']);

        // Apply filters

         if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['type_id'])) {
            $query->where('service_type_id', $filters['type_id']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        if (!empty($filters['price_min']) && !empty($filters['price_max'])) {
            $query->whereBetween('full_price', [$filters['price_min'], $filters['price_max']]);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->where('status', true);
        }

        if (!empty($filters['featured'])) {
            $query->where('is_featured', $filters['featured']);
        }

        if (!empty($filters['popular'])) {
            $query->where('is_popular', $filters['popular']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('translation', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Find service by ID.
     */
    public function findById(int $id): ?Service
    {
        return Service::with([
            'translation',
            'serviceType',
            'media',
            'reviews' => function($q) {
                $q->approved()->with('user');
            },
            'extraCharges',
            'availabilities',
            'itineraries' => function($q) {
                $q->orderByDay();
            }
        ])->find($id);
    }

    /**
     * Find service by slug.
     */
    public function findBySlug(string $slug): ?Service
    {
        return Service::with([
            'translation',
            'serviceType',
            'media',
            'reviews' => function($q) {
                $q->approved()->with('user');
            },
            'extraCharges',
            'availabilities',
            'itineraries' => function($q) {
                $q->orderByDay();
            }
        ])->where('slug', $slug)->first();
    }

    /**
     * Create a new service.
     */
    public function create(array $data): Service
    {
        return Service::create($data);
    }

    /**
     * Update a service.
     */
    public function update(Service $service, array $data): bool
    {
        return $service->update($data);
    }

    /**
     * Delete a service.
     */
    public function delete(Service $service): bool
    {
        return $service->delete();
    }

    /**
     * Create or update service translation.
     */
    public function saveTranslation(Service $service, string $locale, array $data): ServiceTranslation
    {
        $translation = ServiceTranslation::updateOrCreate(
            ['service_id' => $service->id, 'locale' => $locale],
            $data
        );

        return $translation;
    }

    /**
     * Get service translation.
     */
    public function getTranslation(Service $service, string $locale): ?ServiceTranslation
    {
        return ServiceTranslation::where('service_id', $service->id)
            ->where('locale', $locale)
            ->first();
    }

    /**
     * Search services by keyword.
     */
    public function search(string $keyword, int $perPage = 10): LengthAwarePaginator
    {
        return Service::with(['translation', 'serviceType', 'thumbnail'])
            ->active()
            ->whereHas('translation', function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->orWhere('location', 'like', "%{$keyword}%")
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Increment view count for a service.
     */
    public function incrementViews(Service $service): bool
    {
        return $service->increment('views');
    }

    /**
     * Get related services based on service type.
     */
    public function getRelated(Service $service, int $limit = 4): Collection
    {
        return Service::with(['translation', 'serviceType', 'thumbnail'])
            ->active()
            ->where('id', '!=', $service->id)
            ->where('service_type_id', $service->service_type_id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
