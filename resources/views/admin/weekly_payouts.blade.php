@extends('admin.includes.main')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Weekly Payout</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Weekly Payouts</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">

              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Weekly Payouts</h5>
              </div>

              <!-- Date filter -->
              <form method="GET" action="{{ url('admin/weekly_payouts') }}" class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                  <label for="from_date" class="form-label">From Date</label>
                  <input type="date" class="form-control" id="from_date" name="from_date" value="{{ $from_date ?? '' }}">
                </div>
                <div class="col-md-3">
                  <label for="to_date" class="form-label">To Date</label>
                  <input type="date" class="form-control" id="to_date" name="to_date" value="{{ $to_date ?? '' }}">
                </div>
                <div class="col-md-3">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Filter
                  </button>
                  <a href="{{ url('admin/weekly_payouts') }}" class="btn btn-secondary">
                    Reset
                  </a>
                </div>
              </form>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Payout Token</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Payment Status</th>
                    <th>Created At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $sno = 1; ?>
                  @forelse($data as $value)
                  <tr class="even pointer">
                    <td>{{ $sno++ }}</td>
                    <td>{{ $value->payout_token }}</td>
                    <td>
                      {{ $value->user_name ?? 'N/A' }}
                      @if(!empty($value->user_email))
                        <br><small class="text-muted">{{ $value->user_email }}</small>
                      @endif
                    </td>
                    <td>₹ {{ number_format((float)$value->amount, 2, '.', '') }}</td>
                    <td>
                      @if($value->payment_status == 1)
                        <span class="badge bg-warning text-dark">Pending</span>
                      @elseif($value->payment_status == 2)
                        <span class="badge bg-success">Accepted</span>
                      @else
                        <span class="badge bg-secondary">Unknown</span>
                      @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($value->created_at)->format('d-m-Y h:i A') }}</td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center">No payout records found.</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

@endsection