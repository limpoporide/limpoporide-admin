@extends('admin.includes.main')
@section('content')

<style>
.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
    position: relative;
}

.status-dot.online {
    background: #28a745;
}

.status-dot.online::after {
    content: "";
    position: absolute;
    top: -4px;
    left: -4px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(40, 167, 69, 0.4);
    animation: pulse-green 1.5s infinite;
}

.status-dot.offline {
    background: #dc3545;
}

.status-dot.offline::after {
    content: "";
    position: absolute;
    top: -4px;
    left: -4px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.4);
    animation: pulse-red 1.5s infinite;
}

@keyframes pulse-green {
    0% {
        transform: scale(0.8);
        opacity: 1;
    }
    100% {
        transform: scale(1.8);
        opacity: 0;
    }
}

@keyframes pulse-red {
    0% {
        transform: scale(0.8);
        opacity: 1;
    }
    100% {
        transform: scale(1.8);
        opacity: 0;
    }
}
</style>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>All Drivers</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Drivers</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">All Drivers</h5>
                <!--<a class="btn btn-success" href="{{ url('admin/add_new_drivers') }}">-->
                <!--  <i class="bi bi-plus-circle me-1"></i> Add New Driver-->
                <!--</a>-->
              </div>

              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Profile</th>
                    <th>Name</th>
                     <th>Online Status</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Vehicle Type</th>
                    <th>Vehicle No.</th>
                    <th>Vehicle Status</th>
                    <th>Total Km</th>
                    <th>Wallet</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($data as $res){ ?>
                  <tr>
                    {{-- Profile --}}
                    <td>
                      <?php if($res->profile != ""): ?>
                        <a target="_blank" href="{{ url('/public/profile_image') }}/{{ $res->profile }}">
                          <img style="height:36px;width:36px;border-radius:50%;object-fit:cover;"
                               src="{{ url('/public/profile_image') }}/{{ $res->profile }}">
                        </a>
                      <?php else: ?>
                        <a target="_blank" href="{{ url('/public/assets/img/default_img.jpg') }}">
                          <img style="height:36px;width:36px;border-radius:50%;object-fit:cover;"
                               src="{{ url('/public/assets/img/default_img.jpg') }}">
                        </a>
                      <?php endif; ?>
                    </td>

                    <td>{{ $res->name }}</td>
                    <td>
                        @if($res->is_live == "1")
                            <span class="status-dot online"></span>
                        @else
                            <span class="status-dot offline"></span>
                        @endif
                    </td>
                    <td>{{ $res->email ?? '—' }}</td>
                    <td>{{ $res->mobile_no }}</td>

                    {{-- Vehicle Type --}}
                    <td>{{ $res->vehicle_name ?? '—' }}</td>

                    {{-- Vehicle Number --}}
                    <td>{{ $res->vehicle_number ?? '—' }}</td>

                    {{-- Vehicle Status Badge --}}
                    <td>
                      @if(!empty($res->vehicle_number))
                        <span class="badge bg-success">
                          <i class="bi bi-check-circle me-1"></i>Assigned
                        </span>
                      @else
                        <span class="badge bg-warning text-dark">
                          <i class="bi bi-exclamation-circle me-1"></i>Not Assigned
                        </span>
                      @endif
                    </td>

                    <td>{{ get_total_distance($res->id) }} Km</td>
                    <td>{{ $res->wallet_amount }}</td>
                    <td>{{ date('d-m-Y', strtotime($res->created_at)) }}</td>

                    {{-- Actions --}}
                    <td>
                        <div style="display:flex;">
                        <a href="{{ url('admin/edit_driver_details') }}/{{ $res->id }}">
                          <button type="button" class="btn-sm btn btn-primary">
                            <i class="bi bi-pencil"></i> Edit
                          </button>
                        </a>
                        <a style="margin-left:10px;" href="{{ url('admin/assign_vehicle') }}/{{ $res->id }}">
                          <button type="button" class="btn-sm btn {{ !empty($res->vehicle_number) ? 'btn-info' : 'btn-warning' }}">
                            <i class="bi bi-car-front"></i>
                            {{ !empty($res->vehicle_number) ? 'Edit Vehicle' : 'Assign Vehicle' }}
                          </button>
                        </a>
                        <a style="margin-left:10px;" href="{{ url('admin/driver_face_verifications') }}/{{ $res->id }}">
                          <button type="button" class="btn-sm btn btn-success">
                            <i class="bi bi-person"></i>
                            Face Verification
                          </button>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>

            </div>
          </div>

        </div>
      </div>
    </section>
  </main>
@endsection