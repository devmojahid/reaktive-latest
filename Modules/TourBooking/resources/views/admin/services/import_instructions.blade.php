@extends('admin.master_layout')
@section('title')
  <title>Import Columns Guide</title>
@endsection

@section('body-header')
  <h3 class="crancy-header__title m-0">Import Columns Guide</h3>
  <p class="crancy-header__text">Tour Booking >> Import instructions</p>
@endsection

@section('body-content')
<section class="crancy-adashboard crancy-show">
  <div class="container container__bscreen">
    <div class="row">
      <div class="col-12">
        <div class="crancy-body">
          <div class="crancy-dsinner">
            <div class="card" style="border-radius:14px; border:1px solid #eef1f5;">
              <div class="card-body">
                <p class="mb-3">
                  La import, <strong>ID-urile și câmpurile tehnice</strong> (ex: id, user_id, created_at, updated_at, slug dacă lipsește) sunt
                  <strong>generate automat de platformă</strong>. Furnizează doar datele de conținut enumerate mai jos.
                </p>

                <div class="table-responsive">
                  <table class="table table-bordered align-middle">
                    <thead>
                      <tr>
                        <th>Column</th>
                        <th>Type / Examples</th>
                        <th>Notes</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><code>title</code></td>
                        <td>string — “Classic Munich – History & Traditions”</td>
                        <td>Obligatoriu. Slug se generează automat dacă lipsește.</td>
                      </tr>
                      <tr>
                        <td><code>description</code></td>
                        <td>string (poate conține HTML)</td>
                        <td>Descrierea produsului.</td>
                      </tr>
                      <tr>
                        <td><code>service_type</code></td>
                        <td>string — numele sau slug-ul tipului (“Tours”, “rentals”, …)</td>
                        <td>Se mapează automat la tipul existent. Dacă nu se găsește, rândul este marcat ca “skipped”.</td>
                      </tr>
                      <tr>
                        <td><code>destination</code></td>
                        <td>string — “Munich”, “Crete”, “Istanbul”</td>
                        <td>Destinația afișată în site.</td>
                      </tr>
                      <tr>
                        <td><code>location</code></td>
                        <td>string — “Germany, Munich”</td>
                        <td>Locația exactă/afişată.</td>
                      </tr>
                      <tr>
                        <td><code>language</code></td>
                        <td>string — ISO (ex. “EN”, “DE”, “RO”)</td>
                        <td>Limba principală a experienței.</td>
                      </tr>
                      <tr>
                        <td><code>price</code></td>
                        <td>numeric — 49.00</td>
                        <td>Prețul de bază afişat.</td>
                      </tr>
                      <tr>
                        <td><code>currency</code></td>
                        <td>string — ISO 4217 (ex. “EUR”, “USD”)</td>
                        <td>Moneda prețului.</td>
                      </tr>
                      <tr>
                        <td><code>status</code></td>
                        <td>0/1</td>
                        <td>0 = Inactive, 1 = Active (implicit 1 dacă lipsește).</td>
                      </tr>
                      <tr>
                        <td><code>images</code></td>
                        <td>string — listează URL-uri separate cu <code>|</code></td>
                        <td>Opțional. Ex.: <small>https://site/img1.jpg|https://site/img2.jpg</small></td>
                      </tr>
                      <tr>
                        <td><code>amenities</code></td>
                        <td>string — nume separate cu <code>|</code></td>
                        <td>Opțional. Trebuie să existe în sistem sau se vor ignora.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="alert alert-secondary mt-3">
                  <strong>Notă:</strong> câmpuri tehnice <em>generate automat</em>: <code>id</code>, <code>user_id</code>,
                  <code>created_at</code>, <code>updated_at</code>, <code>slug</code> (dacă lipsește), orice chei interne necesare platformei.
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                  <a href="{{ route('admin.tourbooking.services.export.template') }}" class="btn btn-outline-primary">
                    <i class="fa fa-download"></i> Download Template
                  </a>
                  <a href="{{ route('admin.tourbooking.services.index') }}" class="btn btn-secondary">
                    Back to list
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
