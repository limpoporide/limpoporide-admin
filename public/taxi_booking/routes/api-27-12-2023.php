<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SmartrideController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\DriverController;


    Route::GET('language_list', [SmartrideController::class,'language_list']);
    Route::POST('language_search', [SmartrideController::class,'language_search']);
    Route::POST('user_login', [AuthController::class,'user_login']);
    Route::POST('user_verify_otp', [AuthController::class,'user_verify_otp']);
    Route::POST('signup_save_profile', [AuthController::class,'signup_save_profile']);
    Route::POST('user_loginwith_pass', [AuthController::class,'user_loginwith_pass']);
    Route::POST('recovery_password', [AuthController::class,'recovery_password']);
    Route::POST('recovery_password_verify_otp', [AuthController::class,'recovery_password_verify_otp']);
    Route::POST('update_password', [AuthController::class,'update_password']);
    
    Route::POST('driver_register', [DriverController::class,'driver_register']);

 Route::group(['middleware' => 'auth:api'], function()
 {
    Route::POST('add_ride_details',[SmartrideController::class,'add_ride_details']);
    Route::GET('get_riders_details',[SmartrideController::class,'get_riders_details']);
        Route::POST('add_saved_places',[SmartrideController::class,'add_saved_places']);
        Route::GET('get_saved_place_list',[SmartrideController::class,'get_saved_place_list']);
        Route::GET('my_profile',[SmartrideController::class,'my_profile']);
        Route::POST('update_profile',[SmartrideController::class,'update_profile']);
        // Route::GET('my_profile',[SmartrideController::class,'my_profile']);
        Route::POST('change_password',[SmartrideController::class,'change_password']);
        Route::GET('about_us',[SmartrideController::class,'about_us']);
        Route::GET('customer_support',[SmartrideController::class,'customer_support']);
        Route::GET('faq',[SmartrideController::class,'faq']);
        
        
        
        Route::POST('get_vehicles',[SmartrideController::class,'get_vehicles']);
        Route::GET('get_coupon_codes',[SmartrideController::class,'get_coupon_codes']);
        Route::POST('apply_coupon_code',[SmartrideController::class,'apply_coupon_code']);
        Route::GET('get_reason_cancel_list',[SmartrideController::class,'get_reason_cancel_list']);
        Route::POST('add_review',[SmartrideController::class,'add_review']);
        Route::GET('get_dodont_list',[SmartrideController::class,'get_dodont_list']);
        Route::GET('contact_us',[SmartrideController::class,'contact_us']);
        Route::GET('smart_ride_safery',[SmartrideController::class,'smart_ride_safery']);
        Route::GET('get_trip_issue',[SmartrideController::class,'get_trip_issue']);
        Route::GET('get_safety_issue',[SmartrideController::class,'get_safety_issue']);
        Route::POST('get_wallet_list',[SmartrideController::class,'get_wallet_list']);
        Route::POST('get_ongoing_ride',[SmartrideController::class,'get_ongoing_ride']);
        Route::POST('book_ride',[SmartrideController::class,'book_ride']);
        Route::POST('add_contact',[SmartrideController::class,'add_contact']);
        Route::GET('emergency_contact_list',[SmartrideController::class,'emergency_contact_list']);
        Route::POST('emergency_contact_id',[SmartrideController::class,'emergency_contact_id']);
        Route::POST('emergency_contact_delete',[SmartrideController::class,'emergency_contact_delete']);
        
        Route::POST('confirm_booking',[UserController::class,'confirm_booking']);
        Route::POST('booking_detail',[UserController::class,'booking_detail']);
        Route::POST('search_vehicle',[UserController::class,'search_vehicle']);
        Route::POST('search_vehicle_id',[UserController::class,'search_vehicle_id']);
        
        
        Route::POST('book_a_ride',[UserController::class,'book_a_ride']);
        Route::POST('search_ride',[UserController::class,'search_ride']);
        Route::POST('cancel_ride',[UserController::class,'cancel_ride']);
        Route::POST('rating',[UserController::class,'rating']);
        Route::GET('rating_list',[UserController::class,'rating_list']);
        Route::POST('user_em_details',[UserController::class,'user_em_details']);
        Route::POST('add_chats',[UserController::class,'add_chats']);
        Route::POST('chat_with_user',[UserController::class,'chat_with_user']);
        Route::GET('user_ride_history',[UserController::class,'user_ride_history']);
        Route::POST('check_user_booking_ride',[UserController::class,'check_user_booking_ride']);
        
        Route::POST('ride_start_verfiy_otp',[UserController::class,'ride_start_verfiy_otp']);
        Route::POST('new_confirm_booking',[UserController::class,'new_confirm_booking']);
        Route::POST('user_booking_cancel',[UserController::class,'user_booking_cancel']);
        Route::GET('notification_list',[UserController::class,'notification_list']);
        Route::GET('my_offer',[UserController::class,'my_offer']);
        Route::GET('safety_list',[UserController::class,'safety_list']);
        Route::POST('safety_list_id',[UserController::class,'safety_list_id']);
        Route::POST('upload_audio',[UserController::class,'upload_audio']);
        Route::POST('upload_video',[UserController::class,'upload_video']);
        
        
       
        Route::GET('ongoing_booking_lists',[UserController::class,'ongoing_booking_lists']);
        
        Route::GET('cancel_booking_lists',[UserController::class,'cancel_booking_lists']);
        Route::POST('driver_start_trip',[UserController::class,'driver_start_trip']);
        Route::GET('complete_list_ride',[UserController::class,'complete_list_ride']);
        
        Route::POST('driver_end_trip',[UserController::class,'driver_end_trip']);
        
        
        Route::POST('vehicle_register',[DriverController::class,'vehicle_register']);
        Route::POST('driver_profile_register',[DriverController::class,'driver_profile_register']);
        Route::POST('bank_details',[DriverController::class,'bank_details']);
        Route::GET('all_booking_lists',[DriverController::class,'all_booking_lists']);
        
        Route::POST('recording_list',[DriverController::class,'recording_list']);
        Route::POST('video_list',[DriverController::class,'video_list']);
        Route::POST('complete_ride',[DriverController::class,'complete_ride']);
        Route::GET('scheduled_list_ride',[DriverController::class,'scheduled_list_ride']);
        Route::POST('scheduled_ride_details',[DriverController::class,'scheduled_ride_details']);
        Route::POST('chat_with_driver',[DriverController::class,'chat_with_driver']);
         Route::POST('driver_request_booking',[DriverController::class,'driver_request_booking']);
        
        Route::POST('booking_status_reject',[DriverController::class,'booking_status_reject']);
        Route::POST('booking_status_accepted',[DriverController::class,'booking_status_accepted']);
        Route::POST('driver_cancel_ride',[DriverController::class,'driver_cancel_ride']);
        Route::POST('driver_ride_start_verfiy_otp',[DriverController::class,'driver_ride_start_verfiy_otp']);
        Route::POST('driver_schedule_booking_cancel',[DriverController::class,'driver_schedule_booking_cancel']);
        Route::POST('driver_schedule_request_booking',[DriverController::class,'driver_schedule_request_booking']);
        Route::POST('recent_booking',[DriverController::class,'recent_booking']);
        Route::GET('driver_booking_lists',[DriverController::class,'driver_booking_lists']);
        Route::POST('upcoming_booking',[DriverController::class,'upcoming_booking']);
        Route::GET('wallet',[DriverController::class,'wallet']);
        
        Route::GET('driver_rating',[DriverController::class,'driver_rating']);
        Route::POST('withdrawal_amount_request',[DriverController::class,'withdrawal_amount_request']);
        Route::POST('my_payment',[DriverController::class,'my_payment']);
        Route::POST('payment_history',[DriverController::class,'payment_history']);
        Route::post('onoff',[DriverController::class,'onoff']);
        
        
        
        
 });
