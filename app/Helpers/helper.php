<?php

use App\Models\Frontend;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Currency\App\Models\Currency;
use Modules\GlobalSetting\App\Models\GlobalSetting;
use Modules\Language\App\Models\Language;
use Modules\TourBooking\App\Models\Destination;
use Modules\TourBooking\App\Models\Service;
use Modules\TourBooking\App\Models\ServiceType;

function admin_lang()
{
    return 'en';
}

function front_lang()
{
    return Session::get('front_lang');
}


function html_decode($text)
{
    $decode_text = htmlspecialchars_decode($text, ENT_QUOTES);
    return $decode_text;
}


function currency($amount)
{

    // currency information will be loaded from session value

    $defaultCurrency = Currency::where('is_default', 'yes')->first();

    $currency_icon = $defaultCurrency?->currency_icon ?? Session::get('currency_icon', '$');
    $currency_code = $defaultCurrency?->currency_code ?? Session::get('currency_code', 'USD');
    $currency_rate = $defaultCurrency?->currency_rate ?? Session::get('currency_rate', 1);
    $currency_position = $defaultCurrency?->currency_position ?? Session::get('currency_position', 'before_price');

    $amount = $amount * $currency_rate;
    $amount = number_format($amount, 2, '.', ',');

    if ($currency_position == 'before_price') {
        $amount = $currency_icon . $amount;
    } elseif ($currency_position == 'before_price_with_space') {
        $amount = $currency_icon . ' ' . $amount;
    } elseif ($currency_position == 'after_price') {
        $amount = $amount . $currency_icon;
    } elseif ($currency_position == 'after_price_with_space') {
        $amount = $amount . ' ' . $currency_icon;
    } else {
        $amount = $currency_icon . $amount;
    }

    return $amount;
}

function currency_price($amount)
{
    $defaultCurrency = Currency::where('is_default', 'yes')->first();

    $currency_rate = $defaultCurrency?->currency_rate ?? Session::get('currency_rate', 1);
    $amount = $amount * $currency_rate;
    return $amount;
}

function default_currency()
{
    $defaultCurrency = Currency::where('is_default', 'yes')->first();
    return [
        'currency_rate' => $defaultCurrency?->currency_rate ?? 1,
        'currency_icon' => $defaultCurrency?->currency_icon ?? '$',
        'currency_position' => $defaultCurrency?->currency_position ?? 'before_price',
        'currency_code' => $defaultCurrency?->currency_code ?? 'USD',
    ];
}

function revenue_calculate($total_income)
{
    $commission_type = GlobalSetting::where('key', 'commission_type')->value('value');
    $commission_per_sale = GlobalSetting::where('key', 'commission_per_sale')->value('value');


    $total_commission = 0.00;
    $net_income = $total_income;
    if ($commission_type == 'commission') {
        $total_commission = ($commission_per_sale / 100) * $total_income;
        $net_income = $total_income - $total_commission;
    }

    return $net_income;
}

function commission_calculate($total_income)
{
    $commission_type = GlobalSetting::where('key', 'commission_type')->value('value');
    $commission_per_sale = GlobalSetting::where('key', 'commission_per_sale')->value('value');


    $total_commission = 0.00;
    $net_income = $total_income;
    if ($commission_type == 'commission') {
        $total_commission = ($commission_per_sale / 100) * $total_income;
    }

    return $total_commission;
}


function getAllResourceFiles($dir, &$results = array())
{
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = $dir . "/" . $value;
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getAllResourceFiles($path, $results);
        }
    }
    return $results;
}

function getRegexBetween($content)
{

    preg_match_all("%\{{ __\(['|\"](.*?)['\"]\) }}%i", $content, $matches1, PREG_PATTERN_ORDER);
    preg_match_all("%\@lang\(['|\"](.*?)['\"]\)%i", $content, $matches2, PREG_PATTERN_ORDER);
    preg_match_all("%trans\(['|\"](.*?)['\"]\)%i", $content, $matches3, PREG_PATTERN_ORDER);
    $Alldata = [$matches1[1], $matches2[1], $matches3[1]];
    $data = [];
    foreach ($Alldata as  $value) {
        if (!empty($value)) {
            foreach ($value as $val) {
                $data[$val] = $val;
            }
        }
    }
    return $data;
}

function generateLang($path = '')
{

    // user panel
    $paths = getAllResourceFiles(resource_path('views'));

    $paths = array_merge($paths, getAllResourceFiles(app_path()));

    $paths = array_merge($paths, getAllResourceFiles(base_path('Modules')));

    // end user panel

    $AllData = [];
    foreach ($paths as $key => $path) {
        $AllData[] = getRegexBetween(file_get_contents($path));
    }
    $modifiedData = [];
    foreach ($AllData as  $value) {
        if (!empty($value)) {
            foreach ($value as $val) {
                $modifiedData[$val] = $val;
            }
        }
    }

    $modifiedData = var_export($modifiedData, true);

    file_put_contents('lang/en/translate.php', "<?php\n return {$modifiedData};\n ?>");
}


function checkModule($module_name)
{
    $json_module_data = file_get_contents(base_path('modules_statuses.json'));
    $module_status = json_decode($json_module_data);

    if (isset($module_status->$module_name) && $module_status->$module_name && File::exists(base_path('Modules') . '/' . $module_name)) {
        return true;
    }

    return false;
}




function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views\admin') . '\settings.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}



function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    $query = Frontend::query();

    if ($singleQuery) {
        $content = $query->where('data_keys', $dataKeys)
            ->orderBy('id', 'desc')
            ->first();
    } else {
        if ($limit != null) {
            $query->limit($limit);
        }

        if ($orderById) {
            $query->orderBy('id');
        } else {
            $query->orderBy('id', 'desc');
        }

        $content = $query->where('data_keys', $dataKeys)->get();
    }

    return $content;
}

function getTranslatedValue($content, $key)
{
    if (!$content) {
        return '';
    }

    $lang = 'en';

    $front_lang = Session::get('front_lang');

    if ($front_lang) {
        $lang = $front_lang;
    }

    // If translations exist and language is not English
    if ($lang !== 'en') {
        $translations = json_decode($content->data_translations, true);


        // Loop through the translations to find the matching language code
        foreach ($translations as $translation) {
            if (isset($translation['language_code']) && $translation['language_code'] === $lang) {
                // Return the translated value if it exists
                $decode_value = isset($translation['values'][$key]) ? $translation['values'][$key] : '';
                return html_decode($decode_value);
            }
        }


        // If no translation found for requested language, return default string

        $decode_value = isset($content->data_values[$key]) ? $content->data_values[$key] : '';

        return html_decode($decode_value);
    }

    // Fallback to English content
    $decode_value = isset($content->data_values[$key]) ? $content->data_values[$key] : '';

    return html_decode($decode_value);
}


function getImage($content, $key)
{

    return isset($content->data_values['images'][$key]) ? $content->data_values['images'][$key] : '';
}

function getSingleImage($content, $key)
{
    return isset($content->data_values[$key]) ? $content->data_values[$key] : '';
}
function getLink($link)
{
    $isValidUrl = filter_var($link, FILTER_VALIDATE_URL);
    $url = $isValidUrl ? $link : 'https://' . str_replace(' ', '', strtolower($link));
    return $url;
}

if (!function_exists('getImageOrPlaceholder')) {
    function getImageOrPlaceholder(?string $imagePath, string $size = '800x600'): string
    {
        if ($imagePath && file_exists(public_path($imagePath))) {
            return asset($imagePath);
        }

        return "https://placehold.co/{$size}?text={$size}";
    }
}

function randomNumber($length = 10)
{
    $random = '';
    $possible = '0123456789';

    for ($i = 0; $i < $length; $i++) {
        $random .= $possible[rand(0, strlen($possible) - 1)];
    }

    return $random;
}


function getTranslatedSlides(?object $content, string $key): array
{
    if (!$content) {
        return [];
    }

    $lang = Session::get('front_lang', 'en');

    // Get default values (usually English)
    $defaultValues = $content->data_values[$key] ?? [];

    // Get translations array
    $translations = json_decode($content->data_translations, true);

    if (is_array($translations)) {
        foreach ($translations as $translation) {
            if (
                isset($translation['language_code'], $translation['values'][$key])
                && $translation['language_code'] === $lang
            ) {
                // Merge translated values with default to fill any missing keys
                if (is_array($translation['values'][$key]) && is_array($defaultValues)) {
                    return array_replace_recursive($defaultValues, $translation['values'][$key]);
                }
                return $translation['values'][$key];
            }
        }
    }

    // Fallback to English/default
    return $defaultValues;
}

function customPaginationCount($items)
{
    $from = ($items->currentPage() - 1) * $items->perPage() + 1;
    $to = min($from + $items->count() - 1, $items->total());
    $total = $items->total();

    return "Showing {$from} - {$to} of {$total} entries";
}

function serviceTypeTab()
{
    return ServiceType::select('id', 'name', 'image', 'icon')
        ->where('status', true)
        ->where('show_on_homepage', true)
        ->orderBy('id', 'desc')
        ->take(6)
        ->get();
}

function destinations()
{
    return Destination::select('id', 'name')
        ->where('status', true)
        ->get();
}

function popularServices($count = 8, $isPagination = false)
{
    $query = Service::select('id', 'price_per_person', 'slug', 'location', 'is_featured', 'full_price', 'discount_price', 'is_new', 'duration', 'group_size', 'service_type_id', 'destination_id', 'is_per_person')
        ->where('status', true)
        ->where('is_popular', true)
        ->where('show_on_homepage', true)
        ->withExists('myWishlist')
        ->with([
            'thumbnail:id,service_id,caption,file_path',
            'translation:id,service_id,locale,title,short_description',
            'destination:id,name',
        ])
        ->withCount('activeReviews')
        ->withAvg('activeReviews', 'rating')
        ->latest();

    return $isPagination ? $query->paginate($count) : $query->take($count)->get();
}

function popularDestinations($count = 4, $isPagination = false)
{
    $query = Destination::select('id', 'name', 'country', 'image', 'tags')
        ->where('status', true)
        ->where('is_featured', true)
        ->withCount(['services' => function ($query) {
            $query->where('status', true);
        }])
        ->latest();

    return $isPagination ? $query->paginate($count) : $query->take($count)->get();
}
