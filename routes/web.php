<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\File;

// Website Controller
// Route::match(['get', 'post'], '/', [AuthController::class, 'index']);
Route::match(['get', 'post'], '/', [AuthController::class, 'login']);

Route::match(['get', 'post'], 'web_privacy_policy', [AuthController::class, 'web_privacy_policy']);
Route::match(['get', 'post'], 'web_terms_conditions', [AuthController::class, 'web_terms_conditions']);
Route::match(['get', 'post'], 'web_cancel_policy', [AuthController::class, 'web_cancel_policy']);
Route::match(['get', 'post'], 'web_refund_policy', [AuthController::class, 'web_refund_policy']);

Route::match(['get', 'post'], 'qr_code_samples', [AuthController::class, 'qr_code_samples']);
Route::match(['get', 'post'], 'qr_code_submit', [AuthController::class, 'qr_code_submit']);
Route::match(['get', 'post'], 'qr_code_details/{id}', [AuthController::class, 'qr_code_details'])->name('qr.details');

// Admin Controller
Route::match(['get', 'post'], '/admin', [AuthController::class, 'login']);
Route::match(['get', 'post'], 'admin/login', [AuthController::class, 'login']);

Route::group(['namespace' => 'Admin','prefix'=>'admin', 'middleware'=>['afterLoginAuth']], function () {
    
    Route::get('dashboard/chart-data', [AuthController::class, 'getChartDataAjax'])->name('admin.dashboard.chart-data');

    Route::match(['get', 'post'], 'logout', [AuthController::class, 'logout']);
    Route::match(['get', 'post'], 'dashboard', [AdminController::class, 'index']);
    Route::get('dashboard/chart-data', [AdminController::class, 'chartData']);
    Route::match(['get', 'post'], 'profile', [AuthController::class, 'profile']);

    Route::match(['get', 'post'], 'normal_users', [AdminController::class, 'normal_users']);
    Route::match(['get', 'post'], 'user_change_status', [AdminController::class, 'user_change_status']);

    Route::match(['get', 'post'], 'edit_user', [AdminController::class, 'edit_user']);
    Route::match(['get', 'post'], 'delete_user', [AdminController::class, 'delete_user']);

    Route::match(['get', 'post'], 'awative_drivers', [AdminController::class, 'awative_drivers']);
    Route::match(['get', 'post'], 'manage_drivers', [AdminController::class, 'manage_drivers']);
    Route::match(['get', 'post'], 'add_new_drivers', [AdminController::class, 'add_new_drivers']);
    
    Route::get('approved_drivers_list', [AdminController::class, 'approved_drivers_list']);
    Route::post('suspend_driver/{id}', [AdminController::class, 'suspend_driver']);
    Route::post('reactivate_driver/{id}', [AdminController::class, 'reactivate_driver']);
    
    Route::match(['get','post'],'/edit_driver_details/{id}',[AdminController::class, 'edit_driver_details']);
    Route::match(['get','post'],'/update_driver_details/{id}',[AdminController::class, 'update_driver_details']);
    Route::match(['get','post'],'/driver_details/{id}',[AdminController::class, 'driver_details']);

    Route::match(['get', 'post'], 'get-user-details', [AdminController::class, 'getUserDetails']);

    Route::match(['get', 'post'], 'about_us', [AdminController::class, 'about_us'])->name('admin.about_us');
    Route::match(['get', 'post'], 'contact_us', [AdminController::class, 'contact_us'])->name('admin.contact_us');
    Route::match(['get', 'post'], 'terms_conditions', [AdminController::class, 'terms_conditions'])->name('admin.terms_conditions');
    Route::match(['get', 'post'], 'terms_conditions_driver', [AdminController::class, 'terms_conditions_driver'])->name('admin.terms_conditions_driver');
    Route::match(['get', 'post'], 'privacy_policy', [AdminController::class, 'privacy_policy'])->name('admin.privacy_policy');
    Route::match(['get', 'post'], 'privacy_policy_driver', [AdminController::class, 'privacy_policy_driver'])->name('admin.privacy_policy_driver');
    Route::match(['get', 'post'], 'faqs', [AdminController::class, 'faqs'])->name('admin.faqs');
    Route::match(['get', 'post'], 'ride_cancel_reasons', [AdminController::class, 'ride_cancel_reasons'])->name('admin.ride_cancel_reasons');

    Route::match(['get', 'post'], 'cancel_reason_change_status', [AdminController::class, 'cancel_reason_change_status']);
    Route::match(['get', 'post'], 'faqs_change_status', [AdminController::class, 'faqs_change_status']);
    Route::match(['get', 'post'], 'delete_reason', [AdminController::class, 'delete_reason']);
    Route::match(['get', 'post'], 'delete_faqs', [AdminController::class, 'delete_faqs']);

    // Map
    Route::match(['get','post'],'/show_drivers_map',[AdminController::class,'show_drivers_map'])->name('show_drivers_map');
    Route::match(['get','post'],'/driverLocations',[AdminController::class,'driverLocations'])->name('driverLocations');

    // Drivers
    Route::match(['get','post'],'/new_driver_list',[AdminController::class, 'new_driver_list']);
    Route::match(['get','post'],'/approved_drivers_list',[AdminController::class, 'approved_drivers_list']);
    Route::match(['get','post'],'/active_drivers_list',[AdminController::class, 'active_drivers_list']);
    Route::match(['get','post'],'/sespended_drivers_list',[AdminController::class, 'sespended_drivers_list']);
    
    Route::get('/driver_face_verifications/{id}', [AdminController::class, 'driver_face_verifications']);
    
    Route::get('/add_new_drivers',             [AdminController::class, 'add_new_drivers']);
    Route::post('/store_new_driver',           [AdminController::class, 'store_new_driver']);
    Route::get('/assign_vehicle/{driver_id}',  [AdminController::class, 'assign_vehicle']);
    Route::post('/save_assign_vehicle/{driver_id}', [AdminController::class, 'save_assign_vehicle']);

    /*licence status check*/
    Route::match(['get','post'],'/driver_licence_status',[AdminController::class, 'driver_licence_status']);
    Route::match(['get','post'],'/driver_idproof_status',[AdminController::class, 'driver_idproof_status']);
    Route::match(['get','post'],'/driver_rc_status',[AdminController::class, 'driver_rc_status']);
    Route::match(['get','post'],'/driver_pollution_status',[AdminController::class, 'driver_pollution_status']);
    Route::match(['get','post'],'/driver_audit_status',[AdminController::class, 'driver_audit_status']);
    Route::match(['get','post'],'/change_reupload_status',[AdminController::class, 'change_reupload_status']);
    
    // All Vehicles
    Route::get('/all_vehicles',             [AdminController::class, 'all_vehicles'])->name('admin.all_vehicles');
    Route::get('/add_vehicle_page',         [AdminController::class, 'add_vehicle_page']);
    Route::post('/add_vehicle',             [AdminController::class, 'add_vehicle']);
    Route::get('/view_vehicle/{id}',        [AdminController::class, 'view_vehicle']);
    Route::get('/edit_vehicle_page/{id}',   [AdminController::class, 'edit_vehicle_page']);
    Route::post('/edit_vehicle',            [AdminController::class, 'edit_vehicle']);
    Route::post('/change_vehicle_status',   [AdminController::class, 'change_vehicle_status']);
    Route::post('/delete_vehicle',          [AdminController::class, 'delete_vehicle']);
    Route::get('/get_models_by_type',       [AdminController::class, 'get_models_by_type']);

    /* All type vehicles */
    Route::match(['get','post'],'/vehicle_all_types',[AdminController::class, 'vehicle_all_types']);
    Route::match(['get','post'],'/add_type_vehicle',[AdminController::class, 'add_type_vehicle']);
    Route::match(['get','post'],'/delete_type_vehicle',[AdminController::class, 'delete_type_vehicle']);
    Route::match(['get','post'],'/change_vehicle_type_status',[AdminController::class, 'change_vehicle_type_status']);
    Route::match(['get','post'],'/change_manage_fare_status',[AdminController::class, 'change_manage_fare_status']);
    
    // Coupon codes
    Route::get('/coupon_codes', [AdminController::class, 'coupon_all_codes']);
    Route::post('/add_coupon_code', [AdminController::class, 'add_coupon_code']);
    Route::get('/edit_coupon_code/{id}', [AdminController::class, 'edit_coupon_code']);
    Route::post('/delete_coupon_code', [AdminController::class, 'delete_coupon_code']);
    Route::post('/change_coupon_status', [AdminController::class, 'change_coupon_status']);
    
    Route::get('/weekly_payouts', [AdminController::class, 'weekly_payouts']);

    // Bookings
    Route::match(['get','post'],'/pending_booking',[AdminController::class,'pending_booking'])->name('admin.pending_booking');
    Route::match(['get','post'],'/complete_booking',[AdminController::class,'complete_booking'])->name('admin.complete_booking');
    Route::match(['get','post'],'/cancelled_booking',[AdminController::class,'cancelled_booking'])->name('admin.cancelled_booking');
    Route::match(['get','post'],'/schedule_booking',[AdminController::class,'schedule_booking'])->name('admin.schedule_booking');
    
    Route::get('/get-booking-activity-log', [AdminController::class, 'getBookingActivityLog'])->name('admin.getBookingActivityLog');

    Route::match(['get','post'],'/booking_payments',[AdminController::class,'booking_payments']);
    Route::match(['get','post'],'/manage_comission',[AdminController::class,'manage_comission']);

    Route::match(['get', 'post'], 'all_notifications', [AdminController::class, 'all_notifications'])->name('admin.all_notifications');
    Route::match(['get', 'post'], 'delete_notifications', [AdminController::class, 'delete_notifications']);
    
    Route::match(['get', 'post'], 'withdrawal_status', [AdminController::class, 'withdrawal_status']);

    // Withdraw
    Route::get('/withdrawal_request',[AdminController::class,'withdrawal_request']);
    Route::match(['get','post'],'/third_party',[AdminController::class, 'third_party'])->name('admin.third_party');
});
