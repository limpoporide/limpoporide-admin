<?php

//driver_complete_ride Api change 13/11/2024

$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$booking_info->picup_lat.",".$booking_info->picup_long."&destinations=".$driver_latitude.",".$driver_longitude."&departure_time=now&key=".get_option_data('google_map_api_key');

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
            $distance = $dist = $time = $time_val = "0";
            if(isset($response_a['rows'][0]['elements'][0]['distance']['text'])){
                $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            }
            if(isset($response_a['rows'][0]['elements'][0]['duration']['text'])){
                $time = $response_a['rows'][0]['elements'][0]['duration']['text'];
            }
            if(isset($response_a['rows'][0]['elements'][0]['distance']['value'])){
                $distance = $response_a['rows'][0]['elements'][0]['distance']['value'];
            }
            if(isset($response_a['rows'][0]['elements'][0]['duration']['value'])){
                $time_val = $response_a['rows'][0]['elements'][0]['duration']['value'];
            }
           
            $dis = round($distance/1000, 2);
            $str = $time;
            $lenth = strlen($str);
             
            $net_mintue = $time_val/60;
            // dd(gmdate("H:i:s", $time_val));
            
            if($dis>0){
                $total_km = $dis;
            }else{
                $total_km = 1; 
            }

            
            $latlong = $booking_info->picup_lat.",".$booking_info->picup_long;
            // dd($latlong);
            $city_name = $this->get_city_name($latlong);
            
            $dlatlong = $driver_latitude.",".$driver_longitude;
            $drop_city_name = $this->get_city_name($dlatlong);
            
            if($city_name == $drop_city_name){
                $outstation = "0";
            }else{
                $outstation = "1";
            }
            
            $get_vehicle_data = DB::table('vehicle_type')->where('id', $booking_info->vechicle_id)->first();
            
            $picup_date = ($booking_info->booking_date != "" && $booking_info->booking_date != "Invalid date")? $booking_info->booking_date : date('d-m-Y');
            $picup_time = ($booking_info->booking_time)??date('h:i:s');
            $ride_time = date("Y-m-d", strtotime($picup_date)).' '.$picup_time.':00'; 
            $night_charge = is_night_ride($ride_time, $booking_info->picup_lat, $booking_info->picup_long);
            $trip_type = $booking_info->trip_type;
            $data_fare = calculace_vehicle_fare($booking_info->vechicle_id, $city_name, $dis, $night_charge, $outstation, $trip_type, $picup_time);
             
            $price = ($outstation == "1" && $trip_type == "1")?$get_vehicle_data->outstation_rate:$get_vehicle_data->price; 
            if($trip_type == "2"){
               $price = $price*2; 
            }
            
            if(isset($data_fare['price']) && $data_fare['price'] != ""){
                $fare = $data_fare['price'];
            }else{
                $fare = ($total_km*$price)+$get_vehicle_data->base_fare;
                $gst_percentage = get_option_data('gst_percentage');
                if(isset($night_charge) && $night_charge == "Night"){
                    $night_fare = DB::table('vehicle_charger')->where('vehicle_name', $booking_info->vechicle_id)->where('status', 1)->first();
                    if($night_fare){
                        $fare = $fare+$night_fare->price;
                    }
                }
                if($gst_percentage){
                    $gst = ($fare*$gst_percentage)/100;
                    $fare = $fare+$gst;
                }
            }
            $new_fare = number_format((float)$fare, 2, '.', '');  