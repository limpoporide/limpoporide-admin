@extends('admin.includes.main')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Edit Vehicle</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('admin/all_vehicles') }}">All Vehicles</a></li>
        <li class="breadcrumb-item"><a href="{{ url('admin/view_vehicle/' . $vehicle->id) }}">Vehicle Details</a></li>
        <li class="breadcrumb-item active">Edit Vehicle</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <form id="editVehicleForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="id" value="{{ $vehicle->id }}">

      <div class="row g-3">

        {{-- LEFT COLUMN --}}
        <div class="col-lg-8">

          {{-- Driver Info Banner --}}
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

                <div class="col-md-6">
                  <label class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_number" class="form-control"
                         value="{{ $vehicle->vehicle_number }}" placeholder="e.g. ABC 123 GP">
                  <span class="text-danger d-block mt-1 err-vehicle_number" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Name <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_name" class="form-control"
                         value="{{ $vehicle->vehicle_name }}" placeholder="e.g. Toyota Corolla">
                  <span class="text-danger d-block mt-1 err-vehicle_name" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Manufacturer <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_manufacture" class="form-control"
                         value="{{ $vehicle->vehicle_manufacture }}" placeholder="e.g. Toyota">
                  <span class="text-danger d-block mt-1 err-vehicle_manufacture" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                  <select name="vehicle_type" id="vehicle_type" class="form-select">
                    <option value="">Select Type</option>
                    @foreach($vehicle_types as $vt)
                    <option value="{{ $vt->id }}" {{ $vehicle->vehicle_type == $vt->id ? 'selected' : '' }}>
                      {{ $vt->name }}
                    </option>
                    @endforeach
                  </select>
                  <span class="text-danger d-block mt-1 err-vehicle_type" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Model <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_model" id="vehicle_model" class="form-control" value="{{ $vehicle->vehicle_model }}" placeholder="Enter vehicle model">
                  <span class="text-danger d-block mt-1 err-vehicle_model" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Registration Year <span class="text-danger">*</span></label>
                  <input type="number" name="vehicle_reg_year" class="form-control"
                         value="{{ $vehicle->vehicle_reg_year }}" placeholder="e.g. 2020"
                         min="1990" max="{{ date('Y') }}">
                  <span class="text-danger d-block mt-1 err-vehicle_reg_year" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Fuel Type <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_fuel_type" id="vehicle_fuel_type" class="form-control" value="{{ $vehicle->vehicle_fuel_type }}" placeholder="Enter Fuel Type">
                  <span class="text-danger d-block mt-1 err-vehicle_fuel_type" style="font-size:12px;"></span>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Seating Capacity <span class="text-danger">*</span></label>
                  <input type="number" name="seating_capicity" id="seating_capicity" class="form-control" value="{{ $vehicle->seating_capicity }}" placeholder="Enter Seating Capacity">
                  <span class="text-danger d-block mt-1 err-seating_capicity" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Color <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_color" id="vehicle_color" class="form-control" value="{{ $vehicle->vehicle_color }}" placeholder="Enter Vehicle Color">
                  <span class="text-danger d-block mt-1 err-vehicle_color" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Owner Name</label>
                  <input type="text" name="vehicle_owner_name" class="form-control"
                         value="{{ $vehicle->vehicle_owner_name }}" placeholder="Vehicle Owner Name">
                </div>

              </div>
            </div>
          </div>

          {{-- Documents Card --}}
          <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-4">
              <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <i class="bi bi-file-earmark-text me-2 text-warning"></i>Documents & Photos
              </h6>
              <div class="row g-3">

                @php
                  $docFields = [
                    ['input' => 'reg_front',  'label' => 'Vehicle Registration — Front', 'icon' => 'bi-cloud-upload', 'existing' => $vehicle->id_proof],
                    ['input' => 'reg_back',   'label' => 'Vehicle Registration — Back',  'icon' => 'bi-cloud-upload', 'existing' => $vehicle->id_proof2],
                    ['input' => 'insurance',  'label' => 'Insurance Paper',               'icon' => 'bi-cloud-upload', 'existing' => $vehicle->photo1],
                    ['input' => 'inspection', 'label' => 'Vehicle Inspection Report',     'icon' => 'bi-cloud-upload', 'existing' => $vehicle->photo2],
                    ['input' => 'photo1',     'label' => 'Vehicle Photo — Front',         'icon' => 'bi-camera',       'existing' => $vehicle->certificate1],
                    ['input' => 'photo2',     'label' => 'Vehicle Photo — Back',          'icon' => 'bi-camera',       'existing' => $vehicle->certificate2],
                  ];
                @endphp

                @foreach($docFields as $doc)
                <div class="col-md-6">
                  <label class="form-label">{{ $doc['label'] }}</label>

                  @if($doc['existing'])
                  @php
                    $ext = strtolower(pathinfo($doc['existing'], PATHINFO_EXTENSION));
                    $isPdf = ($ext === 'pdf');
                    $fileUrl = URL::to('public/vehicle_image/vehicle_photo/' . $doc['existing']);
                  @endphp
                  <div class="existing-file-preview mb-2" style="border:1px solid #d4edda;border-radius:10px;overflow:hidden;background:#fff;">
                    @if($isPdf)
                      <div style="height:110px;background:#f8f9fa;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;">
                        <i class="bi bi-file-earmark-pdf text-danger" style="font-size:36px;"></i>
                        <span style="font-size:11px;color:#888;">PDF Document</span>
                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-danger" style="font-size:11px;padding:2px 10px;">
                          <i class="bi bi-eye me-1"></i>View PDF
                        </a>
                      </div>
                    @else
                      <a href="{{ $fileUrl }}" target="_blank">
                        <img src="{{ $fileUrl }}"
                             alt="{{ $doc['label'] }}"
                             id="imgprev-{{ $doc['input'] }}"
                             style="width:100%;height:110px;object-fit:cover;display:block;"
                             onerror="imgError('{{ $doc['input'] }}')">
                      </a>
                      <div id="imgerr-{{ $doc['input'] }}"
                           style="display:none;height:110px;background:#f8f9fa;align-items:center;justify-content:center;">
                        <i class="bi bi-image text-muted" style="font-size:32px;"></i>
                      </div>
                    @endif
                    <div class="d-flex align-items-center justify-content-between px-2 py-1" style="background:#f0faf6;border-top:1px solid #d4edda;">
                      <span style="font-size:11px;color:#3b6d11;">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>Current file saved
                      </span>
                      <span style="font-size:10px;color:#aaa;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $doc['existing'] }}">
                        {{ $doc['existing'] }}
                      </span>
                    </div>
                  </div>
                  @endif

                  <div class="upload-box {{ $doc['existing'] ? 'replace-box' : '' }}"
                       id="box-{{ $doc['input'] }}"
                       onclick="document.getElementById('{{ $doc['input'] }}').click()">
                    <i class="bi {{ $doc['icon'] }} fs-4 text-muted"></i>
                    <p class="mb-0 mt-1" style="font-size:12px;color:#888;">
                      {{ $doc['existing'] ? 'Click to replace' : 'Click to upload' }}
                    </p>
                    <img id="prev-{{ $doc['input'] }}" src=""
                         style="display:none;max-height:90px;margin-top:8px;border-radius:6px;">
                  </div>
                  <input type="file" name="{{ $doc['input'] }}" id="{{ $doc['input'] }}"
                         accept="image/*,application/pdf" class="d-none"
                         onchange="previewFile(this,'prev-{{ $doc['input'] }}','box-{{ $doc['input'] }}')">
                  <span class="text-danger d-block mt-1 err-{{ $doc['input'] }}" style="font-size:12px;"></span>
                </div>
                @endforeach

              </div>
            </div>
          </div>

        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="col-lg-4">

          <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
            <div class="card-body p-4">
              <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <i class="bi bi-toggles me-2 text-success"></i>Status & Actions
              </h6>

              <div id="formAlert" class="alert d-none mb-3" role="alert" style="font-size:13px;border-radius:8px;"></div>

              <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="doc_status1" class="form-select">
                  <option value="1" {{ $vehicle->doc_status1 == 1 ? 'selected' : '' }}>Active</option>
                  <option value="2" {{ $vehicle->doc_status1 == 2 ? 'selected' : '' }}>Inactive</option>
                </select>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-warning text-white" id="submitBtn">
                  <i class="bi bi-check-circle me-1"></i> Update Vehicle
                </button>
                <a href="{{ url('admin/view_vehicle/' . $vehicle->id) }}" class="btn btn-secondary">
                  <i class="bi bi-eye me-1"></i> View Details
                </a>
                <a href="{{ url('admin/all_vehicles') }}" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
              </div>
            </div>
          </div>

          {{-- Quick Info Card --}}
          <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
            <div class="card-body p-4">
              <h6 class="fw-semibold mb-3" style="font-size:13px;border-bottom:1px solid #f0f0f0;padding-bottom:8px;">
                <i class="bi bi-info-circle me-1 text-primary"></i> Current Details
              </h6>
              @php
                $summary = [
                  'Vehicle #'   => $vehicle->vehicle_number ?: '—',
                  'Type'        => $vehicle->vehicle_type_name ?: '—',
                  'Model'       => $vehicle->vehicle_model ?: '—',
                  'Fuel'        => $vehicle->vehicle_fuel_type ?: '—',
                  'Reg Year'    => $vehicle->vehicle_reg_year ?: '—',
                ];
              @endphp
              @foreach($summary as $lbl => $val)
              <div class="d-flex justify-content-between align-items-center py-1
                          {{ !$loop->last ? 'border-bottom' : '' }}"
                   style="font-size:12px;">
                <span class="text-muted">{{ $lbl }}</span>
                <span class="fw-medium text-end" style="max-width:60%;word-break:break-all;">{{ $val }}</span>
              </div>
              @endforeach
            </div>
          </div>

          <div class="card border-0 shadow-sm" style="border-radius:12px;background:#f8f9ff;">
            <div class="card-body p-4">
              <h6 class="fw-semibold mb-2" style="font-size:13px;color:#534ab7;">
                <i class="bi bi-lightbulb me-1"></i> Tips
              </h6>
              <ul class="mb-0 ps-3" style="font-size:12px;color:#666;line-height:1.9;">
                <li>Leave document fields blank to keep existing files.</li>
                <li>PDF or image files accepted for documents.</li>
              </ul>
            </div>
          </div>

        </div>

      </div>
    </form>
  </section>
</main>

<style>
.upload-box {
  border: 2px dashed #d0d0d0;
  border-radius: 10px;
  padding: 16px;
  text-align: center;
  cursor: pointer;
  transition: border-color .2s, background .2s;
  background: #fafafa;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.upload-box:hover  { border-color: #4154f1; background: #f0f1ff; }
.upload-box.has-file  { border-color: #1D9E75; background: #f0faf6; }
.upload-box.replace-box { border-color: #f0ad4e; background: #fffbf0; }
.upload-box.replace-box:hover { border-color: #e6890a; background: #fff3d6; }
</style>

<script>
function imgError(inputName) {
    var img = document.getElementById('imgprev-' + inputName);
    var err = document.getElementById('imgerr-' + inputName);
    if (img) img.style.display = 'none';
    if (err) err.style.display = 'flex';
}

function previewFile(input, previewId, boxId) {
    var file = input.files[0];
    var box  = document.getElementById(boxId);
    if (!file) return;
    box.classList.remove('replace-box');
    box.classList.add('has-file');
    if (file.type.startsWith('image/')) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById(previewId);
            img.src = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById(previewId).style.display = 'none';
        var existingLabel = box.querySelector('.file-name-label');
        if (!existingLabel) {
            var lbl = document.createElement('p');
            lbl.className = 'file-name-label mt-1 mb-0';
            lbl.style.cssText = 'font-size:12px;color:#1D9E75;word-break:break-all;';
            box.appendChild(lbl);
        }
        box.querySelector('.file-name-label').textContent = file.name;
    }
}

document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('editVehicleForm').addEventListener('submit', function (e) {
        e.preventDefault();

        document.querySelectorAll('[class*="err-"]').forEach(function(el) { el.textContent = ''; });
        document.querySelectorAll('.form-control, .form-select').forEach(function(el) { el.classList.remove('is-invalid'); });

        var submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        var alertBox = document.getElementById('formAlert');
        alertBox.className = 'alert d-none mb-3';
        alertBox.textContent = '';

        fetch('{{ url("admin/edit_vehicle") }}', {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Vehicle';

            if (res.status === 'true' || res.status === true) {
                alertBox.className = 'alert alert-success mb-3';
                alertBox.textContent = res.msg || 'Vehicle updated successfully!';
                setTimeout(function() {
                    window.location.href = '{{ url("admin/view_vehicle/" . $vehicle->id) }}';
                }, 1200);
            } else {
                if (res.msg && typeof res.msg === 'object') {
                    Object.keys(res.msg).forEach(function(field) {
                        var errEl = document.querySelector('.err-' + field);
                        var inputEl = document.querySelector('[name="' + field + '"]');
                        if (errEl) errEl.textContent = res.msg[field][0];
                        if (inputEl) inputEl.classList.add('is-invalid');
                    });
                    alertBox.className = 'alert alert-danger mb-3';
                    alertBox.textContent = 'Please fix the errors below.';
                } else {
                    alertBox.className = 'alert alert-danger mb-3';
                    alertBox.textContent = res.msg || 'Something went wrong.';
                }
            }
        })
        .catch(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Vehicle';
            alertBox.className = 'alert alert-danger mb-3';
            alertBox.textContent = 'Server error. Please try again.';
        });
    });

});
</script>

@endsection