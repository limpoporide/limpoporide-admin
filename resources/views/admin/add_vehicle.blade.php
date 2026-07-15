@extends('admin.includes.main')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Add New Vehicle</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('admin/all_vehicles') }}">All Vehicles</a></li>
        <li class="breadcrumb-item active">Add New Vehicle</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <form id="addVehicleForm" enctype="multipart/form-data">
      @csrf

      <div class="row g-3">

        <div class="col-lg-8">

          <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
            <div class="card-body p-4">
              <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <i class="bi bi-car-front me-2 text-primary"></i>Vehicle Information
              </h6>
              <div class="row g-3">

                <div class="col-md-6">
                  <label class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" placeholder="e.g. ABC 123 GP">
                  <span class="text-danger d-block mt-1 err-vehicle_number" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Name <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_name" id="vehicle_name" class="form-control" placeholder="e.g. Toyota Corolla">
                  <span class="text-danger d-block mt-1 err-vehicle_name" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Manufacturer <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_manufacture" id="vehicle_manufacture" class="form-control" placeholder="e.g. Toyota">
                  <span class="text-danger d-block mt-1 err-vehicle_manufacture" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                  <select name="vehicle_type" id="vehicle_type" class="form-select">
                    <option value="">Select Type</option>
                    @foreach($vehicle_types as $vt)
                    <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                    @endforeach
                  </select>
                  <span class="text-danger d-block mt-1 err-vehicle_type" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Model <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_model" id="vehicle_model" class="form-control" placeholder="Vehicle model">
                  <span class="text-danger d-block mt-1 err-vehicle_model" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Registration Year <span class="text-danger">*</span></label>
                  <input type="number" name="vehicle_reg_year" id="vehicle_reg_year" class="form-control" placeholder="e.g. 2020" min="1990" max="{{ date('Y') }}">
                  <span class="text-danger d-block mt-1 err-vehicle_reg_year" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Fuel Type <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_fuel_type" id="vehicle_fuel_type" class="form-control" placeholder="Enter Fuel Type">
                  <span class="text-danger d-block mt-1 err-vehicle_fuel_type" style="font-size:12px;"></span>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Seating Capacity <span class="text-danger">*</span></label>
                  <input type="number" name="seating_capicity" id="seating_capicity" class="form-control" placeholder="Enter Seating Capacity">
                  <span class="text-danger d-block mt-1 err-seating_capicity" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Color <span class="text-danger">*</span></label>
                  <input type="text" name="vehicle_color" id="vehicle_color" class="form-control" placeholder="Enter Vehicle Color">
                  <span class="text-danger d-block mt-1 err-vehicle_color" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Owner Name</label>
                  <input type="text" name="vehicle_owner_name" class="form-control" placeholder="Vehicle Owner Name">
                </div>

              </div>
            </div>
          </div>

          <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body p-4">
              <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <i class="bi bi-file-earmark-text me-2 text-warning"></i>Documents
              </h6>
              <div class="row g-3">

                <div class="col-md-6">
                  <label class="form-label">Vehicle Registration — Front</label>
                  <div class="upload-box" id="box-reg_front" onclick="document.getElementById('reg_front').click()">
                    <i class="bi bi-cloud-upload fs-3 text-muted"></i>
                    <p class="mb-0 mt-1" style="font-size:12px;color:#888;">Click to upload</p>
                    <img id="prev-reg_front" src="" style="display:none;max-height:100px;margin-top:8px;border-radius:6px;">
                  </div>
                  <input type="file" name="reg_front" id="reg_front" accept="image/*,application/pdf" class="d-none" onchange="previewFile(this,'prev-reg_front','box-reg_front')">
                  <span class="text-danger d-block mt-1 err-reg_front" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Registration — Back</label>
                  <div class="upload-box" id="box-reg_back" onclick="document.getElementById('reg_back').click()">
                    <i class="bi bi-cloud-upload fs-3 text-muted"></i>
                    <p class="mb-0 mt-1" style="font-size:12px;color:#888;">Click to upload</p>
                    <img id="prev-reg_back" src="" style="display:none;max-height:100px;margin-top:8px;border-radius:6px;">
                  </div>
                  <input type="file" name="reg_back" id="reg_back" accept="image/*,application/pdf" class="d-none" onchange="previewFile(this,'prev-reg_back','box-reg_back')">
                  <span class="text-danger d-block mt-1 err-reg_back" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Insurance Paper</label>
                  <div class="upload-box" id="box-insurance" onclick="document.getElementById('insurance').click()">
                    <i class="bi bi-cloud-upload fs-3 text-muted"></i>
                    <p class="mb-0 mt-1" style="font-size:12px;color:#888;">Click to upload</p>
                    <img id="prev-insurance" src="" style="display:none;max-height:100px;margin-top:8px;border-radius:6px;">
                  </div>
                  <input type="file" name="insurance" id="insurance" accept="image/*,application/pdf" class="d-none" onchange="previewFile(this,'prev-insurance','box-insurance')">
                  <span class="text-danger d-block mt-1 err-insurance" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Inspection Report</label>
                  <div class="upload-box" id="box-inspection" onclick="document.getElementById('inspection').click()">
                    <i class="bi bi-cloud-upload fs-3 text-muted"></i>
                    <p class="mb-0 mt-1" style="font-size:12px;color:#888;">Click to upload</p>
                    <img id="prev-inspection" src="" style="display:none;max-height:100px;margin-top:8px;border-radius:6px;">
                  </div>
                  <input type="file" name="inspection" id="inspection" accept="image/*,application/pdf" class="d-none" onchange="previewFile(this,'prev-inspection','box-inspection')">
                  <span class="text-danger d-block mt-1 err-inspection" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Photo — Front</label>
                  <div class="upload-box" id="box-photo1" onclick="document.getElementById('photo1').click()">
                    <i class="bi bi-camera fs-3 text-muted"></i>
                    <p class="mb-0 mt-1" style="font-size:12px;color:#888;">Click to upload</p>
                    <img id="prev-photo1" src="" style="display:none;max-height:100px;margin-top:8px;border-radius:6px;">
                  </div>
                  <input type="file" name="photo1" id="photo1" accept="image/*" class="d-none" onchange="previewFile(this,'prev-photo1','box-photo1')">
                  <span class="text-danger d-block mt-1 err-photo1" style="font-size:12px;"></span>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vehicle Photo — Back</label>
                  <div class="upload-box" id="box-photo2" onclick="document.getElementById('photo2').click()">
                    <i class="bi bi-camera fs-3 text-muted"></i>
                    <p class="mb-0 mt-1" style="font-size:12px;color:#888;">Click to upload</p>
                    <img id="prev-photo2" src="" style="display:none;max-height:100px;margin-top:8px;border-radius:6px;">
                  </div>
                  <input type="file" name="photo2" id="photo2" accept="image/*" class="d-none" onchange="previewFile(this,'prev-photo2','box-photo2')">
                  <span class="text-danger d-block mt-1 err-photo2" style="font-size:12px;"></span>
                </div>

              </div>
            </div>
          </div>

        </div>

        <div class="col-lg-4">

          <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
            <div class="card-body p-4">
              <h6 class="fw-semibold mb-3" style="font-size:14px;border-bottom:1px solid #f0f0f0;padding-bottom:10px;">
                <i class="bi bi-toggles me-2 text-success"></i>Status & Submission
              </h6>

              <div id="formAlert" class="alert d-none mb-3" role="alert" style="font-size:13px;border-radius:8px;"></div>

              <div class="mb-3">
                <label class="form-label">Initial Status</label>
                <select name="doc_status1" class="form-select">
                  <option value="1">Active</option>
                  <option value="2">Inactive</option>
                </select>
              </div>

              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success" id="submitBtn">
                  <i class="bi bi-check-circle me-1"></i> Add Vehicle
                </button>
                <a href="{{ url('admin/all_vehicles') }}" class="btn btn-secondary">
                  <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
              </div>
            </div>
          </div>

          <div class="card border-0 shadow-sm" style="border-radius:12px;background:#f8f9ff;">
            <div class="card-body p-4">
              <h6 class="fw-semibold mb-2" style="font-size:13px;color:#534ab7;">
                <i class="bi bi-info-circle me-1"></i> Tips
              </h6>
              <ul class="mb-0 ps-3" style="font-size:12px;color:#666;line-height:1.9;">
                <li>Select vehicle type first to load available models.</li>
                <li>Upload clear images of all documents.</li>
                <li>PDF or image files accepted for documents.</li>
                <li>Front and back vehicle photos are required.</li>
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
  padding: 20px;
  text-align: center;
  cursor: pointer;
  transition: border-color .2s, background .2s;
  background: #fafafa;
  min-height: 100px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.upload-box:hover { border-color: #4154f1; background: #f0f1ff; }
.upload-box.has-file { border-color: #1D9E75; background: #f0faf6; }
</style>

<script>
function previewFile(input, previewId, boxId) {
    var file = input.files[0];
    var box  = document.getElementById(boxId);
    if (!file) return;
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
        var prev = document.getElementById(previewId);
        prev.style.display = 'none';
        var existingLabel = box.querySelector('.file-name-label');
        if (!existingLabel) {
            var label = document.createElement('p');
            label.className = 'file-name-label mt-1 mb-0';
            label.style.fontSize = '12px';
            label.style.color = '#1D9E75';
            box.appendChild(label);
        }
        box.querySelector('.file-name-label').textContent = file.name;
    }
}

document.addEventListener('DOMContentLoaded', function () {

    // document.getElementById('vehicle_type').addEventListener('change', function () {
    //     var modelSelect = document.getElementById('vehicle_model');
    //     if (!this.value) {
    //         modelSelect.innerHTML = '<option value="">Select Type First</option>';
    //         return;
    //     }
    //     modelSelect.innerHTML = '<option value="">Loading...</option>';
    //     fetch('{{ url("admin/get_models_by_type") }}?type_id=' + this.value)
    //         .then(function(r) { return r.json(); })
    //         .then(function(data) {
    //             modelSelect.innerHTML = '<option value="">Select Model</option>';
    //             data.forEach(function(m) {
    //                 var opt = document.createElement('option');
    //                 opt.value = m.id;
    //                 opt.textContent = m.name;
    //                 modelSelect.appendChild(opt);
    //             });
    //         })
    //         .catch(function() {
    //             modelSelect.innerHTML = '<option value="">Error loading models</option>';
    //         });
    // });

    document.getElementById('addVehicleForm').addEventListener('submit', function (e) {
        e.preventDefault();

        document.querySelectorAll('[class*="err-"]').forEach(function(el) { el.textContent = ''; });
        document.querySelectorAll('.form-control, .form-select').forEach(function(el) { el.classList.remove('is-invalid'); });

        var submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        var alertBox = document.getElementById('formAlert');
        alertBox.className = 'alert d-none mb-3';
        alertBox.textContent = '';

        var formData = new FormData(this);

        fetch('{{ url("admin/add_vehicle") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Add Vehicle';

            if (res.status === 'true' || res.status === true) {
                alertBox.className = 'alert alert-success mb-3';
                alertBox.textContent = res.msg || 'Vehicle added successfully!';
                setTimeout(function() {
                    window.location.href = '{{ url("admin/all_vehicles") }}';
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
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Add Vehicle';
            alertBox.className = 'alert alert-danger mb-3';
            alertBox.textContent = 'Server error. Please try again.';
        });
    });

});
</script>

@endsection