<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SubAdmincontroller;
use App\Http\Controllers\Website\UserController;
use App\Http\Controllers\Website\AstrologerController; 
use App\Http\Controllers\Website\WebController;

/**** For Admin Start ******/


        Route::match(['get','post'],'/admin',[AuthController::class,'login']);
        Route::match(['get','post'],'admin/login',[AuthController::class,'login']); 
        Route::match(['get','post'],'areapartner',[AuthController::class,'area_login']);
        Route::match(['get','post'],'areapartner/login',[AuthController::class,'area_login']); 
        Route::match(['get','post'],'sponsor',[AuthController::class,'service_login']); 
        Route::match(['get','post'],'sponsor/login',[AuthController::class,'service_login']);  
        Route::any('area_sponsor/my_profile',[AuthController::class,'area_sponsor_my_profile'])->name('area_sponsor_my_profile'); 
             
        Route::any('area_sponsor/profile',[AuthController::class,'area_sponsor_profile'])->name('area_sponsor_profile'); 
        
        Route::match(['get','post'], 'area_sponsor/profile/area_sponsor_update_password', [AuthController::class, 'area_sponsor_updatePassword']);   
        
        Route::match(['get','post'], 'area_sponsor_profile/update', [AuthController::class, 'area_sponsor_update_profile']); 
         
        Route::match(['get','post'], 'area_sponsor/area_sponsor_transaction', [AuthController::class, 'area_sponsor_transaction']); 
         
        Route::match(['get','post'], 'area_sponsor/area_partner_transaction', [AuthController::class, 'area_partner_transaction']);  
         
         
        Route::match(['get','post'],'area_logout',[AuthController::class,'area_logout']); 
        Route::match(['get','post'],'service_logout',[AuthController::class,'service_logout']); 
     
           
        Route::get('forgot_password',[AuthController::class,'forgot_password']);
        Route::POST('change_password',[AuthController::class,'change_password'])->name('change_password'); 
         
        Route::middleware(['prevent-back-history'])->group(function () { 
              
        Route::group(['namespace' => 'AreaPartner','prefix'=>'areapartner', 'middleware'=>['IsLoginArea']], function () {
            
        Route::match(['get','post'],'dashboard',[AreaController::class, 'index'])->name('dashboard');  
        
          /*Service Providers*/
         Route::get('sponsors',[AreaController::class,'sponsors']); 
         Route::any('/add_sponsors',[AreaController::class,'add_sponsors']);
         Route::any('/update_sponsors/{key}',[AreaController::class,'update_sponsors']);
         Route::post('/area_sponsor_status',[AreaController::class,'area_sponsor_status']); 
         Route::post('/area_sponsor_del',[AreaController::class,'area_sponsor_del']);   
         
            
        }); 
        
        Route::group(['namespace' => 'Sponsor','prefix'=>'sponsor', 'middleware'=>['IsLoginSponsor']], function () {
             
            Route::match(['get','post'],'dashboard',[ServiceController::class, 'index'])->name('dashboard');  
             
            Route::match(['get','post'],'/driver',[ServiceController::class, 'driver'])->name('sponsor.driver');
     
            Route::match(['get','post'],'/driver_status',[ServiceController::class, 'driver_status'])->name('driver.status');
            
            Route::match(['get','post'],'/sponsor_delete_driver',[ServiceController::class, 'sponsor_delete_driver']);
            
            Route::match(['get','post'],'/edittalkcharge',[ServiceController::class, 'edittalkcharge'])->name('sponsor.driverdata.edit_driver'); 
             
            Route::match(['get','post'],'/sponsor_approved_drivers_list',[ServiceController::class, 'sponsor_approved_drivers_list']);
            
            Route::match(['get','post'],'/sponsor_new_driver_list',[ServiceController::class, 'sponsor_new_driver_list']); 
            
            Route::match(['get','post'],'/drivers-existing/delete/{id}',[ServiceController::class, 'delete_driver']);
            
            Route::match(['get','post'],'/sponsor_edit_driver_details/{id}',[ServiceController::class, 'sponsor_edit_driver_details']); 
            
            Route::post('/stateOfCity',[ServiceController::class,'stateOfCity'])->name('stateOfCity');
                     
            Route::post('/stateOfCity_driver',[ServiceController::class,'stateOfCity_driver'])->name('stateOfCity_driver');
            
            Route::match(['get','post'],'/sponsor_update_driver_detail/',[ServiceController::class, 'sponsor_update_driver_detail']);
            
            Route::match(['get','post'],'/sponsor_driver_block_reason/',[ServiceController::class, 'sponsor_driver_block_reason']);
             
            Route::match(['get','post'],'/sponsor_driver_block/',[ServiceController::class, 'sponsor_driver_block']); 
            
            Route::match(['get','post'],'/sponsor_driver_details/{id}',[ServiceController::class, 'sponsor_driver_details']);  
            
         Route::match(['get','post'],'/sponsor_online_drivers_list',[ServiceController::class, 'sponsor_online_drivers_list']);
        Route::match(['get','post'],'/sponsor_offline_drivers_list',[ServiceController::class, 'sponsor_offline_drivers_list']);
        Route::match(['get','post'],'/sponsor_block_drivers_list',[ServiceController::class, 'sponsor_block_drivers_list']);
        
          Route::get('/sponsor_renew_licence',[ServiceController::class,'sponsor_renew_licence']);
        //sponsors
        
         Route::get('/area_partners',[AdminController::class,'area_partners']);
        
        });
        
        Route::group(['namespace' => 'Admin','prefix'=>'admin', 'middleware'=>['afterLoginAuth']], function () {
        
        Route::match(['get','post'],'dashboard',[AdminController::class, 'index'])->name('dashboard');
        Route::match(['get','post'],'dashboard',[AdminController::class, 'index']);
        Route::match(['get','post'],'logout',[AuthController::class,'logout']);
        Route::match(['get','post'], 'profile/update', [AuthController::class, 'update_profile']);
        Route::any('profile',[AuthController::class,'profile'])->name('profile');
        Route::match(['get','post'], 'profile/update_password', [AuthController::class, 'updatePassword']);
        
        Route::any('my_profile',[AuthController::class,'my_profile'])->name('my_profile'); 
     
        # User management
        Route::match(['get','post'],'/users',[AdminController::class, 'users'])->name('admin.users');
        Route::match(['get','post'],'/user_status',[AdminController::class, 'user_status'])->name('users.status');
        Route::match(['get','post'],'/users/user_status',[AdminController::class, 'user_status'])->name('admin.user_status');
        Route::match(['get','post'],'/users/user_delete',[AdminController::class, 'user_delete'])->name('users.user_delete');
        Route::match(['get','post'],'/delete_driver',[AdminController::class, 'delete_driver']);
        Route::match(['get','post'],'/user_details/{id}',[AdminController::class, 'user_details']);
        Route::match(['get','post'],'/new_user',[AdminController::class, 'new_user'])->name('admin.new_user');
        Route::match(['get','post'],'/Existing_user',[AdminController::class, 'Existing_user'])->name('admin.Existing_user');
        
       
        
        Route::match(['get','post'],'/sugeested/product',[AdminController::class, 'suggest_product'])->name('users.sugeested/product');
        Route::match(['get','post'],'product',[AdminController::class, 'products'])->name('admin.product');
        Route::match(['get','post'],'product.edit_category',[AdminController::class, 'edit_category'])->name('product.edit_category');
        
        
         /*Area Partners*/
         Route::get('/area_partners',[AdminController::class,'area_partners']);  
         Route::any('/add_area_partners',[AdminController::class,'add_area_partners']);
         Route::any('/update_area_partners/{key}',[AdminController::class,'update_area_partners']);
         Route::post('/area_partner_status',[AdminController::class,'area_partner_status']);
         Route::post('/area_partner_del',[AdminController::class,'area_partner_del']); 
         
         /*Service Providers*/
         Route::get('/service_partners',[AdminController::class,'service_partners']);
         Route::any('/add_service_partners',[AdminController::class,'add_service_partners']);
         Route::any('/update_service_partners/{key}',[AdminController::class,'update_service_partners']);
         Route::post('/area_service_status',[AdminController::class,'area_service_status']);
         Route::post('/area_service_del',[AdminController::class,'area_service_del']); 
         
         Route::any('/transactions',[AdminController::class,'transactions']);
           
         
         /*Admin Area Sponsor*/ 
         Route::get('/percentages',[AdminController::class,'percentages']);
         Route::any('/update_percentages/{key}',[AdminController::class,'update_percentages']);
         
         # expertises
        Route::match(['get','post'],'/expertises',[AdminController::class, 'expertises'])->name('admin.expertises');
        Route::match(['get','post'],'/addexperties',[AdminController::class, 'addexperties'])->name('admin.addexperties');
        Route::match(['get','post'],'/expert_status',[AdminController::class, 'expert_status'])->name('expertises.status');
        Route::match(['get','post'],'/editexpert',[AdminController::class, 'editexpert'])->name('admin.editexpert');
        # languages
        Route::match(['get','post'],'/languages',[AdminController::class, 'languages'])->name('admin.languages');
        Route::match(['get','post'],'/lang_status',[AdminController::class, 'lang_status'])->name('lang.status');
        Route::match(['get','post'],'/addlanguage',[AdminController::class, 'addlanguage'])->name('admin.addlanguage');
        Route::match(['get','post'],'/editlanguage',[AdminController::class, 'editlanguage'])->name('admin.editlang');
        #reason 
        Route::match(['get','post'],'/cancel/reason',[AdminController::class, 'cancelreason'])->name('admin.cancelreason');
        Route::match(['get','post'],'/cancel_status',[AdminController::class, 'cancel_status']); 
        Route::match(['get','post'],'/reason_del',[AdminController::class, 'reason_del']);   
        Route::match(['get','post'],'/add_reason',[AdminController::class, 'add_reason']);
        Route::match(['get','post'],'/update_reason/{id}',[AdminController::class, 'update_reason']);
        Route::match(['get','post'],'/save_update_reason',[AdminController::class, 'save_update_reason']);
         
        #radius  
        Route::match(['get','post'],'/radius',[AdminController::class, 'radius'])->name('admin.radius');
        Route::match(['get','post'],'/radius_status',[AdminController::class, 'radius_status'])->name('admin.radius_status');
        Route::match(['get','post'],'/editradius',[AdminController::class, 'editradius'])->name('admin.editradius');
        
        #Fare Management
        Route::match(['get','post'],'/fare_management',[AdminController::class, 'fare_management']);
        Route::match(['get','post'],'/add_manage_fare', [AdminController::class, 'add_manage_fare']);
        Route::match(['get','post'],'/edit_manage_fare/{id}', [AdminController::class, 'edit_manage_fare']);
        Route::match(['get','post'],'/update_manage_fare/{id}',[AdminController::class, 'update_manage_fare']); 
        Route::match(['get','post'],'/delete_manage_fare', [AdminController::class, 'delete_manage_fare']);
        
        #Location Management
        Route::match(['get','post'],'/location_management',[AdminController::class, 'location_management']);
        Route::match(['get','post'],'/add_location',[AdminController::class, 'add_location']);
        Route::match(['get','post'],'/edit_location/{id}',[AdminController::class, 'edit_location']);
        Route::match(['get','post'],'/update_location/{id}',[AdminController::class, 'update_location']); 
        Route::match(['get','post'],'/delete_location', [AdminController::class, 'delete_location']);
        
        # skill
        Route::match(['get','post'],'/skill',[AdminController::class, 'skill'])->name('admin.skill');
        Route::match(['get','post'],'/skill_status',[AdminController::class, 'skill_status'])->name('skill.status');
        Route::match(['get','post'],'/addskill',[AdminController::class, 'addskill'])->name('admin.addskill');
        Route::match(['get','post'],'/editskill',[AdminController::class, 'editskill'])->name('admin.editskil');
        #banner
        Route::match(['get','post'],'/banner',[AdminController::class, 'banner'])->name('admin.banner');
        Route::match(['get','post'],'/banner_status',[AdminController::class, 'banner_status'])->name('admin.banner_status');
        Route::match(['get','post'],'/edit_banner',[AdminController::class, 'edit_banner'])->name('edit_banner');
        # customer_concern
        Route::match(['get','post'],'/customer_concern',[AdminController::class, 'customer_concern'])->name('admin.customer_concern');
        Route::match(['get','post'],'/customer_concern',[AdminController::class, 'customer_concern'])->name('admin.customer_concern');
        # subadmin
        Route::match(['get','post'],'/subadmin',[AdminController::class, 'subadmin'])->name('admin.subadmin');
        Route::match(['get','post'],'/subadminstatus',[AdminController::class, 'subadminstatus'])->name('admin.subadminstatus');
        Route::match(['get','post'],'/addSubadmin',[AdminController::class, 'addSubadmin'])->name('admin.addSubadmin');
        Route::match(['get','post'],'/editSubadmin',[AdminController::class, 'editSubadmin'])->name('admin.editSubadmin');
        Route::match(['get','post'],'/subadmin_delete',[AdminController::class, 'subadmin_delete'])->name('admin.subadmin_delete');
        Route::match(['get','post'],'/subadmin/stauts',[AdminController::class, 'subadminstatus_status']); 
        
        // Route::match(['get','post'],'/assignrole/{id}',[AdminController::class, 'assignrole']);
        /*subadmin permission*/
        Route::get('sub-admin/assign-role/{id}',[SubAdmincontroller::class,'assign_role']);
        Route::post('sub-admin/update-role',[SubAdmincontroller::class,'update_role']);
        Route::get('sub-admin/assign-roles/{id}',[SubAdmincontroller::class,'assign_roles']);
        Route::post('sub-admin/update-roles',[SubAdmincontroller::class,'update_roles']);
        
        
        # products
        Route::match(['get','post'],'/products',[AdminController::class, 'products'])->name('admin.products');
        Route::match(['get','post'],'/product_add',[AdminController::class, 'product_add'])->name('admin.product_add');
        Route::match(['get','post'],'/edit_product',[AdminController::class, 'edit_product'])->name('admin.edit_product');
        Route::match(['get','post'],'/product_status',[AdminController::class, 'product_status'])->name('admin.product_status');
        # categories
        Route::match(['get','post'],'/category',[AdminController::class, 'category'])->name('admin.category');
        Route::match(['get','post'],'/addCategory',[AdminController::class, 'addCategory'])->name('admin.addCategory');
        Route::match(['get','post'],'/category_status',[AdminController::class, 'category_status'])->name('category.status');
        Route::match(['get','post'],'/edit_category',[AdminController::class, 'edit_category'])->name('category.edit_category');
        Route::match(['get','post'],'/category_delete',[AdminController::class, 'category_delete'])->name('category_delete');
        # coupan
        Route::match(['get','post'],'/coupan',[AdminController::class, 'coupan'])->name('admin.coupan');
        Route::match(['get','post'],'/coupantatus',[AdminController::class, 'coupantatus'])->name('admin.coupantatus');
        Route::match(['get','post'],'/addcoupan',[AdminController::class, 'addcoupan'])->name('admin.addcoupan');
        Route::match(['get','post'],'/editcaupan',[AdminController::class, 'editcaupan'])->name('admin.editcaupan');
        # astrologers
        Route::match(['get','post'],'/driver',[AdminController::class, 'driver'])->name('admin.driver');
        Route::match(['get','post'],'/driver_status',[AdminController::class, 'driver_status'])->name('driver.status');
        Route::match(['get','post'],'/astro_bank',[AdminController::class, 'astro_bank'])->name('admin.astro_bank');
        Route::match(['get','post'],'/edittalkcharge',[AdminController::class, 'edittalkcharge'])->name('admin.driverdata.edit_driver');
        
        
        
        # order
        Route::match(['get','post'],'/order',[AdminController::class, 'order'])->name('admin.order');
        Route::match(['get','post'],'/edit_order',[AdminController::class, 'edit_order'])->name('admin.edit_order');
        # rating
        Route::match(['get','post'],'/rating',[AdminController::class, 'rating'])->name('admin.rating');
        Route::match(['get','post'],'/delete_rating',[AdminController::class, 'delete_rating'])->name('delete_rating'); 
        # cms
        Route::match(['get','post'],'/about_us',[AdminController::class, 'about_us'])->name('admin.about_us');
        Route::match(['get','post'],'/about_us_status',[AdminController::class, 'about_us_status'])->name('admin.about_us_status');
        Route::match(['get','post'],'/editabout_us',[AdminController::class, 'editabout_us'])->name('admin.editaboutus');
        Route::match(['get','post'],'/faq',[AdminController::class, 'faq'])->name('admin.faq');
        Route::match(['get','post'],'/editfaq',[AdminController::class, 'editfaq'])->name('admin.editfaq');
        Route::match(['get','post'],'/addfaq',[AdminController::class, 'addfaq'])->name('admin.addfaq');
        Route::match(['get','post'],'/faq_delete',[AdminController::class, 'faq_delete'])->name('admin.faq_delete');
        
        Route::match(['get','post'],'/customer_support',[AdminController::class, 'customer_support'])->name('admin.customer_support');
        Route::match(['get','post'],'/edit_customer_support',[AdminController::class, 'edit_customer_support'])->name('admin.edit_customer_support');
        
        
        Route::match(['get','post'],'/approved_drivers_list',[AdminController::class, 'approved_drivers_list']);
        Route::match(['get','post'],'/new_driver_list',[AdminController::class, 'new_driver_list']);
        Route::match(['get','post'],'/drivers-existing/delete/{id}',[AdminController::class, 'delete_driver']);
        Route::match(['get','post'],'/edit_driver_details/{id}',[AdminController::class, 'edit_driver_details']);
        Route::post('/stateOfCity',[AdminController::class,'stateOfCity'])->name('stateOfCity');
         
        Route::post('/stateOfCity_driver',[AdminController::class,'stateOfCity_driver'])->name('stateOfCity_driver');
        Route::match(['get','post'],'/update_driver_detail/',[AdminController::class, 'update_driver_detail']);
        Route::match(['get','post'],'/driver_block_reason/',[AdminController::class, 'driver_block_reason']);
        Route::match(['get','post'],'/driver_block/',[AdminController::class, 'driver_block']);
        Route::match(['get','post'],'/driver_details/{id}',[AdminController::class, 'driver_details']); 
        
        Route::match(['get','post'],'/manage_charges',[AdminController::class, 'manage_charges']);
        Route::match(['get','post'],'/add_manage_charges',[AdminController::class, 'add_manage_charges']);
         Route::match(['get','post'],'/vehicle_type',[AdminController::class, 'vehicle_type']);
         Route::match(['get','post'],'/add_vehicle_type',[AdminController::class, 'add_vehicle_type']);
         Route::match(['get','post'],'/update_vehicle_type/{id}',[AdminController::class, 'update_vehicle_type']);
         Route::match(['get','post'],'/save_update_vehicle_type',[AdminController::class, 'save_update_vehicle_type']);
         Route::match(['get','post'],'/vehicle_status',[AdminController::class, 'vehicle_status']); 
         Route::match(['get','post'],'/vehicle_del',[AdminController::class, 'vehicle_del']);
         
         /*vehicle models*/
         Route::get('/vehiclemodels',[AdminController::class,'vehiclemodels']);
         Route::any('/vehiclemodels_add',[AdminController::class,'vehiclemodels_add']);
         Route::any('/update_vehiclemodel/{key}',[AdminController::class,'update_vehiclemodel']);
          Route::post('/vehiclemodels_status',[AdminController::class,'vehiclemodels_status']);
         Route::post('/vehiclemodel_del',[AdminController::class,'vehiclemodel_del']);
         
         /*vehicle varients*/
         Route::get('vehiclevarients_list',[AdminController::class,'vehiclevarients_list']);
         Route::post('/vehiclevarients_add',[AdminController::class,'vehiclevarients_add']);
         Route::post('/vehiclevarient_del',[AdminController::class,'vehiclevarient_del']);
         Route::any('update_vehiclevarient/{key}',[AdminController::class,'update_vehiclevarient']);
         
           /* All type vehicles */
         Route::match(['get','post'],'/vehicle_all_types',[AdminController::class, 'vehicle_all_types']);
         Route::match(['get','post'],'/add_type_vehicle',[AdminController::class, 'add_type_vehicle']);
         Route::match(['get','post'],'/delete_type_vehicle',[AdminController::class, 'delete_type_vehicle']);
         Route::match(['get','post'],'/change_vehicle_type_status',[AdminController::class, 'change_vehicle_type_status']);
        
        /*Taxi Type*/
        Route::match(['get','post'],'/taxi_type',[AdminController::class,'taxi_type']);
        Route::match(['get','post'],'/taxi_status',[AdminController::class, 'taxi_status']);
         Route::match(['get','post'],'/taxi_del',[AdminController::class, 'taxi_del']);
        Route::match(['get','post'],'/add_taxi_type',[AdminController::class, 'add_taxi_type']);
        Route::match(['get','post'],'/update_taxi_type/{id}',[AdminController::class, 'update_taxi_type']);
        Route::match(['get','post'],'/save_update_taxi_type',[AdminController::class, 'save_update_taxi_type']);
        
        /*Offer List*/
        Route::match(['get','post'],'/offer_list',[AdminController::class, 'offer_list']);
        Route::match(['get','post'],'/add_offer_type',[AdminController::class, 'add_offer_type']);
        Route::match(['get','post'],'/update_offer/{id}',[AdminController::class, 'update_offer']);
        Route::match(['get','post'],'/offer_status',[AdminController::class, 'offer_status']);
        Route::match(['get','post'],'/save_update_offer_data',[AdminController::class, 'save_update_offer_data']);
        Route::match(['get','post'],'/offer_delete',[AdminController::class, 'offer_delete']);
        
        /*Send Notification*/
        Route::get('send_notification',[AdminController::class,'send_notification']);
        Route::any('us_send_notifications',[AdminController::class,'us_send_notifications']);
        
        // ************************************add subscription******************************************//
        Route::match(['get','post'],'/add_subscription',[AdminController::class, 'add_subscription']);
        Route::match(['get','post'],'/listsubscription',[AdminController::class, 'listsubscription']);
        Route::match(['get','post'],'/edit_subscription/{id}',[AdminController::class, 'edit_subscription']);
        Route::match(['get','post'],'/banner-existing/delete/{id}',[AdminController::class, 'delete_subscription']);
        
        /*Night Charge*/
        
        Route::match(['get','post'],'/vehicle_charger',[AdminController::class, 'vehicle_charger']);
        Route::match(['get','post'],'/add_vehicle_charger',[AdminController::class, 'add_vehicle_charger']);
        Route::match(['get','post'],'/vehicle_statu',[AdminController::class, 'vehicle_statu']);
        Route::match(['get','post'],'/update_charge/{id}',[AdminController::class, 'update_charge']);
        Route::match(['get','post'],'/save_update_change',[AdminController::class, 'save_update_change']);
        Route::match(['get','post'],'/vehiclcharger_del',[AdminController::class, 'vehiclcharger_del']);
        
        /*licence status check*/
        Route::match(['get','post'],'/driver_licence_status',[AdminController::class, 'driver_licence_status']);
        Route::match(['get','post'],'/driver_idproof_status',[AdminController::class, 'driver_idproof_status']);
        Route::match(['get','post'],'/driver_rc_status',[AdminController::class, 'driver_rc_status']);
        Route::match(['get','post'],'/driver_pollution_status',[AdminController::class, 'driver_pollution_status']);
        Route::match(['get','post'],'/driver_audit_status',[AdminController::class, 'driver_audit_status']);
        
        /*Booking Complete*/
        Route::match(['get','post'],'/complete_booking',[AdminController::class,'complete_booking']);
        Route::match(['get','post'],'/com_booking_detail/{id}',[AdminController::class, 'com_booking_detail']);
        Route::match(['get','post'],'/com_booking/delete/{id}',[AdminController::class, 'delete_booking']);
        Route::match(['get','post'],'/booking_delete',[AdminController::class, 'booking_delete']);
        
        /*Booking Cancel*/
        Route::match(['get','post'],'/cancel_booking',[AdminController::class,'cancel_booking']);
        Route::match(['get','post'],'/can_booking/delete/{id}',[AdminController::class, 'delete_can_booking']);
        
        /*Booking Accept*/
        Route::match(['get','post'],'/schedule_booking',[AdminController::class,'schedule_booking']);
        Route::match(['get','post'],'/acc_booking/delete/{id}',[AdminController::class, 'delete_acc_booking']);
        
        /*Booking Cancel User change*/
        Route::match(['get','post'],'/cancel_charge',[AdminController::class,'cancel_charge']);
        Route::match(['get','post'],'/cancel_ride_detail/{id}',[AdminController::class, 'cancel_ride_detail']);
        
        /*Bank Verification*/
        Route::get('/bank_verification',[AdminController::class,'bank_verification']);
        
        Route::get('/payout_history/{id}',[AdminController::class,'payout_history'])->name('payout_history');
         
        
        Route::post('/payout_amount/{id}',[AdminController::class,'payout_amount'])->name('payout_amount');
        
        Route::match(['get','post'],'/bank_status',[AdminController::class, 'bank_status']);
         
        /*licence Notification*/
        Route::get('/renew_licence',[AdminController::class,'renew_licence']);
        
        /*notification List*/
        Route::get('/notification_list',[AdminController::class,'notification_list']);
        Route::get('/cash_driver',[AdminController::class,'cash_driver']);
        
        
        
        
        Route::match(['get','post'],'/send_notification_driver',[AdminController::class,'send_notification_driver']);
        
        Route::match(['get','post'],'/licence_expire_status',[AdminController::class,'licence_expire_status']);
        
        /*management state*/
        Route::match(['get','post'],'/state_management',[AdminController::class,'state_management']);
        Route::match(['get','post'],'/state_status',[AdminController::class, 'state_status']);
         Route::match(['get','post'],'/state_del',[AdminController::class, 'state_del']);
        Route::match(['get','post'],'/add_state',[AdminController::class, 'add_state']);
        Route::match(['get','post'],'/update_state/{id}',[AdminController::class, 'update_state']);
        Route::match(['get','post'],'/save_update_state',[AdminController::class, 'save_update_state']);
        
        /*management city*/
        // Route::match(['get','post'],'/city_management',[AdminController::class,'city_management']);
        /*management city*/
        Route::match(['get','post'],'/city_management',[AdminController::class,'city_management']);
        Route::match(['get','post'],'/city_status',[AdminController::class, 'city_status']);
        Route::match(['get','post'],'/city_del',[AdminController::class, 'city_del']);
        Route::match(['get','post'],'/add_city',[AdminController::class, 'add_city']);
        Route::match(['get','post'],'/update_city/{id}',[AdminController::class, 'update_city']);
        Route::match(['get','post'],'/save_update_city',[AdminController::class, 'save_update_city']);
        /*management city end*/
        
        Route::match(['get','post'],'/total_no_vehicle',[AdminController::class, 'total_no_vehicle']);
        
        /*withdrawal_request payment*/
        Route::get('/withdrawal_request',[AdminController::class,'withdrawal_request']);
        Route::get('/view-histroy/{id}',[AdminController::class,'view_histroy']);
        Route::match(['get','post'],'edit-request/{id}',[AdminController::class,'edit_request']);
        Route::get('/withdrawal_action/{id}',[AdminController::class,'withdrawal_action']);
        Route::post('/withdrawal_action_save/{id}',[AdminController::class,'withdrawal_action_save']);
        Route::get('/accept',[AdminController::class,'accept_request']);
        Route::get('/diened',[AdminController::class,'diened_request']);
        Route::get('/pending',[AdminController::class,'pending_request']);
          
        Route::get('/complete/{id}',[AdminController::class,'complete']);
        Route::get('/cancel/{id}',[AdminController::class,'cancel']);
        
        Route::match(['get','post'],'/profile_image_id',[AdminController::class, 'profile_image_id']);
        
        
        Route::match(['get','post'],'/rider_details',[AdminController::class, 'rider_details']);
        Route::match(['get','post'],'/rider_status',[AdminController::class, 'rider_status']);
        Route::match(['get','post'],'/rider_del',[AdminController::class, 'rider_del']);
        
        Route::match(['get','post'],'/emergency_contact',[AdminController::class, 'emergency_contact']);
        Route::match(['get','post'],'/emergency_status',[AdminController::class, 'emergency_status']);
        Route::match(['get','post'],'/emergency_del',[AdminController::class, 'emergency_del']);
        
        Route::match(['get','post'],'/refund_policy',[AdminController::class, 'refund_policy'])->name('admin.refund_policy');
        Route::match(['get','post'],'/addrefund_policy',[AdminController::class, 'addrefund_policy'])->name('admin.addrefund_policy');
        Route::match(['get','post'],'/editrefund_policy',[AdminController::class, 'editrefund_policy'])->name('admin.editrefund_policy');
        Route::match(['get','post'],'/refund_policy_delete',[AdminController::class, 'refund_policy_delete'])->name('admin.refund_policy_delete');
        
        Route::match(['get','post'],'/online_drivers_list',[AdminController::class, 'online_drivers_list']);
        Route::match(['get','post'],'/offline_drivers_list',[AdminController::class, 'offline_drivers_list']);
        Route::match(['get','post'],'/block_drivers_list',[AdminController::class, 'block_drivers_list']);
        
        
        /*Safety List*/ 
        Route::match(['get','post'],'/safety_issue',[AdminController::class, 'safety_issue']);
        Route::match(['get','post'],'/add_safety',[AdminController::class, 'add_safety']);
        Route::match(['get','post'],'/safety_status',[AdminController::class, 'safety_status']);
        Route::match(['get','post'],'/safety_delete',[AdminController::class, 'safety_delete']);
        Route::match(['get','post'],'/update_safety/{id}',[AdminController::class, 'update_safety']);
        Route::match(['get','post'],'/save_safety_data',[AdminController::class, 'save_safety_data']);
         
        /*Dodont List*/
        Route::match(['get','post'],'/dodont_list',[AdminController::class, 'dodont_list']);
        Route::match(['get','post'],'/add_dodont',[AdminController::class, 'add_dodont']);
        Route::match(['get','post'],'/dodont_status',[AdminController::class, 'dodont_status']);
        Route::match(['get','post'],'/dodont_delete',[AdminController::class, 'dodont_delete']);
        Route::match(['get','post'],'/update_dodont/{id}',[AdminController::class, 'update_dodont']);
        Route::match(['get','post'],'/save_dodont_data',[AdminController::class, 'save_dodont_data']);
        
        Route::match(['get','post'],'/revenue_booking',[AdminController::class, 'revenue_booking']);
        Route::match(['get','post'],'/complete_revenue',[AdminController::class, 'complete_revenue']);
        Route::match(['get','post'],'/cancel_revenue',[AdminController::class, 'cancel_revenue']);
        Route::match(['get','post'],'/all_revenue',[AdminController::class, 'all_revenue']);
        
        Route::post('/withdrawal_status',[AdminController::class,'withdrawal_status']);
         
        Route::get('/support_ticket',[AdminController::class,'support_ticket'])->name('/support_ticket');
        Route::get('/viewsupport_ticket/{id}',[AdminController::class,'viewsupport_ticket'])->name('viewsupport_ticket');
         
        Route::get('/replysupport_ticket/{id}',[AdminController::class,'replysupport_ticket'])->name('replysupport_ticket'); 
         
        Route::post('/reply_ticket/{id}',[AdminController::class,'reply_ticket'])->name('reply_ticket');
        Route::post('/ticket_close/{id}',[AdminController::class,'ticket_close'])->name('ticket_close');  
        
        });
    
        });
    
   