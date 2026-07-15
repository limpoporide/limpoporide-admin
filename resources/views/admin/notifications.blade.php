@extends('admin.includes.main')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Notifications</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Notifications</li>
          <li class="breadcrumb-item active">Notifications</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title">Notifications</h5>
                     <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addReasonModal" id="add_new_notification_btn">
                          <i class="bi bi-plus-circle me-1"></i>Add Notifications
                     </a>
                 </div>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Sr No</th>
                    <th>Title</th>
                    <th>Messages</th>
                    <th>Send To</th>
                    <!--<th>Target ID</th>-->
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                 <?php foreach($data as $key=>$res){?>
                  <tr id="notification_row_{{ $res->id }}">
                    <td>{{ $key+1 }}</td>
                    <td>{{ $res->title }}</td>
                    <td>{{ $res->message }}</td>
                    <td>
                        <?php
                        $send_to_labels = [
                            'all_users' => 'All Users',
                            'all_drivers' => 'All Drivers',
                            'specific_user' => 'Specific User',
                            'specific_driver' => 'Specific Driver',
                        ];
                        echo $send_to_labels[$res->send_to] ?? ucfirst(str_replace('_', ' ', $res->send_to));
                        ?>
                    </td>
                    <!--<td>{{ $res->target_id ?? '-' }}</td>-->
                    <td style="display:flex;">
                        <button class="btn-sm btn btn-danger delete_record" data-url="{{ url('admin/delete_notifications') }}?id={{ $res->id }}" style="margin-left:5px" data-id="{{ $res->id }}" >Delete</button>
                    </td>
                  </tr>
                <?php }?>
                </tbody>
              </table>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

    <div class="modal fade" id="addReasonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Notification</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" class="form_submit" id="notification_form" data-action="{{ url('admin/all_notifications') }}">
              @csrf
              <div class="modal-body">

                 <div class="mb-3">
                    <label for="send_to" class="form-label">Send Notification To</label>
                    <select class="form-control" name="send_to" id="send_to">
                        <option value="">--Select--</option>
                        <option value="all_users">All Users</option>
                        <option value="all_drivers">All Drivers</option>
                        <!--<option value="specific_user">Specific User (by ID)</option>-->
                        <!--<option value="specific_driver">Specific Driver (by ID)</option>-->
                    </select>
                    <span class="error error-send_to"></span>
                  </div>

                  <div class="mb-3" id="target_id_wrapper" style="display:none;">
                    <label for="target_id" class="form-label">Target ID</label>
                    <input type="number" class="form-control" name="target_id" id="target_id" placeholder="Enter User / Driver ID">
                    <span class="error error-target_id"></span>
                  </div>

                  <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" id="title">
                    <span class="error error-title"></span>
                  </div>

                  <div class="mb-3">
                    <label for="message" class="form-label">Messages</label>
                    <textarea class="form-control" name="message" id="message"></textarea>
                    <span class="error error-message"></span>
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    var sendToSelect = document.getElementById('send_to');
    var targetIdWrapper = document.getElementById('target_id_wrapper');
    var targetIdInput = document.getElementById('target_id');

    sendToSelect.addEventListener('change', function () {
        if (this.value == 'specific_user' || this.value == 'specific_driver') {
            targetIdWrapper.style.display = 'block';
        } else {
            targetIdWrapper.style.display = 'none';
            targetIdInput.value = '';
        }
    });

    document.getElementById('add_new_notification_btn').addEventListener('click', function () {
        document.getElementById('notification_form').reset();
        targetIdWrapper.style.display = 'none';
        document.querySelectorAll('.error').forEach(function (el) { el.textContent = ''; });
    });

});
</script>

@endsection