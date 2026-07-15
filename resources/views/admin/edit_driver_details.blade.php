@extends('admin.includes.main')
@section('content')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Edit Driver</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Drivers</li>
        <li class="breadcrumb-item active">Edit Driver</li>
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
            <i class="bi bi-exclamation-circle me-1"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if($driver->is_suspended)
          <div class="alert alert-danger">
            <i class="bi bi-slash-circle me-1"></i>
            <strong>This driver is currently suspended.</strong>
            <div class="mt-1">Reason: {{ $driver->suspension_reason }}</div>
            <div class="small text-muted">Suspended on: {{ !empty($driver->suspended_at) ? date('d M Y, h:i A', strtotime($driver->suspended_at)) : '—' }}</div>
          </div>
        @endif

        <form action="{{ url('admin/update_driver_details') }}/{{ $driver->id }}"
              method="POST" enctype="multipart/form-data">
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
                         src="{{ !empty($driver->profile)
                                  ? url('public/profile_image/' . $driver->profile)
                                  : asset('assets/img/default_img.jpg') }}"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($driver->name) }}&background=6c757d&color=fff&size=110&rounded=true'"
                         style="width:110px;height:110px;border-radius:50%;object-fit:cover;
                                border:3px solid #dee2e6;box-shadow:0 2px 8px rgba(0,0,0,.12);">
                    <label for="profile"
                           title="Change photo"
                           style="position:absolute;bottom:4px;right:4px;background:#0d6efd;
                                  color:#fff;border-radius:50%;width:30px;height:30px;
                                  display:flex;align-items:center;justify-content:center;
                                  cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.3);">
                      <i class="bi bi-camera-fill" style="font-size:14px;"></i>
                    </label>
                  </div>
                  <div class="mt-1">
                    <small class="text-muted">Click the camera icon to change photo</small>
                  </div>
                  <input type="file" id="profile" name="profile" class="d-none" accept="image/*">
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">
                    Full Name <span class="text-danger">*</span>
                  </label>
                  <input type="text" name="name"
                         class="form-control @error('name') is-invalid @enderror"
                         value="{{ old('name', $driver->name) }}"
                         placeholder="Full name" required>
                  @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Email</label>
                  <input type="email" name="email"
                         class="form-control @error('email') is-invalid @enderror"
                         value="{{ old('email', $driver->email) }}"
                         placeholder="Email address">
                  @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">
                    Mobile No <span class="text-danger">*</span>
                  </label>
                  <input type="text" name="mobile_no"
                         class="form-control @error('mobile_no') is-invalid @enderror"
                         value="{{ old('mobile_no', $driver->mobile_no) }}"
                         placeholder="Mobile number" required>
                  @error('mobile_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Gender</label>
                  <select name="gender" class="form-select">
                    <option value="0" {{ old('gender', $driver->gender) == 0 ? 'selected':'' }}>Other</option>
                    <option value="1" {{ old('gender', $driver->gender) == 1 ? 'selected':'' }}>Male</option>
                    <option value="2" {{ old('gender', $driver->gender) == 2 ? 'selected':'' }}>Female</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Date of Birth</label>
                  <input type="date" name="dob" class="form-control"
                         value="{{ old('dob', $driver->dob) }}">
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Pincode</label>
                  <input type="text" name="pincode" class="form-control" maxlength="6"
                         value="{{ old('pincode', $driver->pincode) }}"
                         placeholder="Pincode">
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">State</label>
                  <select name="state" id="state_select" class="form-select">
                    <option value="">-- Select State --</option>
                    @foreach($states as $st)
                      <option value="{{ $st->id }}"
                        {{ old('state', $driver->state) == $st->id ? 'selected':'' }}>
                        {{ $st->state_name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">City</label>
                  <select name="city" id="city_select" class="form-select">
                    <option value="">-- Select City --</option>
                    @foreach($cities as $ct)
                      <option value="{{ $ct->id }}"
                        {{ old('city', $driver->city) == $ct->id ? 'selected':'' }}>
                        {{ $ct->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Status</label>
                  <select name="status" class="form-select">
                    <option value="1" {{ old('status', $driver->status) == 1 ? 'selected':'' }}>Active</option>
                    <option value="0" {{ old('status', $driver->status) == 0 ? 'selected':'' }}>Inactive</option>
                  </select>
                </div>

                <div class="col-md-12">
                  <label class="form-label fw-semibold">Address</label>
                  <textarea name="address" class="form-control" rows="2"
                            placeholder="Full address">{{ old('address', $driver->address) }}</textarea>
                </div>

                <div class="col-md-12">
                  <label class="form-label fw-semibold">About</label>
                  <textarea name="about_us" class="form-control" rows="2"
                            placeholder="Short bio / about the driver">{{ old('about_us', $driver->about_us) }}</textarea>
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
                    <option value="passport" {{ old('govt_id_type', $driver->govt_id_type)=='passport' ? 'selected':'' }}>Passport</option>
                    <option value="national_id" {{ old('govt_id_type', $driver->govt_id_type)=='national_id' ? 'selected':'' }}>National ID</option>
                    <option value="driving_license" {{ old('govt_id_type', $driver->govt_id_type)=='driving_license' ? 'selected':'' }}>Driver's License</option>
                  </select>
                  @error('govt_id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Government ID Number <span class="text-danger">*</span></label>
                  <input type="text" name="govt_id_number"
                         class="form-control @error('govt_id_number') is-invalid @enderror"
                         value="{{ old('govt_id_number', $driver->govt_id_number) }}"
                         placeholder="Enter ID number" required>
                  @error('govt_id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Driving License Number <span class="text-danger">*</span></label>
                  <input type="text" name="driving_licence_number"
                         class="form-control @error('driving_licence_number') is-invalid @enderror"
                         value="{{ old('driving_licence_number', $driver->driving_licence_number) }}"
                         placeholder="Enter license number" required>
                  @error('driving_licence_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Driving License Expiry Date <span class="text-danger">*</span></label>
                  <input type="date" name="driving_licence_expiry"
                         class="form-control @error('driving_licence_expiry') is-invalid @enderror"
                         value="{{ old('driving_licence_expiry', $driver->driving_licence_expiry) }}" required>
                  @error('driving_licence_expiry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-8">
                  <label class="form-label fw-semibold">License Document</label>
                  <input type="file" name="licence_document"
                         class="form-control @error('licence_document') is-invalid @enderror"
                         accept=".jpg,.jpeg,.png,.pdf">
                  @if(!empty($driver->licence_document))
                    <small class="text-muted d-block mt-1">
                      Current file:
                      <a href="{{ url('public/licence_documents/' . $driver->licence_document) }}" target="_blank">
                        View uploaded document
                      </a>
                    </small>
                  @endif
                  <small class="text-muted">Accepted formats: JPG, PNG, PDF (max 5MB). Leave blank to keep existing document.</small>
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
                         value="{{ old('bank_account_number', $driver->bank_account) }}"
                         placeholder="Enter account number" required>
                  @error('bank_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                  <input type="text" name="bank_name"
                         class="form-control @error('bank_name') is-invalid @enderror"
                         value="{{ old('bank_name', $driver->bank_name) }}"
                         placeholder="Enter bank name" required>
                  @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                  <input type="text" name="account_holder_name"
                         class="form-control @error('account_holder_name') is-invalid @enderror"
                         value="{{ old('account_holder_name', $driver->bank_ac_holder_name) }}"
                         placeholder="Enter account holder name" required>
                  @error('account_holder_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

              </div>
            </div>
          </div>

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-lock me-1"></i> Change Password
                <small class="text-muted fs-6 fw-normal">(Leave blank to keep current password)</small>
              </h5>
              <hr>

              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-semibold">New Password</label>
                  <div class="input-group">
                    <input type="password" name="password" id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter new password (min 6 chars)">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassBtn">
                      <i class="bi bi-eye" id="eye-icon"></i>
                    </button>
                    @error('password')
                      <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                  </div>
                  <small class="text-muted">Only fill this if you want to change the password.</small>
                </div>
              </div>

            </div>
          </div>

          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">
                <i class="bi bi-info-circle me-1"></i> Account Info
              </h5>
              <hr>
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label fw-semibold text-muted small">Driver ID</label>
                  <div class="form-control bg-light text-muted">{{ $driver->id }}</div>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold text-muted small">Account Type</label>
                  <div class="form-control bg-light text-muted">
                    {{ $driver->type == 2 ? 'Driver' : 'User' }}
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold text-muted small">Registered On</label>
                  <div class="form-control bg-light text-muted">
                    {{ date('d M Y, h:i A', strtotime($driver->created_at)) }}
                  </div>
                </div>
                <div class="col-md-3">
                  <label class="form-label fw-semibold text-muted small">Last Updated</label>
                  <div class="form-control bg-light text-muted">
                    {{ date('d M Y, h:i A', strtotime($driver->updated_at)) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ url('admin/approved_drivers_list') }}" class="btn btn-secondary px-4">
              <i class="bi bi-arrow-left me-1"></i> Back to Drivers
            </a>
            <div class="d-flex gap-2">
              <a href="{{ url('admin/assign_vehicle') }}/{{ $driver->id }}"
                 class="btn btn-outline-info px-4">
                <i class="bi bi-car-front me-1"></i> Manage Vehicle
              </a>

              @if($driver->is_suspended)
                <button type="button" class="btn btn-outline-success px-4"
                        onclick="if(confirm('Reactivate this driver?')) document.getElementById('reactivateForm').submit();">
                  <i class="bi bi-check2-circle me-1"></i> Reactivate
                </button>
              @else
                <button type="button" class="btn btn-outline-danger px-4" data-bs-toggle="modal" data-bs-target="#suspendModal">
                  <i class="bi bi-slash-circle me-1"></i> Suspend Driver
                </button>
              @endif

              <button type="submit" class="btn btn-success px-5">
                <i class="bi bi-check-circle me-1"></i> Update Driver
              </button>
            </div>
          </div>

        </form>

        <form id="reactivateForm" action="{{ url('admin/reactivate_driver') }}/{{ $driver->id }}" method="POST" class="d-none">
          @csrf
        </form>

        <div class="modal fade" id="suspendModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form action="{{ url('admin/suspend_driver') }}/{{ $driver->id }}" method="POST">
                @csrf
                <div class="modal-header">
                  <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-1"></i> Suspend Driver</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <label class="form-label fw-semibold">Reason for Suspension <span class="text-danger">*</span></label>
                  <textarea name="suspension_reason" class="form-control" rows="3" placeholder="Enter reason for suspending this driver" required></textarea>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">
                    <i class="bi bi-slash-circle me-1"></i> Confirm Suspension
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

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

})();
</script>
@endsection