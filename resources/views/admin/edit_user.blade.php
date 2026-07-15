@extends('admin.includes.main')
@section('content')


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit User</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Edit User</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    <form class="row g-3 form_submit" method ="post" data-action="{{ url('/admin/edit_user') }}">
    @csrf
    
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
            
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Basic Info</h5>

              <!-- Multi Columns Form -->
                  <input type="hidden" class="form-control" id="id" name="id" value="{{ $user->id }}">
                  
                  <div class="row">
                      <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                        <span class="error error-name"></span>
                      </div>
                    
                      <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        <span class="error error-email"></span>
                      </div>
                  </div>
                  <br>
                  <div class="row">
                      <div class="col-md-6">
                        <label for="mobile" class="form-label">Phone Number</label>
                        <input type="number" class="form-control" id="mobile" name="mobile" value="{{ $user->mobile_no }}">
                        <span class="error error-mobile"></span>
                      </div>
                      
                      <div class="col-md-6">
                        <label for="dob" class="form-label">DOB</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="{{ $user->dob }}">
                        <span class="error error-dob"></span>
                      </div>
                  </div>
                  <br>
                  <div class="row">
                      <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <span class="error error-password"></span>
                      </div>
                      <div class="col-md-6">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="text" class="form-control" id="confirmPassword" name="confirmPassword">
                        <span class="error error-confirmPassword"></span>
                      </div>
                  </div>
                  <br>
                 <div class="col-md-12">
                    <label for="email" class="form-label">Status</label>
                    <select class="form-control" name="status">
                        <option value="1" {{ $user->status == "1"?"selected":"" }}>Active</option>
                        <option value="0" {{ $user->status == "0"?"selected":"" }}>Inactive</option>
                    </select>
                    <span class="error error-status"></span>
                 </div>
                 <br>
                 <div class="col-md-12">
                    <label for="confirmPassword" class="form-label">Profile</label>
                    <input type="file" class="form-control" id="profile" name="profile">
                    <span class="error error-profile"></span>
                  </div>
                  
                  <div class="col-md-12">
                    <?php if($user->profile != ""){?>
				    <a target="_blank" href="{{ url('/public/assets/profile') }}/{{ $user->profile }}"><img style="height:36px;" src="{{ url('/public/assets/profile') }}/{{ $user->profile }}" /></a>
				    <?php }else{?>
				    <a target="_blank" href="{{ url('/public/assets/img/dummy_image.jpg') }}"><img style="height:36px;" src="{{ url('/public/assets/img/dummy_image.jpg') }}" /></a>
				    <?php }?>
                  </div>
 
 
            </div>
          </div>

        </div>

      </div>
    </section>
    
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                
                <div class="text-center mt-3">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="{{ url('admin/normal_users') }}" class="btn btn-secondary">Back</a>
                </div>
                
            </div>
          </div>    
          
        </div>  
      </div> 
     </section>
     
     </form>

  </main><!-- End #main -->

@endsection