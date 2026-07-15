@extends('admin.includes.main')
@section('content')


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>All Subadmins</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Subadmins</li>
          <li class="breadcrumb-item active">Manage Subadmins</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

         <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">All Subadmins</h5>
                <a class="btn btn-success" href="{{ url('/admin/add_subdmins') }}">
                  <i class="bi bi-plus-circle me-1"></i> Add Subadmin
                </a>
              </div>
              
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Mobile</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 <?php foreach($users as $res){?>    
                  <tr>
                    <td>
                        <?php if($res->profile != ""){?>
					    <a target="_blank" href="{{ url('/public/assets/admin_profile') }}/{{ $res->profile }}"><img style="height:36px;" src="{{ url('/public/assets/admin_profile') }}/{{ $res->profile }}" /></a>
					    <?php }else{?>
					    <a target="_blank" href="{{ url('/public/assets/img/default_img.jpg') }}"><img style="height:36px;" src="{{ url('/public/assets/img/default_img.jpg') }}" /></a>
					    <?php }?>
                    </td>
                    <td>{{ $res->name }}</td>
                    <td>{{ $res->email??"----" }}</td>
                    <td>{{ $res->confirm_password??"----" }}</td>
                    <td>{{ $res->mobile }}</td>
                    <td>{{ date('d-m-Y', strtotime($res->created_at)) }}</td>
                    <td>
                        <a href="{{ url('admin/edit_subdmins') }}/{{ $res->id }}">
                         <button type="button" class="btn-sm btn btn-primary edit_user">
                          Edit
                         </button>
                        </a>
                        <button class="btn-sm btn btn-danger delete_record" data-url="{{ url('admin/delete_subdmins') }}" data-id="{{ $res->id }}" >Delete</button>
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