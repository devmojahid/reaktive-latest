<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use Illuminate\Http\Request;
use File;
use App\Services\FrontendFieldService;

class FrontEndManagementController extends Controller
{
    protected $fieldService;

    public function __construct(FrontendFieldService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function index()
    {
        // Get common settings
        $commonSettings = app('theme')->getCommonSettings();

        // Get active theme settings
        $activeTheme = app('theme')->current();
        $themeSettings = app('theme')->getThemeSettings();

        // Merge settings
        $sections = array_merge($commonSettings, $themeSettings);

        // Sort sections by order if defined
        $sections = $this->sortSectionsByOrder($sections);

        // Group sections by page
        $sectionsByPage = $this->groupSectionsByPage($sections);

        // Get a list of available themes
        $themes = app('theme')->all();

        return view('admin.frontend-management.index', compact('sections', 'sectionsByPage', 'themes', 'activeTheme'));
    }

    /**
     * Sort sections by order
     */
    protected function sortSectionsByOrder(array $sections): array
    {
        // Create a copy for sorting
        $sortableSections = $sections;

        // Sort by order if defined, otherwise use natural ordering
        uasort($sortableSections, function($a, $b) {
            $aOrder = $a['order'] ?? 999;
            $bOrder = $b['order'] ?? 999;

            return $aOrder <=> $bOrder;
        });

        return $sortableSections;
    }

    /**
     * Group sections by page
     */
    protected function groupSectionsByPage(array $sections): array
    {
        $sectionsByPage = [];

        foreach ($sections as $key => $section) {
            $page = $section['page'] ?? 'other';

            if (!isset($sectionsByPage[$page])) {
                $sectionsByPage[$page] = [];
            }

            $sectionsByPage[$page][$key] = $section;
        }

        // Sort pages alphabetically, but keep 'global' and 'home' at the top
        uksort($sectionsByPage, function($a, $b) {
            if ($a === 'global') return -1;
            if ($b === 'global') return 1;
            if ($a === 'home') return -1;
            if ($b === 'home') return 1;
            return strcmp($a, $b);
        });

        return $sectionsByPage;
    }

    public function section($key)
    {
        $lang_code = request('lang_code', 'en');

        // Get merged settings
        $sections = app('theme')->getMergedSettings();

        if (!isset($sections[$key])) {
            abort(404, "Section not found for key: $key");
        }

        $section = $sections[$key];
        $contentType = isset($section['content']) ? 'content' : (isset($section['element']) ? 'element' : null);

        if (!$contentType) {
            abort(404, "Content or Element not found for section: $key");
        }

        $dataKeys = $key . '.' . $contentType;
        $content = $section[$contentType];

        // Get frontend data
        $frontend = Frontend::where('data_keys', $dataKeys)->first();

        if ($lang_code === 'en') {
            $dataValues = $frontend ? $frontend->data_values : [];
        } else {
            if ($frontend) {
                $translations = json_decode($frontend->data_translations, true) ?? [];
                $translation = collect($translations)->first(function ($item) use ($lang_code) {
                    return $item['language_code'] === $lang_code;
                });

                if ($translation) {
                    $dataValues = $translation['values'];
                } else {
                    $dataValues = $frontend->data_values;
                    unset($dataValues['images']);

                    $translations[] = [
                        'language_code' => $lang_code,
                        'values' => $frontend->data_values
                    ];

                    $frontend->data_translations = json_encode($translations);
                    $frontend->save();
                }
            } else {
                $dataValues = [];
            }
        }

        // Get the theme info if it's a theme-specific section
        $themeInfo = null;
        if (isset($section['theme'])) {
            $themeInfo = app('theme')->loadThemeInfo($section['theme']);
        }

        $page_title = $section['name'] ?? trans('translate.Frontend Management');

        return view('admin.frontend-management.edit', compact(
            'page_title',
            'key',
            'content',
            'dataValues',
            'frontend',
            'contentType',
            'lang_code',
            'themeInfo'
        ));
    }

    public function store(Request $request, $key, $id = null)
    {
        // Get and validate language code
        $lang_code = $request->get('lang_code');
        if (!$lang_code) {
            return back()->with('error', 'Language code is required');
        }

        // Load settings
        $sections = app('theme')->getMergedSettings();
        if (!isset($sections[$key])) {
            abort(404, "Section not found for key: $key");
        }

        $section = $sections[$key];
        $contentType = isset($section['content']) ? 'content' : (isset($section['element']) ? 'element' : null);

        if (!$contentType) {
            abort(404, "Content or Element not found for section: $key");
        }

        // Validate the request
        $rules = [];
        foreach ($section[$contentType] as $fieldName => $field) {
            if (is_array($field)) {
                // Check if field has a type key defined
                $fieldType = $field['type'] ?? 'text';
                $fieldOptions = $field['options'] ?? [];

                // If there's an existing image and it's an image field, make it optional
                if ($fieldType === 'image' && $request->has("{$fieldName}_existing") && $request->get("{$fieldName}_existing")) {
                    $fieldOptions['required'] = false;
                }

                $rules[$fieldName] = $this->fieldService->getValidationRules(
                    $fieldType,
                    $fieldOptions
                );
            }
        }

        // Process the data
        $data = $request->except(['_token', '_method', 'type', 'lang_code']);
        $frontend = $id ? Frontend::findOrFail($id) : new Frontend();
        $existingData = $frontend->data_values ?? [];

        // Get existing translations
        $translations = json_decode($frontend->data_translations ?? '[]', true) ?? [];

        // Process fields
        $processedData = [];

        foreach ($section[$contentType] as $fieldName => $field) {
            if (is_array($field)) {
                $fieldType = $field['type'] ?? 'text';
                $fieldOptions = $field['options'] ?? [];

                // Handle direct image fields - only process for default language (en)
                if ($fieldType === 'image' && $lang_code === 'en') {
                    if ($request->hasFile($fieldName)) {
                        // Upload new image
                        $image = $request->file($fieldName);
                        $imageName = time() . '_' . $fieldName . '.' . $image->getClientOriginalExtension();

                        // Delete old file if it exists
                        $oldFile = $existingData[$fieldName] ?? null;
                        if ($oldFile && File::exists(public_path($oldFile))) {
                            unlink(public_path($oldFile));
                        }

                        // Move the uploaded file
                        $image->move(public_path('uploads/website-images'), $imageName);
                        $processedData[$fieldName] = 'uploads/website-images/' . $imageName;
                    } elseif ($request->has("{$fieldName}_existing")) {
                        // Use existing image if provided
                        $existingImage = $request->get("{$fieldName}_existing");
                        if ($existingImage) {
                            $processedData[$fieldName] = $existingImage;
                        }
                    } elseif (isset($existingData[$fieldName])) {
                        // Keep existing image if no new upload and no explicit removal
                        $processedData[$fieldName] = $existingData[$fieldName];
                    }
                    continue;
                }

                // Handle images array separately to preserve existing images - only for default language
                if ($fieldName === 'images' && is_array($field) && $lang_code === 'en') {
                    $processedData[$fieldName] = $this->processImagesArray(
                        $request,
                        $fieldName,
                        $field,
                        $existingData[$fieldName] ?? []
                    );
                    continue;
                }

                // Process other field types
                $processedData[$fieldName] = $this->fieldService->processFieldValue(
                    $data[$fieldName] ?? null,
                    $fieldType,
                    $fieldOptions
                );
            } else {
                // For scalar values (strings, numbers, etc.), store as is
                $processedData[$fieldName] = $data[$fieldName] ?? $field;
            }
        }

        // Handle translations
        if ($lang_code === 'en') {
            // For English, update data_values directly
            // Make sure we keep any existing images if they weren't updated
            if (isset($existingData['images']) && !isset($processedData['images'])) {
                $processedData['images'] = $existingData['images'];
            }

            $frontend->data_values = $processedData;

            // Update English translation
            $translationExists = false;
            foreach ($translations as $key => $translation) {
                if ($translation['language_code'] === 'en') {
                    // Keep images in English translation but update other fields
                    $enValues = $processedData;
                    $translations[$key]['values'] = $enValues;
                    $translationExists = true;
                    break;
                }
            }

            if (!$translationExists) {
                $translations[] = [
                    'language_code' => 'en',
                    'values' => $processedData
                ];
            }
        } else {
            // For non-English languages, only update translations
            // But preserve image data from English version
            $translationExists = false;

            // Make a copy of processed data for the translation
            $translatedData = $processedData;

            // Images should only be managed in English
            if (isset($frontend->data_values['images'])) {
                $translatedData['images'] = $frontend->data_values['images'];
            }

            foreach ($translations as $key => $translation) {
                if ($translation['language_code'] === $lang_code) {
                    $translations[$key]['values'] = $translatedData;
                    $translationExists = true;
                    break;
                }
            }

            if (!$translationExists) {
                $translations[] = [
                    'language_code' => $lang_code,
                    'values' => $translatedData
                ];
            }

            // Handle new records without English data
            if (empty($frontend->data_values)) {
                $frontend->data_values = [];

                // Add default English translation if not exists
                $hasEnglishTranslation = false;
                foreach ($translations as $translation) {
                    if ($translation['language_code'] === 'en') {
                        $hasEnglishTranslation = true;
                        break;
                    }
                }

                if (!$hasEnglishTranslation) {
                    $translations[] = [
                        'language_code' => 'en',
                        'values' => []
                    ];
                }
            }
        }

        // Set data keys if new record
        if (!$frontend->data_keys) {
            $frontend->data_keys = $key . '.' . $contentType;
        }

        // Save translations
        $frontend->data_translations = json_encode($translations);
        $frontend->save();

        return redirect()->back()->with('success', trans('translate.Update successfully'));
    }

    /**
     * Process images array
     */
    protected function processImagesArray($request, $fieldName, $field, $existingImages = [])
    {
        $processedImages = [];

        // Handle each image in the array
        foreach ($field as $imageKey => $imageOptions) {
            // Check for new uploaded file
            $fileKey = "{$fieldName}_{$imageKey}";

            if ($request->hasFile($fileKey)) {
                // Upload new image
                $image = $request->file($fileKey);
                $imageName = time() . '_' . $imageKey . '.' . $image->getClientOriginalExtension();

                // Delete old file if it exists
                $oldImage = $existingImages[$imageKey] ?? null;
                if ($oldImage && File::exists(public_path($oldImage))) {
                    unlink(public_path($oldImage));
                }

                // Move the uploaded file
                $image->move(public_path('uploads/website-images'), $imageName);
                $processedImages[$imageKey] = 'uploads/website-images/' . $imageName;
            } elseif ($request->hasFile($imageKey)) { // Check if image is directly uploaded with key
                // Upload new image
                $image = $request->file($imageKey);
                $imageName = time() . '_' . $imageKey . '.' . $image->getClientOriginalExtension();

                // Delete old file if it exists
                $oldImage = $existingImages[$imageKey] ?? null;
                if ($oldImage && File::exists(public_path($oldImage))) {
                    unlink(public_path($oldImage));
                }

                // Move the uploaded file
                $image->move(public_path('uploads/website-images'), $imageName);
                $processedImages[$imageKey] = 'uploads/website-images/' . $imageName;
            } else {
                // Check if we have an existing image that should be kept
                $existingImageKey = "{$fieldName}_{$imageKey}_existing";

                if ($request->has($existingImageKey)) {
                    $existingValue = $request->get($existingImageKey);

                    if (!empty($existingValue)) {
                        // Keep existing image
                        $processedImages[$imageKey] = $existingValue;
                    }
                } elseif (isset($existingImages[$imageKey])) {
                    // Keep existing image if none uploaded and no explicit removal
                    $processedImages[$imageKey] = $existingImages[$imageKey];
                }
            }
        }

        return $processedImages;
    }

    /**
     * Get a field template for use in dynamic front-end management
     */
    public function getFieldTemplate(Request $request)
    {
        $type = $request->get('type', 'text');

        // Validate the field type
        if (!in_array($type, array_keys(config('frontend-fields.field_types')))) {
            return response()->json(['error' => 'Invalid field type'], 400);
        }

        // Get the template
        $viewName = config("frontend-fields.field_types.{$type}.view");

        if (!$viewName) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        // Render the template with placeholder values
        $html = view($viewName, [
            'name' => '__NAME__',
            'label' => '__LABEL__',
            'value' => null,
            'required' => '__REQUIRED__',
            'help' => null,
        ])->render();

        return response($html);
    }
}
