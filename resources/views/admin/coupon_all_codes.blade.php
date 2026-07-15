@extends('admin.includes.main')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Coupon Codes</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Marketing</li>
          <li class="breadcrumb-item active">Coupon Codes</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title">Coupon Codes</h5>
                  <a class="btn btn-success" data-bs-toggle="modal" data-bs-target="#couponModal" id="add_new_coupon_btn">
                      <i class="bi bi-plus-circle me-1"></i> Add Coupon Code
                  </a>
                 </div>

              <table class="table datatable">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Code</th>
                    <th>Discount Type</th>
                    <th>Discount Value</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="coupon_table_body">
                  <?php $sno = 1; ?>
                  @foreach($coupons as $value)
                  <tr class="even pointer" id="coupon_row_{{ $value->id }}">
                     <td>{{ $sno++ }}</td>
                     <td>{{ $value->code }}</td>
                     <td>{{ ucfirst($value->discount_type) }}</td>
                     <td>{{ $value->discount_type == 'percentage' ? $value->discount_value.' %' : '₹ '.number_format((float)$value->discount_value, 2, '.', '') }}</td>
                     <td>{{ $value->expiry_date }}</td>
                     <td>
                        @if($value->status == 1)
                        <button type="button" data-id="{{ $value->id }}" class="btn-sm btn btn-success change_coupon_status">
                          Active
                        </button>
                        @else
                        <button type="button" data-id="{{ $value->id }}" class="btn-sm btn btn-danger change_coupon_status">
                          Inactive
                        </button>
                        @endif
                     </td>
                     <td>
                      <div style="display:flex;">
                       <a class="btn btn-sm btn-success edit_coupon_code mytooltip ml-2" data-id="{{ $value->id }}" href="javascript:void(0);">Edit</a>
                       <a class="btn btn-sm btn-danger mytooltip ml-2 delete_coupon_code" style="margin-left:10px;" data-id="{{ $value->id }}" href="javascript:void(0);">Delete</a>
                      </div>
                     </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

    <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="couponModalLabel"><span class="change_title">Add</span> Coupon Code</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form id="coupon_form" data-action="{{ url('admin/add_coupon_code') }}" class="form_submit" enctype="multipart/form-data">
            @csrf

          <div class="modal-body">

              <input type="hidden" name="cid" id="cid" value="" />

              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="code" class="form-label">Coupon Code</label>
                    <input type="text" class="form-control" id="code" name="code" placeholder="e.g. SAVE20">
                    <span class="error error-code text-danger"></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="discount_type" class="form-label">Discount Type</label>
                    <select name="discount_type" id="discount_type" class="form-select">
                      <option value="">Select Type</option>
                      <option value="percentage">Percentage</option>
                      <option value="fixed">Fixed</option>
                    </select>
                    <span class="error error-discount_type text-danger"></span>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="discount_value" class="form-label">Discount Value</label>
                    <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" placeholder="Discount Value">
                    <span class="error error-discount_value text-danger"></span>
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
                    <span class="error error-status text-danger"></span>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="expiry_date" class="form-label">Expiry Date</label>
                    <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                    <span class="error error-expiry_date text-danger"></span>
                  </div>
                </div>
              </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success btn-sm" id="submit_coupon_btn">Submit</button>
          </div>

          </form>

        </div>
      </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('add_new_coupon_btn').addEventListener('click', function () {
        document.getElementById('coupon_form').reset();
        document.getElementById('cid').value = '';
        document.querySelector('.change_title').textContent = 'Add';
        document.querySelectorAll('.text-danger').forEach(function (el) { el.textContent = ''; });
    });

    document.addEventListener('click', function (e) {

        var editBtn = e.target.closest('.edit_coupon_code');
        if (editBtn) {
            var id = editBtn.getAttribute('data-id');
            document.querySelectorAll('.text-danger').forEach(function (el) { el.textContent = ''; });

            fetch('{{ url("admin/edit_coupon_code") }}/' + id, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (response) { return response.json(); })
            .then(function (response) {
                if (response.status == 1) {
                    var data = response.data;
                    document.getElementById('cid').value = data.id;
                    document.getElementById('code').value = data.code;
                    document.getElementById('discount_type').value = data.discount_type;
                    document.getElementById('discount_value').value = data.discount_value;
                    document.getElementById('expiry_date').value = data.expiry_date;
                    document.getElementById('status').value = data.status;
                    document.querySelector('.change_title').textContent = 'Edit';

                    var modalEl = document.getElementById('couponModal');
                    var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modalInstance.show();
                } else {
                    alert(response.message);
                }
            })
            .catch(function (error) {
                console.error('Edit fetch failed:', error);
                alert('Could not load coupon details. Check console for details.');
            });

            return;
        }

        var deleteBtn = e.target.closest('.delete_coupon_code');
        if (deleteBtn) {
            var id = deleteBtn.getAttribute('data-id');
            var confirmDelete = confirm('Are you sure you want to delete this coupon code? This action cannot be undone.');

            if (confirmDelete) {
                fetch('{{ url("admin/delete_coupon_code") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'id=' + encodeURIComponent(id) + '&_token=' + encodeURIComponent('{{ csrf_token() }}')
                })
                .then(function (response) { return response.json(); })
                .then(function (response) {
                    if (response.status == 1) {
                        var row = document.getElementById('coupon_row_' + id);
                        if (row) { row.remove(); }
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                })
                .catch(function (error) {
                    console.error('Delete failed:', error);
                    alert('Could not delete coupon code. Check console for details.');
                });
            }

            return;
        }

        var statusBtn = e.target.closest('.change_coupon_status');
        if (statusBtn) {
            var id = statusBtn.getAttribute('data-id');

            fetch('{{ url("admin/change_coupon_status") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'id=' + encodeURIComponent(id) + '&_token=' + encodeURIComponent('{{ csrf_token() }}')
            })
            .then(function (response) { return response.json(); })
            .then(function (response) {
                if (response.status == 1) {
                    if (response.new_status == 1) {
                        statusBtn.classList.remove('btn-danger');
                        statusBtn.classList.add('btn-success');
                        statusBtn.textContent = 'Active';
                    } else {
                        statusBtn.classList.remove('btn-success');
                        statusBtn.classList.add('btn-danger');
                        statusBtn.textContent = 'Inactive';
                    }
                }
            })
            .catch(function (error) {
                console.error('Status change failed:', error);
                alert('Could not update status. Check console for details.');
            });

            return;
        }

    });

});
</script>

@endsection