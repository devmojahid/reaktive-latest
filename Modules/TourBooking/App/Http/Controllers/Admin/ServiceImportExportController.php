<?php

declare(strict_types=1);

namespace Modules\TourBooking\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\TourBooking\App\Models\Service;
use Modules\TourBooking\App\Models\ServiceType;
use Modules\TourBooking\App\Models\Destination;
use Modules\TourBooking\App\Models\ServiceTranslation;

final class ServiceImportExportController extends Controller
{
    
    public function instructions()
    {
        // 1) Preferă view-ul din modul (Modules/TourBooking/resources/views/admin/services/import_instructions.blade.php)
        if (view()->exists('tourbooking::admin.services.import_instructions')) {
            return view('tourbooking::admin.services.import_instructions');
        }

        // 2) Fallback dacă ai publicat view-ul în resources/views/admin/services/import_instructions.blade.php
        return view('admin.services.import_instructions');
    }
    
    /** Coloanele MANUALE – import/export (fără tehnice) */
    private array $manualColumns = [
        'title','short_description','description',
        'service_type','destination',
        'location','latitude','longitude',
        'price_per_person','full_price','discount_price','child_price','infant_price',
        'security_deposit','deposit_required','deposit_percentage',
        'duration','group_size','languages',
        'check_in_time','check_out_time','ticket',
        'amenities','facilities','rules','safety','cancellation_policy',
        'video_url','address','email','phone','website',
        'check_in_date','check_out_date',
        'is_featured','is_popular','show_on_homepage','status','is_per_person',
    ];

    /** === EXPORT: doar câmpuri manuale (cu date) === */
    public function exportManual(Request $request)
    {
        $format = $request->get('format', 'xlsx'); // xlsx|csv

        $headings = $this->manualColumns;

        $rows = Service::with(['serviceType:id,name', 'destination:id,name'])
            ->get()
            ->map(function (Service $s) {
                // Helper JSON -> CSV
                $toCsv = function ($val) {
                    if (is_array($val)) return implode(', ', $val);
                    $decoded = json_decode((string) $val, true);
                    return is_array($decoded) ? implode(', ', $decoded) : (string) $val;
                };

                return [
                    'title'             => $s->title,
                    'short_description' => $s->short_description,
                    'description'       => $s->description,
                    'service_type'      => $s->serviceType?->name,
                    'destination'       => $s->destination?->name,
                    'location'          => $s->location,
                    'latitude'          => $s->latitude,
                    'longitude'         => $s->longitude,
                    'price_per_person'  => $s->price_per_person,
                    'full_price'        => $s->full_price,
                    'discount_price'    => $s->discount_price,
                    'child_price'       => $s->child_price,
                    'infant_price'      => $s->infant_price,
                    'security_deposit'  => $s->security_deposit,
                    'deposit_required'  => $s->deposit_required ? 1 : 0,
                    'deposit_percentage'=> $s->deposit_percentage,
                    'duration'          => $s->duration,
                    'group_size'        => $s->group_size,
                    'languages'         => $toCsv($s->languages),
                    'check_in_time'     => $s->check_in_time,
                    'check_out_time'    => $s->check_out_time,
                    'ticket'            => $s->ticket,
                    'amenities'         => $toCsv($s->amenities),
                    'facilities'        => $toCsv($s->facilities),
                    'rules'             => $s->rules,
                    'safety'            => $s->safety,
                    'cancellation_policy'=> $s->cancellation_policy,
                    'video_url'         => $s->video_url,
                    'address'           => $s->address,
                    'email'             => $s->email,
                    'phone'             => $s->phone,
                    'website'           => $s->website,
                    'check_in_date'     => $s->check_in_date,
                    'check_out_date'    => $s->check_out_date,
                    'is_featured'       => $s->is_featured ? 1 : 0,
                    'is_popular'        => $s->is_popular ? 1 : 0,
                    'show_on_homepage'  => $s->show_on_homepage ? 1 : 0,
                    'status'            => $s->status ? 1 : 0,
                    'is_per_person'     => $s->is_per_person ? 1 : 0,
                ];
            });

        $export = new class($rows, $headings) implements FromCollection, WithHeadings {
            public function __construct(private Collection $rows, private array $headings) {}
            public function collection() { return $this->rows; }
            public function headings(): array { return $this->headings; }
        };

        $filename = 'services_manual_' . now()->format('Ymd_His') . '.' . $format;
        return Excel::download($export, $filename);
    }

    /** === EXPORT: template (doar headere, fără rânduri) === */
    public function exportTemplate(Request $request)
    {
        $format = $request->get('format', 'xlsx'); // xlsx|csv
        $headings = $this->manualColumns;

        $export = new class($headings) implements FromCollection, WithHeadings {
            public function __construct(private array $headings) {}
            public function collection() { return collect(); }
            public function headings(): array { return $this->headings; }
        };

        $filename = 'services_template_' . now()->format('Ymd_His') . '.' . $format;
        return Excel::download($export, $filename);
    }

    /** === IMPORT: CSV/XLSX cu câmpuri manuale; tehnice se generează === */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt|max:20480',
            'locale' => 'nullable|string' // limbă pt traducere, implicit admin_lang()
        ]);

        $locale = $request->get('locale', admin_lang());

        $sheets = Excel::toCollection(null, $request->file('file'));
        $rows   = $sheets->first() ?? collect();

        if ($rows->isEmpty()) {
            return back()->with(['message' => 'Empty file', 'alert-type' => 'error']);
        }

        // Prima linie = headere
        $headers = collect($rows->first())->map(fn($h) => Str::of((string)$h)->lower()->trim()->toString())->all();
        $rows    = $rows->slice(1); // exclude headere

        // map pentru acces rapid col->index
        $idx = [];
        foreach ($headers as $i => $h) {
            $idx[$h] = $i;
        }

        $report = [];
        $ok = $skip = $err = 0;

        foreach ($rows as $rIndex => $row) {
            try {
                $get = function (string $key) use ($idx, $row) {
                    if (!array_key_exists($key, $idx)) return null;
                    $v = $row[$idx[$key]] ?? null;
                    return is_string($v) ? trim($v) : $v;
                };

                $title = (string)($get('title') ?? '');
                if ($title === '') { $skip++; $report[] = [$rIndex+2,'skipped','Missing title','']; continue; }

                // 1) Service Type (creăm dacă nu există)
                $typeVal = $get('service_type');
                $serviceTypeId = null;
                if ($typeVal !== null && $typeVal !== '') {
                    if (is_numeric($typeVal)) {
                        $serviceTypeId = (int)$typeVal;
                        $exists = ServiceType::find($serviceTypeId);
                        if (!$exists) $serviceTypeId = null;
                    }
                    if (!$serviceTypeId) {
                        $st = ServiceType::firstOrCreate(
                            ['name' => $typeVal],
                            ['slug' => Str::slug($typeVal), 'status' => 1]
                        );
                        $serviceTypeId = $st->id;
                    }
                }

                // 2) Destination (creăm dacă nu există)
                $destVal = $get('destination');
                $destinationId = null;
                if ($destVal !== null && $destVal !== '') {
                    if (is_numeric($destVal)) {
                        $destinationId = (int)$destVal;
                        $exists = Destination::find($destinationId);
                        if (!$exists) $destinationId = null;
                    }
                    if (!$destinationId) {
                        $d = Destination::firstOrCreate(
                            ['name' => $destVal],
                            ['slug' => Str::slug($destVal), 'status' => 1]
                        );
                        $destinationId = $d->id;
                    }
                }

                // 3) Normalize booleans & JSON
                $bool = fn($v) => in_array(Str::lower((string)$v), ['1','true','yes','y','da'], true) ? 1 : 0;
                $csvToJson = function($v) {
                    if ($v === null || $v === '') return null;
                    // dacă e deja JSON valid, păstrăm
                    $d = json_decode((string)$v, true);
                    if (json_last_error() === JSON_ERROR_NONE) return json_encode($d);
                    // altfel CSV -> array -> json
                    $arr = array_values(array_filter(array_map(fn($x)=>trim($x), explode(',', (string)$v)), fn($x)=>$x!==''));
                    return $arr ? json_encode($arr) : null;
                };

                // 4) Slug unic
                $baseSlug = Str::slug($title);
                $slug = $baseSlug;
                $i = 2;
                while (Service::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$i;
                    $i++;
                }

                $service = Service::create([
                    'title'             => $title,
                    'short_description' => $get('short_description'),
                    'description'       => $get('description'),
                    'slug'              => $slug,
                    'location'          => $get('location'),
                    'latitude'          => $get('latitude'),
                    'longitude'         => $get('longitude'),
                    'service_type_id'   => $serviceTypeId ?? 1, // fallback, dar în mod normal îl setăm corect
                    'destination_id'    => $destinationId,
                    'price_per_person'  => $get('price_per_person'),
                    'full_price'        => $get('full_price'),
                    'discount_price'    => $get('discount_price'),
                    'child_price'       => $get('child_price'),
                    'infant_price'      => $get('infant_price'),
                    'security_deposit'  => $get('security_deposit'),
                    'deposit_required'  => $bool($get('deposit_required')),
                    'deposit_percentage'=> $get('deposit_percentage'),
                    'duration'          => $get('duration'),
                    'group_size'        => $get('group_size'),
                    'languages'         => $csvToJson($get('languages')),
                    'check_in_time'     => $get('check_in_time'),
                    'check_out_time'    => $get('check_out_time'),
                    'ticket'            => $get('ticket'),
                    'amenities'         => $csvToJson($get('amenities')),
                    'facilities'        => $csvToJson($get('facilities')),
                    'rules'             => $get('rules'),
                    'safety'            => $get('safety'),
                    'cancellation_policy'=> $get('cancellation_policy'),
                    'video_url'         => $get('video_url'),
                    'address'           => $get('address'),
                    'email'             => $get('email'),
                    'phone'             => $get('phone'),
                    'website'           => $get('website'),
                    'check_in_date'     => $get('check_in_date') ?: null,
                    'check_out_date'    => $get('check_out_date') ?: null,
                    'is_featured'       => $bool($get('is_featured')),
                    'is_popular'        => $bool($get('is_popular')),
                    'show_on_homepage'  => $bool($get('show_on_homepage')),
                    'status'            => $bool($get('status')) ?: 1,
                    'is_per_person'     => $bool($get('is_per_person')),
                    'user_id'           => Auth::id(),
                ]);

                // traducere minimă în limba admin (ca în create/update din controllerul tău)
                ServiceTranslation::updateOrCreate(
                    ['service_id' => $service->id, 'locale' => $locale],
                    [
                        'title'             => $service->title,
                        'description'       => $service->description,
                        'short_description' => $service->short_description,
                        'seo_title'         => null,
                        'seo_description'   => null,
                        'seo_keywords'      => null,
                        'included'          => $service->included,
                        'excluded'          => $service->excluded,
                        'amenities'         => json_decode($service->amenities, true) ?: [],
                        'facilities'        => $service->facilities,
                        'rules'             => $service->rules,
                        'safety'            => $service->safety,
                        'cancellation_policy' => $service->cancellation_policy,
                    ]
                );

                $ok++;
                $report[] = [$rIndex+2,'created','OK',$service->id];
            } catch (\Throwable $e) {
                $err++;
                $report[] = [$rIndex+2,'error', Str::limit($e->getMessage(), 200), ''];
            }
        }

        // scriem raport CSV în storage și îl prezentăm în modal
        $folder = 'import-reports';
        Storage::makeDirectory($folder);
        $filename = $folder.'/services_import_'.now()->format('Ymd_His').'.csv';

        $csv = implode(",", ['Row','Status','Message','ServiceID'])."\n";
        foreach ($report as $line) {
            $csv .= implode(",", array_map(fn($x)=>'"'.str_replace('"','""',(string)$x).'"',$line))."\n";
        }
        Storage::put($filename, $csv);

        return redirect()
            ->route('admin.tourbooking.services.index')
            ->with('import_result', [
                'ok'   => $ok,
                'skip' => $skip,
                'err'  => $err,
                'report_path' => $filename,
            ]);
    }

    /** === descarcă raportul CSV din sesiune === */
    public function downloadReport(Request $request)
    {
        $path = $request->query('path');
        abort_unless($path && Storage::exists($path), 404);

        return Storage::download($path, basename($path));
    }
}
