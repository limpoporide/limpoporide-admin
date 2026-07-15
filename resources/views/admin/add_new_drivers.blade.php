@extends('admin.includes.main')
@section('content')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Add New Driver</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Drivers</li>
        <li class="breadcrumb-item active">Add New Driver</li>
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

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <form action="{{ url('admin/store_new_driver') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-person-circle me-1"></i> Personal Information
              </h5>
              <hr>

              <div class="row g-3">

                <div class="col-12 text-center mb-2">
                  <div style="display:inline-block; position:relative;">
                    <img id="profile-preview"
                         src="{{ asset('assets/img/default_img.jpg') }}"
                         onerror="this.src='https://ui-avatars.com/api/?name=Driver&background=6c757d&color=fff&size=110&rounded=true'"
                         style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid #dee2e6;box-shadow:0 2px 8px rgba(0,0,0,.12);">
                    <label for="profile"
                           style="position:absolute;bottom:4px;right:4px;background:#0d6efd;
                                  color:#fff;border-radius:50%;width:30px;height:30px;
                                  display:flex;align-items:center;justify-content:center;
                                  cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.3);">
                      <i class="bi bi-camera-fill" style="font-size:14px;"></i>
                    </label>
                  </div>
                  <div class="mt-1">
                    <small class="text-muted">Click the camera icon to upload a photo</small>
                  </div>
                  <input type="file" id="profile" name="profile" class="d-none" accept="image/*">
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                  <input type="text" name="name"
                         class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name') }}" placeholder="Enter full name" required>
                  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Email</label>
                  <input type="email" name="email"
                         class="form-control @error('email') is-invalid @enderror"
                         value="{{ old('email') }}" placeholder="Enter email address">
                  @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Mobile No <span class="text-danger">*</span></label>
                  <input type="text" name="mobile_no"
                         class="form-control @error('mobile_no') is-invalid @enderror"
                         value="{{ old('mobile_no') }}" placeholder="Enter mobile number" required>
                  @error('mobile_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Set a password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassBtn">
                      <i class="bi bi-eye" id="eye-icon"></i>
                    </button>
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Gender</label>
                  <select name="gender" class="form-select">
                    <option value="other" {{ old('gender','other')=='other' ? 'selected':'' }}>Other</option>
                    <option value="male" {{ old('gender')=='male' ? 'selected':'' }}>Male</option>
                    <option value="female" {{ old('gender')=='female' ? 'selected':'' }}>Female</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Date of Birth</label>
                  <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                </div>

                <div class="col-md-8">
                  <label class="form-label fw-semibold">Address</label>
                  <textarea name="address" class="form-control" rows="2"
                            placeholder="Enter full address">{{ old('address') }}</textarea>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Pincode</label>
                  <input type="text" name="pincode" class="form-control"
                         value="{{ old('pincode') }}" placeholder="Pincode" maxlength="6">
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">State</label>
                  <select name="state" class="form-select">
                    <option value="">-- Select State --</option>
                    @foreach($states as $st)
                      <option value="{{ $st->id }}" {{ old('state')==$st->id ? 'selected':'' }}>
                        {{ $st->state_name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">City</label>
                  <select name="city" class="form-select">
                    <option value="">-- Select City --</option>
                    @foreach($cities as $ct)
                      <option value="{{ $ct->id }}" {{ old('city')==$ct->id ? 'selected':'' }}>
                        {{ $ct->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Status</label>
                  <select name="status" class="form-select">
                    <option value="1" {{ old('status','1')=='1' ? 'selected':'' }}>Active</option>
                    <option value="0" {{ old('status')=='0' ? 'selected':'' }}>Inactive</option>
                  </select>
                </div>

              </div>
            </div>
          </div>

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-card-text me-1"></i> Government ID & License Details
              </h5>
              <hr>

              <div class="row g-3">

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Government ID Type <span class="text-danger">*</span></label>
                  <select name="govt_id_type" class="form-select @error('govt_id_type') is-invalid @enderror" required>
                    <option value="">-- Select ID Type --</option>
                    <option value="passport" {{ old('govt_id_type')=='passport' ? 'selected':'' }}>Passport</option>
                    <option value="national_id" {{ old('govt_id_type')=='national_id' ? 'selected':'' }}>National ID</option>
                    <option value="driving_license" {{ old('govt_id_type')=='driving_license' ? 'selected':'' }}>Driver's License</option>
                  </select>
                  @error('govt_id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Government ID Number <span class="text-danger">*</span></label>
                  <input type="text" name="govt_id_number"
                         class="form-control @error('govt_id_number') is-invalid @enderror"
                         value="{{ old('govt_id_number') }}" placeholder="Enter ID number" required>
                  @error('govt_id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Driving License Number <span class="text-danger">*</span></label>
                  <input type="text" name="driving_licence_number"
                         class="form-control @error('driving_licence_number') is-invalid @enderror"
                         value="{{ old('driving_licence_number') }}" placeholder="Enter license number" required>
                  @error('driving_licence_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Driving License Expiry Date <span class="text-danger">*</span></label>
                  <input type="date" name="driving_licence_expiry"
                         class="form-control @error('driving_licence_expiry') is-invalid @enderror"
                         value="{{ old('driving_licence_expiry') }}" required>
                  @error('driving_licence_expiry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-8">
                  <label class="form-label fw-semibold">License Document Upload</label>
                  <input type="file" name="licence_document"
                         class="form-control @error('licence_document') is-invalid @enderror"
                         accept=".jpg,.jpeg,.png,.pdf">
                  <small class="text-muted">Accepted formats: JPG, PNG, PDF (max 5MB)</small>
                  @error('licence_document')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

              </div>
            </div>
          </div>

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-bank me-1"></i> Bank Account Details
              </h5>
              <hr>

              <div class="row g-3">

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Bank Account Number <span class="text-danger">*</span></label>
                  <input type="text" name="bank_account_number"
                         class="form-control @error('bank_account_number') is-invalid @enderror"
                         value="{{ old('bank_account_number') }}" placeholder="Enter account number" required>
                  @error('bank_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                  <input type="text" name="bank_name"
                         class="form-control @error('bank_name') is-invalid @enderror"
                         value="{{ old('bank_name') }}" placeholder="Enter bank name" required>
                  @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                  <input type="text" name="account_holder_name"
                         class="form-control @error('account_holder_name') is-invalid @enderror"
                         value="{{ old('account_holder_name') }}" placeholder="Enter account holder name" required>
                  @error('account_holder_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

              </div>
            </div>
          </div>

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-car-front me-1"></i> Assign Vehicle
              </h5>
              <hr>

              @if($unassigned_vehicles->isEmpty())
                <div class="alert alert-warning mb-0">
                  <i class="bi bi-exclamation-triangle me-1"></i>
                  No unassigned vehicles available. Please add a vehicle first in
                  <strong>Vehicle Management</strong>, then assign it here.
                </div>
              @else
                <div class="row g-3">

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Select Vehicle</label>
                    <select name="vehicle_id" id="vehicle_dropdown" class="form-select">
                      <option value="">-- Select a Vehicle --</option>
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
                                {{ old('vehicle_id')==$v->id ? 'selected':'' }}>
                          {{ $v->vehicle_number }} — {{ $v->vehicle_name }}
                          @if(!empty($v->vehicle_type_name)) ({{ $v->vehicle_type_name }}) @endif
                        </option>
                      @endforeach
                    </select>
                    <small class="text-muted">Only unassigned vehicles are listed here.</small>
                  </div>

                  <div class="col-md-6" id="vehicle-preview-wrap" style="display:none;">
                    <label class="form-label fw-semibold">Vehicle Details</label>
                    <div class="border rounded p-3 bg-light">
                      <table class="table table-sm table-borderless mb-0">
                        <tbody>
                          <tr>
                            <td class="text-muted fw-semibold" style="width:45%">Vehicle No.</td>
                            <td id="pv-number">—</td>
                          </tr>
                          <tr>
                            <td class="text-muted fw-semibold">Name / Brand</td>
                            <td id="pv-name">—</td>
                          </tr>
                          <tr>
                            <td class="text-muted fw-semibold">Type</td>
                            <td id="pv-type">—</td>
                          </tr>
                          <tr>
                            <td class="text-muted fw-semibold">Model</td>
                            <td id="pv-model">—</td>
                          </tr>
                          <tr>
                            <td class="text-muted fw-semibold">Manufacturer</td>
                            <td id="pv-manufacture">—</td>
                          </tr>
                          <tr>
                            <td class="text-muted fw-semibold">Reg. Year</td>
                            <td id="pv-year">—</td>
                          </tr>
                          <tr>
                            <td class="text-muted fw-semibold">Fuel Type</td>
                            <td id="pv-fuel">—</td>
                          </tr>
                          <tr>
                            <td class="text-muted fw-semibold">Owner Name</td>
                            <td id="pv-owner">—</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                </div>
              @endif

            </div>
          </div>

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-send me-1"></i> Credential Delivery
              </h5>
              <hr>

              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch"
                       id="send_credentials" name="send_credentials" value="1"
                       {{ old('send_credentials') ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="send_credentials">
                  Send login credentials to driver via SMS and Email
                </label>
              </div>
              <small class="text-muted">If enabled, the driver will receive their mobile number and password on the email/mobile provided above.</small>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ url('admin/approved_drivers_list') }}" class="btn btn-secondary px-4">
              <i class="bi bi-arrow-left me-1"></i> Cancel
            </a>
            <button type="submit" class="btn btn-success px-5">
              <i class="bi bi-check-circle me-1"></i> Save Driver
            </button>
          </div>

        </form>
      </div>
    </div>
  </section>
</main>

<script>
(function () {

  var profileInput   = document.getElementById('profile');
  var profilePreview = document.getElementById('profile-preview');

  if (profileInput && profilePreview) {
    profileInput.addEventListener('change', function () {
      var file = this.files[0];
      if (!file) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        profilePreview.src = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  }

  var toggleBtn = document.getElementById('togglePassBtn');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      var inp = document.getElementById('password');
      var ico = document.getElementById('eye-icon');
      if (inp.type === 'password') {
        inp.type = 'text';
        ico.classList.replace('bi-eye', 'bi-eye-slash');
      } else {
        inp.type = 'password';
        ico.classList.replace('bi-eye-slash', 'bi-eye');
      }
    });
  }

  var vehicleDrop = document.getElementById('vehicle_dropdown');
  if (vehicleDrop) {
    vehicleDrop.addEventListener('change', function () {
      var opt  = this.options[this.selectedIndex];
      var wrap = document.getElementById('vehicle-preview-wrap');
      if (!this.value) {
        wrap.style.display = 'none';
        return;
      }
      wrap.style.display = 'block';
      document.getElementById('pv-number').textContent      = opt.dataset.number      || '—';
      document.getElementById('pv-name').textContent        = opt.dataset.name        || '—';
      document.getElementById('pv-type').textContent        = opt.dataset.type        || '—';
      document.getElementById('pv-model').textContent       = opt.dataset.model       || '—';
      document.getElementById('pv-manufacture').textContent = opt.dataset.manufacture || '—';
      document.getElementById('pv-year').textContent        = opt.dataset.year        || '—';
      document.getElementById('pv-fuel').textContent        = opt.dataset.fuel        || '—';
      document.getElementById('pv-owner').textContent       = opt.dataset.owner       || '—';
    });
    if (vehicleDrop.value) {
      vehicleDrop.dispatchEvent(new Event('change'));
    }
  }

})();
</script>
@endsection