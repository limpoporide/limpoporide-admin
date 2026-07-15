<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Session;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use App\Services\WebSocketService;

    if (!function_exists('profileImg')) {
        function profileImg(){
            return url('public/assets/img/defult.png');
        }
    }
    if (!function_exists('prodImg')) {
        function prodImg(){
            return url('public/assets/img/dummy_image.jpg');
        }
    }
    
    function get_booking_rating_review($booking_id, $user_id){
        $user_data = DB::table('booking_ratings')->select('rating', 'message')->where('booking_id', $booking_id)->where('user_id', $user_id)->first();
        return $user_data;
    }
    
    function send_otp_sms($mobile, $otp, $name){
        
        return true;
        
        $mobile = "91".$mobile;
        
        $otp_data = [
            'auth_key' => '3632XClN5jWohYogx79iGt',
            'mobiles' => $mobile,
            'sender' => 'RDDYRD',
            'message' => 'Hi '. $name .', your Readyrider verification code is '. $otp .'. Valid for 5 minutes. If you didn\'t request this, ignore it. SKYLITE TRADEINDIA TECHNOLOGY PVT LTD.',
            'route' => '4',
            'templateid' => '1207176345377505509',
        ];
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://sms.shreetripada.com/api/sendapi.php',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $otp_data,
        ));
        
        $response = curl_exec($curl);
        
        \Log::info('SMS Response : ');
        \Log::info($response);
        
        curl_close($curl);
        return $response;
        
    }
    
    function generate_weekly_payout(){
        
        date_default_timezone_set('Africa/Johannesburg');
        
        $vehicledata = DB::table('users')
                    ->where('type', 2)
                    ->orderBy('id', 'DESC')
                    ->get();
                    
            foreach($vehicledata as $res){
            
                $wallet_amount = get_wallet_balance($res->id);
                $minimum_payout = get_option_data('minimum_payout_generate');
                
                if($wallet_amount > 0){
                    
                    if($wallet_amount >= $minimum_payout){
                        
                        $wallet = [
                            "user_id" => $res->id,
                            "amount" => $wallet_amount,
                            "transaction_type" => 2,
                            "transaction_date" => date('Y-m-d'),
                            "payment_type" => "payout"
                        ];
                        
                        DB::table('my_wallet')->insert($wallet); 
                        
                        $payout = [
                            "payout_token" => "PAYOUT".rand(11111, 99999),
                            "user_id" => $res->id,
                            "amount" => $wallet_amount,
                            "payment_status" => "1",
                            "created_at" => date('Y-m-d H:i:s'),
                        ];
                        DB::table('weekly_payouts')->insert($payout); 
                        
                        $title = "Weekly Payout Generated Successfully!";
                        $message = "Your weekly payout of R" . $wallet_amount . " has been generated. The amount will be credited shortly.";
                        
                        send_firebase_notification($title, $message, $res->id);
                    
                }else{
                    
                    $title = "Weekly Payout Not Generated";
                    $message = "Your weekly payout was not generated because your total amount of R" . $wallet_amount . " is less than the minimum required amount of R100.";
                    
                    send_firebase_notification($title, $message, $res->id);
                    
                }
                

            }
            
            
        } 
        return true;
        
    }
    
    #Menu Permission
    if (!function_exists('checkAccesses')) {
       function checkAccesses($id){
         
         $amin_id = auth()->user()->id;
        $is_array= DB::table('assign_roles')->where('admin_id',$amin_id)->where('m_id',$id)->get()->toArray();
        
        if(count($is_array) > 0){
            $aaaa = $is_array[0]->role;
            
            if($aaaa == 1){
              return true;
            }
            if($aaaa == 2){
              return true;
            }
        }
          
        $amin_id = auth()->user()->id; 
        
        $admin =  DB::table('menu_permissions_assigns')->where('admin_id',$amin_id)->where('m_id',$id)->first();
 
        if($admin){
            if($admin->role == "1"){
               return false; 
            }
            
            return true;
        }else{
            return false; 
        }
       }
   }
  if (!function_exists('checkAccessesdit')) {
       function checkAccessesdit($id){
         
        $is_array= DB::table('admins')->where('id',auth()->user()->id)->get()->toArray();
        if(count($is_array) > 0){
            $aaaa = $is_array[0]->role;
            if($aaaa == 1){
              return true;
            }
        }
          
        $amin_id = auth()->user()->id; 
        
        $admin =  DB::table('assign_roles')->where('admin_id',$amin_id)->where('m_id',$id)->first();
       //print_r($admin);die;
        if($admin){
            if($admin->role == "0"){
               return false; 
            }
             if($admin->role == "1"){
               return false; 
            }
            if($admin->role == "2"){
               return true; 
            }
            return false;
        }else{
            return false; 
        }
       }
   }

    function get_option_all_data($key){
       $value = "";
       if($key != ""){
           $value = DB::table('options')->where('key',$key)->first();
       }
       return $value; 
    }
    

    if (!function_exists('imageCompres')) {
        function imageCompres($tmp_name, $name, $size, $path){
            // File info 
            $fileName = basename($name); 
            $imageUploadPath = $path ."/". $fileName; 
            $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION); 
    
            // Allow certain file formats 
            $allowTypes = array('png','jpg','jpeg', 'wap', 'webp'); 
            if(in_array($fileType, $allowTypes)){ 
                // Image temp source 
                $imageTemp = $tmp_name; 
                $imageSize = $size;
                 
                // Compress size and upload image 
                $compressedImage = compressImage($imageTemp, $imageUploadPath, 25); 
                 
                if($compressedImage){ 
                    $compressedImageSize = filesize($compressedImage);

                    $status = 'success'; 
                    return true;
                }else{ 
                    return true;
                } 
            }else{ 
                return false;
            } 
       }
       
       function compressImage($source, $destination, $quality) { 
            // Get image info 
            $imgInfo = getimagesize($source); 
            $mime = $imgInfo['mime']; 
             
            // Create a new image from file 
            switch($mime){ 
                case 'image/jpeg': 
                    $image = imagecreatefromjpeg($source); 
                    break; 
                case 'image/png': 
                    $image = imagecreatefrompng($source); 
                    break; 
                case 'image/gif': 
                    $image = imagecreatefromgif($source); 
                    break; 
                default: 
                    $image = imagecreatefromjpeg($source); 
            } 
             
            // Save image 
            imagejpeg($image, $destination, $quality); 
            // Return compressed image 
            return $destination; 
        } 
   }
   
   
    function check_all_documents_complete($id){
        if($id != ""){
            
            $data = DB::table('vehicle_registration')->where('id', $id)->first();
            
            if($data){
                
                if($data->idproof_front != "" && $data->idproof_back != "" && $data->dl_front != "" && $data->dl_back != "" 
                && $data->registration_certificate != "" && $data->vehicle_front != "" 
                && $data->vehicle_back != ""  && $data->vehicle_right != ""
                 && $data->vehicle_left != "" && $data->vehicle_inside != ""){
                    
                    DB::table('users')->where('id', $data->driver_id)->update(['is_profile_complete' => '1']);
                    
                }
                
            }
            
            
        }
        return true;
    }
    
    function check_driver_vehicle($driver_id){
        
        $data = DB::table('driver_details_basic_details as d')
                ->select('v.name as vehicle_name')
                ->leftjoin('vehicle_type as v','v.id','=','d.vehicle_type')
                ->where('d.driver_id', $driver_id)->first();
        
        $vehicle_name = "";        
        if($data){
            $vehicle_name = $data->vehicle_name;
        } 
        return $vehicle_name;
        
    }
    
    function check_documents_complete_percentage($id)
    {
        if ($id != "") {
            $data = DB::table('driver_details_basic_details')->where('id', $id)->first();
    
            if ($data) {

                $fields = [
                    'id_proof',
                    'id_proof2',
                    'photo1',
                    'photo2',
                    'certificate1',
                    'certificate2',
                    'vehicle_audit_photo1',
                    'vehicle_audit_photo2',
                    'driving_licence',
                    'driving_licence2',
                ];
    
                $total = count($fields);
                $filled = 0;
    
                foreach ($fields as $field) {
                    if (!empty($data->$field)) {
                        $filled++;
                    }
                }
    
                $percentage = ($filled / $total) * 100;
    
                return round($percentage, 2);
            }
        }
    
        return 0;
    }
   
   function get_driver_avg_rating($user_id){
       if($user_id != ""){
           
           $user_data = DB::table('booking_ratings as r')
                            ->leftjoin('bookings as b','b.booking_id','=','r.booking_id')
                            ->where('b.driver_id', $user_id)
                            ->where('r.user_id', '!=', $user_id)->sum('r.rating');
           
           $avarage_rating = 0;
           if($user_data != "0"){
               $total_count = get_driver_total_rating($user_id);
               $averageRating = $user_data / $total_count;
               $avarage_rating = number_format($averageRating, 1);
           }
           return $avarage_rating;
       }
   }
   
   function get_driver_total_rating($user_id){
       if($user_id != ""){
           $user_data = DB::table('booking_ratings as r')
                            ->leftjoin('bookings as b','b.booking_id','=','r.booking_id')
                            ->where('b.driver_id', $user_id)
                            ->where('r.user_id', '!=', $user_id)->count();
           return $user_data;
       }
   }
   
   function check_booking_rating_status($booking_id, $user_id){
       if($booking_id != ""){
           $user_data = DB::table('booking_ratings')->where('booking_id', $booking_id)->where('user_id', $user_id)->count();
           return ($user_data > 0)?"1":"0";
       }
   }
   
   function get_areapartner_total_earning($user_id){
       if($user_id != ""){
           $user_data = DB::table('list_subscription as l')
                        ->where('l.area_partner_id', $user_id)
                        ->sum('l.area_partner_amount');
           return number_format((float)$user_data, 2, '.', '');;
       }
   }
   
   function get_sponser_total_earning($user_id){
       if($user_id != ""){
           $user_data = DB::table('list_subscription as l')
                        ->where('l.service_partner_id', $user_id)
                        ->sum('l.service_partner_amount');
           return number_format((float)$user_data, 2, '.', '');;
       }
   }
   
   function get_total_distance($user_id){
       if($user_id != ""){
           $data_user = DB::table('users')->where('id', $user_id)->first();
           
           $user_data = DB::table('bookings');
           if(isset($data_user->type) && $data_user->type == "1"){
               $user_data->where('user_id', $user_id);
           }else{
               $user_data->where('driver_id', $user_id); 
           }
           $data = $user_data->where('booking_status', '5')->sum('distance');
           $distance = number_format((float)$data, 2, '.', '');
           return $distance;
       }
   }
   
   function get_option_data($key){
       $value = "";
       if($key != ""){
           $data = DB::table('options')->where('key',$key)->first();
           if($data){
               $value = $data->value;
           }
       }
       return $value; 
   }
   
    function set_option_data($key, $title, $value){
       if($key != ""){
           $data = DB::table('options')->where('key',$key)->first();
           if($data){
               DB::table('options')->where('id',$data->id)->update(['name'=>$title, 'value'=>$value]);
           }else{
               DB::table('options')->insert(['key'=>$key, 'name'=>$title ,'value'=>$value]);
           }
       }
       return true;
    }
    
    function set_option_all_data($key, $value){
       if($key != ""){
           $data = DB::table('options')->where('key',$key)->first();
           if($data){
               DB::table('options')->where('id',$data->id)->update(['value'=>$value]);
           }else{
               DB::table('options')->insert(['key'=>$key, 'value'=>$value]);
           }
       }
       return true;
    }
   
    function get_wallet_balance($user_id){
      if($user_id != ""){
          $wallet_data = DB::table('my_wallet')
                        ->select(DB::raw('sum(case when transaction_type = "1" then amount else -amount end) balance '))
                        ->where('user_id', $user_id)
                        ->where('status','1')
                        ->first();
          if($wallet_data){
              $wallet_amount = ($wallet_data->balance)??0;
          } else{
              $wallet_amount = "0";
          } 
          return number_format((float)$wallet_amount, 2, '.', '');
      }
    }
    
    function send_firebase_notification($title, $message, $user_id){
        if($user_id != ""){
            
            $user_data = DB::table('users')->where('id', $user_id)->first();
            
            if(isset($user_data) && $user_data->notification_status == "1"){
                
                $message = strip_tags($message);
                    
                $data = [
                    'user_id' => $user_id,
                    'title' => $title,
                    'message' => $message,
                ];
                    
                DB::table('notifications')->insert($data);  
                
                if(isset($user_data) && $user_data->device_token != ""){
    
                    $response = sendPushNotification($user_data->device_token, $title, $message, $data = [], $user_data->device_name);
                    return $response;
                }
                
            }

        }
        return true;
    }
    
    
   function calculace_vehicle_fare($vehicle_id, $city_name, $distance, $night_charge, $outstation, $trip_type, $picup_time){
        $data = [];
         
        if($vehicle_id != "" && $city_name != "" && $distance != ""){
           $get_city_data = DB::table('locations')->where('name', 'like', '%' . $city_name . '%')->first();
        //   dd($get_city_data);
           if($get_city_data){
               $fare_data = DB::table('manage_fare')->where('vehicle_id',$vehicle_id)->where('location_id',$get_city_data->id)->where('status', '1')->first();
                
               if($fare_data){
                    $price = ($outstation == "1" && $trip_type == "1")?$fare_data->outstation_rate:$fare_data->per_km;
                    
                    $price_per_km = $price;
                    // dd($fare_data);
                    if($trip_type == "2"){
                       $price = $price*2; 
                    }
                    if(($picup_time > $fare_data->start_peak) && ($picup_time < $fare_data->end_peak)){
                        $peak_price = $fare_data->peak_fare;
                    }else{
                        $peak_price = 0;
                    }
                    

                    // dd($peak_price);
                    $night_apply_charge = "0";
                    $total_fare = ($price*$distance)+($fare_data->base_fare)+$peak_price;
                    
                    if(isset($night_charge) && $night_charge == "Night"){
                        $night_fare = DB::table('vehicle_charger')->where('vehicle_name', $vehicle_id)->where('status', 1)->first();
                        if(isset($night_fare) && $fare_data->apply_night == "Yes"){
                            $total_fare = $total_fare+$night_fare->price;
                            $night_apply_charge = $night_fare->price;
                        }
                    }
                    $without_gst_fare = $total_fare;
                    $gst_percentage = get_option_data('gst_percentage');
                    if($gst_percentage){
                        $gst = ($total_fare*$gst_percentage)/100;
                        $total_fare = $total_fare+$gst;
                    }
                    
                    if($vehicle_id == "15"){
                        // dd($total_fare);
                    }
                    
                    $data['price'] =  $total_fare;
                    $data['price_charge'] = $price;
                    $data['base_fare'] = $fare_data->base_fare;
                    $data['wating'] =  $fare_data->waiting_charge;
                    $data['without_gst_fare'] =  $without_gst_fare;
                    $data['night_apply_charge'] = $night_apply_charge;
                    $data['gst_amount'] =  $gst;
                    $data['price_per_km'] =  $price_per_km;
               }
           }
        }
        return $data;
    }
    
    function getAddress($latitude, $longitude) {
        
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&key=".get_option_data('google_map_api_key');
    
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);
    
        $responseData = json_decode($response, true);
        
        $new_address = "";
        if ($responseData['status'] == 'OK') {
            $new_address = $responseData['results'][0]['formatted_address'];
        }
        return $new_address;
    }
    
    function is_night_ride($ride_time, $latitude, $longitude) {
        
        date_default_timezone_set('UTC');
        // Convert ride time to timestamp
        $timestamp = strtotime($ride_time);
        
        // Get ride hour
        $ride_hour = (int)date('H', $timestamp);

        // Check if the ride time falls explicitly between 21:00 and 06:00
        if ($ride_hour >= 21 || $ride_hour < 6) {
            return "Night";
        }else{
            return "Day";
        }
            
        // Get sunrise and sunset times
        // $sun_info = date_sun_info($timestamp, $latitude, $longitude);
    
        // $sunrise = $sun_info['sunrise'];
        // $sunset = $sun_info['sunset'];
    
        // // Compare ride time with sunrise and sunset
        // if ($timestamp >= $sunrise && $timestamp <= $sunset) {
        //     return "Day";
        // } else {
        //     return "Night";
        // }
    }
    
    function isNightChargeApplicable($pickupDatetime, $dropDatetime) {
        $pickup = new DateTime($pickupDatetime);
        $drop = new DateTime($dropDatetime);
    
        $pickupHour = (int) $pickup->format('H');
        $dropHour = (int) $drop->format('H');
        
        // Condition 1: Pickup before 21 and Drop after 6
        if ($pickupHour < 21 && $dropHour >= 6 && $drop > $pickup) {
            return countNightMinutes($pickup, $drop) >= 40 ? "Night" : "Day";
        }
        
        // Condition 2: Pickup after 21 and Drop before 6
        if ($pickupHour >= 21 || $pickupHour < 6) {
            if ($dropHour < 6 || $dropHour >= 0) {
                return "Night";
            }
        }
    
        // Condition 3: Pickup before 21 and Drop before 6 → check 40 min night time
        if ($pickupHour < 21 && $dropHour < 6) {
            return countNightMinutes($pickup, $drop) >= 40 ? "Night" : "Day";
        }
    
        // Condition 4: Pickup after 21 and Drop after 6 → check 40 min night time
        if ($pickupHour >= 21 && $dropHour >= 6) {
            return countNightMinutes($pickup, $drop) >= 40 ? "Night" : "Day";
        }
    
        // Default fallback - just in case
        return countNightMinutes($pickup, $drop) >= 40 ? "Night" : "Day";
    }

    
    // Helper function to count actual night minutes between two datetimes
    function countNightMinutes($start, $end) {
        $current = clone $start;
        $nightMinutes = 0;
    
        while ($current < $end) {
            $hour = (int) $current->format('H');
    
            if ($hour >= 21 || $hour < 6) {
                $nightMinutes++;
            }
    
            $current->modify('+1 minute');
        }
    
        return $nightMinutes;
    }


    function calculate_total_distance_time($pick_up_lat, $pick_up_long, $drop_lat, $drop_long){
        date_default_timezone_set("Asia/Calcutta"); 
             
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$pick_up_lat.",".$pick_up_long."&destinations=".$drop_lat.",".$drop_long."&departure_time=now&key=".get_option_data('google_map_api_key');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $response_a = json_decode($response, true);
        // dd($response_a);
        
        $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
        $time = $response_a['rows'][0]['elements'][0]['duration']['text'];    
        $distance = $response_a['rows'][0]['elements'][0]['distance']['value'];
        $time_val = $response_a['rows'][0]['elements'][0]['duration']['value'];
        
        $current_location = (isset($response_a['destination_addresses'][0]))?$response_a['destination_addresses'][0]:"";
        // dd($response_a);
       
        $dis = round($distance/1000, 2);
        $str = $time;
        $lenth = strlen($str);
         
        $net_mintue = $time_val/60;
        
        $data = [
            'dist' =>$dis, 
            'dist_value' =>$distance, 
            'lenth' =>$lenth,
            'time' =>$time,
            'time_val' =>$time_val,
            'current_location' =>$current_location,
        ];
        
        return $data;
    }
    
    function track_booking_record($booking_id, $latitude, $longitude, $current_location, $total_km, $total_time, $dist_value) {
        
        $data = [
            'booking_id' => $booking_id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current_location' => $current_location,
            'total_km' => $total_km,
            'total_km_value' => $dist_value,
            'total_time' => $total_time,
        ];
        
        DB::table('ride_track_location')->insert($data);
        
        return true;
    }
    
    function send_document_verification_notification($id){
        
         $vehicle_data_details =DB::table('driver_details_basic_details')->where('id',$id)->first(); 
         
        //  dd($vehicle_data_details);
         
         if($vehicle_data_details){
             
             
             
            if(($vehicle_data_details->doc_status1 == 1) && ($vehicle_data_details->doc_status2 == 1) && ($vehicle_data_details->doc_status3 == 1) && ($vehicle_data_details->doc_status4 == 1) && ($vehicle_data_details->doc_status5 == 1)){
                
                $title = "Document verification Successfully !";
                $message = "Your Document Verification successfully done. Now you can login !";
                
                send_firebase_notification($title, $message, $vehicle_data_details->driver_id);
                
            } 
            
         }
                
        
        
    }
    
     /////////////////////////////////  Websocket Setup Start ////////////////////////////////
    
    function send_websocket_notification($members, $title, $message, $slug){
        
        if(!empty($members)){
            
            $notification_body = [
                "title" => $title,
                "body" => $message,
                "notification_type" => $slug,
            ];
            
            $webSocketService = app(WebSocketService::class);
            
            return $webSocketService->sendNotification(explode(",", $members), $title, $message, $notification_body);
            
        }
        
    }
    
    function send_websocket_searchride_notification($members, $title, $message, $notification_body){
        
        if(!empty($members)){
            
            $webSocketService = app(WebSocketService::class);
            
            return $webSocketService->sendNotification(explode(",", $members), $title, $message, $notification_body);
            
        }
        
    }
    
    function send_websocket_driver_livelocation_notification($members, $title, $message, $notification_body){
        
        if(!empty($members)){
            
             \Log::info('send_websocket_driver_livelocation_notification');
            
            $webSocketService = app(WebSocketService::class);
            
            return $webSocketService->sendNotification(explode(",", $members), $title, $message, $notification_body);
            
        }
        
    }
    
    function get_upcoming_booking_strecture($schedule_id){
        
        $get_bookings = UserScheduleBooking::with('user:id,profile,name,mobile', 'payment_mode:id,name')
                ->where('id', $schedule_id)
                // ->where('booking_status', '1')
                ->whereDate('created_at', Carbon::today())
                ->orderBy('id', 'DESC')
                ->first();  
                
        $return_data = [];
        
        if($get_bookings){
            $profile = isset($get_bookings->user->image_url)?$get_bookings->user->image_url:profileImg();
            $payment_mode = ($get_bookings->payment_mode)??"Wallet";
            $schedule_later_booking = date('d M', strtotime($get_bookings->created_at))." | ".date('h:i A', strtotime($get_bookings->created_at));
            
            $user_avg_rating = get_user_avg_rating($get_bookings->user_id);
            $user_total_trips = get_user_completed_trips($get_bookings->user_id);
            
            $data = [
                "id" => $get_bookings->id,
                "booking_id" => $get_bookings->booking_id,
                "booking_type" => $get_bookings->booking_type,
                "user_id" => $get_bookings->user_id,
                "driver_id" => $get_bookings->driver_id,
                "vechicle_id" => $get_bookings->vechicle_id,
                "picup_location" => $get_bookings->picup_location,
                "picup_lat" => $get_bookings->picup_lat,
                "picup_long" => $get_bookings->picup_long,
                "drop_location" => $get_bookings->drop_location,
                "drop_lat" => $get_bookings->drop_lat,
                "drop_long" => $get_bookings->drop_long,
                "fare" => $get_bookings->fare,
                "rating" => $get_bookings->rating,
                "distance" => $get_bookings->distance,
                "payment_mode" => $payment_mode,
                "booking_status" => $get_bookings->booking_status,
                "payment_status" => $get_bookings->payment_status,
                "transaction_id" => $get_bookings->transaction_id,
                "invoice_number" => $get_bookings->invoice_number,
                "booking_date" => $get_bookings->booking_date,
                "booking_time" => $get_bookings->booking_time,
                "name_someone" => $get_bookings->name_someone,
                "age_someone" => $get_bookings->age_someone,
                "mobile_someone" => $get_bookings->mobile_someone,
                "email_someone" => $get_bookings->email_someone,
                "booking_for_type" => $get_bookings->booking_for_type,
                "ride_start_otp" => $get_bookings->ride_start_otp,
                "cancel_resion_id" => $get_bookings->cancel_resion_id,
                "taxi_type" => $get_bookings->taxi_type,
                "created_at" => date('Y-m-d H:i', strtotime($get_bookings->created_at)),
                "updated_at" =>  date('Y-m-d H:i', strtotime($get_bookings->updated_at)),
                "schedule_later_booking" => $schedule_later_booking,
                "user_avg_rating" => $user_avg_rating,
                "user_total_trips" => $user_total_trips, 
                "user_profile" => $profile, 
                "user_name" => $get_bookings->user->name??'', 
                "user_mobile" => $get_bookings->user->mobile??'', 
            ];
            
            $return_data = [
                "title" => "New Ride Request: Ready to Roll?",
                "body" => "You have a new ride request. Please proceed to the pickup location at " . $get_bookings->picup_location . " to pick up your passenger. Thank you for driving with Myride !",
                "notification_type" => "search_user_ride",
                "data" => $data
            ];
            
        }
        
        return $return_data;
        
    }
    
    
    /////////////////////////////////////////   Firebase push notification new implement /////////////////////////////////////
    
    function getAccessToken() {
        
        $jsonKeyFilePath = public_path('google-credentials.json');
        
        $client = new Client([
            'base_uri' => 'https://oauth2.googleapis.com',
        ]);
        
        $jsonKey = json_decode(file_get_contents($jsonKeyFilePath), true);
        
        $response = $client->post('/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => generateJWT($jsonKey),
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }
    
    
    function generateJWT(array $jsonKey) {
        $now = time();
        $exp = $now + 3600; // Token expiration time set to 1 hour
    
        $jwtHeader = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];
    
        $jwtPayload = [
            'iss' => $jsonKey['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $exp,
            'iat' => $now,
        ];
    
        $header = base64UrlEncode(json_encode($jwtHeader));
        $payload = base64UrlEncode(json_encode($jwtPayload));
    
        $signatureInput = $header . '.' . $payload;
        openssl_sign($signatureInput, $signature, openssl_pkey_get_private($jsonKey['private_key']), 'SHA256');
    
        return $signatureInput . '.' . base64UrlEncode($signature);
    }
    
    function base64UrlEncode($data) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
    
    function stringifyForFirebase($value) {
        return is_string($value) ? $value : json_encode($value);
    }
    
    function buildMessage($token, $title, $body, $payload, $device_type) {

        if($device_type == "ios"){

            $message = [
                'message' => [
                    'token' => $token,
                ],
            ];
            $message['message']['notification'] = [
                'title' => $title,
                'body' => "",
            ];
            $message['message']['apns'] = [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        // 'alert' => [
                        //     'title' => $title,
                        //     'body' => "",
                        // ],
                        'content-available' => 1,
                        'sound' => 'default',
                    ],
                    'data' => array_merge([
                        'body' => $body,
                        'title' => $title,
                    ], $payload),
                ],
            ];
            
        }else{

            $message = [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                    "android" => [
                        "notification" => [
                            "sound" => "ring_bell",
                            "channel_id" => "sound_channel"
                        ]
                    ],
                    "data" => [
                        "title" => $title,
                        "body" => $body,
                        "sound" => "ring_bell",
                        "android_channel_id" => "sound_channel"
                    ]
                ]
            ];
            
        }

        return $message;
    }
    
    function sendPushNotification($fcm_token, $title, $body, $data = [], $device_type = 'android') {
        $accessToken = getAccessToken();
    
        $url = 'https://fcm.googleapis.com/v1/projects/limpopo-project-f6403/messages:send'; // replace this (your-project-id) with your firebase project id
    
        $payload = [];
        if (isset($data) && !empty($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $payload[$key] = stringifyForFirebase($value);
            }
        }
    
        $message = buildMessage($fcm_token, $title, $body, $payload, $device_type);
        // dd($message);
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        
        $response = curl_exec($ch);
        
        \Log::info('firebase notification : ');
        \Log::info($response);
        
        curl_close($ch);
        // dd($response);
        return $response;
    }


    ///////////////////// Schedule Ride Notification //////////////////////////
    
    function send_today_schedule_ride_15min_before_notifications(){
        
        // \Log::info('send_today_schedule_ride_15min_before_notifications');
        
        date_default_timezone_set("Asia/Calcutta"); 
        
        $data = DB::table('bookings')
                ->where('booking_type', '2')
                ->where('booking_status', '2')
                ->where('booking_date', date('d-m-Y'))
                ->whereRaw("STR_TO_DATE(CONCAT(booking_date, ' ', booking_time), '%d-%m-%Y %H:%i') 
                            BETWEEN DATE_ADD(NOW(), INTERVAL 15 MINUTE) AND DATE_ADD(NOW(), INTERVAL 16 MINUTE)")
                ->get();
        
                    
        foreach($data as $res){
            
            $title = "Your Booking (". $res->booking_id .") is Coming Up Soon!";
            $message = "This is a friendly reminder that your booking is scheduled to start in 15 minutes. Please make sure you're prepared and ready.";
            
            send_firebase_notification($title, $message, $res->user_id);
            send_firebase_notification($title, $message, $res->driver_id);
        }        
        
    }
    
    function send_today_schedule_ride_notifications(){
        
        // \Log::info('send_today_schedule_ride_notifications');
        
        date_default_timezone_set("Asia/Calcutta"); 
        
        $data = DB::table('bookings')
                ->where('booking_type', '2')
                ->where('booking_status', '2')
                ->where('booking_date', date('d-m-Y'))
                ->where('booking_time', date('H:i'))
                ->get();
                    
        foreach($data as $res){
            
            $title = "Your Booking (#". $res->booking_id .") is Starting Now!";
            $message = "This is a reminder that your booking is scheduled to start right now at " . $res->booking_time . ". Please be ready.";
            
            send_firebase_notification($title, $message, $res->user_id);
            send_firebase_notification($title, $message, $res->driver_id);
        }        
        
    }
    
    
    
    
    
    