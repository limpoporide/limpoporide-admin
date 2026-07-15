@extends('admin.includes.main')
@section('content')


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Booking Payments</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Bookings</li>
          <li class="breadcrumb-item active">Booking Payments</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Booking Payments</h5>
              
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                 <tr class="headings">
                    <th class="column-title">S.No</th>
                    <th class="column-title">Booking ID </th>
                    <th class="column-title">Payment Type</th>
                    <th class="column-title">Vehicle Type</th>
                    <th class="column-title">Total fare</th>
                    <th class="column-title">Gst (%)</th>
                    <th class="column-title">Gst Amount</th>
                    <th class="column-title">Driver name</th>
                    <th class="column-title">Driver Amount</th>
                    <th class="column-title">Admin (%) </th>
                    <th class="column-title">Admin Amount</th>
                    <th class="column-title">Date </th>
                  </tr>
                </thead>
                <tbody>
                 <?php foreach($data as $key=>$res){?>    
                  <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $res->booking_id }}</td>
                    <td>{{ $res->payment_mode }}</td>
                    <td>{{ $res->vehicle_type_name }}</td>
                    <td>{{ $res->fare_amount }}</td>
                    <td>{{ $res->gst_percentage }}%</td>
                    <td>{{ $res->gst_amount }}</td>
                    <td>{{ $res->driver_name }}</td>
                    <td>{{ $res->driver_amount }}</td>
                    <td>{{ $res->admin_commision_percentage }}%</td>
                    <td>{{ $res->admin_amount }}</td>
                    <td>{{ date('d-m-Y', strtotime($res->created_at)) }}</td>
                  </tr>
                <?php }?>
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