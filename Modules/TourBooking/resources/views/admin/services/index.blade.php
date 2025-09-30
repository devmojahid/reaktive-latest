@extends('admin.master_layout')
@section('title')
    <title>{{ __('translate.Services List') }}</title>
@endsection

@section('body-header')
    <h3 class="crancy-header__title m-0">{{ __('translate.Services List') }}</h3>
    <p class="crancy-header__text">{{ __('translate.Tour Booking') }} >> {{ __('translate.Services List') }}</p>
@endsection

@section('body-content')
    <section class="crancy-adashboard crancy-show">
        <div class="container container__bscreen">
            <div class="row">
                <div class="col-12">
                    <div class="crancy-body">
                        <div class="crancy-dsinner">
                            <div class="crancy-table crancy-table--v3 mg-top-30">

                                <div class="crancy-customer-filter">
                                    <div class="crancy-header__form crancy-header__form--customer create_new_btn_inline_box">
                                        <h4 class="crancy-product-card__title">{{ __('translate.All Services') }}</h4>

                                        <div class="d-flex align-items-center gap-2">
                                            {{-- EXISTENT (ajustat): Add new -> dimensiune egală --}}
                                            <a href="{{ route('admin.tourbooking.services.create') }}" class="crancy-btn equal-btn">
                                                <i class="fa fa-plus"></i> {{ __('Add New Experience') }}
                                            </a>

                                            {{-- NOU: Dropdown Import/Export (aceeași dimensiune & stil) --}}
                                            <div class="dropdown">
                                                <button class="crancy-btn brand-btn dropdown-toggle equal-btn"
                                                        type="button" id="impexpMenuBtn"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-file-arrow-up"></i>
                                                    Import / Export
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="impexpMenuBtn">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                                           href="#"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#importModal">
                                                            <i class="fa-solid fa-file-import"></i> Import
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                                           href="{{ route('admin.tourbooking.services.export.manual') }}">
                                                            <i class="fa-solid fa-file-export"></i> Export Manual
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                                           href="{{ route('admin.tourbooking.services.export.template') }}">
                                                            <i class="fa-solid fa-download"></i> Export Template
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2"
                                                           href="{{ route('admin.tourbooking.services.import.instructions') }}" target="_blank">
                                                            <i class="fa-regular fa-circle-question"></i> Import Instructions
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                            {{-- /NOU --}}
                                        </div>
                                    </div>
                                </div>

                                <div id="crancy-table__main_wrapper" class=" dt-bootstrap5 no-footer">
                                    <table class="crancy-table__main crancy-table__main-v3  no-footer" id="dataTable">
                                        <thead class="crancy-table__head">
                                            <tr>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Image') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Title') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Type') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Location') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Status') }}</th>
                                                <th class="crancy-table__column-2 crancy-table__h2 sorting">{{ __('translate.Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="crancy-table__body">
                                            @foreach ($services as $service)
                                                <tr class="odd">
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        @if ($service->thumbnail && $service->thumbnail->file_path)
                                                            <img src="{{ asset($service->thumbnail->file_path) }}"
                                                                 alt="{{ $service->translation->title ?? $service->title }}" width="80">
                                                        @else
                                                            <img src="{{ asset('admin/img/img-placeholder.jpg') }}" alt="No image" width="80">
                                                        @endif
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        {{ Str::limit($service->translation->title ?? $service->title, 50) }}
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        {{ $service->serviceType->name ?? 'N/A' }}
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        {{ $service->location ?? 'N/A' }}
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        @if ($service->status)
                                                            <span class="crancy-badge crancy-badge-success">{{ __('translate.Active') }}</span>
                                                        @else
                                                            <span class="crancy-badge crancy-badge-danger">{{ __('translate.Inactive') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="crancy-table__column-2 crancy-table__data-2">
                                                        <a href="{{ route('admin.tourbooking.services.edit', ['service' => $service->id, 'lang_code' => admin_lang()]) }}"
                                                           class="crancy-action__btn crancy-action__edit crancy-btn">
                                                            <i class="fa fa-edit"></i> {{ __('translate.Edit') }}
                                                        </a>
                                                        <a onclick="itemDeleteConfrimation({{ $service->id }})"
                                                           href="javascript:;" data-bs-toggle="modal"
                                                           data-bs-target="#exampleModal"
                                                           class="crancy-btn delete_danger_btn">
                                                            <i class="fas fa-trash"></i>
                                                        </a>

                                                        <div class="dropdown" style="display: inline;">
                                                            <button class="crancy-action__btn" type="button"
                                                                    style="width: 40px;"
                                                                    id="dropdownMenuButton{{ $service->id }}"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $service->id }}">
                                                                <li><a class="dropdown-item" href="{{ route('admin.tourbooking.services.itineraries', $service->id) }}">{{ __('translate.Itineraries') }}</a></li>
                                                                @if ($service->is_per_person)
                                                                    <li><a class="dropdown-item" href="{{ route('admin.tourbooking.services.extra-charges', $service->id) }}">{{ __('translate.Extra Charges') }}</a></li>
                                                                @endif
                                                                <li><a class="dropdown-item" href="{{ route('admin.tourbooking.services.availability', $service->id) }}">{{ __('translate.Availability') }}</a></li>
                                                                <li><a class="dropdown-item" href="{{ route('admin.tourbooking.services.media', $service->id) }}">{{ __('translate.Media Gallery') }}</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


<!-- Delete Confirmation Modal (existent) -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('translate.Delete Confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('translate.Are you realy want to delete this item?') }}</p>
            </div>
            <div class="modal-footer">
                <form action="" id="item_delect_confirmation" class="delet_modal_form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('translate.Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('translate.Yes, Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- NOU: Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form id="importForm" action="{{ route('admin.tourbooking.services.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="importModalLabel">Import Experiences</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info small d-flex align-items-start gap-2">
            <i class="fa fa-circle-info mt-1"></i>
            <div>
              IDs & câmpuri tehnice sunt generate automat la import.
              Citește ghidul complet al coloanelor:
              <a href="{{ route('admin.tourbooking.services.import.instructions') }}" target="_blank">Import columns guide</a>.
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">File (CSV, XLSX)</label>
            <input type="file" name="file" class="form-control" accept=".csv,.xlsx,.xls" required>
            <div class="form-text">
              Ai nevoie de un șablon?
              <a href="{{ route('admin.tourbooking.services.export.template') }}">Download template</a>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="crancy-btn brand-btn equal-btn" id="importStartBtn">
            <i class="fa fa-upload"></i> Start Import
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- NOU: Import Result Modal (preview + butoane) --}}
<div class="modal fade" id="importResultModal" tabindex="-1" aria-labelledby="importResultLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importResultLabel">Import result</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="impSummary" class="mb-3"></div>
        <div class="table-responsive import-preview-wrapper">
          <table class="table table-sm table-striped" id="impPreviewTable">
            <thead></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer justify-content-end">
        <a id="impReportBtn" href="#" class="crancy-btn brand-btn equal-btn" download>
          <i class="fa fa-download"></i> Download CSV report
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('style_section')
<style>
  /* Brand color pentru butoanele dropdown + import */
  .brand-btn{
    background:#ff4200 !important;
    border-color:#ff4200 !important;
    color:#fff !important;
  }
  .brand-btn:hover,
  .brand-btn:focus{
    background:#e53b00 !important;
    border-color:#e53b00 !important;
    color:#fff !important;
  }

  /* aceeași înălțime & aliniere ca Add New Experience */
  .equal-btn{
    height: 56px;              /* aliniază cu butonul existent din temă */
    padding: 0 28px;
    display: inline-flex;
    align-items: center;
    gap: .75rem;
    border-radius: 16px;
    line-height: 1.2;
  }

  .import-preview-wrapper{ max-height:55vh; overflow:auto; }
</style>
@endpush

@push('js_section')
<script>
"use strict";

/* păstrat: delete confirmation */
function itemDeleteConfrimation(id) {
  document.getElementById("item_delect_confirmation")
          .setAttribute("action", '{{ url('admin/tourbooking/services/') }}' + "/" + id);
}

/* Import AJAX -> așteaptă JSON din controller (summary/preview/report_url sau report_token) */
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('importForm');
  if (!form) return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const btn = document.getElementById('importStartBtn');
    const importModalEl = document.getElementById('importModal');
    const resultModalEl = document.getElementById('importResultModal');
    const resultModal = new bootstrap.Modal(resultModalEl);

    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Importing...';

    try {
      const fd = new FormData(form);
      const res = await fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      if (!res.ok) throw new Error('Import request failed: ' + res.status);

      const data = await res.json();

      // închide import modal
      bootstrap.Modal.getInstance(importModalEl)?.hide();
      form.reset();

      // sumar + preview
      renderImportSummary(data?.summary);
      renderImportPreview(data?.preview);

      // buton raport
      const reportBtn = document.getElementById('impReportBtn');
      if (data?.report_url) {
        reportBtn.href = data.report_url;
      } else if (data?.report_token) {
        reportBtn.href = "{{ route('admin.tourbooking.services.import.report') }}" + "?token=" + encodeURIComponent(data.report_token);
      } else {
        reportBtn.removeAttribute('href');
      }

      resultModal.show();
    } catch (err) {
      console.error(err);
      alert('Import failed. Please check your file and try again.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
    }
  });
});

function renderImportSummary(summary) {
  const box = document.getElementById('impSummary');
  const s = summary || {};
  box.innerHTML = `
    <div class="row g-2">
      <div class="col-auto"><span class="badge bg-success">Created: ${Number(s.created||0)}</span></div>
      <div class="col-auto"><span class="badge bg-primary">Updated: ${Number(s.updated||0)}</span></div>
      <div class="col-auto"><span class="badge bg-warning text-dark">Skipped: ${Number(s.skipped||0)}</span></div>
      <div class="col-auto"><span class="badge bg-danger">Errors: ${Number(s.errors||0)}</span></div>
    </div>
  `;
}

function renderImportPreview(rows) {
  const thead = document.querySelector('#impPreviewTable thead');
  const tbody = document.querySelector('#impPreviewTable tbody');
  thead.innerHTML = '';
  tbody.innerHTML = '';

  if (!rows || !rows.length) {
    thead.innerHTML = '<tr><th>Preview</th></tr>';
    tbody.innerHTML = '<tr><td class="text-muted">No preview rows.</td></tr>';
    return;
  }

  const keys = Object.keys(rows[0]);
  thead.innerHTML = `<tr>${keys.map(k=>`<th class="text-capitalize">${escapeHtml(k.replace(/_/g,' '))}</th>`).join('')}</tr>`;
  tbody.innerHTML = rows.map(r => `<tr>${keys.map(k => `<td>${escapeHtml(r[k] ?? '')}</td>`).join('')}</tr>`).join('');
}

function escapeHtml(v){
  return String(v ?? '')
    .replaceAll('&','&amp;')
    .replaceAll('<','&lt;')
    .replaceAll('>','&gt;')
    .replaceAll('"','&quot;')
    .replaceAll("'",'&#039;');
}
</script>
@endpush
