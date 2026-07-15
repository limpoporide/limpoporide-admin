@extends('admin.includes.main')
@section('content')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Withdraw Requests</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item">Withdraw</li>
          <li class="breadcrumb-item active">Withdraw Requests</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title">Withdraw Requests</h5>
                 </div> 
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr class="headings">
                    <th class="column-title">S.No </th>
                    <th class="column-title">Withdraw Token</th>
                    <th class="column-title">Type</th>
                    <th class="column-title">Name </th>
                    <th class="column-title">Requested Amount </th>
                    <th class="column-title">Bank Name </th>
                    <th class="column-title">Branch Name</th>
                    <th class="column-title">Account Number </th>
                    <th class="column-title">IFCS Code</th>
                    <!--<th class="column-title">Account Status</th>-->
                    <th class="column-title">Request Date</th>
                    <th class="column-title">Status </th>
                  </tr>
                </thead>
                <tbody>
                <?php foreach($data as $key=>$value){ ?>
                      <tr class="even pointer">
                        <td><?php echo $key+1; ?></td>  
                        <td>{{ $value->withdraw_token }}</td> 
                        <td class="">
                            <?php 
                            if($value->user_type == "3"){echo "Area partner";}else if($value->user_type == "4"){echo "Service Provider";}else{echo "Driver";}
                            ?>
                        </td>
                        <td class="">{{ $value->name }}</td>    
                        <td class="">₹ {{ number_format((float)$value->amount, 2, '.', '') }}</td>
                        <td class="">{{ $value->user_bank_name }}</td>
                        <td class="">{{ $value->user_branch_name }}</td>
                        <td class="">{{ $value->user_bank_accaount }}</td>
                        <td class="">{{ $value->user_code}}</td>
                        <!--<td><?php if($value->user_bank_status == "1"){ echo'<span class="badge badge-pill badge-success">Verified</span>'; }else{ echo'<span class="badge badge-pill badge-warning">Pending</span>'; }?></td> -->
                        <td class="">{{ date('d-m-Y h:i:s', strtotime($value->created_at)) }}</td>
                        <td class="">
                            <?php if($value->payment_status == "2"){?>
                            <button type="button" class="btn-sm btn btn-success">
                              Accepted
                            </button>
                            <?php }else if($value->payment_status == "3"){?>
                               <button type="button" class="btn-sm btn btn-danger">
                              Cancelled
                              </button> 
                            <?php }else{?>
                                <select name="status" data-id="{{ $value->id }}" class="change_withdraw_status">
                                <option value="1" {{ $value->status == 1 ? 'selected' : '' }}>Pending</option>
                                <option value="2" {{ $value->status == 2 ? 'selected' : '' }}>Accept</option>
                                <option value="3" {{ $value->status == 3 ? 'selected' : '' }}>Denied</option>
                            </select>
                            <?php }?>
                        </td>
                      </tr>
                  <?php } ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script>
    $(document).ready(function() {
        // Handle withdraw status change
        $('.change_withdraw_status').on('change', function (event) {
            let $select = $(this);
            let status = $select.val();
            let id = $select.data('id');
            let previousValue = $select.data('previous-value') || $select.val();
            
            // Store current value as previous for potential rollback
            $select.data('previous-value', previousValue);
        
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to change the withdrawal status?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Change!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    $select.prop('disabled', true);
                    
                    $.ajax({
                        url: "{{ secure_url('admin/withdrawal_status') }}",
                        type: "POST",
                        dataType: "json",
                        data: {
                            _token: "{{ csrf_token() }}",
                            status: status,
                            id: id
                        },
                        success: function (response) {
                            $select.prop('disabled', false);
                            
                            if (response.status === "true" || response.status === true) {
                                // Update the previous value to current
                                $select.data('previous-value', status);
                                
                                Swal.fire({
                                    title: "Success!",
                                    text: response.msg ?? "Status changed successfully",
                                    icon: "success",
                                    confirmButtonColor: '#17a2b8'
                                }).then(function () {
                                    if (response.return_url) {
                                        window.location.href = response.return_url;
                                    } else {
                                        location.reload();
                                    }
                                });
                            } else {
                                // Rollback to previous value
                                $select.val(previousValue);
                                
                                Swal.fire({
                                    title: "Error!",
                                    text: response.msg ?? "Something went wrong",
                                    icon: "error",
                                    confirmButtonColor: '#d33'
                                });
                            }
                        },
                        error: function (xhr) {
                            // Rollback to previous value
                            $select.val(previousValue);
                            $select.prop('disabled', false);
                            
                            Swal.fire({
                                title: "Request Failed!",
                                text: xhr.responseJSON?.message ?? "Unable to process request",
                                icon: "error",
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                } else {
                    // User cancelled, rollback to previous value
                    $select.val(previousValue);
                }
            });
        });
        
        // Initialize previous values for all selects
        $('.change_withdraw_status').each(function() {
            $(this).data('previous-value', $(this).val());
        });
    });
  </script>

@endsection