@extends('admin.includes.main')
@section('content')

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Dashboard</li>
      </ol>
    </nav>
  </div>

  <section class="section dashboard">

    {{-- Row 1: 4 primary stat cards --}}
    <div class="row g-3 mb-3">
      <div class="col-xxl-3 col-md-6">
        <div class="card dash-card">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="stat-icon bg-blue-soft"><i class="bi bi-people-fill text-blue"></i></div>
            <div>
              <p class="stat-lbl">Total Users</p>
              <h5 class="stat-val">{{ $all_users }}</h5>
              <span class="stat-delta">+{{ $users_growth ?? 12 }}% this week</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dash-card">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="stat-icon bg-green-soft"><i class="bi bi-car-front-fill text-green"></i></div>
            <div>
              <p class="stat-lbl">Total Drivers</p>
              <h5 class="stat-val">{{ $all_drivers }}</h5>
              <span class="stat-delta">+{{ $drivers_growth ?? 5 }}% this week</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dash-card">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="stat-icon bg-orange-soft"><i class="bi bi-geo-alt-fill text-orange"></i></div>
            <div>
              <p class="stat-lbl">Rides Today</p>
              <h5 class="stat-val">{{ $today_rides }}</h5>
              <span class="stat-delta">+{{ $rides_growth ?? 8 }}% this week</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dash-card">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="stat-icon bg-purple-soft"><i class="bi bi-currency-dollar text-purple"></i></div>
            <div>
              <p class="stat-lbl">Revenue Today</p>
              <h5 class="stat-val">R {{ number_format((float)$today_earnings, 2) }}</h5>
              <span class="stat-delta">+{{ $revenue_growth ?? 15 }}% this week</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Row 2: 4 secondary stat cards --}}
    <div class="row g-3 mb-3">
      <div class="col-xxl-3 col-md-6">
        <div class="card dash-card">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="stat-icon bg-teal-soft"><i class="bi bi-circle-fill text-teal" style="font-size:10px;"></i></div>
            <div>
              <p class="stat-lbl">Drivers Online</p>
              <h5 class="stat-val">{{ $drivers_online }}</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dash-card">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="stat-icon bg-orange-soft"><i class="bi bi-hourglass-split text-orange"></i></div>
            <div>
              <p class="stat-lbl">Pending Rides</p>
              <h5 class="stat-val">{{ $pending_rides }}</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dash-card">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="stat-icon bg-red-soft"><i class="bi bi-exclamation-circle-fill text-red"></i></div>
            <div>
              <p class="stat-lbl">Active SOS</p>
              <h5 class="stat-val">{{ $active_sos }}</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xxl-3 col-md-6">
        <div class="card dash-card">
          <div class="card-body d-flex align-items-center gap-3 py-3">
            <div class="stat-icon bg-blue-soft"><i class="bi bi-calendar-event-fill text-blue"></i></div>
            <div>
              <p class="stat-lbl">Scheduled Today</p>
              <h5 class="stat-val">{{ $scheduled_today }}</h5>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Row 3: Revenue chart + Donut --}}
    <div class="row g-3 mb-3">
      <div class="col-lg-8">
        <div class="card dash-card h-100">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="card-title mb-0">
                Revenue — Last 7 Days
                <span id="filter-label" class="text-muted fw-normal" style="font-size:12px;">/This Year</span>
              </h6>
              <div class="dropdown">
                <a href="#" class="text-muted" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><h6 class="dropdown-header">Filter</h6></li>
                  <li><a class="dropdown-item filter-option" href="#" data-filter="today">Today</a></li>
                  <li><a class="dropdown-item filter-option" href="#" data-filter="month">This Month</a></li>
                  <li><a class="dropdown-item filter-option" href="#" data-filter="year">This Year</a></li>
                </ul>
              </div>
            </div>
            <div id="reportsChart"></div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card dash-card h-100">
          <div class="card-body">
            <h6 class="card-title mb-3">Rides by Category</h6>
            <div id="categoryDonut"></div>
            <div class="mt-3">
              <div class="cat-row border-bottom">
                <div class="d-flex align-items-center gap-2">
                  <span class="cat-dot" style="background:#1D9E75;"></span>
                  <span style="color:#1D9E75;font-size:13px;">Bike Rides</span>
                </div>
                <span class="cat-count">{{ $bike_rides }}</span>
              </div>
              <div class="cat-row border-bottom">
                <div class="d-flex align-items-center gap-2">
                  <span class="cat-dot" style="background:#378ADD;"></span>
                  <span style="color:#378ADD;font-size:13px;">Auto Rides</span>
                </div>
                <span class="cat-count">{{ $auto_rides }}</span>
              </div>
              <div class="cat-row border-bottom">
                <div class="d-flex align-items-center gap-2">
                  <span class="cat-dot" style="background:#BA7517;"></span>
                  <span style="color:#BA7517;font-size:13px;">Car Rides</span>
                </div>
                <span class="cat-count">{{ $car_rides }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Row 4: Live Map + Recent Rides --}}
    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card dash-card h-100">
          <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
              <h6 class="mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-map"></i> Live Driver Locations
              </h6>
              <div class="d-flex align-items-center gap-2" style="font-size:13px;">
                <span class="d-flex align-items-center gap-1">
                  <span style="width:8px;height:8px;border-radius:50%;background:#1D9E75;display:inline-block;"></span>
                  <span class="text-muted">{{ $drivers_online }} online</span>
                </span>
                <a href="{{ url('admin/dashboard') }}" class="text-primary text-decoration-none ms-2">Refresh</a>
              </div>
            </div>
            <div id="liveMap" style="height:504px;width:100%;"></div>
            <p class="text-end mb-0 px-2 py-1 border-top" style="font-size:10px;color:#aaa;">
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card dash-card h-100">
          <div class="card-body">
            <h6 class="card-title mb-3">Recent Rides</h6>

            @forelse($recent_rides as $ride)
            <div class="d-flex align-items-start gap-2 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
              <i class="bi bi-geo-alt-fill text-danger mt-1" style="font-size:13px;flex-shrink:0;"></i>
              <div style="min-width:0;">
                <p class="mb-1" style="font-size:12px">
                  {{ $ride->picup_location ?? 'N/A' }}
                </p>
                <div class="d-flex align-items-center gap-2">
                  @if($ride->booking_status == '1')
                    <span class="ride-badge badge-pending">Pending</span>
                  @elseif($ride->booking_status == '5')
                    <span class="ride-badge badge-completed">Completed</span>
                  @else
                    <span class="ride-badge badge-cancelled">Cancelled</span>
                  @endif
                  <span class="text-muted" style="font-size:11px;">
                    R {{ number_format((float)($ride->fare ?? 0), 2) }}
                  </span>
                </div>
              </div>
            </div>
            @empty
            <p class="text-muted text-center py-3" style="font-size:13px;">No recent rides</p>
            @endforelse

            <div class="text-end mt-3">
              <a href="{{ url('admin/complete_booking') }}" class="text-primary text-decoration-none" style="font-size:13px;">
                View all rides →
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
</main>

{{-- ── Styles ── --}}
<style>
.dash-card { border: 0.5px solid #e8e8e8; border-radius: 12px; box-shadow: none; }
.stat-icon  { width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; }
.stat-lbl   { font-size:12px; color:#888; margin-bottom:2px; }
.stat-val   { font-size:22px; font-weight:600; color:#222; line-height:1.2; margin-bottom:2px; }
.stat-delta { font-size:11px; color:#3b6d11; }

.bg-blue-soft   { background:#e6f1fb; } .text-blue   { color:#185fa5; }
.bg-green-soft  { background:#eaf3de; } .text-green  { color:#3b6d11; }
.bg-orange-soft { background:#faeeda; } .text-orange { color:#854f0b; }
.bg-purple-soft { background:#eeedfe; } .text-purple { color:#534ab7; }
.bg-teal-soft   { background:#e1f5ee; } .text-teal   { color:#0f6e56; }
.bg-red-soft    { background:#fcebeb; } .text-red    { color:#a32d2d; }

.cat-row    { display:flex; align-items:center; justify-content:space-between; padding:6px 0; }
.cat-dot    { width:10px; height:10px; border-radius:2px; display:inline-block; }
.cat-count  { font-size:13px; font-weight:500; }

.ride-badge           { display:inline-block; font-size:11px; padding:2px 8px; border-radius:6px; font-weight:500; }
.badge-pending        { background:#faeeda; color:#854f0b; }
.badge-completed      { background:#eaf3de; color:#3b6d11; }
.badge-cancelled      { background:#f1efe8; color:#5f5e5a; }

#liveMap { z-index:0; }
</style>

{{-- ── Leaflet CSS ── --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

{{-- ── Scripts ── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

  /* ── Revenue Chart (ApexCharts) ── */
  var chartRides    = @json($chart_data['rides']);
  var chartRevenue  = @json($chart_data['revenue']);
  var chartBookings = @json($chart_data['bookings']);
  var chartCats     = @json($chart_data['categories']);

  var revenueOptions = {
    series: [
      { name: 'Total Rides',  data: chartRides },
      { name: 'Revenue (R)',  data: chartRevenue },
      { name: 'Bookings',     data: chartBookings }
    ],
    chart:  { type: 'area', height: 280, toolbar: { show: false } },
    colors: ['#4154f1', '#1D9E75', '#ff771d'],
    fill:   { type: 'gradient', gradient: { shadeIntensity:1, opacityFrom:0.25, opacityTo:0.05, stops:[0,90,100] } },
    dataLabels: { enabled: false },
    stroke:     { curve: 'smooth', width: 2 },
    markers:    { size: 3 },
    xaxis: {
      type: 'datetime',
      categories: chartCats,
      labels: { style: { fontSize:'11px', colors:'#aaa' } }
    },
    yaxis:  { labels: { style: { fontSize:'11px', colors:'#aaa' } } },
    grid:   { borderColor:'#f0f0f0', strokeDashArray:4 },
    legend: { show: true, position:'top', fontSize:'12px' },
    tooltip: { x: { format: 'dd MMM yy' } },
    noData: { text: 'Loading...', align: 'center', verticalAlign: 'middle' }
  };

  var revenueChart = new ApexCharts(document.querySelector('#reportsChart'), revenueOptions);
  revenueChart.render();

  /* Filter clicks */
  document.querySelectorAll('.filter-option').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      var filter = this.getAttribute('data-filter');
      var labels = { today: '/Today', month: '/This Month', year: '/This Year' };
      document.getElementById('filter-label').textContent = labels[filter] || '';

      fetch('{{ url("admin/dashboard/chart-data") }}?filter=' + filter)
        .then(function (r) { return r.json(); })
        .then(function (data) {
          revenueChart.updateOptions({
            series: [
              { data: data.rides },
              { data: data.revenue },
              { data: data.bookings }
            ],
            xaxis: { categories: data.categories }
          });
        })
        .catch(console.error);
    });
  });

  /* ── Rides by Category Donut (ApexCharts) ── */
  var econ = parseInt('{{ $bike_rides }}')  || 0;
  var std  = parseInt('{{ $auto_rides }}') || 0;
  var prem = parseInt('{{ $car_rides }}')  || 0;
  var total = econ + std + prem;

  new ApexCharts(document.querySelector('#categoryDonut'), {
    series: total > 0 ? [econ, std, prem] : [1, 0, 0],
    labels: ['Bikes', 'Auto', 'Cars'],
    colors: ['#1D9E75', '#378ADD', '#BA7517'],
    chart:  { type: 'donut', height: 220 },
    plotOptions: { pie: { donut: { size: '68%' } } },
    dataLabels: { enabled: false },
    legend:     { show: false },
    tooltip:    { y: { formatter: function(v) { return v + ' rides'; } } }
  }).render();

  /* ── Live Driver Map (Leaflet) ── */
  var map = L.map('liveMap', { zoomControl: true, attributionControl: false }).setView([20, 0], 5);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 18 }).addTo(map);

  var driverLocations = @json($driver_locations);
  if (driverLocations && driverLocations.length) {
    var driverIcon = L.divIcon({
      className: '',
      html: '<span style="background:#1D9E75;width:14px;height:14px;border-radius:50%;display:block;border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,.3);"></span>',
      iconSize: [14, 14],
      iconAnchor: [7, 7]
    });
    driverLocations.forEach(function (d) {
      if (d.lat && d.lng) {
        L.marker([d.lat, d.lng], { icon: driverIcon })
          .addTo(map)
          .bindPopup('<strong>' + (d.name || 'Driver') + '</strong>');
      }
    });
  }

});
</script>

@endsection