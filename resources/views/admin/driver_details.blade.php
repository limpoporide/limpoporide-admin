@extends('admin.includes.main')
@section('content')
<main id="main" class="main">
    <section class="section">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">View Driver Details</h3>
                        <div class="d-flex gap-2">
                            <a href="<?php echo url('admin/edit_driver_details')."/".$driver_id; ?>" class="btn btn-success btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="row g-4">
                <!-- Driver Details Card -->
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white text-center">
                            <h5 class="mb-0">Driver Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Full Name:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">{{$driver_data->name}}</p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Email:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">{{$driver_data->email}}</p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Mobile:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">{{$driver_data->mobile_no}}</p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Gender:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">
                                        <?php if($driver_data->gender == "1"){?>
                                            Male
                                        <?php }elseif($driver_data->gender == "2"){?>
                                            Female
                                        <?php }else{?>
                                            Other
                                        <?php }?>
                                    </p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">DOB:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">{{$driver_data->dob}}</p>
                                </div>
                            </div>

                            <!--<div class="row mb-3 pb-3 border-bottom">-->
                            <!--    <div class="col-5">-->
                            <!--        <p class="mb-0 fw-semibold">Age:</p>-->
                            <!--    </div>-->
                            <!--    <div class="col-7">-->
                            <!--        <p class="text-muted mb-0">{{$driver_data->age}} year</p>-->
                            <!--    </div>-->
                            <!--</div>-->

                            <!--<div class="row mb-3 pb-3 border-bottom">-->
                            <!--    <div class="col-5">-->
                            <!--        <p class="mb-0 fw-semibold">State:</p>-->
                            <!--    </div>-->
                            <!--    <div class="col-7">-->
                            <!--        <p class="text-muted mb-0">{{$driver_data->state_name}}</p>-->
                            <!--    </div>-->
                            <!--</div>-->

                            <!--<div class="row mb-3 pb-3 border-bottom">-->
                            <!--    <div class="col-5">-->
                            <!--        <p class="mb-0 fw-semibold">City:</p>-->
                            <!--    </div>-->
                            <!--    <div class="col-7">-->
                            <!--        <p class="text-muted mb-0">{{$driver_data->city_name}}</p>-->
                            <!--    </div>-->
                            <!--</div>-->

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Wallet:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">₹ {{$wallet_amount}}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Total Booking:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">₹ {{@number_format((float)$totalbooking_amount, 2, '.', '')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Details Card -->
                <div class="col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white text-center">
                            <h5 class="mb-0">Vehicle Details</h5>
                        </div>
                        <div class="card-body">
                            <!--<div class="row mb-3 pb-3 border-bottom">-->
                            <!--    <div class="col-5">-->
                            <!--        <p class="mb-0 fw-semibold">Licence No:</p>-->
                            <!--    </div>-->
                            <!--    <div class="col-7">-->
                            <!--        <p class="text-muted mb-0">{{@$driver_data->licence_number}}</p>-->
                            <!--    </div>-->
                            <!--</div>-->

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Vehicle Number:</p>
                                </div>
                                <div class="col-7">
                                    @if($vehicle_details->vehicle_number)
                                    <p class="text-muted mb-0">{{$vehicle_details->vehicle_number}}</p>
                                    @else
                                    <p class="text-muted mb-0">-</p>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Vehicle Model:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">{{$vehicle_details->vehicle_model_name}}</p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Vehicle Type:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">{{$vehicle_details->vehicle_type_name}}</p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Reg Year:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">{{$vehicle_details->vehicle_reg_year}}</p>
                                </div>
                            </div>

                            <!--<div class="row mb-3 pb-3 border-bottom">-->
                            <!--    <div class="col-5">-->
                            <!--        <p class="mb-0 fw-semibold">Fuel Type:</p>-->
                            <!--    </div>-->
                            <!--    <div class="col-7">-->
                            <!--        <p class="text-muted mb-0">{{$vehicle_details->varient_name}}</p>-->
                            <!--    </div>-->
                            <!--</div>-->

                            <div class="row">
                                <div class="col-5">
                                    <p class="mb-0 fw-semibold">Owner Name:</p>
                                </div>
                                <div class="col-7">
                                    <p class="text-muted mb-0">{{$vehicle_details->vehicle_owner_name}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile & Bank Details -->
                <div class="col-lg-4">
                    <!-- Profile Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white text-center">
                            <h5 class="mb-0">Profile</h5>
                        </div>
                        <div class="card-body text-center">
                            @if (!empty($driver_data->profile))
                            <a href="{{asset('public/profile_image/'.$driver_data->profile)}}" target="_blank">
                                <img src="{{asset('public/profile_image/'.$driver_data->profile)}}" 
                                     class="rounded-circle img-fluid shadow" 
                                     style="width: 200px; height: 200px; object-fit: cover;" 
                                     alt="Driver Profile">
                            </a>
                            @else
                            <img src="{{asset('public/profile_image/download.png')}}" 
                                 alt="Default Avatar" 
                                 class="rounded-circle img-fluid shadow" 
                                 style="width: 200px; height: 200px; object-fit: cover;">
                            @endif
                            <p class="mt-3 mb-0 text-muted">Profile Photo</p>
                        </div>
                    </div>

                    <!-- Bank Details Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white text-center">
                            <h5 class="mb-0">Bank Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-4">
                                    <p class="mb-0 fw-semibold">Holder:</p>
                                </div>
                                <div class="col-8">
                                    <p class="text-muted mb-0">{{$driver_data->bank_ac_holder_name}}</p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-4">
                                    <p class="mb-0 fw-semibold">Bank:</p>
                                </div>
                                <div class="col-8">
                                    <p class="text-muted mb-0">{{$driver_data->bank_name}}</p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-4">
                                    <p class="mb-0 fw-semibold">Account:</p>
                                </div>
                                <div class="col-8">
                                    <p class="text-muted mb-0">{{$driver_data->bank_account}}</p>
                                </div>
                            </div>

                            <div class="row mb-3 pb-3 border-bottom">
                                <div class="col-4">
                                    <p class="mb-0 fw-semibold">Branch:</p>
                                </div>
                                <div class="col-8">
                                    <p class="text-muted mb-0">{{$driver_data->branch}}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4">
                                    <p class="mb-0 fw-semibold">IFSC:</p>
                                </div>
                                <div class="col-8">
                                    <p class="text-muted mb-0">{{$driver_data->code}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Table -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Document Verification</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="text-center">S.No</th>
                                            <th scope="col">Document Name</th>
                                            <th scope="col" class="text-center">Document</th>
                                            <th scope="col" class="text-center">View</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col" class="text-center">Doc Verification</th>
                                            <th scope="col" class="text-center">Document Re Upload</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <!-- Driving Licence -->
                                        <tr>
                                            <th scope="row" class="text-center">1</th>
                                            <td>Driving Licence</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->driving_licence))
                                                {
                                                    $filename_driving = url('public/vehicle_image/driving_licence/'.$vehicle_details->driving_licence);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/driving_licence/'.$vehicle_details->driving_licence); ?>" 
                                                         alt="RC Front" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/driving_licence/'.$vehicle_details->driving_licence); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->driving_licence))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/driving_licence/'.$vehicle_details->driving_licence); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if($vehicle_details->doc_status1 == 1){ ?>
                                                    <span class="badge bg-success fs-6 px-3 py-2">Approved</span>
                                                <?php }else{ ?>
                                                    <span class="badge bg-danger fs-6 px-3 py-2">Not Approved</span>
                                                <?php } ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if(!empty($vehicle_details->driving_licence) && !empty($vehicle_details->driving_licence)){
                                                    if($vehicle_details->doc_status1 == 1){ ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus({{$vehicle_details->id}}, {{$vehicle_details->doc_status1}})">
                                                            Change Status
                                                        </button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus({{$vehicle_details->id}}, {{$vehicle_details->doc_status1}})">
                                                            Change Status
                                                        </button>
                                                    <?php }}else{ ?>
                                                        <button class="btn btn-danger btn-sm" disabled>Document Not Upload</button>
                                                    <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->driving_licence)){
                                                    if($vehicle_details->doc1_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc1_reupload}}, 'doc1_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc1_reupload}}, 'doc1_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <!-- Driving Licence Back -->
                                        <tr>
                                            <th scope="row" class="text-center">2</th>
                                            <td>Driving Licence Back</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->driving_licence2))
                                                {
                                                    $filename_driving = url('public/vehicle_image/driving_licence/'.$vehicle_details->driving_licence2);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/driving_licence/'.$vehicle_details->driving_licence2); ?>" 
                                                         alt="RC Back" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/driving_licence/'.$vehicle_details->driving_licence2); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->driving_licence2))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/driving_licence/'.$vehicle_details->driving_licence2); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->driving_licence2)){
                                                    if($vehicle_details->doc2_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc2_reupload}}, 'doc2_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc2_reupload}}, 'doc2_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        
                                        
                                        <!-- Id proof -->
                                        <tr>
                                            <th scope="row" class="text-center">3</th>
                                            <td>Vehicle Registration Front Image</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->id_proof))
                                                {
                                                    $filename_driving = url('public/vehicle_image/id_proof/'.$vehicle_details->id_proof);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/id_proof/'.$vehicle_details->id_proof); ?>" 
                                                         alt="RC Front" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/id_proof/'.$vehicle_details->id_proof); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->id_proof))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/id_proof/'.$vehicle_details->id_proof); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if($vehicle_details->doc_status2 == 1){ ?>
                                                    <span class="badge bg-success fs-6 px-3 py-2">Approved</span>
                                                <?php }else{ ?>
                                                    <span class="badge bg-danger fs-6 px-3 py-2">Not Approved</span>
                                                <?php } ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if(!empty($vehicle_details->id_proof) && !empty($vehicle_details->id_proof)){
                                                    if($vehicle_details->doc_status3 == 1){ ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus2({{$vehicle_details->id}}, {{$vehicle_details->doc_status2}})">
                                                            Change Status
                                                        </button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus2({{$vehicle_details->id}}, {{$vehicle_details->doc_status2}})">
                                                            Change Status
                                                        </button>
                                                    <?php }}else{ ?>
                                                        <button class="btn btn-danger btn-sm" disabled>Document Not Upload</button>
                                                    <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->id_proof)){
                                                    if($vehicle_details->doc3_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc3_reupload}}, 'doc3_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc3_reupload}}, 'doc3_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <!-- ID Proof Back -->
                                        <tr>
                                            <th scope="row" class="text-center">4</th>
                                            <td>Vehicle Registration Back Image</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->id_proof2))
                                                {
                                                    $filename_driving = url('public/vehicle_image/id_proof/'.$vehicle_details->id_proof2);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/id_proof/'.$vehicle_details->id_proof2); ?>" 
                                                         alt="RC Back" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/id_proof/'.$vehicle_details->id_proof2); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->id_proof2))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/id_proof/'.$vehicle_details->id_proof2); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->id_proof2)){
                                                    if($vehicle_details->doc4_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc4_reupload}}, 'doc4_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc4_reupload}}, 'doc4_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>
                                        
                                        <!-- RC Front -->
                                        <tr>
                                            <th scope="row" class="text-center">5</th>
                                            <td>Insurance Paper</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->photo1))
                                                {
                                                    $filename_driving = url('public/vehicle_image/vehicle_photo/'.$vehicle_details->photo1);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->photo1); ?>" 
                                                         alt="RC Front" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->photo1); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->photo1))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->photo1); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if($vehicle_details->doc_status3 == 1){ ?>
                                                    <span class="badge bg-success fs-6 px-3 py-2">Approved</span>
                                                <?php }else{ ?>
                                                    <span class="badge bg-danger fs-6 px-3 py-2">Not Approved</span>
                                                <?php } ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if(!empty($vehicle_details->photo1) && !empty($vehicle_details->photo2)){
                                                    if($vehicle_details->doc_status3 == 1){ ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus3({{$vehicle_details->id}}, {{$vehicle_details->doc_status3}})">
                                                            Change Status
                                                        </button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus3({{$vehicle_details->id}}, {{$vehicle_details->doc_status3}})">
                                                            Change Status
                                                        </button>
                                                    <?php }}else{ ?>
                                                        <button class="btn btn-danger btn-sm" disabled>Document Not Upload</button>
                                                    <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->photo1)){
                                                    if($vehicle_details->doc5_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc5_reupload}}, 'doc5_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc5_reupload}}, 'doc5_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <!-- RC Back -->
                                        <tr>
                                            <th scope="row" class="text-center">6</th>
                                            <td>Vehicle Inspection Report</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->photo2))
                                                {
                                                    $filename_driving = url('public/vehicle_image/vehicle_photo/'.$vehicle_details->photo2);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->photo2); ?>" 
                                                         alt="RC Back" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->photo2); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->photo2))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->photo2); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->photo2)){
                                                    if($vehicle_details->doc6_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc6_reupload}}, 'doc6_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc6_reupload}}, 'doc6_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <!-- Pollution Certificate Front -->
                                        <tr>
                                            <th scope="row" class="text-center">7</th>
                                            <td>Vehicle Photos Front</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->certificate1))
                                                {
                                                    $filename_driving = url('public/vehicle_image/vehicle_photo/'.$vehicle_details->certificate1);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->certificate1); ?>" 
                                                         alt="Pollution Certificate" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->certificate1); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->certificate1))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->certificate1); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if($vehicle_details->doc_status4 == 1){ ?>
                                                    <span class="badge bg-success fs-6 px-3 py-2">Approved</span>
                                                <?php }else{ ?>
                                                    <span class="badge bg-danger fs-6 px-3 py-2">Not Approved</span>
                                                <?php } ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if(!empty($vehicle_details->certificate1) && !empty($vehicle_details->certificate2)){
                                                    if($vehicle_details->doc_status4 == 1){ ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus4({{$vehicle_details->id}}, {{$vehicle_details->doc_status4}})">
                                                            Change Status
                                                        </button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus4({{$vehicle_details->id}}, {{$vehicle_details->doc_status4}})">
                                                            Change Status
                                                        </button>
                                                    <?php }}else{ ?>
                                                        <button class="btn btn-danger btn-sm" disabled>Document Not Upload</button>
                                                    <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->certificate1)){
                                                    if($vehicle_details->doc7_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc7_reupload}}, 'doc7_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc7_reupload}}, 'doc7_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <!-- Pollution Certificate Back -->
                                        <tr>
                                            <th scope="row" class="text-center">8</th>
                                            <td>Vehicle Photos Back</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->certificate2))
                                                {
                                                    $filename_driving = url('public/vehicle_image/vehicle_photo/'.$vehicle_details->certificate2);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->certificate2); ?>" 
                                                         alt="Pollution Certificate Back" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->certificate2); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->certificate2))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->certificate2); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->certificate2)){
                                                    if($vehicle_details->doc8_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc8_reupload}}, 'doc8_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc8_reupload}}, 'doc8_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <!-- Vehicle Audit Front -->
                                        <tr>
                                            <th scope="row" class="text-center">9</th>
                                            <td>Road Test Document</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->vehicle_audit_photo1))
                                                {
                                                    $filename_driving = url('public/vehicle_image/vehicle_photo/'.$vehicle_details->vehicle_audit_photo1);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->vehicle_audit_photo1); ?>" 
                                                         alt="Vehicle Audit" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->vehicle_audit_photo1); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->vehicle_audit_photo1))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->vehicle_audit_photo1); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if($vehicle_details->doc_status5 == 1){ ?>
                                                    <span class="badge bg-success fs-6 px-3 py-2">Approved</span>
                                                <?php }else{ ?>
                                                    <span class="badge bg-danger fs-6 px-3 py-2">Not Approved</span>
                                                <?php } ?>
                                            </td>
                                            <td rowspan="2" class="text-center align-middle">
                                                <?php
                                                if(!empty($vehicle_details->vehicle_audit_photo1) && !empty($vehicle_details->vehicle_audit_photo2)){
                                                    if($vehicle_details->doc_status5 == 1){ ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus5({{$vehicle_details->id}}, {{$vehicle_details->doc_status5}})">
                                                            Change Status
                                                        </button>
                                                    <?php } else { ?>
                                                        <button class="btn btn-info btn-sm" onclick="changeStatus5({{$vehicle_details->id}}, {{$vehicle_details->doc_status5}})">
                                                            Change Status
                                                        </button>
                                                    <?php }}else{ ?>
                                                        <button class="btn btn-danger btn-sm" disabled>Document Not Upload</button>
                                                    <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->vehicle_audit_photo1)){
                                                    if($vehicle_details->doc9_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc9_reupload}}, 'doc9_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc9_reupload}}, 'doc9_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>

                                        <!-- Vehicle Audit Back -->
                                        <tr>
                                            <th scope="row" class="text-center">10</th>
                                            <td>Driver Photo</td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->vehicle_audit_photo2))
                                                {
                                                    $filename_driving = url('public/vehicle_image/vehicle_photo/'.$vehicle_details->vehicle_audit_photo2);
                                                    $file_ext = pathinfo($filename_driving, PATHINFO_EXTENSION);
                                                    if($file_ext!="pdf")
                                                    {
                                                    ?>
                                                    <img src="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->vehicle_audit_photo2); ?>" 
                                                         alt="Vehicle Audit Back" 
                                                         class="img-thumbnail" 
                                                         style="width:60px; height:60px; object-fit: cover;">
                                                    <?php
                                                    }  
                                                    if($file_ext=="pdf")
                                                    {
                                                    ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->vehicle_audit_photo2); ?>" 
                                                       class="btn btn-sm btn-outline-primary" download>
                                                        <i class="bi bi-download"></i> PDF
                                                    </a>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning text-dark">No Document</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->vehicle_audit_photo2))
                                                {
                                                ?>
                                                    <a href="<?php echo url('public/vehicle_image/vehicle_photo/'.$vehicle_details->vehicle_audit_photo2); ?>" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                <?php
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="text-muted">-</span>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                if(!empty($vehicle_details->vehicle_audit_photo2)){
                                                    if($vehicle_details->doc10_reupload == "1"){ ?>
                                                    <button class="btn btn-success btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc10_reupload}}, 'doc10_reupload')">
                                                        Request Re-upload
                                                    </button>
                                                    <?php }else{ ?>
                                                    <button class="btn btn-danger btn-sm" onclick="changeReuploadStatus({{$vehicle_details->id}}, {{$vehicle_details->doc10_reupload}}, 'doc10_reupload')">
                                                        Already Requested
                                                    </button>
                                                <?php } }else{ ?>
                                                <span class="badge bg-warning text-dark">No Document</span>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function changeReuploadStatus(id, status, column_name){
        if(status == 0){
            var msg_txt = " Request Re Upload ";
        }else{
            var msg_txt = " Not Re Upload  ";
        }
        Swal.fire({
          title: "Are you sure?",
          text: "you want to "+ msg_txt +" the documents !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes",
          cancelButtonText: "No"
        })
        .then((willDelete) => {
         if (willDelete.isConfirmed) {
          $.ajax({
            url: "{{ url('admin/change_reupload_status') }}",
            type: "post", 
            data: { id: id, status:status, column_name:column_name},
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                Swal.fire('Success!', 'Status updated successfully.', 'success');
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error!', 'Failed to update status.', 'error');
            }
          });
         }
        });
    }

    function changeStatus(id, status){
        if(status == 0){
            var msg_txt = " Approve ";
        }else{
            var msg_txt = " Not Approve ";
        }
        Swal.fire({
          title: "Are you sure?",
          text: "you want to "+ msg_txt +" the documents !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes",
          cancelButtonText: "No"
        })
        .then((willDelete) => {
         if (willDelete.isConfirmed) {
          $.ajax({
            url: "{{ url('admin/driver_licence_status') }}",
            type: "post", 
            data: { id: id, status:status},
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                Swal.fire('Success!', 'Status updated successfully.', 'success');
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error!', 'Failed to update status.', 'error');
            }
          });
         }
        });
    }
    
    function changeStatus2(id, status){
        if(status == 0){
            var msg_txt = " Approve ";
        }else{
            var msg_txt = " Not Approve ";
        }  
        Swal.fire({
          title: "Are you sure?",
          text: "you want to "+ msg_txt +" the documents !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes",
          cancelButtonText: "No"
        })
        .then((willDelete) => {
         if (willDelete.isConfirmed) {
          $.ajax({
            url: "{{ url('admin/driver_idproof_status') }}",
            type: "post", 
            data: { id: id, status:status},
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                Swal.fire('Success!', 'Status updated successfully.', 'success');
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error!', 'Failed to update status.', 'error');
            }
          });
         }
        });
    }
    
    function changeStatus3(id, status){
         if(status == 0){
            var msg_txt = " Approve ";
         }else{
            var msg_txt = " Not Approve ";
         }
        Swal.fire({
          title: "Are you sure?",
          text: "you want to "+ msg_txt +" the documents !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes",
          cancelButtonText: "No"
        })
        .then((willDelete) => {
         if (willDelete.isConfirmed) {
          $.ajax({
            url: "{{ url('admin/driver_rc_status') }}",
            type: "post", 
            data: { id: id, status:status},
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                Swal.fire('Success!', 'Status updated successfully.', 'success');
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error!', 'Failed to update status.', 'error');
            }
          });
         }
        });
    }
    
    function changeStatus4(id, status){
          if(status == 0){
                var msg_txt = " Approve ";
            }else{
                var msg_txt = " Not Approve ";
            }
        Swal.fire({
          title: "Are you sure?",
          text: "you want to "+ msg_txt +" the documents !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes",
          cancelButtonText: "No"
        })
        .then((willDelete) => {
         if (willDelete.isConfirmed) {
          $.ajax({
            url: "{{ url('admin/driver_pollution_status') }}",
            type: "post", 
            data: { id: id, status:status},
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                Swal.fire('Success!', 'Status updated successfully.', 'success');
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error!', 'Failed to update status.', 'error');
            }
          });
         }
        });
    }
    
    function changeStatus5(id, status){
          if(status == 0){
            var msg_txt = " Approve ";
          }else{
            var msg_txt = " Not Approve ";
          }
        Swal.fire({
          title: "Are you sure?",
          text: "you want to "+ msg_txt +" the documents !",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Yes",
          cancelButtonText: "No"
        })
        .then((willDelete) => {
         if (willDelete.isConfirmed) {
          $.ajax({
            url: "{{ url('admin/driver_audit_status') }}",
            type: "post", 
            data: { id: id, status:status},
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                Swal.fire('Success!', 'Status updated successfully.', 'success');
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire('Error!', 'Failed to update status.', 'error');
            }
          });
         }
        });
    }
</script>
@endsection   