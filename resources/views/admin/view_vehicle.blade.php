@extends('admin.includes.main')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Vehicle Details</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('admin/all_vehicles') }}">All Vehicles</a></li>
        <li class="breadcrumb-item active">Vehicle Details</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row g-3">

      {{-- LEFT COLUMN --}}
      <div class="col-lg-8">

        {{-- Driver Banner --}}
        @if($vehicle->driver_id && $vehicle->driver_id != '')
        <div class="d-flex align-items-center gap-3 mb-3 p-3"
             style="background:#e1f5ee;border:1px solid #b2dfdb;border-radius:10px;">
          <i class="bi bi-person-check-fill text-success fs-4"></i>
          <div>
            <p class="mb-0 fw-semibold" style="font-size:14px;">Assigned Driver</p>
            <p class="mb-0 text-muted" style="font-size:13px;">
              {{ $vehicle->driver_name ?? 'Driver #'.$vehicle->driver_id }}
              @if($vehicle->driver_mobile) &nbsp;·&nbsp; {{ $vehicle->driver_mobile }} @endif
              @if($vehicle->driver_email) &nbsp;·&nbsp; {{ $vehicle->driver_email }} @endif
            </p>
          </div>
        </div>
        @else
        <div class="d-flex align-items-center gap-3 mb-3 p-3"
             style="background:#faeeda;border:1px solid #f5cba7;border-radius:10px;">
          <i class="bi bi-exclamation-circle-fill text-warning fs-4"></i>
          <div>
            <p class="mb-0 fw-semibold" style="font-size:14px;">Unassigned Vehicle</p>
            <p class="mb-0 text-muted" style="font-size:13px;">This vehicle is not assigned to any driver.</p>
          </div>
        </div>
        @endif

        {{-- Vehicle Information Card --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
          <div class="card-body p-4">
            <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
              <i class="bi bi-car-front me-2 text-primary"></i>Vehicle Information
            </h6>
            <div class="row g-3">
              @php
                $fields = [
                  ['label' => 'Vehicle Number',    'value' => $vehicle->vehicle_number,    'icon' => 'bi-upc'],
                  ['label' => 'Vehicle Name',      'value' => $vehicle->vehicle_name,      'icon' => 'bi-card-text'],
                  ['label' => 'Manufacturer',      'value' => $vehicle->vehicle_manufacture,'icon' => 'bi-building'],
                  ['label' => 'Vehicle Type',      'value' => $vehicle->vehicle_type_name, 'icon' => 'bi-tag'],
                  ['label' => 'Model',             'value' => $vehicle->vehicle_model,     'icon' => 'bi-layers'],
                  ['label' => 'Registration Year', 'value' => $vehicle->vehicle_reg_year,  'icon' => 'bi-calendar'],
                  ['label' => 'Fuel Type',         'value' => $vehicle->vehicle_fuel_type, 'icon' => 'bi-fuel-pump'],
                  ['label' => 'Owner Name',        'value' => $vehicle->vehicle_owner_name,'icon' => 'bi-person'],
                  ['label' => 'Country Operating', 'value' => $vehicle->country_operating, 'icon' => 'bi-globe'],
                  ['label' => 'Seating Capacity',  'value' => $vehicle->seating_capicity, 'icon' => 'bi-globe'],
                  ['label' => 'Vehicle Color',     'value' => $vehicle->vehicle_color, 'icon' => 'bi-globe'],
                ];
              @endphp
              @foreach($fields as $f)
              <div class="col-md-4">
                <div style="background:#f8f9fa;border-radius:8px;padding:12px 14px;">
                  <p class="mb-1 d-flex align-items-center gap-1"
                     style="font-size:11px;color:#888;text-transform:uppercase;letter-spacing:.5px;">
                    <i class="bi {{ $f['icon'] }}" style="font-size:11px;"></i> {{ $f['label'] }}
                  </p>
                  <p class="mb-0 fw-medium" style="font-size:14px;">{{ $f['value'] ?: '—' }}</p>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        {{-- Documents & Photos Card --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
          <div class="card-body p-4">
            <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
              <i class="bi bi-file-earmark-text me-2 text-warning"></i>Documents & Photos
            </h6>
            @php
              $docs = [
                ['label' => 'Registration — Front',   'file' => $vehicle->id_proof,  'icon' => 'bi-cloud-upload'],
                ['label' => 'Registration — Back',    'file' => $vehicle->id_proof2, 'icon' => 'bi-cloud-upload'],
                ['label' => 'Insurance Paper',        'file' => $vehicle->photo1,     'icon' => 'bi-shield-check'],
                ['label' => 'Inspection Report',      'file' => $vehicle->photo2,     'icon' => 'bi-clipboard-check'],
                ['label' => 'Vehicle Photo — Front',  'file' => $vehicle->certificate1,           'icon' => 'bi-camera'],
                ['label' => 'Vehicle Photo — Back',   'file' => $vehicle->certificate2,           'icon' => 'bi-camera'],
              ];
            @endphp
            <div class="row g-3">
              @foreach($docs as $doc)
              <div class="col-md-4">
                @if($doc['file'])
                <div class="doc-card" onclick="openLightbox('{{ URL::to('public/vehicle_image/vehicle_photo/' . $doc['file']) }}','{{ $doc['label'] }}')">
                  <img src="{{ URL::to('public/vehicle_image/vehicle_photo/' . $doc['file']) }}"
                       alt="{{ $doc['label'] }}"
                       onerror="this.parentElement.classList.add('doc-card-missing'); this.style.display='none';">
                  <div class="doc-card-overlay"><i class="bi bi-zoom-in"></i></div>
                  <div class="doc-card-label">
                    <i class="bi {{ $doc['icon'] }} me-1"></i>{{ $doc['label'] }}
                  </div>
                </div>
                @else
                <div class="doc-card doc-card-empty">
                  <i class="bi {{ $doc['icon'] }} fs-3 text-muted"></i>
                  <p class="mb-0 mt-1" style="font-size:11px;color:#bbb;">Not uploaded</p>
                  <div class="doc-card-label" style="background:#f5f5f5;color:#aaa;">
                    {{ $doc['label'] }}
                  </div>
                </div>
                @endif
              </div>
              @endforeach
            </div>
          </div>
        </div>

      </div>

      {{-- RIGHT SIDEBAR --}}
      <div class="col-lg-4">

        {{-- Actions Card --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
          <div class="card-body p-4">
            <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
              <i class="bi bi-lightning me-2 text-warning"></i>Actions
            </h6>
            <div class="d-grid gap-2">
              <a href="{{ url('admin/edit_vehicle_page/' . $vehicle->id) }}"
                 class="btn btn-warning text-white">
                <i class="bi bi-pencil me-1"></i> Edit Vehicle
              </a>
              <button type="button" class="btn btn-outline-secondary" id="toggleStatusBtn"
                      data-id="{{ $vehicle->id }}"
                      data-status="{{ $vehicle->doc_status1 }}">
                @if($vehicle->doc_status1 == 1)
                  <i class="bi bi-toggle-on me-1 text-success"></i> Set Inactive
                @else
                  <i class="bi bi-toggle-off me-1 text-danger"></i> Set Active
                @endif
              </button>
              <a href="{{ url('admin/all_vehicles') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
              </a>
            </div>
          </div>
        </div>

        {{-- Status & Summary Card --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
          <div class="card-body p-4">
            <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
              <i class="bi bi-info-circle me-2 text-primary"></i>Summary
            </h6>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="text-muted" style="font-size:13px;">Status</span>
              <span id="statusBadge"
                    class="badge px-3 py-2 {{ $vehicle->doc_status1 == 1 ? 'bg-success' : 'bg-danger' }}"
                    style="font-size:12px;">
                {{ $vehicle->doc_status1 == 1 ? 'Active' : 'Inactive' }}
              </span>
            </div>

            @php
              $summary = [
                ['label' => 'Vehicle #',   'value' => $vehicle->vehicle_number  ?: '—'],
                ['label' => 'Type',        'value' => $vehicle->vehicle_type_name ?: '—'],
                ['label' => 'Model',       'value' => $vehicle->vehicle_model   ?: '—'],
                ['label' => 'Fuel',        'value' => $vehicle->vehicle_fuel_type ?: '—'],
                ['label' => 'Reg Year',    'value' => $vehicle->vehicle_reg_year ?: '—'],
              ];
            @endphp
            @foreach($summary as $s)
            <div class="d-flex justify-content-between align-items-center py-2
                        {{ !$loop->last ? 'border-bottom' : '' }}"
                 style="font-size:13px;">
              <span class="text-muted">{{ $s['label'] }}</span>
              <span class="fw-medium text-end" style="max-width:60%;word-break:break-all;">{{ $s['value'] }}</span>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Driver Card --}}
        @if($vehicle->driver_id && $vehicle->driver_id != '')
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
          <div class="card-body p-4">
            <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
              <i class="bi bi-person me-2 text-success"></i>Driver Info
            </h6>
            <div class="d-flex align-items-center gap-3 mb-2">
              <div style="width:42px;height:42px;border-radius:50%;background:#e1f5ee;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-person-fill text-success fs-5"></i>
              </div>
              <div>
                <p class="mb-0 fw-semibold" style="font-size:14px;">{{ $vehicle->driver_name ?? '—' }}</p>
                <p class="mb-0 text-muted" style="font-size:12px;">{{ $vehicle->driver_email ?? '—' }}</p>
              </div>
            </div>
            @if($vehicle->driver_mobile)
            <div class="d-flex align-items-center gap-2 mt-2 p-2"
                 style="background:#f8f9fa;border-radius:8px;font-size:13px;">
              <i class="bi bi-telephone text-muted"></i>
              <span>{{ $vehicle->driver_mobile }}</span>
            </div>
            @endif
          </div>
        </div>
        @endif

      </div>
    </div>
  </section>
</main>

{{-- Lightbox --}}
<div id="lightbox" onclick="closeLightbox()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;
            align-items:center;justify-content:center;flex-direction:column;gap:12px;">
  <button onclick="closeLightbox()" style="position:absolute;top:16px;right:20px;background:none;border:none;color:#fff;font-size:28px;cursor:pointer;">
    <i class="bi bi-x-lg"></i>
  </button>
  <img id="lightboxImg" src="" alt=""
       style="max-width:90vw;max-height:80vh;border-radius:10px;object-fit:contain;box-shadow:0 8px 40px rgba(0,0,0,.5);">
  <p id="lightboxLabel" style="color:#ddd;font-size:13px;margin:0;"></p>
</div>

<style>
.doc-card {
  border: 1px solid #e8e8e8;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  position: relative;
  transition: box-shadow .2s, transform .15s;
}
.doc-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.12); transform: translateY(-2px); }
.doc-card img { width: 100%; height: 130px; object-fit: cover; display: block; }
.doc-card-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background .2s;
  bottom: 32px;
}
.doc-card:hover .doc-card-overlay { background: rgba(0,0,0,.28); }
.doc-card-overlay i { color: #fff; font-size: 22px; opacity: 0; transition: opacity .2s; }
.doc-card:hover .doc-card-overlay i { opacity: 1; }
.doc-card-label {
  font-size: 12px;
  color: #555;
  background: #f9f9f9;
  padding: 5px 10px;
  text-align: center;
  border-top: 1px solid #f0f0f0;
}
.doc-card-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 130px;
  background: #fafafa;
  cursor: default;
}
.doc-card-empty:hover { box-shadow: none; transform: none; }
</style>

<script>
function openLightbox(src, label) {
    var lb = document.getElementById('lightbox');
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxLabel').textContent = label;
    lb.style.display = 'flex';
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.getElementById('lightboxImg').src = '';
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeLightbox(); });

document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('toggleStatusBtn');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var id = this.dataset.id;
        var self = this;
        fetch('{{ url("admin/change_vehicle_status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: id })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.status === 'true' || res.status === true) {
                var badge = document.getElementById('statusBadge');
                if (res.new_status == 1) {
                    badge.textContent = 'Active';
                    badge.className = 'badge px-3 py-2 bg-success';
                    self.innerHTML = '<i class="bi bi-toggle-on me-1 text-success"></i> Set Inactive';
                    self.dataset.status = '1';
                } else {
                    badge.textContent = 'Inactive';
                    badge.className = 'badge px-3 py-2 bg-danger';
                    self.innerHTML = '<i class="bi bi-toggle-off me-1 text-danger"></i> Set Active';
                    self.dataset.status = '2';
                }
            }
        })
        .catch(function() { alert('Status change failed.'); });
    });
});
</script>

@endsection