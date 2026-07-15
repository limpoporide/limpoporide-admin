@extends('admin.includes.main')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Faqs</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Users</li>
          <li class="breadcrumb-item active">Faqs</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title">Faqs</h5>
                     <a class="btn btn-success"  data-bs-toggle="modal" data-bs-target="#addReasonModal">
                          <i class="bi bi-plus-circle me-1"></i> Add Faqs
                     </a>
                 </div> 
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Sr No</th>
                    <th>Type</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 <?php foreach($data as $key=>$res){?>    
                  <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ ($res->type == "1")?"User":"Driver" }}</td>
                    <td>{{ $res->questions }}</td>
                    <td>{{ $res->answers }}</td>
                    <td>
                        <?php if($res->status == "1"){?>
                        <button type="button" data-id="{{ $res->id }}" data-url="{{ url('admin/faqs_change_status') }}" class="btn-sm btn btn-success user_change_status">
                          Active
                        </button>
                        <?php }else{?>
                        <button type="button" data-id="{{ $res->id }}" data-url="{{ url('admin/faqs_change_status') }}" class="btn-sm btn btn-danger user_change_status">
                          Inactive
                        </button>
                        <?php }?>
                    </td>
                    <td style="display:flex;">
                        <a href="#"><button type="button" data-id="{{ $res->id }}" data-type="{{ $res->type }}" data-status="{{ $res->status }}" data-questions="{{ $res->questions }}" data-answer="{{ $res->answers }}" class="btn-sm btn btn-primary edit_faqs">
                          Edit
                        </button></a>
                        <button class="btn-sm btn btn-danger delete_record" data-url="{{ url('admin/delete_faqs') }}?id={{ $res->id }}" style="margin-left:5px" data-id="{{ $res->id }}" >Delete</button>
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
            <h1 class="modal-title fs-5" id="exampleModalLabel" class="reason_text">Add/ Edit Faqs</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" class="form_submit" data-action="{{ url('admin/faqs') }}">
              @csrf
              <div class="modal-body">
                  
                  <input type="hidden" name="update_id">
                  <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Type</label>
                    <select class="form-select" name="type">
                        <option value="1">User</option>
                        <option value="2">Driver</option>
                    </select>
                    <span class="error error-type"></span>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <span class="error error-status"></span>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Question</label>
                    <input type="text" class="form-control" name="questions">
                    <span class="error error-questions"></span>
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Answer</label>
                    <textarea class="form-control" name="answer"></textarea>
                    <span class="error error-answer"></span>
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