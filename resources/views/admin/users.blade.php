@extends('admin.includes.main')
@section('content')


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>All Users</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">All users</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All Users</h5>
              
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 <?php foreach($users as $res){?>    
                  <tr>
                    <td>
                        <?php if($res->profile != ""){?>
					    <a target="_blank" href="{{ url('/public/assets/profile') }}/{{ $res->profile }}"><img style="height:36px;" src="{{ url('/public/assets/profile') }}/{{ $res->profile }}" /></a>
					    <?php }else{?>
					    <a target="_blank" href="{{ url('/public/assets/img/dummy_image.jpg') }}"><img style="height:36px;" src="{{ url('/public/assets/img/dummy_image.jpg') }}" /></a>
					    <?php }?>
                    </td>
                    <td>{{ $res->name }}</td>
                    <td>{{ $res->email??"----" }}</td>
                    <td>{{ $res->mobile_no }}</td>
                    <td>{{ date('d-m-Y', strtotime($res->created_at)) }}</td>
                    <td>
                       <?php if($res->status == "1"){?>
                        <button type="button" data-id="{{ $res->id }}" data-url="{{ url('admin/user_change_status') }}"  class="btn-sm btn btn-success user_change_status">
                          Active
                        </button>
                        <?php }else{?>
                        <button type="button" data-id="{{ $res->id }}" data-url="{{ url('admin/user_change_status') }}"  class="btn-sm btn btn-danger user_change_status">
                          Inactive
                        </button>
                        <?php }?>
                    </td>
                    <td style="display:flex;">
                        <a href="{{ url('admin/edit_user') }}?id={{ $res->id }}"><button type="button" class="btn-sm btn btn-primary">
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