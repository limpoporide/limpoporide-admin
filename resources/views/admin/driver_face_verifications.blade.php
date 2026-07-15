@extends('admin.includes.main')
@section('content')
<main id="main" class="main">

  <div class="pagetitle">
    <h1>Face Verification History</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item">Drivers</li>
        <li class="breadcrumb-item active">Face Verification History</li>
      </ol>
    </nav>
  </div>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-person-badge me-1"></i> Driver Details
            </h5>
            <hr>
            <div class="d-flex align-items-center gap-3">
              <img src="{{ !empty($driver->profile)
                            ? url('public/profile_image/' . $driver->profile)
                            : asset('assets/img/default_img.jpg') }}"
                   onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($driver->name) }}&background=6c757d&color=fff&size=70&rounded=true'"
                   style="width:70px;height:70px;border-radius:50%;object-fit:cover;border:2px solid #dee2e6;">
              <div>
                <h6 class="mb-1">{{ $driver->name }}</h6>
                <div class="text-muted small">{{ $driver->mobile_no }} @if(!empty($driver->email)) | {{ $driver->email }} @endif</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">
              <i class="bi bi-shield-check me-1"></i> Face Verification Records
            </h5>
            <hr>

            @if($verifications->isEmpty())
              <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle me-1"></i>
                No face verification records found for this driver.
              </div>
            @else
              <div class="table-responsive">
                <table id="faceVerificationTable" class="table table-bordered table-hover align-middle" style="width:100%">
                  <thead class="table-light">
                    <tr>
                      <th>#</th>
                      <th>Uploaded Image</th>
                      <th>Status</th>
                      <th>Confidence Score</th>
                      <th>Date & Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($verifications as $index => $v)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                          @if(!empty($v->image))
                            <a href="{{ url('public/face_verification/' . $v->image) }}" target="_blank">
                              <img src="{{ url('public/face_verification/' . $v->image) }}"
                                   style="width:55px;height:55px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;">
                            </a>
                          @else
                            <span class="text-muted">—</span>
                          @endif
                        </td>
                        <td data-order="{{ $v->status }}">
                          @if(strtolower($v->status) == 'success')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Success</span>
                          @else
                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Failed</span>
                          @endif
                        </td>
                        <td>{{ !empty($v->confidence_score) ? $v->confidence_score . '%' : '—' }}</td>
                        <td data-order="{{ strtotime($v->created_at) }}">
                          {{ date('d M Y, h:i A', strtotime($v->created_at)) }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @endif

          </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
          <a href="{{ url('admin/approved_drivers_list') }}" class="btn btn-secondary px-4">
            <i class="bi bi-arrow-left me-1"></i> Back to Drivers
          </a>
        </div>

      </div>
    </div>
  </section>
</main>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
  $('#faceVerificationTable').DataTable({
    order: [[4, 'desc']],
    columnDefs: [
      { orderable: false, targets: 1 }
    ],
    language: {
      search: "Search records:",
      lengthMenu: "Show _MENU_ entries",
      info: "Showing _START_ to _END_ of _TOTAL_ records",
      emptyTable: "No face verification records found."
    }
  });
});
</script>
@endsection