@extends('admin.includes.main')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Manage Commission</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Commission</li>
          <li class="breadcrumb-item active">Manage Commission</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title">Manage Commission</h5>
                 </div> 
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Sr No</th>
                    <th>Type</th>
                    <th>Commission Percentage</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 <?php foreach($data as $key=>$res){?>    
                  <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $res->user_name }}</td>
                    <td>{{ $res->percent }} %</td>
                    <td style="display:flex;">
                        <a href="#"><button type="button" data-id="{{ $res->id }}" data-commission="{{ $res->percent }}" class="btn-sm btn btn-primary edit_commission">
                          Edit
                        </button></a>
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

    <!-- Modal -->
    <div class="modal fade" id="addReasonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel" class="reason_text">Edit Commission</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" class="form_submit" data-action="{{ url('admin/manage_comission') }}">
              @csrf
              <div class="modal-body">
                  
                  <input type="hidden" name="update_id">
                  
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Commission Percentage</label>
                    <input type="text" class="form-control" name="commission">
                    <span class="error error-commission"></span>
                  </div>
                  

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
              </div>
          </form>
        </div>
      </div>
    </div>


@endsection