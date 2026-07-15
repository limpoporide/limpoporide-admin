<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApiController;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SmartrideController;
use App\Http\Controllers\Api\DriverController;  
    
    route::POST('return_online_with_bookingid_test', [UserController::class,'return_online_with_bookingid_test'])->name('return_online_with_bookingid_test'); 
    
    Route::POST('testing_api', [SmartrideController::class,'testing_api']);
    
    Route::GET('language_list', [SmartrideController::class,'language_list']);
    Route::POST('language_search', [SmartrideController::class,'language_search']);
    
    
    ///////////////////////////////////////////////////////////////////////////////////
    
    Route::get('get_banners', [AuthController::class,'get_banners']);
    
    
    Route::POST('user_signup', [AuthController::class,'user_signup']);
    Route::POST('user_login', [AuthController::class,'user_login']);
    Route::POST('user_verify_otp', [AuthController::class,'user_verify_otp']);
    Route::POST('reset_otp_send', [AuthController::class,'reset_otp_send']);
    Route::POST('update_password', [AuthController::class,'update_password']);
    Route::GET('terms_conditions',[UserController::class,'terms_conditions']);
    Route::GET('terms_conditions_driver',[UserController::class,'terms_conditions_driver']); // Driver
    
    Route::POST('faq', [ApiController::class,'faq']);
    
    Route::POST('deiver_signup', [AuthController::class,'deiver_signup']);
    Route::POST('driver_login', [AuthController::class,'driver_login']);
    
    Route::POST('vehicle_register',[ApiController::class,'vehicle_register']);
    
    Route::GET('vehicle_company',[DriverController::class,'vehicle_company']);
    Route::POST('vehicle_model',[DriverController::class,'vehicle_model']);
    Route::POST('document_upload',[DriverController::class,'document_upload']); // All Document Uploads
    
    Route::GET('vehicle_types',[ApiController::class,'vehicle_types']);
    
    Route::POST('bank_account_update',[DriverController::class,'bank_account_update']);
    
    ///////////////////////////////////////////////////////////////////////////////////

    
    // Route::POST('signup_save_profile', [AuthController::class,'signup_save_profile']);
    // Route::POST('user_loginwith_pass', [AuthController::class,'user_loginwith_pass']); 
    
    // Route::POST('resend_otp', [AuthController::class,'resend_otp']);
    
    Route::POST('recovery_password', [AuthController::class,'recovery_password']);
    Route::POST('recovery_password_verify_otp', [AuthController::class,'recovery_password_verify_otp']);

    
    // Route::POST('driver_register', [DriverController::class,'driver_register']);

    Route::GET('ccavenue_payment',[DriverController::class,'ccavenue_payment']);
    Route::POST('ccavenue_payment_redirect',[DriverController::class,'ccavenue_payment_redirect'])->name('cc-response');
    
    
    
    Route::GET('privacy_policy_user',[UserController::class,'privacy_policy_user']);
    Route::GET('privacy_policy_driver',[UserController::class,'privacy_policy_driver']);

    Route::GET('get_google_map_api_key',[UserController::class,'get_google_map_api_key']);
    
    Route::GET('latest_version_popup_show',[UserController::class,'latest_version_popup_show']);
   
    Route::GET('get_apk_latest_version',[UserController::class,'get_apk_latest_version']);
    Route::GET('get_driver_apk_latest_version',[UserController::class,'get_driver_apk_latest_version']); 
    
    Route::GET('get_ios_user_latest_version',[UserController::class,'get_ios_user_latest_version']);
    Route::GET('get_ios_driver_latest_version',[UserController::class,'get_ios_driver_latest_version']); 
    
    Route::GET('rental_rate_apply_km',[UserController::class,'rental_rate_apply_km']);
    
    Route::GET('about_us',[SmartrideController::class,'about_us']);
    Route::GET('cancellation_privacy_policy',[SmartrideController::class,'cancellation_privacy_policy']);
    
    // Socket Apis
    Route::get('/health', [ApiController::class, 'healthCheck']);
    Route::get('/online-users', [ApiController::class, 'getOnlineUsers']);
    Route::post('/check-users-online', [ApiController::class, 'checkUsersOnline']);
    Route::post('/send-notification', [ApiController::class, 'sendNotification']);

    Route::group(['middleware' => 'auth:api'], function()
    {
        
        //////////////////////////////////////////////////////////////////
        
        Route::GET('update_notification_status', [AuthController::class,'update_notification_status']);
        
        Route::POST('update_user_password', [AuthController::class,'update_user_password']);
        
        Route::POST('get_wallet_list',[ApiController::class,'get_wallet_list']);
        Route::POST('add_wallet_money', [ApiController::class,'add_wallet_money']);
        Route::GET('notification_list',[ApiController::class,'notification_list']);
        Route::GET('help',[ApiController::class,'help']);
        
        Route::GET('user_profile',[ApiController::class,'user_profile']);
        Route::POST('update_profile',[ApiController::class,'update_profile']);
        Route::POST('update_profile_picture',[ApiController::class,'update_profile_picture']);
        
        Route::POST('update_email',[ApiController::class,'update_email']);
        Route::POST('send_email_otp',[ApiController::class,'send_email_otp']);
        Route::POST('email_otp_verify',[ApiController::class,'email_otp_verify']);
        
        Route::POST('send_phone_otp',[ApiController::class,'send_phone_otp']);
        Route::POST('phone_otp_verify',[ApiController::class,'phone_otp_verify']);
        Route::POST('update_phone',[ApiController::class,'update_phone']);

        Route::POST('search_vehicle',[ApiController::class,'search_vehicle']);
        Route::POST('search_vehicle_id',[ApiController::class,'search_vehicle_id']); 
        
        Route::GET('payment_modes',[ApiController::class,'payment_modes']); 
        Route::POST('add_payment_method',[ApiController::class,'add_payment_method']);
        
        Route::POST('add_ride_details',[ApiController::class,'add_ride_details']);
        Route::GET('get_ride_details',[ApiController::class,'get_ride_details']);
        
        Route::POST('book_a_ride',[ApiController::class,'book_a_ride']);
        Route::POST('search_ride',[ApiController::class,'search_ride']);
        Route::POST('check_booking_status',[ApiController::class,'check_booking_status']);
        
        Route::POST('user_booking_trip_info',[ApiController::class,'user_booking_trip_info']);
        
        
        Route::POST('booking_status_reject',[ApiController::class,'booking_status_reject']);
        Route::POST('booking_status_accepted',[ApiController::class,'booking_status_accepted']);
        
        Route::GET('get_reason_cancel_list',[ApiController::class,'get_reason_cancel_list']);
        Route::POST('user_cancel_ride',[ApiController::class,'user_cancel_ride']);
        
        Route::POST('add_chats',[ApiController::class,'add_chats']);
        Route::POST('chat_with_user',[ApiController::class,'chat_with_user']);
        
        Route::POST('add_booking_rating',[ApiController::class,'add_booking_rating']); // Implemented
        
        
        Route::POST('ride_start_verfiy_otp',[ApiController::class,'ride_start_verfiy_otp']);
        
        Route::POST('driver_complete_ride',[ApiController::class,'driver_complete_ride']);
        Route::POST('user_pay_online',[ApiController::class,'user_pay_online']);
        
        Route::POST('ride_complete_booking',[ApiController::class,'ride_complete_booking']);
        
        Route::POST('user_all_rides',[ApiController::class,'user_all_rides']);
        Route::POST('driver_all_rides',[ApiController::class,'driver_all_rides']);
        
        Route::GET('upcoming_booking',[DriverController::class,'upcoming_booking']);
        Route::POST('driver_online_offline',[DriverController::class,'driver_online_offline']);
        
        Route::POST('driver_change_location',[DriverController::class,'driver_change_location']); // Update Driver Lat Long
        Route::GET('driver_dashboard',[DriverController::class,'driver_dashboard']);
        
        Route::GET('driver_earning',[DriverController::class,'driver_earning']);
        
        Route::POST('withdrawal_amount_request',[DriverController::class,'withdrawal_amount_request']);
        Route::GET('withdrawal_history',[DriverController::class,'withdrawal_history']);
        
        Route::POST('get_driver_lat_long',[DriverController::class,'get_driver_lat_long']);
        
        Route::POST('driver_reject_ride',[DriverController::class,'driver_reject_ride']); 
        
        Route::GET('get_driver_reason_cancel_list',[ApiController::class,'get_driver_reason_cancel_list']);
        Route::POST('driver_cancel_ride',[DriverController::class,'driver_cancel_ride']); 
        
        Route::GET('vehicle_details',[DriverController::class,'vehicle_details']);
        
        Route::match(['get','post'],'user_logout',[AuthController::class,'user_logout']); 
        
        Route::GET('user_recent_search_rides',[AuthController::class,'user_recent_search_rides']); 
        
        
        /////////////////////////////////////////////////////////////////
        
        // Driver User Apis 
        
        Route::GET('delete_account',[AuthController::class,'delete_account']); 
         
        Route::GET('get_payout_data',[AuthController::class,'get_payout_data']);
        
        Route::GET('get_riders_details',[SmartrideController::class,'get_riders_details']);
        Route::POST('add_saved_places',[SmartrideController::class,'add_saved_places']);
        Route::GET('get_saved_place_list',[SmartrideController::class,'get_saved_place_list']);
        
        Route::GET('user_bank_details',[SmartrideController::class,'user_bank_details']);
        Route::POST('delete_saved_place',[SmartrideController::class,'delete_saved_place']);
    
        Route::POST('driver_update_profile',[DriverController::class,'driver_update_profile']);   
        // Route::GET('my_profile',[SmartrideController::class,'my_profile']);
        Route::POST('change_password',[SmartrideController::class,'change_password']);
        
        Route::GET('customer_support',[SmartrideController::class,'customer_support']);
        Route::GET('country_list',[SmartrideController::class,'country_list']);
        Route::GET('year_list',[SmartrideController::class,'year_list']);
            
            
        Route::POST('get_vehicles',[SmartrideController::class,'get_vehicles']);
        Route::GET('get_coupon_codes',[SmartrideController::class,'get_coupon_codes']);
        Route::POST('apply_coupon_code',[SmartrideController::class,'apply_coupon_code']);
        
        Route::POST('add_review',[SmartrideController::class,'add_review']);
        Route::GET('get_dodont_list',[SmartrideController::class,'get_dodont_list']);
        Route::GET('contact_us',[SmartrideController::class,'contact_us']);
        Route::GET('smart_ride_safery',[SmartrideController::class,'smart_ride_safery']);
        Route::GET('get_trip_issue',[SmartrideController::class,'get_trip_issue']);
        Route::GET('get_safety_issue',[SmartrideController::class,'get_safety_issue']);
        
        Route::GET('get_ongoing_ride',[SmartrideController::class,'get_ongoing_ride']); 

        Route::POST('add_contact',[SmartrideController::class,'add_contact']);
        Route::GET('emergency_contact_list',[SmartrideController::class,'emergency_contact_list']);
        Route::POST('emergency_contact_id',[SmartrideController::class,'emergency_contact_id']);
        Route::POST('emergency_contact_delete',[SmartrideController::class,'emergency_contact_delete']);
    
        
        Route::POST('confirm_booking',[UserController::class,'confirm_booking']);
        Route::POST('booking_list',[UserController::class,'booking_list']);
        Route::POST('booking_details',[UserController::class,'booking_details']);
        
        Route::POST('cancel_ride',[UserController::class,'cancel_ride']);
        Route::POST('rating',[UserController::class,'rating']);
        Route::GET('rating_list',[UserController::class,'rating_list']);
        Route::GET('rating_user_list',[UserController::class,'rating_user_list']); 
        Route::POST('user_em_details',[UserController::class,'user_em_details']);
        
        Route::POST('add_voice_chat',[UserController::class,'add_voice_chat']);
        
        Route::GET('user_ride_history',[UserController::class,'user_ride_history']); // Not
        Route::POST('check_user_booking_ride',[UserController::class,'check_user_booking_ride']);
        
        
        Route::POST('new_confirm_booking',[UserController::class,'new_confirm_booking']);
        Route::POST('user_booking_cancel',[UserController::class,'user_booking_cancel']);
        
        Route::GET('my_offer',[UserController::class,'my_offer']);
        Route::GET('safety_list',[UserController::class,'safety_list']);
        Route::POST('safety_list_id',[UserController::class,'safety_list_id']);
        
        // Route::POST('upload_audio',[UserController::class,'upload_audio']);
        // Route::POST('upload_video',[UserController::class,'upload_video']);
        
        Route::GET('ongoing_booking_lists',[UserController::class,'ongoing_booking_lists']); 
        
        Route::GET('cancel_booking_lists',[UserController::class,'cancel_booking_lists']);
        Route::POST('driver_start_trip',[UserController::class,'driver_start_trip']);
        Route::GET('complete_list_ride',[UserController::class,'complete_list_ride']);
        
        
        Route::GET('user_schedule_ride',[UserController::class,'user_schedule_ride']);
        
        Route::POST('driver_end_trip',[UserController::class,'driver_end_trip']); 
        
        // Cupons
        Route::GET('get_cupon_list',[UserController::class,'get_cupon_list']);
        Route::POST('apply_cupon',[UserController::class,'apply_cupon']);
        
        
        
        Route::GET('driver_profile', [DriverController::class,'driver_profile']); 
       
        Route::POST('vehicle_details_update',[DriverController::class,'vehicle_details_update']); 
        Route::POST('driver_profile_register',[DriverController::class,'driver_profile_register']);
        Route::POST('bank_details',[DriverController::class,'bank_details']);
        Route::GET('get_bank_details',[DriverController::class,'get_bank_details']);
        
        Route::GET('all_booking_lists',[DriverController::class,'all_booking_lists']);
        
        
        Route::POST('audio_recording_store',[DriverController::class,'audio_recording_store']);
        Route::POST('recording_list',[DriverController::class,'recording_list']);
        Route::POST('video_recording_store',[DriverController::class,'video_recording_store']);
        Route::POST('video_list',[DriverController::class,'video_list']);
        
        Route::GET('scheduled_list_ride',[DriverController::class,'scheduled_list_ride']);
        Route::POST('scheduled_ride_details',[DriverController::class,'scheduled_ride_details']);
        Route::POST('chat_with_driver',[DriverController::class,'chat_with_driver']); 
         Route::POST('driver_request_booking',[DriverController::class,'driver_request_booking']);  
        
        
        Route::POST('driver_ride_start_verfiy_otp',[DriverController::class,'driver_ride_start_verfiy_otp']); // Not Implement
        Route::POST('driver_schedule_booking_cancel',[DriverController::class,'driver_schedule_booking_cancel']);
        Route::POST('driver_schedule_request_booking',[DriverController::class,'driver_schedule_request_booking']);
        Route::POST('recent_booking',[DriverController::class,'recent_booking']);
        
        
        Route::POST('driver_collect_amount',[DriverController::class,'driver_collect_amount']);
        
        Route::GET('driver_booking_lists',[DriverController::class,'driver_booking_lists']);  
        
        Route::GET('driver_rating',[DriverController::class,'driver_rating']);
        Route::POST('driver_user_rating',[DriverController::class,'driver_user_rating']);
        
        
        Route::POST('my_payment',[DriverController::class,'my_payment']);
        Route::POST('today_income',[DriverController::class,'today_income']); 
        Route::GET('payment_history',[DriverController::class,'payment_history']); 
        Route::post('onoff',[DriverController::class,'onoff']); 
        
        Route::POST('change_drop_location',[DriverController::class,'change_drop_location']);
        
        Route::POST('change_booking_status',[DriverController::class,'change_booking_status']);
        
        
        Route::GET('driver_online_offline_status',[DriverController::class,'driver_online_offline_status']);
        
        
        Route::GET('vehicle_fuel_types',[DriverController::class,'vehicle_fuel_types']);

        Route::GET('get_state_list',[DriverController::class,'get_state_list']);
        Route::POST('get_city_list',[DriverController::class,'get_city_list']);
        
        Route::POST('user_schedule_ride',[DriverController::class,'user_schedule_ride']);
        Route::GET('driver_payment_history',[DriverController::class,'driver_payment_history']);
        
        
        route::POST('user_add_money_in_wallet_by_qr', [UserController::class,'user_add_money_in_wallet_by_qr']);
        route::POST('check_wallet_paymet_status', [UserController::class,'check_wallet_paymet_status']);
        
        Route::POST('user_pay_driver_by_wallet',[UserController::class,'user_pay_driver_by_wallet']); // user Pay by wallet
        Route::POST('user_pay_driver_by_online',[UserController::class,'user_pay_driver_by_online']); // user Pay by wallet
        
        Route::GET('total_wallet_amount',[UserController::class,'total_wallet_amount']);
        
        
        Route::POST('invoice_details',[UserController::class,'invoice_details']);
        
        Route::POST('check_payment_status',[UserController::class,'check_payment_status']);
        
        Route::POST('composute_amount',[UserController::class,'composute_amount']);
        
        Route::POST('send_testing_notification',[UserController::class,'send_testing_notification']);
        
        Route::GET('get_all_rental_type',[UserController::class,'get_all_rental_type']);
          
   });
   
   Route::POST('check_reupload_document',[DriverController::class,'check_reupload_document']);
   Route::POST('upload_rejected_reupload_document',[DriverController::class,'upload_rejected_reupload_document']);
 
   Route::GET('generate_pdf',[DriverController::class,'generate_pdf']);
   Route::GET('download_invoice',[DriverController::class,'download_invoice']);
   
   Route::GET('generate_qr_code',[DriverController::class,'generate_qr_code']);
   Route::GET('collect_pay',[DriverController::class,'collect_pay']);
   
   route::POST('driver_online_payment', [UserController::class,'driver_online_payment'])->name('driver_online_payment');
   
   route::POST('call_back_transaction', [DriverController::class,'call_back_transaction'])->name('call_back_transaction');
 
   route::POST('user_received_money_authentication', [UserController::class,'user_received_money_authentication'])->name('money_add_confirm');
   route::POST('return_user_pay_online_with_bookingid', [UserController::class,'return_user_pay_online_with_bookingid'])->name('return_redirect_online_pay_with_booking_id'); // Bank QR Code Return Api
  
  
 
