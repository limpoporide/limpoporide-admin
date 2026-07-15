$(document).on('submit','.form_submit',function(e){
   e.preventDefault();
   
   var action = $(this).data("action");
   var fd = new FormData(this);
   var submitBtn = $(this).find('button[type="submit"]');
   var originalBtnText = submitBtn.html();
   
   // Disable submit button and show loading
   submitBtn.prop('disabled', true);
   submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
   
    $.ajax({
        url: action,
        data: fd,
        cache: false,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (response) {
            console.log(response);
            if(response.status == "false"){
                    $(".error").text(" ");
                    $.each(response.msg, function (key, val) {
                        console.log(key);
                        $(".error-"+key).text(val[0]).css('color', '#eb3d3d');
                });
                
                // Re-enable submit button and restore original text
                submitBtn.prop('disabled', false);
                submitBtn.html(originalBtnText);
            }else{
                Swal.fire({
                    title: "Success !",
                    text: response.msg,
                    confirmButtonColor: '#17a2b8',
                    type: "success"
                }).then(function() {
                    window.location.href = response.return_url;
                });
            }
        },
        error: function() {
            // Re-enable submit button on error
            submitBtn.prop('disabled', false);
            submitBtn.html(originalBtnText);
        }
    });
   
});

$(document).on('click','.edit_user',function(e){
    var profile = $(this).data('profile');
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="first_name"]').val($(this).data('first_name'));
    $('input[name="last_name"]').val($(this).data('last_name'));
    $('input[name="email"]').val($(this).data('email'));
    $('input[name="mobile"]').val($(this).data('mobile'));
    $('select[name="gender"]').val($(this).data('gender'));
    $('select[name="status"]').val($(this).data('status'));
    $(".image_preview").text("");
    var base_url = $(".image_preview").data("base_url");
    if(profile !== ""){
        var img_url = base_url+"/public/profile/"+profile;
        $(".image_preview").append('<img src="'+img_url+'" style="width:50px;height:50px;" />');
    }
});

$(document).on('click','.edit_home_banner',function(e){
    var profile = $(this).data('image');
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="title"]').val($(this).data('title'));
    $('input[name="link"]').val($(this).data('link'));
    $('textarea[name="description"]').html($(this).data('description'));
    $('select[name="status"]').val($(this).data('status'));
    $(".image_preview").text("");
    var base_url = $(".image_preview").data("base_url");
    if(profile !== ""){
        var img_url = base_url+"/public/assets/home_banner/"+profile;
        $(".image_preview").append('<img src="'+img_url+'" style="width:50px;height:50px;" />');
    }
});


$(document).on('click','.edit_scheme_category',function(e){
    var profile = $(this).data('image');
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="title"]').val($(this).data('title'));
    $('textarea[name="description"]').html($(this).data('description'));
    $('select[name="status"]').val($(this).data('status'));
    $(".image_preview").text("");
    var base_url = $(".image_preview").data("base_url");
    if(profile !== ""){
        var img_url = base_url+"/public/assets/categories/"+profile;
        $(".image_preview").append('<img src="'+img_url+'" style="width:50px;height:50px;" />');
    }
});


$(document).on('click','.edit_scheme',function(e){
    var profile = $(this).data('image');
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="title"]').val($(this).data('title'));
    $('textarea[name="description"]').html($(this).data('description'));
    $('input[name="beneficiaries"]').val($(this).data('beneficiaries'));
    $('input[name="eligible"]').val($(this).data('eligible'));
    $('input[name="link"]').val($(this).data('link'));
    $('select[name="is_featured"]').val($(this).data('is_featured'));
    $('select[name="category_id"]').val($(this).data('category_id'));
    $('select[name="status"]').val($(this).data('status'));
    $(".image_preview").text("");
    var base_url = $(".image_preview").data("base_url");
    if(profile !== ""){
        var img_url = base_url+"/public/assets/scheme_banner/"+profile;
        $(".image_preview").append('<img src="'+img_url+'" style="width:50px;height:50px;" />');
    }
});

$(document).on('click','.edit_announcement',function(e){
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="title"]').val($(this).data('title'));
    $('select[name="perority"]').val($(this).data('perority'));
    $('textarea[name="description"]').html($(this).data('description'));
    $('select[name="status"]').val($(this).data('status'));
});

$(document).on('click','.edit_review_rating',function(e){
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="rating"]').val($(this).data('rating'));
    $('textarea[name="review"]').html($(this).data('review'));
    $('select[name="status"]').val($(this).data('status'));
});


$(document).on('click','.edit_scheme_benefit',function(e){
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="benifit"]').val($(this).data('benifit'));
    $('select[name="status"]').val($(this).data('status'));
});



$(document).on('click','.delete_record',function(e){
    e.preventDefault();
    
    var deleteButton = $(this);
    var recordId = deleteButton.data('id'); 
    var deleteUrl = deleteButton.data('url');
    
    // SweetAlert2 confirmation popup
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger mx-2',
            cancelButton: 'btn btn-secondary mx-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the record.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), 
                    _method: 'POST',
                    id: recordId
                },
                success: function(response) {
                    // Check if response status is false
                    // console.log("Status response >>>>>", response.success);
                    // console.log("Full response >>>>>", JSON.stringify(response.success));
                    if (response.success === false) {
                        Swal.fire({
                            title: 'Cannot Delete!',
                            text: response.message || 'Unable to delete this record.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                        return;
                        
                    }else{
                        
                        // Close loading popup and show success message
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message || 'Record has been deleted successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Remove the row from table or reload page
                            deleteButton.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                            });
                            
                            // Alternative: Reload the page
                            // location.reload();
                        });
                        
                    }

                },
                error: function(xhr, status, error) {
                    // Handle error response
                    var errorMessage = 'An error occurred while deleting the record.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = 'Record not found.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'You do not have permission to delete this record.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please try again later.';
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
});


// Datatable Start


$(document).ready(function() {
    // Initialize all tables with class 'datatable-records'
    $('.datatable-records').each(function() {
        var table = $(this);
        var columnCount = table.find('thead tr:first th').length;
        
        // Define columns that should not be sortable (usually image and action columns)
        var nonSortableColumns = [];
        
        // Check each column header to identify non-sortable columns
        table.find('thead tr:first th').each(function(index) {
            var headerText = $(this).text().toLowerCase().trim();
            
            // Add common non-sortable column patterns
            if (headerText.includes('image') || 
                headerText.includes('photo') || 
                headerText.includes('picture') ||
                headerText.includes('actions') || 
                headerText.includes('action') ||
                headerText.includes('operations') ||
                headerText.includes('edit') ||
                headerText.includes('delete')) {
                nonSortableColumns.push(index);
            }
        });
        
        // Initialize DataTable with dynamic configuration
        table.DataTable({
            "columnDefs": nonSortableColumns.length > 0 ? [
                { "orderable": false, "targets": nonSortableColumns }
            ] : [],
            "pageLength": 10,
            "responsive": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "Showing 0 to 0 of 0 entries",
                "infoFiltered": "(filtered from _MAX_ total entries)",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "processing": true,
            "serverSide": false,
            "stateSave": false,
            "order": [], // No default ordering
            "autoWidth": false,
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                   '<"row"<"col-sm-12"tr>>' +
                   '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });
    });
    
    // Alternative: If you want to specify different configurations for different tables
    // You can use data attributes on your tables
    
    // Example usage with data attributes:
    // <table class="datatable-records" data-page-length="25" data-non-sortable="0,3,5">
    
    $('.datatable-records[data-page-length], .datatable-records[data-non-sortable]').each(function() {
        var table = $(this);
        var pageLength = table.data('page-length') || 10;
        var nonSortable = table.data('non-sortable');
        var nonSortableArray = [];
        
        if (nonSortable) {
            nonSortableArray = nonSortable.toString().split(',').map(function(item) {
                return parseInt(item.trim());
            });
        }
        
        // Only initialize if not already initialized
        if (!$.fn.DataTable.isDataTable(table)) {
            table.DataTable({
                "columnDefs": nonSortableArray.length > 0 ? [
                    { "orderable": false, "targets": nonSortableArray }
                ] : [],
                "pageLength": pageLength,
                "responsive": true,
                "order": []
            });
        }
    });
});

// Function to reinitialize DataTables (useful for dynamic content)
function reinitializeDataTables() {
    $('.datatable-records').each(function() {
        if ($.fn.DataTable.isDataTable(this)) {
            $(this).DataTable().destroy();
        }
    });
    
    // Re-run the initialization
    $('.datatable-records').each(function() {
        // Same initialization code as above
        var table = $(this);
        var columnCount = table.find('thead tr:first th').length;
        var nonSortableColumns = [];
        
        table.find('thead tr:first th').each(function(index) {
            var headerText = $(this).text().toLowerCase().trim();
            if (headerText.includes('image') || 
                headerText.includes('actions') || 
                headerText.includes('action')) {
                nonSortableColumns.push(index);
            }
        });
        
        table.DataTable({
            "columnDefs": nonSortableColumns.length > 0 ? [
                { "orderable": false, "targets": nonSortableColumns }
            ] : [],
            "pageLength": 10,
            "responsive": true,
            "order": []
        });
    });
}

// Handle dynamic content loading
$(document).on('contentChanged', function() {
    reinitializeDataTables();
});



$(document).on("click", ".clear-form", function() {
    let form = $(".form_submit");
    form.find("input[type=text], input[type=email], input[type=password], textarea, select").val("");
    form.find("input[type=checkbox], input[type=radio]").prop("checked", false);
    form.find("input[type=file]").val("");
    $(".image_preview").html("");
});


$('#userSelect').on('change', function() {
    var userId = $(this).val();
    var action = $(this).data("action");
    if (userId) {
      $.ajax({
        url: action + '?id=' + userId,
        type: 'GET',
        success: function(response) {
          if (response) {
            $('#name').val(response.name);
            $('#email').val(response.email);
            $('#mobile').val(response.mobile);
          }
        },
        error: function(xhr) {
          console.log(xhr.responseText);
        }
      });
    } else {
      // Clear fields if no user selected
      $('#name, #email, #mobile').val('');
    }
});


$(document).on('click','.user_change_status',function(e){
    e.preventDefault();
    
    var deleteButton = $(this);
    var recordId = deleteButton.data('id'); 
    var deleteUrl = deleteButton.data('url');
    
    // SweetAlert2 confirmation popup
    Swal.fire({
        title: 'Are you sure?',
        text: "You Want to change status !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger mx-2',
            cancelButton: 'btn btn-secondary mx-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Changing...',
                text: 'Please wait while we change the status.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), 
                    _method: 'POST',
                    id: recordId
                },
                success: function(response) {
                    // Check if response status is false
                    // console.log("Status response >>>>>", response.success);
                    // console.log("Full response >>>>>", JSON.stringify(response.success));
                    if (response.success === false) {
                        Swal.fire({
                            title: 'Cannot change!',
                            text: response.message || 'Unable to change this record.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                        return;
                        
                    }else{
                        
                        // Close loading popup and show success message
                        Swal.fire({
                            title: 'Changed!',
                            text: response.message || 'Status has been change successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Remove the row from table or reload page
                            window.location.href="";
                            
                            // Alternative: Reload the page
                            // location.reload();
                        });
                        
                    }

                },
                error: function(xhr, status, error) {
                    // Handle error response
                    var errorMessage = 'An error occurred while change the status.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = 'Record not found.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'You do not have permission to change this status.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please try again later.';
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
});


$(document).on('click','.edit_cancel_reason',function(e){
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="name"]').val($(this).data('name'));
    $('select[name="status"]').val($(this).data('status'));
    $('#addReasonModal').modal('show');

});

$(document).on('click','.edit_faqs',function(e){
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="questions"]').val($(this).data('questions'));
    $('textarea[name="answer"]').html($(this).data('answer'));
    $('select[name="type"]').val($(this).data('type'));
    $('select[name="status"]').val($(this).data('status'));
    $('#addReasonModal').modal('show');

});

$(document).on('click','.edit_vehicle_type',function(e){
    $('input[name="vid"]').val($(this).data('id'));
    $('select[name="vehicle_category_type"]').val($(this).data('category_type'));
    $('input[name="vehicle_type"]').val($(this).data('type'));
    $('input[name="price"]').val($(this).data('price'));
    $('input[name="base_fare"]').val($(this).data('base_fare'));
    $('select[name="status"]').val($(this).data('status'));
    $('#addReasonModal').modal('show');

});

$(document).on('click','.edit_commission',function(e){
    $('input[name="update_id"]').val($(this).data('id'));
    $('input[name="commission"]').val($(this).data('commission'));
    $('#addReasonModal').modal('show');

});

$(document).on('click','.change_status',function(e){
    e.preventDefault();
    
    var deleteButton = $(this);
    var recordId = deleteButton.data('id'); 
    var deleteUrl = deleteButton.data('url');
    
    // SweetAlert2 confirmation popup
    Swal.fire({
        title: 'Are you sure?',
        text: "You Want to change status !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-danger mx-2',
            cancelButton: 'btn btn-secondary mx-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Changing...',
                text: 'Please wait while we change the status.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), 
                    _method: 'POST',
                    id: recordId
                },
                success: function(response) {
                    // Check if response status is false
                    // console.log("Status response >>>>>", response.success);
                    // console.log("Full response >>>>>", JSON.stringify(response.success));
                    if (response.success === false) {
                        Swal.fire({
                            title: 'Cannot change!',
                            text: response.message || 'Unable to change this record.',
                            icon: 'error',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });
                        return;
                        
                    }else{
                        
                        // Close loading popup and show success message
                        Swal.fire({
                            title: 'Changed!',
                            text: response.message || 'Status has been change successfully.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            // Remove the row from table or reload page
                            window.location.href="";
                            
                            // Alternative: Reload the page
                            // location.reload();
                        });
                        
                    }

                },
                error: function(xhr, status, error) {
                    // Handle error response
                    var errorMessage = 'An error occurred while change the status.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 404) {
                        errorMessage = 'Record not found.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'You do not have permission to change this status.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred. Please try again later.';
                    }
                    
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
});

