<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Modules\TourBooking\App\Models\ServiceType;
use Modules\TourBooking\App\Models\ServiceTypeTranslation;

final class ServiceTypeRepository
{
    /**
     * Get all service types.
     */
    public function getAll(): Collection
    {
        return ServiceType::with('translation')->get();
    }

    /**
     * Get active service types.
     */
    public function getActive(): Collection
    {
        return ServiceType::with('translation')
            ->active()
            ->orderBy('display_order')
            ->get();
    }

    public function getActiveNameId(): Collection
    {
        return ServiceType::select('id', 'name', 'display_order')
            ->active()
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Get featured service types.
     */
    public function getFeatured(): Collection
    {
        return ServiceType::with('translation')
            ->active()
            ->featured()
            ->orderBy('display_order')
            ->get();
    }

    /**
     * Get paginated service types.
     */
    public function getPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return ServiceType::with('translation')
            ->orderBy('display_order')
            ->paginate($perPage);
    }

    /**
     * Find service type by ID.
     */
    public function findById(int $id): ?ServiceType
    {
        return ServiceType::with('translation')->find($id);
    }

    /**
     * Find service type by slug.
     */
    public function findBySlug(string $slug): ?ServiceType
    {
        return ServiceType::with('translation')->where('slug', $slug)->first();
    }

    /**
     * Create a new service type.
     */
    public function create(array $data): ServiceType
    {
        return ServiceType::create($data);
    }

    /**
     * Update a service type.
     */
    public function update(ServiceType $serviceType, array $data): bool
    {
        return $serviceType->update($data);
    }

    /**
     * Delete a service type.
     */
    public function delete(ServiceType $serviceType): bool
    {
        return $serviceType->delete();
    }

    /**
     * Create or update service type translation.
     */
    public function saveTranslation(ServiceType $serviceType, string $locale, array $data): ServiceTypeTranslation
    {
        $translation = ServiceTypeTranslation::updateOrCreate(
            ['service_type_id' => $serviceType->id, 'locale' => $locale],
            $data
        );

        return $translation;
    }

    /**
     * Get service type translation.
     */
    public function getTranslation(ServiceType $serviceType, string $locale): ?ServiceTypeTranslation
    {
        return ServiceTypeTranslation::where('service_type_id', $serviceType->id)
            ->where('locale', $locale)
            ->first();
    }
}
