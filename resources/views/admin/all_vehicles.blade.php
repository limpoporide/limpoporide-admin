@extends('admin.includes.main')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>All Vehicles</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li class="breadcrumb-item">Manage Vehicles</li>
        <li class="breadcrumb-item active">All Vehicles</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
              <h5 class="card-title mb-0">All Vehicles</h5>
              <a class="btn btn-success btn-sm" href="{{ url('admin/add_vehicle_page') }}">
                <i class="bi bi-plus-circle me-1"></i> Add New Vehicle
              </a>
            </div>

            <form method="GET" action="{{ url('admin/all_vehicles') }}" class="row g-2 mb-3">
              <div class="col-md-2">
                <select name="assigned" class="form-select form-select-sm">
                  <option value="">All Vehicles</option>
                  <option value="1" {{ request('assigned')=='1'?'selected':'' }}>Assigned</option>
                  <option value="0" {{ request('assigned')=='0'?'selected':'' }}>Unassigned</option>
                </select>
              </div>
              <div class="col-md-2">
                <select name="vehicle_type" class="form-select form-select-sm">
                  <option value="">All Types</option>
                  @foreach($vehicle_types as $vt)
                  <option value="{{ $vt->id }}" {{ request('vehicle_type')==$vt->id?'selected':'' }}>{{ $vt->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <select name="fuel_type" class="form-select form-select-sm">
                  <option value="">All Fuel Types</option>
                  <option value="Petrol" {{ request('fuel_type')=='Petrol'?'selected':'' }}>Petrol</option>
                  <option value="Diesel" {{ request('fuel_type')=='Diesel'?'selected':'' }}>Diesel</option>
                  <option value="Electric" {{ request('fuel_type')=='Electric'?'selected':'' }}>Electric</option>
                  <option value="CNG" {{ request('fuel_type')=='CNG'?'selected':'' }}>CNG</option>
                  <option value="Hybrid" {{ request('fuel_type')=='Hybrid'?'selected':'' }}>Hybrid</option>
                </select>
              </div>
              <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                  <option value="">All Status</option>
                  <option value="1" {{ request('status')=='1'?'selected':'' }}>Active</option>
                  <option value="2" {{ request('status')=='2'?'selected':'' }}>Inactive</option>
                </select>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                  <i class="bi bi-funnel me-1"></i>Filter
                </button>
              </div>
              <div class="col-md-2">
                <a href="{{ url('admin/all_vehicles') }}" class="btn btn-secondary btn-sm w-100">
                  <i class="bi bi-x-circle me-1"></i>Reset
                </a>
              </div>
            </form>

            <div class="table-responsive">
              <table class="table datatable align-middle table-hover">
                <thead class="table-light">
                  <tr>
                    <th>S.No</th>
                    <th>Photo</th>
                    <th>Vehicle Number</th>
                    <th>Vehicle Name</th>
                    <th>Type</th>
                    <th>Model</th>
                    <th>Fuel</th>
                    <th>Driver</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($vehicles as $i => $v)
                  <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                      @if($v->photo1)
                        <img src="{{ URL::to('public/vehicle_image/vehicle_photo/' . $v->certificate1) }}"
                             style="width:48px;height:38px;object-fit:cover;border-radius:6px;" alt="vehicle">
                      @else
                        <div style="width:48px;height:38px;background:#f0f0f0;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                          <i class="bi bi-car-front text-muted"></i>
                        </div>
                      @endif
                    </td>
                    <td><span class="fw-medium">{{ $v->vehicle_number ?: '—' }}</span></td>
                    <td>{{ $v->vehicle_name ?: '—' }}</td>
                    <td>
                      @if($v->vehicle_type_name)
                        <span class="badge" style="background:#eeedfe;color:#534ab7;font-weight:500;">{{ $v->vehicle_type_name }}</span>
                      @else
                        <span class="text-muted">—</span>
                      @endif
                    </td>
                    <td>{{ $v->vehicle_model_name ?: '—' }}</td>
                    <td>{{ $v->vehicle_fuel_type ?: '—' }}</td>
                    <td>
                      @if($v->driver_id && $v->driver_id != '')
                        <div>
                          <span class="fw-medium" style="font-size:13px;">{{ $v->driver_name ?: 'Driver #'.$v->driver_id }}</span><br>
                          <span class="text-muted" style="font-size:11px;">{{ $v->driver_mobile }}</span>
                        </div>
                      @else
                        <span class="badge" style="background:#faeeda;color:#854f0b;font-size:11px;">Unassigned</span>
                      @endif
                    </td>
                    <td>
                      <button type="button"
                              class="btn btn-sm user_change_status {{ $v->doc_status1 == 1 ? 'btn-success' : 'btn-danger' }}"
                              data-id="{{ $v->id }}"
                              data-url="{{ url('admin/change_vehicle_status') }}"
                              style="min-width:72px;">
                        {{ $v->doc_status1 == 1 ? 'Active' : 'Inactive' }}
                      </button>
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ url('admin/view_vehicle/' . $v->id) }}"
                           class="btn btn-sm btn-info text-white" title="View">
                          <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ url('admin/edit_vehicle_page/' . $v->id) }}"
                           class="btn btn-sm btn-warning text-white"
                           title="Edit">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button"
                                class="btn btn-sm btn-danger delete_record"
                                title="Delete"
                                data-id="{{ $v->id }}"
                                data-url="{{ url('admin/delete_vehicle') }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="10" class="text-center text-muted py-4">No vehicles found.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</main>

{{-- ADD VEHICLE MODAL --}}
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Vehicle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addVehicleForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Vehicle Number <span class="text-danger">*</span></label>
              <input type="text" name="vehicle_number" class="form-control" placeholder="e.g. ABC 123 GP">
              <span class="text-danger err-vehicle_number"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label">Vehicle Name <span class="text-danger">*</span></label>
              <input type="text" name="vehicle_name" class="form-control" placeholder="e.g. Toyota Corolla">
              <span class="text-danger err-vehicle_name"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label">Manufacturer <span class="text-danger">*</span></label>
              <input type="text" name="vehicle_manufacture" class="form-control" placeholder="e.g. Toyota">
              <span class="text-danger err-vehicle_manufacture"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
              <select name="vehicle_type" id="add_vehicle_type" class="form-select">
                <option value="">Select Type</option>
                @foreach($vehicle_types as $vt)
                <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                @endforeach
              </select>
              <span class="text-danger err-vehicle_type"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label">Vehicle Model <span class="text-danger">*</span></label>
              <select name="vehicle_model" id="add_vehicle_model" class="form-select">
                <option value="">Select Type First</option>
              </select>
              <span class="text-danger err-vehicle_model"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label">Reg Year <span class="text-danger">*</span></label>
              <input type="number" name="vehicle_reg_year" class="form-control" placeholder="e.g. 2020" min="1990" max="{{ date('Y') }}">
              <span class="text-danger err-vehicle_reg_year"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fuel Type <span class="text-danger">*</span></label>
              <select name="vehicle_fuel_type" class="form-select">
                <option value="">Select</option>
                <option value="Petrol">Petrol</option>
                <option value="Diesel">Diesel</option>
                <option value="Electric">Electric</option>
                <option value="CNG">CNG</option>
                <option value="Hybrid">Hybrid</option>
              </select>
              <span class="text-danger err-vehicle_fuel_type"></span>
            </div>
            <div class="col-md-6">
              <label class="form-label">Owner Name</label>
              <input type="text" name="vehicle_owner_name" class="form-control" placeholder="Owner Name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Country Operating</label>
              <input type="text" name="country_operating" class="form-control" placeholder="e.g. South Africa">
            </div>
            <div class="col-md-6">
              <label class="form-label">Photo 1</label>
              <input type="file" name="photo1" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="form-label">Photo 2</label>
              <input type="file" name="photo2" class="form-control" accept="image/*">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success btn-sm">
            <i class="bi bi-check-circle me-1"></i>Add Vehicle
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h6 class="modal-title text-danger"><i class="bi bi-trash me-1"></i>Delete Vehicle</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-0">Are you sure you want to delete this vehicle? This cannot be undone.</p>
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<style>
.datatable thead th { font-size: 12px; font-weight: 600; color: #555; white-space: nowrap; }
.datatable tbody td { font-size: 13px; vertical-align: middle; }
.card { border-radius: 12px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var deleteId  = null;
    var deleteUrl = null;

    function loadModels(typeId, selectEl, selectedVal) {
        selectEl.innerHTML = '<option value="">Loading...</option>';
        fetch('{{ url("admin/get_models_by_type") }}?type_id=' + typeId)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                selectEl.innerHTML = '<option value="">Select Model</option>';
                data.forEach(function(m) {
                    var opt = document.createElement('option');
                    opt.value = m.name;
                    opt.textContent = m.name;
                    if (selectedVal && m.name == selectedVal) opt.selected = true;
                    selectEl.appendChild(opt);
                });
            })
            .catch(function() { selectEl.innerHTML = '<option value="">Error loading</option>'; });
    }

    document.getElementById('add_vehicle_type').addEventListener('change', function () {
        if (this.value) {
            loadModels(this.value, document.getElementById('add_vehicle_model'), null);
        } else {
            document.getElementById('add_vehicle_model').innerHTML = '<option value="">Select Type First</option>';
        }
    });

    

    });

    });

    // document.querySelectorAll('.change_vehicle_status').forEach(function (btn) {
    //     btn.addEventListener('click', function () {
    //         var id   = this.dataset.id;
    //         var self = this;
    //         fetch('{{ url("admin/change_vehicle_status") }}', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //                 'X-Requested-With': 'XMLHttpRequest'
    //             },
    //             body: JSON.stringify({ id: id })
    //         })
    //         .then(function(r) { return r.json(); })
    //         .then(function(res) {
    //             if (res.status) {
    //                 if (res.new_status == 1) {
    //                     self.textContent = 'Active';
    //                     self.classList.remove('btn-danger');
    //                     self.classList.add('btn-success');
    //                 } else {
    //                     self.textContent = 'Inactive';
    //                     self.classList.remove('btn-success');
    //                     self.classList.add('btn-danger');
    //                 }
    //             }
    //         })
    //         .catch(function() { alert('Status change failed.'); });
    //     });
    // });


    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
        if (!deleteId) return;
        fetch('{{ url("admin/delete_vehicle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: deleteId })
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.status) {
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                location.reload();
            } else {
                alert(res.message || 'Delete failed.');
            }
        })
        .catch(function() { alert('Server error.'); });
    });

});
</script>

@endsection