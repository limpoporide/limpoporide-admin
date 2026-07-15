@extends('admin.includes.main')
@section('content')


  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Corporate User</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Add Corporate User</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    <form class="row g-3 form_submit" method ="post" data-action="{{ url('/admin/add_corporate_user') }}">
    @csrf
    
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
            
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Basic Info</h5>

              <!-- Multi Columns Form -->
                  
                  <div class="row">
                      <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" >
                        <span class="error error-name"></span>
                      </div>
                    
                      <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" >
                        <span class="error error-email"></span>
                      </div>
                  </div>
                  <br>
                  <div class="row">
                      <div class="col-md-6">
                        <label for="mobile" class="form-label">Phone Number</label>
                        <input type="number" class="form-control" id="mobile" name="mobile" >
                        <span class="error error-mobile"></span>
                      </div>
                      
                      <div class="col-md-6">
                        <label for="dob" class="form-label">DOB</label>
                        <input type="date" class="form-control" id="dob" name="dob" >
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
                        <option value="1" >Active</option>
                        <option value="0" >Inactive</option>
                    </select>
                    <span class="error error-status"></span>
                 </div>
                 <br>
                 <div class="col-md-12">
                    <label for="confirmPassword" class="form-label">Profile</label>
                    <input type="file" class="form-control" id="profile" name="profile">
                    <span class="error error-profile"></span>
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
                  <a href="{{ url('admin/corporate_users') }}" class="btn btn-secondary">Back</a>
                </div>
                
            </div>
          </div>    
          
        </div>  
      </div> 
     </section>
     
     </form>

  </main><!-- End #main -->

@endsection