@extends('admin.includes.main')
@section('content')
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Completed Bookings</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Bookings</li>
          <li class="breadcrumb-item active">Completed Bookings</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Completed Bookings</h5>

              <!-- Filter Bar -->
              <form method="GET" action="{{ route('admin.complete_booking') }}" class="row g-2 align-items-end mb-3">
                <div class="col-auto">
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.complete_booking') }}"
                       class="btn btn-outline-primary {{ !$filter ? 'active' : '' }}">All</a>
                    <a href="{{ route('admin.complete_booking', ['filter' => 'today']) }}"
                       class="btn btn-outline-primary {{ $filter == 'today' ? 'active' : '' }}">Today</a>
                    <a href="{{ route('admin.complete_booking', ['filter' => 'weekly']) }}"
                       class="btn btn-outline-primary {{ $filter == 'weekly' ? 'active' : '' }}">Weekly</a>
                    <a href="{{ route('admin.complete_booking', ['filter' => 'monthly']) }}"
                       class="btn btn-outline-primary {{ $filter == 'monthly' ? 'active' : '' }}">Monthly</a>
                    <a href="{{ route('admin.complete_booking', ['filter' => 'yearly']) }}"
                       class="btn btn-outline-primary {{ $filter == 'yearly' ? 'active' : '' }}">Yearly</a>
                  </div>
                </div>
                <div class="col-auto">
                  <label class="form-label mb-0">From</label>
                  <input type="date" name="fromdate" class="form-control" value="{{ request('fromdate') }}">
                </div>
                <div class="col-auto">
                  <label class="form-label mb-0">To</label>
                  <input type="date" name="enddate" class="form-control" value="{{ request('enddate') }}">
                </div>
                <div class="col-auto">
                  <button type="submit" class="btn btn-success"><i class="bi bi-funnel"></i> Apply</button>
                  <a href="{{ route('admin.complete_booking') }}" class="btn btn-secondary">Reset</a>
                </div>
              </form>
              <!-- End Filter Bar -->

              <table class="table datatable">
                <thead>
                 <tr class="headings">
                    <th>S.No</th>
                    <th>Booking ID </th>
                    <th>User Name </th>
                    <th>Driver Name </th>
                    <th>Pickup Location</th>
                    <th>Drop Location</th>
                    <th>Distance</th>
                    <th>Vehicle</th>
                    <th>Time</th>
                    <th>Fare </th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date </th>
                    <th>Report</th>
                  </tr>
                </thead>
                <tbody>
                 @php
                    $statusColors = [
                        0 => 'info', 1 => 'warning', 2 => 'primary', 3 => 'danger',
                        4 => 'secondary', 5 => 'success', 6 => 'danger', 7 => 'dark', 8 => 'danger',
                    ];
                 @endphp
                 @forelse($complete_booking as $key=>$res)
                  <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $res->booking_id }}</td>
                    <td>{{ $res->name }}</td>
                    <td>
                      @if(empty($res->driver_id))
                        <span class="badge bg-danger text-white">Driver Not Assigned</span>
                      @else
                        {{ $res->driver_name ?? "----" }}
                      @endif
                    </td>
                    <td>{{ $res->picup_location }}</td>
                    <td>{{ $res->drop_location }}</td>
                    <td>{{ $res->distance }} Km</td>
                    <td>{{ $res->vehicle_name }}</td>
                    <td>{{ $res->time_duration }}</td>
                    <td>{{ $res->fare }}</td>
                    <td>
                      @php $color = $statusColors[$res->booking_status_id] ?? 'secondary'; @endphp
                      <span class="badge bg-{{ $color }} text-white">{{ $res->booking_status_name ?? 'N/A' }}</span>
                    </td>
                    <td>
                      @if($res->payment_status == "1")
                        <span class="badge bg-success text-white">Completed</span>
                      @else
                        <span class="badge bg-warning text-dark">Pending</span>
                      @endif
                    </td>
                    <td>{{ date('d-m-Y', strtotime($res->created_at)) }}</td>
                    <td>
                      <button type="button" class="btn btn-sm btn-warning view-report-btn"
                              data-booking-id="{{ $res->booking_id }}">
                        <i class="bi bi-file-earmark-text"></i> Report
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="14" class="text-center">No bookings found.</td></tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Activity Log Modal -->
  <div id="activityLogModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:420px; max-width:92%; max-height:85vh; border-radius:8px; overflow:hidden; display:flex; flex-direction:column;">
      <div style="background:#f5a623; color:#fff; padding:12px 16px; display:flex; justify-content:space-between; align-items:center; font-weight:600;">
        <span>Booking Activity Log <span id="logBookingId"></span></span>
        <span id="closeActivityLogModal" style="cursor:pointer; font-size:22px; line-height:1;">&times;</span>
      </div>
      <div id="activityLogBody" style="padding:16px; overflow-y:auto;">
        <div class="text-center">Loading...</div>
      </div>
    </div>
  </div>

  <style>
    .timeline-step{ display:flex; margin-bottom:18px; }
    .timeline-icon{ width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; margin-right:12px; flex-shrink:0; }
    .timeline-content{ flex:1; border:1px solid #eee; border-radius:6px; padding:8px 12px; }
    .timeline-content .title-row{ display:flex; justify-content:space-between; align-items:center; }
    .badge-pill{ padding:2px 10px; border-radius:12px; font-size:11px; color:#fff; }
  </style>

  <script>
  (function(){
    var modal = document.getElementById('activityLogModal');
    var closeBtn = document.getElementById('closeActivityLogModal');
    var ajaxUrl = "{{ route('admin.getBookingActivityLog') }}";

    function openModal(){ modal.style.display = 'flex'; }
    function closeModal(){ modal.style.display = 'none'; }

    closeBtn.onclick = closeModal;
    modal.addEventListener('click', function(e){ if(e.target === modal) closeModal(); });

    document.addEventListener('click', function(e){
        var btn = e.target.closest('.view-report-btn');
        if(!btn) return;

        var bookingId = btn.getAttribute('data-booking-id');
        document.getElementById('logBookingId').textContent = '(' + bookingId + ')';
        document.getElementById('activityLogBody').innerHTML = '<div class="text-center">Loading...</div>';
        openModal();

        fetch(ajaxUrl + '?booking_id=' + encodeURIComponent(bookingId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res){
            if(!res.ok){ throw new Error('HTTP ' + res.status); }
            return res.json();
        })
        .then(function(res){
            if(!res.status){
                document.getElementById('activityLogBody').innerHTML = '<p class="text-center">No activity log found.</p>';
                return;
            }
            var iconColors = {
                'CREATED':'#6c757d','UPCOMING':'#0dcaf0','ONGOING':'#0d6efd',
                'ARRIVED':'#ffc107','VERIFIED':'#0dcaf0','COMPLETED':'#198754','CANCEL':'#dc3545'
            };
            var html = '';
            res.timeline.forEach(function(step){
                var c = iconColors[step.status] || '#6c757d';
                html += '<div class="timeline-step">';
                html +=   '<div class="timeline-icon" style="background:'+c+';"></div>';
                html +=   '<div class="timeline-content">';
                html +=     '<div class="title-row"><strong>'+step.title+'</strong>';
                if(step.status){ html += '<span class="badge-pill" style="background:'+c+';">'+step.status+'</span>'; }
                html +=     '</div>';
                html +=     '<div>By: '+step.by+'</div>';
                if(step.datetime){ html += '<div class="text-muted small">'+step.datetime+'</div>'; }
                if(step.amount){ html += '<div style="color:#198754;">R '+step.amount+'</div>'; }
                html +=   '</div></div>';
            });
            document.getElementById('activityLogBody').innerHTML = html;
        })
        .catch(function(err){
            console.error('Activity log fetch failed:', err);
            document.getElementById('activityLogBody').innerHTML = '<p class="text-center text-danger">Failed to load activity log.</p>';
        });
    });
  })();
  </script>

@endsection