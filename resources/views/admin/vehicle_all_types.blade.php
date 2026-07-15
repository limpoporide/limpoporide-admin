@extends('admin.includes.main')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Vehicle Categories</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Vehicles</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title">Vehicle Categories</h5>
                  <a class="btn btn-success"  data-bs-toggle="modal" data-bs-target="#addReasonModal">
                      <i class="bi bi-plus-circle me-1"></i> Add Vehicle category
                  </a>
                 </div> 
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Image</th>
                    <th>Type</th>
                    <th>Vehicle Category</th>
                    <th>Price</th>
                    <th>Base Fare</th>
                    <th>Status</th>
                    <th>Action</th
                  </tr>
                </thead>
                <tbody>
                  <?php $sno=1; ?>
                  @foreach($vehicle as $value)
                  <tr class="even pointer">
                     <td>{{ $sno++ }}</td>
                    <td class="">
                        <?php if($value->image != "") {  ?>
                         <img src="<?php echo URL::to('public/vehicle_image/vehicle_type_image/'.$value->image); ?>" alt="avatar"  style="width:51px;">
                        <?php }  ?>
                    </td>
                    <td>{{ ucfirst($value->type) }}</td>
                    <td>{{ $value->name }}</td>
                   <td>₹ {{ number_format((float)$value->price, 2, '.', '') }}</td>
                   <td>₹ {{ number_format((float)$value->base_fare, 2, '.', '') }}</td>

                   <td>
                        <?php if($value->status == "1"){?>
                        <button type="button" data-id="{{ $value->id }}" data-url="{{ url('admin/change_vehicle_type_status') }}" class="btn-sm btn btn-success change_status">
                          Active
                        </button>
                        <?php }else{?>
                        <button type="button" data-id="{{ $value->id }}" data-url="{{ url('admin/change_vehicle_type_status') }}" class="btn-sm btn btn-danger change_status">
                          Inactive
                        </button>
                        <?php }?>
                    </td>

                   <td>
                  <div style="display:flex;">
                   <a class="btn btn-sm btn-success edit_vehicle_type mytooltip ml-2" data-id="{{$value->id}}"
                            data-type="{{$value->name}}" data-category_type="{{$value->type}}" data-price="{{$value->price}}" data-status="{{$value->status}}"
                            data-base_fare="{{$value->base_fare}}"
                            href="javascript:void(0);" >Edit </a>
 
                   <a class="btn btn-sm btn-danger mytooltip ml-2 delete_record" style="margin-left:10px;" data-url="{{ url('admin/delete_type_vehicle') }}?id={{ $value->id }}" data-id="{{ $value->id }}" >Delete</a>
                   </div>
                   </td>
                   
                  </tr>
                  @endforeach
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

    <!-- Modal -->
    <div class="modal fade" id="addReasonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel"><span class="change_title">Add</span> Vehicle Category</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          
          <form data-action="{{ url('admin/add_type_vehicle') }}" class="form_submit" enctype="multipart/form-data">
            @csrf
          
          <div class="modal-body">
            
              <input type="hidden" name="vid" id="vid" value=""/>
              
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="vehicle_category_type" class="form-label">Vehicle Type</label>
                    <select name="vehicle_category_type" id="vehicle_category_type" class="form-select">
                      <option value="">Select Type</option>
                      <option value="bike">Bike</option>
                      <option value="auto">Auto</option>
                      <option value="car">Car</option>
                    </select>
                    <span class="error-vehicle_category_type text-danger"></span>
                  </div>
                </div>
                 <div class="col-md-6">
                  <div class="mb-3">
                    <label for="vehicle_type" class="form-label">Vehicle Category</label>
                    <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" placeholder="Vehicle Category">
                    <span class="error-vehicle_type text-danger"></span>
                  </div>
                </div>
              </div>
              

              <div class="row mb-3">

                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="base_fare" class="form-label">Base fare</label>
                    <input type="number" class="form-control" id="base_fare" name="base_fare" placeholder="Base fare">
                    <span class="error-base_fare text-danger"></span>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                      <option value="">Select</option>
                      <option value="1">Active</option>
                      <option value="2">Inactive</option>
                    </select>
                    <span class="error-status text-danger"></span>
                  </div>
                </div>
                
              </div>

              <div class="row mb-3">
                
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="price" class="form-label">Price (Km)</label>
                    <input type="number" class="form-control" id="price" name="price" placeholder="Vehicle Price">
                    <span class="error-price text-danger"></span>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="vehicle_image" class="form-label">Vehicle Image</label>
                    <input type="file" class="form-control" id="vehicle_image" name="vehicle_image">
                    <span class="error-vehicle_image text-danger"></span>
                  </div>
                </div>

              </div>
              

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success btn-sm add_vehicle">Submit</button>
          </div>
          
          </form>
          
        </div>
      </div>
    </div>

@endsection