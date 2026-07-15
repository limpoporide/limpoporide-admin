@extends('admin.includes.main')
@section('content')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Assign Vehicle</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Drivers</li>
        <li class="breadcrumb-item active">Assign Vehicle</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        {{-- ─── Driver Info Card ─── --}}
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3 flex-wrap">

              {{-- Avatar --}}
              @if(!empty($driver->profile))
                <img src="{{ url('public/profile_image') }}/{{ $driver->profile }}"
                     style="width:64px;height:64px;border-radius:50%;object-fit:cover;
                            border:2px solid #dee2e6;box-shadow:0 2px 6px rgba(0,0,0,.1);">
              @else
                <img src="{{ url('public/assets/img/default_img.jpg') }}"
                     style="width:64px;height:64px;border-radius:50%;object-fit:cover;
                            border:2px solid #dee2e6;box-shadow:0 2px 6px rgba(0,0,0,.1);">
              @endif

              {{-- Info --}}
              <div>
                <h5 class="mb-0 fw-bold">{{ $driver->name }}</h5>
                <small class="text-muted">
                  <i class="bi bi-telephone me-1"></i>{{ $driver->mobile_no }}
                  &nbsp;|&nbsp;
                  <i class="bi bi-envelope me-1"></i>{{ $driver->email ?? '—' }}
                </small>
              </div>

              {{-- Status Badge --}}
              <div class="ms-auto">
                @if($vehicle_detail)
                  <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="bi bi-car-front me-1"></i> Vehicle Assigned
                  </span>
                @else
                  <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                    <i class="bi bi-exclamation-circle me-1"></i> No Vehicle Assigned
                  </span>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- ─── Assign Form ─── --}}
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-car-front me-1"></i>
              {{ $vehicle_detail ? 'Change / Update Assigned Vehicle' : 'Assign a Vehicle' }}
            </h5>
            <hr>

            @if($unassigned_vehicles->isEmpty())
              <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-1"></i>
                No available vehicles found. Please add vehicles in
                <strong>Vehicle Management</strong> first.
              </div>
            @else

              <form action="{{ url('admin/save_assign_vehicle') }}/{{ $driver->id }}" method="POST">
                @csrf

                <div class="row g-4">

                  {{-- Vehicle Dropdown --}}
                  <div class="col-md-5">
                    <label class="form-label fw-semibold">
                      Select Vehicle
                    </label>
                    <select name="vehicle_id" id="vehicle_dropdown"
                            class="form-select @error('vehicle_id') is-invalid @enderror">
                      <option value="">-- No Vehicle / Unassign --</option>
                      @foreach($unassigned_vehicles as $v)
                        <option value="{{ $v->id }}"
                                data-number="{{ $v->vehicle_number }}"
                                data-name="{{ $v->vehicle_name }}"
                                data-type="{{ $v->vehicle_type_name ?? '' }}"
                                data-model="{{ $v->vehicle_model }}"
                                data-manufacture="{{ $v->vehicle_manufacture }}"
                                data-year="{{ $v->vehicle_reg_year }}"
                                data-fuel="{{ $v->vehicle_fuel_type }}"
                                data-owner="{{ $v->vehicle_owner_name }}"
                                {{ (old('vehicle_id', $vehicle_detail->id ?? '') == $v->id) ? 'selected' : '' }}>
                          {{ $v->vehicle_number }} — {{ $v->vehicle_name }}
                          @if(!empty($v->vehicle_type_name)) ({{ $v->vehicle_type_name }}) @endif
                          @if(($vehicle_detail->id ?? null) == $v->id)
                            ★ Currently Assigned
                          @endif
                        </option>
                      @endforeach
                    </select>
                    @error('vehicle_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Unassigned vehicles + currently assigned vehicle are listed.</small>
                  </div>

                  {{-- Live Preview --}}
                  <div class="col-md-7" id="vehicle-preview-wrap"
                       style="{{ ($vehicle_detail || old('vehicle_id')) ? '' : 'display:none;' }}">
                    <label class="form-label fw-semibold">Vehicle Details Preview</label>
                    <div class="border rounded p-3 bg-light">
                      <div class="row g-2">
                        <div class="col-6">
                          <p class="mb-1"><span class="text-muted small">Vehicle No.</span><br>
                            <strong id="pv-number">{{ $vehicle_detail->vehicle_number ?? '—' }}</strong></p>
                        </div>
                        <div class="col-6">
                          <p class="mb-1"><span class="text-muted small">Name / Brand</span><br>
                            <strong id="pv-name">{{ $vehicle_detail->vehicle_name ?? '—' }}</strong></p>
                        </div>
                        <div class="col-6">
                          <p class="mb-1"><span class="text-muted small">Type</span><br>
                            <strong id="pv-type">{{ $vehicle_detail->vehicle_type_name ?? '—' }}</strong></p>
                        </div>
                        <div class="col-6">
                          <p class="mb-1"><span class="text-muted small">Model</span><br>
                            <strong id="pv-model">{{ $vehicle_detail->vehicle_model ?? '—' }}</strong></p>
                        </div>
                        <div class="col-6">
                          <p class="mb-1"><span class="text-muted small">Manufacturer</span><br>
                            <strong id="pv-manufacture">{{ $vehicle_detail->vehicle_manufacture ?? '—' }}</strong></p>
                        </div>
                        <div class="col-6">
                          <p class="mb-1"><span class="text-muted small">Reg. Year</span><br>
                            <strong id="pv-year">{{ $vehicle_detail->vehicle_reg_year ?? '—' }}</strong></p>
                        </div>
                        <div class="col-6">
                          <p class="mb-1"><span class="text-muted small">Fuel Type</span><br>
                            <strong id="pv-fuel">{{ $vehicle_detail->vehicle_fuel_type ?? '—' }}</strong></p>
                        </div>
                        <div class="col-6">
                          <p class="mb-1"><span class="text-muted small">Owner Name</span><br>
                            <strong id="pv-owner">{{ $vehicle_detail->vehicle_owner_name ?? '—' }}</strong></p>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>{{-- /row --}}

                <div class="d-flex justify-content-end gap-2 mt-4">
                  <a href="{{ url('admin/approved_drivers_list') }}" class="btn btn-secondary px-4">
                    <i class="bi bi-arrow-left me-1"></i> Back
                  </a>
                  <button type="submit" id="submit-btn" class="btn btn-primary px-5">
                    <i class="bi bi-save me-1" id="submit-icon"></i>
                    <span id="submit-label">
                      {{ $vehicle_detail ? 'Update Vehicle' : 'Assign Vehicle' }}
                    </span>
                  </button>
                </div>

              </form>

            @endif

          </div>
        </div>

      </div>
    </div>
  </section>
</main>


<script>
var vehicleDrop  = document.getElementById('vehicle_dropdown');
var submitBtn    = document.getElementById('submit-btn');
var submitIcon   = document.getElementById('submit-icon');
var submitLabel  = document.getElementById('submit-label');
var previewWrap  = document.getElementById('vehicle-preview-wrap');
var hasVehicle   = {{ $vehicle_detail ? 'true' : 'false' }};

function updateButton(hasSelection) {
  if (!hasSelection) {
    submitBtn.classList.remove('btn-primary');
    submitBtn.classList.add('btn-danger');
    submitIcon.className = 'bi bi-x-circle me-1';
    submitLabel.textContent = hasVehicle ? 'Unassign Vehicle' : 'Save';
  } else {
    submitBtn.classList.remove('btn-danger');
    submitBtn.classList.add('btn-primary');
    submitIcon.className = 'bi bi-save me-1';
    submitLabel.textContent = hasVehicle ? 'Update Vehicle' : 'Assign Vehicle';
  }
}

if (vehicleDrop) {
  vehicleDrop.addEventListener('change', function () {
    var opt = this.options[this.selectedIndex];
    if (!this.value) {
      previewWrap.style.display = 'none';
      updateButton(false);
      return;
    }
    previewWrap.style.display = '';
    updateButton(true);
    document.getElementById('pv-number').textContent      = opt.dataset.number      || '—';
    document.getElementById('pv-name').textContent        = opt.dataset.name        || '—';
    document.getElementById('pv-type').textContent        = opt.dataset.type        || '—';
    document.getElementById('pv-model').textContent       = opt.dataset.model       || '—';
    document.getElementById('pv-manufacture').textContent = opt.dataset.manufacture || '—';
    document.getElementById('pv-year').textContent        = opt.dataset.year        || '—';
    document.getElementById('pv-fuel').textContent        = opt.dataset.fuel        || '—';
    document.getElementById('pv-owner').textContent       = opt.dataset.owner       || '—';
  });
}
</script>
@endsection