@extends('admin.includes.main')
@section('content')


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Awating Approval Drivers</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Drivers</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title">Awating Approval Drivers</h5>
                 </div> 
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>vehicle Type</th>
                    <th>Doc (%)</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 <?php foreach($data as $res){?>    
                  <tr>
                    <td>
                        <?php if($res->profile != ""){?>
					    <a target="_blank" href="{{ url('/public/profile_image') }}/{{ $res->profile }}"><img style="height:36px;" src="{{ url('/public/profile_image') }}/{{ $res->profile }}" /></a>
					    <?php }else{?>
					    <a target="_blank" href="{{ url('/public/assets/img/default_img.jpg') }}"><img style="height:36px;" src="{{ url('/public/assets/img/default_img.jpg') }}" /></a>
					    <?php }?>
                    </td>
                    <td>{{ $res->name }}</td>
                    <td>{{ $res->email??"----" }}</td>
                    <td>{{ $res->mobile_no }}</td>
                    <td>{{ $res->vehicle_type_name }}</td>
                    <td>{{ check_documents_complete_percentage($res->detail_id) }} %</td>
                    <td>{{ date('d-m-Y', strtotime($res->created_at)) }}</td>
                    <td style="display:flex;">
                        
                        <a href="{{ url('admin/driver_details') }}/{{ $res->id }}"><button type="button" class="btn-sm btn btn-success">
                          View
                        </button></a>

                        <a href="{{ url('admin/edit_driver_details') }}/{{ $res->id }}"><button type="button" class="btn-sm btn btn-primary" style="margin-left:5px">
                          Edit
                        </button></a>

                        <button class="btn-sm btn btn-danger delete_record" data-url="{{ url('admin/delete_user') }}?id={{ $res->id }}" style="margin-left:5px" data-id="{{ $res->id }}" >Delete</button>

                    </td>
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