<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert textarea strings to arrays for fields that need it
        $textareaFields = ['included', 'excluded'];

        foreach ($textareaFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([
                    $field => $this->convertTextareaToArray($this->input($field))
                ]);
            }
        }

        // === NEW: normalize flags and compute has_age_pricing ===
        $ageCats = (array) $this->input('age_categories', []);
        $hasAgePricing = collect($ageCats)->contains(function ($c) {
            return (int)($c['enabled'] ?? 0) === 1;
        });

        $this->merge([
            'has_age_pricing' => $hasAgePricing ? 1 : 0,
            'is_per_person'   => $this->boolean('is_per_person'),
        ]);
        // === /NEW ===
    }

    /**
     * Convert textarea content to array (one item per line)
     */
    private function convertTextareaToArray($text): array
    {
        if (empty($text)) {
            return [];
        }

        // Split by new lines, trim whitespace, and remove empty lines
        return array_filter(
            array_map('trim', preg_split('/\r\n|\r|\n/', $text)),
            function ($item) {
                return !empty($item);
            }
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:30',
            'longitude' => 'nullable|string|max:30',
            'service_type_id' => 'required|exists:service_types,id',
            'destination_id' => 'nullable',

            // (mutăm regulile de preț mai jos, condițional)

            'child_price' => 'nullable|numeric|min:0',
            'infant_price' => 'nullable|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'deposit_required' => 'nullable|boolean',
            'deposit_percentage' => 'nullable|integer|min:0|max:100',
            'included' => 'nullable|array',
            'included.*' => 'string|max:255',
            'excluded' => 'nullable|array',
            'excluded.*' => 'string|max:255',
            'duration' => 'nullable|string|max:100',
            'group_size' => 'nullable|string|max:100',
            'languages' => 'nullable|array',
            'check_in_time' => 'nullable|string|max:50',
            'check_out_time' => 'nullable|string|max:50',
            'ticket' => 'nullable|string|max:255',
            'amenities' => 'nullable|array',
            'facilities' => 'nullable|array',
            'rules' => 'nullable|array',
            'safety' => 'nullable|array',
            'cancellation_policy' => 'nullable|array',
            'is_featured' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'show_on_homepage' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'is_new' => 'nullable|boolean',
            'video_url' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'social_links' => 'nullable|array',
            'user_id' => 'nullable|exists:users,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
            'lang_code' => 'nullable|string|exists:languages,lang_code',
            'room_count' => 'nullable|integer|min:0',
            'adult_count' => 'nullable|integer|min:0',
            'children_count' => 'nullable|integer|min:0',
            'tour_plan_sub_title' => 'nullable|max:255',
            'google_map_sub_title' => 'nullable|max:255',
            'google_map_url' => 'nullable',
            'is_per_person' => 'nullable',
            'trip_types' => 'nullable',
            'check_in_date' => 'nullable|date',
            'check_out_date' => 'nullable|date',

            // NEW: container pentru age categories
            'age_categories' => 'nullable|array',
        ];

        // === NEW: reguli condiționale pentru pricing & age categories ===
        $ageCats = (array) $this->input('age_categories', []);
        $hasAgePricing = (bool) $this->boolean('has_age_pricing');

        if ($hasAgePricing) {
            // Când avem pricing pe categorii: NU cerem full_price / price_per_person
            $rules['price_per_person'] = ['nullable', 'numeric', 'min:0'];
            $rules['full_price']       = ['nullable', 'numeric', 'min:0'];
            $rules['discount_price']   = ['nullable', 'numeric', 'min:0'];

            foreach ($ageCats as $key => $cat) {
                $enabled = (int)($cat['enabled'] ?? 0) === 1;

                $prefix = "age_categories.$key";
                if ($enabled) {
                    $rules["$prefix.price"]   = ['required', 'numeric', 'min:0'];
                    $rules["$prefix.count"]   = ['required', 'integer', 'min:0'];
                    $rules["$prefix.min_age"] = ['required', 'integer', 'min:0'];
                    $rules["$prefix.max_age"] = ['required', 'integer', 'min:0', "gte:$prefix.min_age"];
                } else {
                    $rules["$prefix.price"]   = ['nullable', 'numeric', 'min:0'];
                    $rules["$prefix.count"]   = ['nullable', 'integer', 'min:0'];
                    $rules["$prefix.min_age"] = ['nullable', 'integer', 'min:0'];
                    $rules["$prefix.max_age"] = ['nullable', 'integer', 'min:0'];
                }
            }
        } else {
            // Fără age pricing -> rămâne logica legacy
            if ($this->boolean('is_per_person')) {
                $rules['price_per_person'] = ['required', 'numeric', 'min:0'];
                $rules['full_price']       = ['nullable', 'numeric', 'min:0'];
                $rules['discount_price']   = [
                    'nullable', 'numeric', 'min:0',
                    function ($attribute, $value, $fail) {
                        // doar în modul non-age & non-full, nu comparăm obligatoriu cu full_price
                        if ($this->boolean('is_per_person') == false && $value > ($this->full_price ?? 0)) {
                            $fail('The discount price cannot be greater than the full price.');
                        }
                    }
                ];
            } else {
                $rules['full_price']       = ['required', 'numeric', 'min:0'];
                $rules['price_per_person'] = ['nullable', 'numeric', 'min:0'];
                $rules['discount_price']   = [
                    'nullable', 'numeric', 'min:0',
                    function ($attribute, $value, $fail) {
                        if ($value > ($this->full_price ?? 0)) {
                            $fail('The discount price cannot be greater than the full price.');
                        }
                    }
                ];
            }
        }
        // === /NEW ===

        if ($this->isMethod('POST')) {
            $rules['slug'] = 'nullable|string|unique:services,slug|max:255';
        } else {
            $rules['slug'] = [
                'nullable',
                'string',
                'max:255',
                Rule::unique('services', 'slug')->ignore($this->route('service')),
            ];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => trans('translate.Title is required'),
            'service_type_id.required' => trans('translate.Service type is required'),
            'service_type_id.exists' => trans('translate.Invalid service type'),
            'slug.unique' => trans('translate.Slug already exists'),
            'price_per_person.numeric' => trans('translate.Price must be a valid number'),
            'full_price.numeric' => trans('translate.Price must be a valid number'),
            'discount_price.numeric' => trans('translate.Price must be a valid number'),
            'email.email' => trans('translate.Invalid email format'),
            'video_url.url' => trans('translate.Invalid URL format'),
            'website.url' => trans('translate.Invalid URL format'),
            'included.array' => 'The included field must contain valid items.',
            'excluded.array' => 'The excluded field must contain valid items.',
            'included.*.string' => 'Each included item must be text.',
            'excluded.*.string' => 'Each excluded item must be text.',
            'included.*.max' => 'Each included item cannot exceed 255 characters.',
            'excluded.*.max' => 'Each excluded item cannot exceed 255 characters.',

            // NEW: mesaje pentru age categories
            'age_categories.*.price.required'   => 'Price is required for enabled age categories.',
            'age_categories.*.count.required'   => 'Count is required for enabled age categories.',
            'age_categories.*.min_age.required' => 'Min age is required for enabled age categories.',
            'age_categories.*.max_age.required' => 'Max age is required for enabled age categories.',
            'age_categories.*.max_age.gte'      => 'Max age must be greater than or equal to min age.',
        ];
    }
}
