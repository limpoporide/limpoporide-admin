@extends('admin.includes.main')
@section('content')

<main id="main" class="main">
    <section class="section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Page Title -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Third Party Settings</h3>
                        <a href="{{ URL::to('admin/dashboard') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                    </div>

                    <!-- Main Card -->
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ route('admin.third_party') }}" id="form-submit" enctype="multipart/form-data" method="post">
                                @csrf
                                
                                <div class="row g-4">
                                    <!-- Razor Pay Payment Gateway -->
                                    <div class="col-lg-6">
                                        <div class="card h-100 border">
                                            <div class="card-header bg-dark text-white text-center">
                                                <h5 class="mb-0">Payfast Payment Gateway</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="merchant_id" class="form-label">Api Key</label>
                                                    <input type="text" class="form-control" id="merchant_id" name="merchant_id" 
                                                           value="{{ get_option_data('merchant_id') }}" placeholder="Enter Api Key">
                                                    <div class="text-danger mt-1 error-merchant_id"></div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="access_code" class="form-label">Secret key</label>
                                                    <input type="text" class="form-control" id="access_code" name="access_code" 
                                                           value="{{ get_option_data('access_code') }}" placeholder="Enter Secret key">
                                                    <div class="text-danger mt-1 error-access_code"></div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Google Map -->
                                    <div class="col-lg-6">
                                        <div class="card h-100 border">
                                            <div class="card-header bg-dark text-white text-center">
                                                <h5 class="mb-0">Google Map</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-0">
                                                    <label for="google_map_api_key" class="form-label">API Key</label>
                                                    <input type="text" class="form-control" id="google_map_api_key" name="google_map_api_key" 
                                                           value="{{ get_option_data('google_map_api_key') }}" placeholder="Enter Google Map API Key">
                                                    <div class="text-danger mt-1 error-google_map_api_key"></div>
                                                </div>
                                                <div class="mt-2">
                                                    <label for="google_map_api_key" class="form-label">Radius</label>
                                                    <input type="number" class="form-control" id="radius_range" name="radius_range" 
                                                           value="{{ $radius->radius_range }}" placeholder="Enter Radius">
                                                    <div class="text-danger mt-1 error-radius_range"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- User Cancellation Charges -->
                                    <div class="col-lg-6">
                                        <div class="card h-100 border">
                                            <div class="card-header bg-dark text-white text-center">
                                                <h5 class="mb-0">User Cancellation Charges</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="user_cancel_time_duration" class="form-label">Cancel Time Duration</label>
                                                    <input type="text" class="form-control" id="user_cancel_time_duration" name="user_cancel_time_duration" 
                                                           value="{{ get_option_data('user_cancel_time_duration') }}" placeholder="e.g., 1.0.0">
                                                    <div class="text-danger mt-1 error-user_cancel_time_duration"></div>
                                                </div>
                                                
                                                <div class="mb-0">
                                                    <label for="user_cancel_charge" class="form-label">Cancel Charge</label>
                                                    <input type="text" class="form-control" id="user_cancel_charge" name="user_cancel_charge" 
                                                           value="{{ get_option_data('user_cancel_charge') }}" placeholder="e.g., 1.0.0">
                                                    <div class="text-danger mt-1 error-user_cancel_charge"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- iOS Application -->
                                    <div class="col-lg-6">
                                        <div class="card h-100 border">
                                            <div class="card-header bg-dark text-white text-center">
                                                <h5 class="mb-0">Driver Cancellation Charges</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="driver_cancel_time_duration" class="form-label">Cancel Time Duration</label>
                                                    <input type="text" class="form-control" id="driver_cancel_time_duration" name="driver_cancel_time_duration" 
                                                           value="{{ get_option_data('driver_cancel_time_duration') }}" placeholder="e.g., 1.0.0">
                                                    <div class="text-danger mt-1 error-driver_cancel_time_duration"></div>
                                                </div>
                                                
                                                <div class="mb-0">
                                                    <label for="driver_cancel_charge" class="form-label">Cancel Charge</label>
                                                    <input type="text" class="form-control" id="driver_cancel_charge" name="driver_cancel_charge" 
                                                           value="{{ get_option_data('driver_cancel_charge') }}" placeholder="e.g., 1.0.0">
                                                    <div class="text-danger mt-1 error-driver_cancel_charge"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="bi bi-check-circle me-2"></i>Submit Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Scripts -->
<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" defer></script>
<script src="{{ asset('assets/js/jquery1-3.4.1.min.js') }}"></script>

@endsection