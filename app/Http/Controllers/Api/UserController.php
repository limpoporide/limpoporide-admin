<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\TimeSlot;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use GuzzleHttp\Client;
use DateTime;
use DateInterval;

class UserController extends Controller
{
    
    
    public function confirm_booking(Request $request)
    {
      $typevalidate=Validator::make($request->all(),[   
                'pick_up_location'=>'required',
                'pick_up_lat'=>'required',
                'pick_up_long'=>'required',
                'drop_location'=>'required',
                'drop_lat'=>'required',
                'drop_long'=>'required',
                'veh_id'=>'required',
                'fare' => 'required',
                'distance'=>'required',
                'booking_type'=>'required',
              ]);
          try {
            if($typevalidate->fails())
            {
                $arr['status']=0;
                $arr['message']="Validation Failed";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $user_id = Auth::id();
           
            $chk_booking = DB::table('bookings')->where('booking_type',1)->where('user_id',$user_id)
                                 ->whereIn('booking_status', [1,2,5])
                                 ->first();
             
            if(!empty($chk_booking)){
                $arr['status']=0;
                $arr['message']="Sorry!! Your Ride is Already Booked";
                $arr['data']=NULL;
                 return response()->json($arr,200);
            }
            
            
             $user_id = $request->user_id;
             $booking_id = 'CARMY'.rand(10000,100000);
             $invoice_number = 'Invoice'.rand(10000,100000);
              
              $data['user_id'] = Auth::id();
              $data['vechicle_id'] = $request->veh_id;
              $data['booking_id'] = $booking_id;
              $data['picup_location'] = $request->pick_up_location;
              $data['picup_lat'] = $request->pick_up_lat;
              $data['picup_long'] = $request->pick_up_long;
              $data['drop_location'] = $request->drop_location;
              $data['drop_lat'] = $request->drop_lat;
              $data['drop_long'] = $request->drop_long;
              $data['fare'] = $request->fare;
              $data['booking_date'] = $request->booking_date;
              $data['booking_time'] = $request->booking_time;
              $data['booking_status'] = 1;
              $data['name_someone'] = $request->name_someone;
              $data['age_someone'] = $request->age_someone;
              $data['mobile_someone'] = $request->mobile_someone;
              $data['email_someone'] = $request->email_someone;
              $data['booking_for_type'] = $request->booking_for_type;
              $data['taxi_type'] = $request->taxi_type;
              $data['booking_type'] = $request->booking_type;
            
              $data['invoice_number'] = $invoice_number;
              $data['distance']=$request->distance;
                    
                      $data_inst = DB::table('bookings')->insert($data);
                             $data_inst =  DB::getPdo()->lastInsertId();
              
                $get_data = DB::table('bookings')->where('id',$data_inst)->first();
                
                $radius_distance = DB::table('radius')->where('id',1)->first();
                $radius_range =$radius_distance->radius_range??0;
        
                 $get_driver = DB::table('users')
                                ->select('users.*',DB::raw("round(6371 * acos(cos(radians(" . $request->pick_up_lat . ")) 
                                * cos(radians(users.latitude)) 
                                * cos(radians(users.longtitude) - radians(" . $request->pick_up_long . ")) 
                                + sin(radians(" .$request->pick_up_lat. ")) 
                                * sin(radians(users.latitude)))) AS distance"))
                                 ->having("distance", "<", $radius_range)
                                //->where('users.type',2)
                                ->where('users.driveractive_status',1)->get(); 
                   
                 
            if(!empty($get_data)){
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$get_data;
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
         }
         catch(\Exception $e){
             $arr['status']=0;
             $arr['message']=$e->getMessage();
             $arr['data']=NULL;
         }
         return response()->json($arr,200); 
            
        
    }
    
    public function latest_version_popup_show(Request $request){
        $arr['status']=1;
        $arr['message']='success';
        $arr['latest_version_popup_show']=get_option_data('latest_version_popup_show');
        return response()->json($arr,200); 
    }
    
    public function get_apk_latest_version(Request $request){
        $arr['status']=1;
        $arr['message']='success';
        $arr['apk_latest_version']=get_option_data('apk_current_version');
        return response()->json($arr,200); 
    }
    
    public function get_driver_apk_latest_version(Request $request){
        $arr['status']=1;
        $arr['message']='success';
        $arr['apk_latest_version']=get_option_data('apk_driver_current_version');
        return response()->json($arr,200); 
    }
    
    public function get_ios_user_latest_version(Request $request){
        $arr['status']=1;
        $arr['message']='success';
        $arr['apk_latest_version']=get_option_data('apk_current_version_ios');
        return response()->json($arr,200); 
    }
    
    public function get_ios_driver_latest_version(Request $request){
        $arr['status']=1;
        $arr['message']='success';
        $arr['apk_latest_version']=get_option_data('apk_driver_current_version_ios');
        return response()->json($arr,200); 
    }
    
    public function booking_list(Request $request){
         
            $typevalidate=Validator::make($request->all(),[ 
                            'type'=>'required',
                            ]);
            
            try {
                
                if($typevalidate->fails())
            {
                $arr['status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
                $userid = Auth::id();
                $type = $request->type;
            
                
                if($type==1)
                {
                    $bookingDetails = DB::table('bookings')->where('user_id',$userid)->get()->toArray(); 
                }
                else
                {
                     $bookingDetails = DB::table('bookings')->where('driver_id',$userid)->get()->toArray(); 
                }
               
                if(!empty($bookingDetails)){ 
                    
                    $arr['status']=1;
                    $arr['message']='successfully..';
                    $arr['data']=$bookingDetails;
                   
                    return response()->json($arr,200);
                    
                }
                else
                {
                    $arr['status']=0;
                    $arr['message']='No Data found!!';
                    $arr['data']=null;
                    return response()->json($arr,200); 
                }
                    
            } 
            catch(\Exception $e){
                $arr['status']=0;
                $arr['message']="Sorry!! Something Went Wrong";
                $arr['data']=NULL;
            }
        return response()->json($arr,200);
    }   
    
    
    public function booking_details(Request $request){ 
         
        \Log::info('booking_details : ');
        \Log::info($request->all());
     
        $typevalidate=Validator::make($request->all(),[ 
            'booking_id'=>'required',
        ]);
        if($typevalidate->fails())
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }  
        try {
                
            $driver_id = Auth::id(); 


            $data = DB::table('bookings as b')->select('b.id', 'b.time_duration', 'b.booking_id','b.is_rental', 'b.rental_km', 'b.distance', 'b.rental_hour', 'booking_type', 'b.user_id', 'b.driver_id', 'b.rating', 'b.review', 'b.vechicle_id',
               'b.picup_location', 'b.picup_lat', 'b.picup_long', 'b.drop_location', 'b.drop_lat', 'b.drop_long', 'b.fare', 'b.payment_mode', 'b.payment_status', 'b.booking_status','b.picup_datetime', 'b.drop_datetime','u.name','u.profile',
               'u.mobile_no as user_mobile', 'd.name as driver_name', 'd.profile as driver_profile', 'b.first_stop_location', 'b.first_stop_lat', 'b.first_stop_long', 'b.second_stop_location',
               'b.second_stop_lat', 'b.second_stop_long', 'm.name as payment_mode', 'vt.name as vehicle_type_name', 'vt.image as vehicle_type_image', 'b.booking_date', 'b.booking_time',
               'u.name as user_name', 'u.profile', 'bs.name as booking_status_name', 'u.id as userId', 'u.mobile_no as mobile', 'd.mobile_no as driver_mobile')
                ->leftjoin('users as u','u.id','=','b.user_id')
                ->leftjoin('payment_modes as m','m.id','=','b.payment_mode')
                ->leftjoin('booking_status as bs','bs.id','=','b.booking_status')
                ->leftjoin('users as d','d.id','=','b.driver_id')
                ->leftjoin('driver_details_basic_details as detail','detail.driver_id','=','b.driver_id')
                ->leftjoin('vehicle_type as vt','vt.id','=','detail.vehicle_type')
                ->where('b.booking_id', $request->booking_id)
                ->first();
                

            if(!empty($data))
            {
                $data->profile = !empty($data->profile)?url('public/profile_image/'.$data->profile):profileImg();
                $data->driver_profile = !empty($data->driver_profile) ? url('public/profile_image/' . $data->driver_profile) : profileImg();
                
                $data->picup_date = date('d M y', strtotime($data->picup_datetime));
                $data->picup_time = date('h:i A', strtotime($data->picup_datetime));
                $data->drop_date = date('d M y', strtotime($data->drop_datetime));
                $data->drop_time = date('h:i A', strtotime($data->drop_datetime));
                
                $data->vehicle_type_image = !empty($data->vehicle_type_image) ? url('public/vehicle_image/vehicle_type_image/' . $data->vehicle_type_image) : profileImg();
                $data->rating_status = check_booking_rating_status($request->booking_id, $data->driver_id);
                
                $data->user_rating = get_booking_rating_review($request->booking_id, $data->user_id);
                $data->driver_rating = get_booking_rating_review($request->booking_id, $data->driver_id);
                
                $data->booking_date = date('d M Y', strtotime($data->booking_date));
                $data->booking_time = date('h:i A', strtotime($data->booking_time));
                $data->payment_mode = ($data->payment_mode == "")?"Cash":$data->payment_mode;
                
                $data->invoice_link = url('/api').'/download_invoice?id='.$data->id;
                
                $arr['status']=1;
                $arr['message']="Successfully...";
                $arr['data']=$data;
            }else{
                $arr['status'] = 1;
                $arr['message'] = 'No Data Found !';
                $arr['data'] = NULL; 
            }
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    } 
    
    public function convertToMinutes($timeString) {
        $hours = 0;
        $minutes = 0;
        if (preg_match('/(\d+)\s*hour/', $timeString, $matches)) {
            $hours = (int)$matches[1];
        }
        if (preg_match('/(\d+)\s*min/', $timeString, $matches)) {
            $minutes = (int)$matches[1];
        }
        return ($hours * 60) + $minutes;
    }
    
    public function convertToHoursAndMinutes($totalMinutes) {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;   
        return sprintf('%02d hours %02d mins', $hours, $minutes);
    }
    
    public function get_all_rental_type(Request $request){
        try
        {
            $data = DB::table('rental_type')->get();
            $arr['status']=0;
            $arr['message']="Success";
            $arr['data']=$data;
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']=$e->getMessage();
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    
    
    
   public function getAverage_prices($array) {
        if (count($array) === 0) {
            return 0;
        }
    
        $sum = array_sum($array);
        $count = count($array); 
        $average = $sum / $count;
        return $average;
    }


/****************************************** end Search Vehicle ************************************************************************/  

    



/****************************************** end payment mode check ************************************************************************/    



/****************************************** end Search Vehicle ************************************************************************/


/****************************************** end payment mode check ************************************************************************/ 
 

/*************************************search vehicle ride******************************************/


    // public function search_ride(Request $request)
    // {
    //     $typevalidate=Validator::make($request->all(),[   
    //         'booking_id'=>'required',
    //       ]);
    //     if($typevalidate->fails())
    //     {
    //         $arr['status']=0;
    //         $arr['message']= $typevalidate->errors()->first();
    //         $arr['data']=NULL;
    //         return response()->json($arr,200);
    //     }  
    //     try
    //     {
    //           $user_id =  Auth::id();

    //           $radious_data = DB::table('radius')->first();

    //           if($radious_data){
    //              $radius = $radious_data->radius_range;
    //           }else{
    //               $radius=100;
    //           }
    //           $book_status = DB::table('bookings')->where('booking_id',$request->booking_id)->first();

    //           if($book_status){
    //               if($book_status->booking_type == "2"){
    //                     $arr['status']=0;
    //                     $arr['message']= "Booking has been Scheduled !";
    //                     $arr['data']=NULL;
    //                     return response()->json($arr,200);
    //               }       
    //           $latitude=$book_status->picup_lat;
    //           $longtitude=$book_status->picup_long;
    //           $booking_id=$request->booking_id; 
    //           $driver_id = $request->driver_id;
    //           $booking_status = $request->booking_status;
    //           $booking_for_type = ($book_status->rider_id)?"1":"0";
    //           $data=DB::table("users as u")
    //                   ->leftjoin('driver_details_basic_details as dd','dd.driver_id','=','u.id')
    //                   ->leftjoin('vehicle_type as vt','vt.id','=','dd.vehicle_type')
    //                     ->select("vt.*", "u.latitude as driver_latitude", "u.longtitude as driver_longitude"
    //                     ,"u.id as driver_id","u.name as DriverName","u.email as DriverEmail","u.mobile_no","u.gender",
    //                     "dd.vehicle_type as vehicle_type_id"
    //                     ,DB::raw('CONCAT("' .url("public") .'","/vehicle_image/vehicle_type_image/",image)  as image')
    //                     ,DB::raw("round(6371 * acos(cos(radians(" . $latitude . ")) 
    //                     * cos(radians(u.latitude)) 
    //                     * cos(radians(u.longtitude) - radians(" . $longtitude . ")) 
    //                     + sin(radians(" .$latitude. ")) 
    //                     * sin(radians(u.latitude)))) AS distance"))
    //                     // ->having("distance", "<", $radius)
    //                     ->where("u.type",2);
    //                     if($booking_status == 6 && $driver_id != ""){
    //                         $data->where("u.id", "!=", $driver_id);
    //                     }
    //                   $data = $data->where('dd.vehicle_type',$book_status->vechicle_id)
    //                     ->where('u.is_live',1)
    //                     // ->dd();
    //                     ->get()->toArray();
    //         //   dd($data);      
                
    //             if($data){
    //                  $delete = DB::table('user_schedule_booking')->where('user_id',$user_id)->delete();
    //                  $vid = $book_status->vechicle_id;
    //                 //  dd($user_id);
    //                  $vehicle_data = DB::table('vehicle_fare')->where('user_id', $user_id)->where('v_id',$vid)->first();
    //                  if(empty($vehicle_data)){
    //                      $arr['status']=0;
    //                      $arr['message']= "Vehicle fare not found !";
    //                      $arr['data']=NULL;
    //                      return response()->json($arr,200);
    //                  }
    //                  foreach($data as $val){
    //                      $invoice_number = 'Invoice'.rand(10000,100000);
    //                      $val->user_id = $user_id;
    //                      $val->trip_type =$vehicle_data->trip_type;
    //                      $val->pick_up_location =$vehicle_data->pick_up_location;
    //                      $val->pick_up_lat =$vehicle_data->pick_up_lat;
    //                      $val->pick_up_long =$vehicle_data->pick_up_long;
    //                      $val->drop_location =$vehicle_data->drop_location;
    //                      $val->drop_lat =$vehicle_data->drop_lat;
    //                      $val->drop_long =$vehicle_data->drop_long;
    //                      $val->fare =$vehicle_data->fare;
                         
    //                       $data_booking['user_id']        = $user_id;
    //                       $data_booking['driver_id']      = $val->driver_id;
    //                       $data_booking['vechicle_id']    = $vid;
    //                       $data_booking['trip_type']      = $vehicle_data->trip_type;
    //                       $data_booking['booking_id']     = $booking_id;
    //                       $data_booking['invoice_number'] = $invoice_number;
    //                       $data_booking['picup_location'] = $vehicle_data->pick_up_location;
    //                       $data_booking['picup_lat']      = $vehicle_data->pick_up_lat;
    //                       $data_booking['picup_long']     = $vehicle_data->pick_up_long;
    //                       $data_booking['drop_location']  = $vehicle_data->drop_location;
    //                       $data_booking['drop_lat']       = $vehicle_data->drop_lat;
    //                       $data_booking['drop_long']      = $vehicle_data->drop_long;
    //                       $data_booking['distance']       = $val->distance;
    //                       $data_booking['fare']           = $vehicle_data->fare;
    //                       $data_booking['booking_status'] = 1;
    //                       $data_booking['payment_mode']  = $book_status->payment_mode;
    //                       $data_booking['booking_for_type']  = $booking_for_type;
                          
    //                       $data_booking['first_stop_location']  = $vehicle_data->first_stop_location;
    //                       $data_booking['first_stop_lat']       = $vehicle_data->first_stop_lat;
    //                       $data_booking['first_stop_long']      = $vehicle_data->first_stop_long;
    //                       $data_booking['second_stop_location'] = $vehicle_data->second_stop_location;
    //                       $data_booking['second_stop_lat']      = $vehicle_data->second_stop_lat;
    //                       $data_booking['second_stop_long']     = $vehicle_data->second_stop_long;
                          
    //                       $user_status = DB::table('bookings')->where('driver_id', $val->driver_id)->whereIn('booking_status', ['2','4'])->where('booking_date', date('d-m-Y'))->first();
    //                     //   dd($user_status); 
    //                       if(empty($user_status)){
                              
    //                           $title = "New Ride Request: Ready to Roll?";
    //                           $message = "You have a new ride request. Please proceed to the pickup location at ".$vehicle_data->pick_up_location." to pick up your passenger. Thank you for driving with Carmy!";
    //                           send_firebase_notification($title, $message, $val->driver_id);
                              
    //                           $data_inst = DB::table('user_schedule_booking')->insert($data_booking);
    //                       }
    //                  }
                     
    //                  $arr['status']=1;
    //                  $arr['message']="Success";
    //                  $arr['booking_id']=$booking_id;
    //                  $arr['data']=$data;
                     
    //             }else{
    //                  $arr['status']=0;
    //                  $arr['message']="No Data Found";
    //                  $arr['data']=NULL;   
    //             }            
    //         }else{
    //             $arr['status']=0;
    //             $arr['message']="Booking Not Found !";
    //             $arr['data']=NULL;   
    //         }    
    //     }
    //     catch(\Exception $e)
    //     {
    //         $arr['status']=0;
    //         $arr['message']="Sorry!! Something Went Wrong";
    //         $arr['data']=NULL;
    //     }
    //     return response()->json($arr,200);
    // }
    
    
    
    
    
    
/*****************************************end vehicle ride*********************************************/


// cancel ride book

public function cancel_ride(Request $request)
    {
        $user_id =  Auth::id();
        $typevalidate=Validator::make($request->all(),[ 
            'booking_id'=>'required',
            'driver_id'=>'required',
        ]);
        try
        {
         
        
        $checkCancelRide =  DB::table('bookings')->where('booking_id',$request->booking_id)->first(); 
        if(!empty($checkCancelRide)){
           
           $checkresions =  DB::table('bookings')->where('booking_id',$checkCancelRide->booking_id)->where('booking_status',3)->first(); 
           if($checkresions){
               $arr['status']=0;
                $arr['message']='This ride already cancelled..';
                $arr['bookingId'] =$request->booking_id;
                return response()->json($arr,200);
           }else{
          $currentTime = Carbon::now();
          $ctime = $currentTime->toDateTimeString();
        $to    = Carbon::parse(($checkCancelRide->created_at)); //Carbon::createFromFormat('Y-m-d H:s:i', '2023-09-16 15:14:38');
        $from  = Carbon::parse($ctime);
        $diffInMinutes = $to->diffInMinutes($from);
         
         /*5 minutes late payment charge start*/
         
        if($diffInMinutes <= 5){
            $cancelReason = DB::table('cancel_resions')->insert(['booking_id'=>$request->booking_id,
            'user_id'=>$user_id,'driver_id'=>$request->driver_id,'reason_message'=>$request->reason_message]);
            
            $can_booking = DB::table('bookings')->where('booking_id',$request->booking_id)->update(['booking_status'=>3]); 
            $bookingid = $request->booking_id;
            
        }else{
            $userDetails = DB::table('users')->select('id','name','talk_charge','talk_status')->where('id',$user_id)->first();
            $charge = $userDetails->talk_charge;
            $add   =($charge+(30));
            $cancel_charge = DB::table('users')->where('id',$userDetails->id)->update(['talk_charge'=>$add,'talk_status'=>1]);
            
            /*app cancel booking*/
            $cancelReason = DB::table('cancel_resions')->insert(['booking_id'=>$request->booking_id,
            'user_id'=>$user_id,'driver_id'=>$request->driver_id,'reason_message'=>$request->reason_message]);
            
            $can_booking = DB::table('bookings')->where('booking_id',$request->booking_id)->update(['booking_status'=>3]); 
            $bookingid = $request->booking_id;
        }
           }
            
            if(!empty($cancelReason)){
                
                $title = "Ride is cancelled !";
                $message = "Your Booking Id ".$request->booking_id." has been cancelled !";
                send_firebase_notification($title, $message, $checkCancelRide->driver_id);
                
                $arr['status']=1;
                $arr['message']='Cancel successfully..';
                $arr['bookingId'] =$bookingid;
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Cancel un successfully..';
                $arr['bookingId']=null;
                return response()->json($arr,200); 
            }
        } else {
            
                $arr['status']=0;
                $arr['message']='This ride already cancelled..';
                $arr['bookingId'] =$bookingid;
                return response()->json($arr,200);
        }    
            
            }
            catch(\Exception $e){
                $arr['status']=0;
                $arr['message']=$e->getMessage();
                $arr['data']=NULL;
            }
        return response()->json($arr,200);
    }
    
    
    
    // rating add
     public function rating(Request $request){
            
        $typevalidate=Validator::make($request->all(),[
                      'driver_id'=>'required',
                      'rating'=>'required',
                      'message'=>'required',
        ]); 
        try
        {
            if($typevalidate->fails())
            {
                $arr['status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $userid = Auth::id();
            $driverid = $request->driver_id;
            $rating = $request->rating;
            $message = $request->message;
            
            
            $result = DB::table('ride_rating')->insert(['user_id'=>$userid,'driver_id'=>$driverid,'message'=>$message,'rating'=>$rating]);

            if(!empty($result)){
                $arr['status']=1;
                $arr['message']='successfully..';
                 $arr['data']=[];
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='something went wrong ...';
                 $arr['data']='null..';
                return response()->json($arr,200); 
            }
        }
    
        catch(\Exception $e){
                $arr['status']=0;
                $arr['message']=$e->getMessage();
                $arr['data']=NULL;
            }
        return response()->json($arr,200);    

    }  
    
    // All Rating list 
    public function rating_list(Request $request){
        
        $ratingList= DB::table('ride_rating as t1')->select('t1.rating','t1.message','t3.name as driver_name','t2.name as user_name' ,'t1.status')
                ->join('users as t2','t2.id','=','t1.user_id')
                ->join('users as t3','t3.id','=','t1.driver_id')  
                ->get();  

            if(!empty($ratingList)){
                
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$ratingList;
               
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
        
    }
    
    
    
    //  Rating list according user 
    public function rating_user_list(Request $request){
         
        $user_id = Auth::id();
        
        $ratingList= DB::table('ride_rating as t1')->select('t1.rating','t1.message','t2.name as driver_name')
                ->join('users as t2','t2.id','=','t1.user_id')->where('t1.user_id',$user_id)->get();  
        
        
            if(!empty($ratingList)){
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$ratingList;
               
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
        
    }
    
    
    public function user_em_details(Request $request)
  {
      try
      {
          
          $user_id = Auth::id();
          
          $data['user_id'] = $user_id;
          $data['em_username'] = $request->em_username;
          $data['em_mobile_number'] = $request->em_mobile_number;
         
         $data_insert = DB::table('user_emergency_number')->insert($data);
          
          
          if(!empty($data_insert)){
              
              $arr['status']=1;
              $arr['message']="successfully !!!";
              $arr['data']=null;
              return response()->json($arr,200);
              
          }else{
              
               $arr['status']=0;
               $arr['message']="data not found!!";
               $arr['data']=NULL;
               return response()->json($arr,200);
              
          }
          
      }
      catch(\Exception $e)
      {
          $arr['status']=0;
          $arr['message']="Sorry!! Something Went Wrong";
          $arr['data']=NULL;
      }
      return response()->json($arr,200);
  }
  
  

    
    
    
    
     
     
      /*********************************checkbooking accepted**********************************/
     
    public function check_user_booking_ride(Request $request)
  {
      
      \Log::info('check_user_booking_ride : ');
      \Log::info($request->all());
      
      $validate=Validator::make($request->all(),[
                    'booking_id'=>'required'
                    ]);
        
        if($validate->fails()){
            
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();
            $arr['data']=NULL;
           
            return response()->json($arr,200);
        }
      /*try
      {*/
          
          $user_id = Auth::id();
         
         $booking_id =$request->booking_id??'';
         $check_status = DB::table('bookings')
                             ->select('bookings.id','bookings.booking_id','bookings.booking_type','bookings.user_id','bookings.driver_id'
                             ,'bookings.ride_start_otp','bookings.vechicle_id','bookings.distance','bookings.picup_location','bookings.picup_lat',
                             'bookings.picup_long','bookings.drop_location','bookings.drop_lat','bookings.drop_long'
                             ,'bookings.booking_status','bookings.created_at','bookings.updated_at'
            ,'bookings.distance as total_dis'
                            ,'users.email','users.mobile_no','users.name'
                            ,DB::raw('CONCAT("' . url('public/profile_image') . '","/",users.profile)  as profile_image')
                 ,'u.name as driver_name','u.email as driver_email','u.mobile_no as driver_mobile'
                  ,DB::raw('CONCAT("' . url('public/profile_image') . '","/",u.profile)  as driver_profile')            
                             ,'vehicle_type.name as vehicle_name')
                             
                            ->leftjoin('users', 'users.id', '=', 'bookings.user_id')
                            ->leftJoin('users as u','u.id','=','bookings.driver_id')
                             ->leftjoin('vehicle_type', 'vehicle_type.id', '=', 'bookings.vechicle_id')
                            ->where('bookings.booking_id',$booking_id)
                             ->where('bookings.user_id',$user_id)
                             ->orderBy('bookings.id',"DESC")
                          ->first();

              if(!empty($check_status))
              {
                   $driverid =$check_status->driver_id??'';
             
                 $driver_details = DB::table('driver_details_basic_details')->where('driver_id',$driverid)->first();
                 $check_status->vehicle_number =$driver_details->vehicle_number??'';
                 $check_status->vehicle_name =$driver_details->vehicle_name??'';
                 $check_status->vehicle_manufacture =$driver_details->vehicle_manufacture??'';
                 $check_status->vehicle_model =$driver_details->vehicle_model??'';
                 $check_status->vehicle_reg_year =$driver_details->vehicle_reg_year??'';
                 $check_status->vehicle_owner_name =$driver_details->vehicle_owner_name??'';
                 $check_status->country_operating =$driver_details->country_operating??'';
                 
                 $driver_rating = DB::table('ride_rating')->where('driver_id',$driverid)->first();
                 $check_status->rating =$driver_rating->rating??'';
                  
                  $arr['status']=1;
                  $arr['message']="Success";
                  $arr['data']=$check_status;
              }
              else
              {
                  $arr['status']=0;
                  $arr['message']="data Not Found";
                  $arr['data']=NULL;
              }
          
      /*}
      catch(\Exception $e)
      {
          $arr['status']=0;
          $arr['message']="Sorry!! Something Went Wrong";
          $arr['data']=NULL;
      }*/
      return response()->json($arr,200);
  }
     /*********************************checkbooking accepted**********************************/
     
    public function user_ride_history(Request $request)
  {
      try
      {
          $user_id = Auth::id();
              $data['complete_booking'] = DB::table('bookings')->where('booking_status',4)->where('user_id',$user_id)->orderBy('id',"DESC")->get()->toArray();
              $data['cancel_booking'] = DB::table('bookings')->where('booking_status',3)->where('user_id',$user_id)->orderBy('id',"DESC")->get()->toArray();
              $data['upcomming_booking'] = DB::table('bookings')->where('booking_status',2)->where('user_id',$user_id)->orderBy('id',"DESC")->get()->toArray();
              $data['ongoing_booking'] = DB::table('bookings')->where('booking_status',5)->where('user_id',$user_id)->orderBy('id',"DESC")->get()->toArray();
             
              if(!empty($data))
              {
                  $arr['status']=1;
                  $arr['message']="Success";
                  $arr['data']=$data;
              }
              else
              {
                  $arr['status']=0;
                  $arr['message']="data Not Found";
                  $arr['data']=NULL;
              }
          
      }
      catch(\Exception $e)
      {
          $arr['status']=0;
          $arr['message']="Sorry!! Something Went Wrong";
          $arr['data']=NULL;
      }
      return response()->json($arr,200);
  }
  
  
    public function new_confirm_booking(Request $request)
    {
     
      $typevalidate=Validator::make($request->all(),[   
            'pick_up_location'=>'required',
            'pick_up_lat'=>'required',
            'pick_up_long'=>'required',
            'drop_location'=>'required',
            'drop_lat'=>'required',
            'drop_long'=>'required',
            'veh_id'=>'required',
            'fare' => 'required',
            'distance'=>'required',
            'booking_type'=>'required',
             
            
         ]);
         try {
            if($typevalidate->fails())
            {
                $arr['status']=0;
                $arr['message']="Validation Failed";
                $arr['data']=NULL;
               
                return response()->json($arr,200);
            }
            
            $user_id = Auth::id();
            
            $chk_booking = DB::table('bookings')->where('user_id',$user_id)
                                       ->whereIn('booking_status', [1,2,4])
            
            ->first();
            
            if(!empty($chk_booking)){
                $arr['status']=0;
                $arr['message']="Sorry!! Your Ride is Already Booked";
                $arr['data']=NULL;
                 return response()->json($arr,200);
            }
            
            
             $user_id = $request->user_id;
             $booking_id = 'CARMY'.rand(10000,100000);
             $invoice_number = 'Invoice'.rand(10000,100000);
              
              $data['user_id'] = Auth::id();
              $data['vechicle_id'] = $request->veh_id;
              $data['booking_id'] = $booking_id;
              $data['picup_location'] = $request->pick_up_location;
              $data['picup_lat'] = $request->pick_up_lat;
              $data['picup_long'] = $request->pick_up_long;
              $data['drop_location'] = $request->drop_location;
              $data['drop_lat'] = $request->drop_lat;
              $data['drop_long'] = $request->drop_long;
              $data['fare'] = $request->fare;
              $data['booking_date'] = $request->booking_date;
              $data['booking_time'] = $request->booking_time;
              $data['booking_status'] = 1;
              $data['name_someone'] = $request->name_someone;
              $data['age_someone'] = $request->age_someone;
              $data['mobile_someone'] = $request->mobile_someone;
              $data['email_someone'] = $request->email_someone;
              $data['booking_for_type'] = $request->booking_for_type;
              $data['taxi_type'] = $request->taxi_type;
                $data['invoice_number'] = $invoice_number;
               $data['distance']=$request->distance;
                   
                   
                   
              $data1['user_id'] = Auth::id();
              $data1['vechicle_id'] = $request->veh_id;
              $data1['booking_id'] = $booking_id;
              $data1['picup_location'] = $request->pick_up_location;
              $data1['picup_lat'] = $request->pick_up_lat;
              $data1['picup_long'] = $request->pick_up_long;
              $data1['drop_location'] = $request->drop_location;
              $data1['drop_lat'] = $request->drop_lat;
              $data1['drop_long'] = $request->drop_long;
              $data1['fare'] = $request->fare;
              $data1['booking_date'] = $request->booking_date;
              $data1['booking_time'] = $request->booking_time;
              $data1['booking_status'] = 1;
              $data1['name_someone'] = $request->name_someone;
              $data1['age_someone'] = $request->age_someone;
              $data1['mobile_someone'] = $request->mobile_someone;
              $data1['email_someone'] = $request->email_someone;
              $data1['booking_for_type'] = $request->booking_for_type;
              $data1['taxi_type'] = $request->taxi_type;
              $data1['invoice_number'] = $invoice_number;
               
               $data1['distance']=$request->distance;
                       
                       if($request->booking_type == 1){
                                
                      $data_inst = DB::table('bookings')->insert($data);
                             $data_inst =  DB::getPdo()->lastInsertId();
              
                      $get_data = DB::table('bookings')->where('id',$data_inst)->first();
                     }if($request->booking_type == 2){
                     
                       $data_inst = DB::table('schedule_booking')->insert($data1);
                             $data_inst =  DB::getPdo()->lastInsertId();
                      $get_data = DB::table('schedule_booking')->where('id',$data_inst)->first();
                    }
                 
            if(!empty($get_data)){
               
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$get_data;
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
         }
         catch(\Exception $e){
             $arr['status']=0;
             $arr['message']=$e->getMessage();
             $arr['data']=NULL;
         }
         return response()->json($arr,200);
            
        
    }
    
    /*******************user scheduled list**********************/
    
    /*******************user booking cancel start**********************/
    public function user_booking_cancel(Request $request){
      
         $typevalidate=Validator::make($request->all(),[ 
                               'booking_id'=>'required',
                                ]);
        try
        {
            if($typevalidate->fails())
            {
                $arr['status']=0;
                $arr['message']="Validation Failed";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
                
                   $user_id = Auth::id();
                   $data['cancel_resion_id'] = $request->resion_id;
                   $data['booking_status'] = 3;
                    
                    $get_booking = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
                    $get_booking_se = DB::table('user_schedule_booking')->where('booking_id',$request->booking)->first();
                    if(!empty($get_booking)){
                        
                        $data_update = DB::table('bookings')->where('booking_id',$request->booking_id)->update($data);
                    }elseif(!empty($get_booking_se)){
                        
                        $data_update = DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)->update($data);        
                    }

            if(!empty($data_update)){
                
                $title = "Ride is Cancelled !";
                $message = "Your Booking Id ".$request->booking_id." has been cancelled !";
                send_firebase_notification($title, $message, $get_booking->driver_id);
                
                $arr['status']=1;
                $arr['message']='User Cancel successfully..';
                $arr['data']=null;
               
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
      
  }    
    
    catch(\Exception $e){
                $arr['status']=0;
                $arr['message']=$e->getMessage();
                $arr['data']=NULL;
            }
        return response()->json($arr,200);
    
  }
    /*******************user booking cancel end**********************/
    
    /****************************************** Notification list ***********************************************************/

    

   /****************************************** End Notification list *****************************************************/
    
    /****************************************** My Offers list *****************************************/
    public function my_offer(Request $request)
      {
     
             $user_id =  Auth::id();
            
            $get_offer = DB::table('offers')->get()->toArray();
            
            if(!empty($get_offer)){
                
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$get_offer;
               
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
      
           
    }
   /****************************************** End My Offers list **************************************************/
    
    
      /****************************************** Safety list *****************************************/
    public function safety_list(Request $request)
      {
     
             $user_id =  Auth::id();
            
            $get_safety = DB::table('safety')->select('id','title','status')->get()->toArray();
            
            if(!empty($get_safety)){
                
                $arr['status']=1;
                $arr['message']='safety list successfully..';
                $arr['data']=$get_safety;
               
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
      
           
    }
   /****************************************** End Safety list **************************************************/
   
   /****************************************** Safety id *****************************************/
    public function safety_list_id(Request $request)
      {
     
             $user_id =  Auth::id();
             $id      = $request->id;
            $safety = DB::table('safety')->where('id',$id)->first();
            
            if(!empty($safety)){
                
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$safety;
               
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
      
           
    }
   /****************************************** End Safety id **************************************************/
   
   
    
    /*******************user video start**********************/
//     public function upload_video(Request $request){
      
//          $typevalidate=Validator::make($request->all(),[ 
//                               'booking_id'=>'required',
//                               'video'=>'required',
//                                 ]);
//         try
//         {
//             if($typevalidate->fails())
//             {
//                 $arr['status']=0;
//                 $arr['message']="Validation Failed";
//                 $arr['data']=NULL;
//                 return response()->json($arr,200);
//             }
                
                
//                   if($request->file('video'))
//               {
//                 $file_name = date('dmy').rand(1,4).$request->file('video')->getClientOriginalName();
//                 $store=$request->file('video')->move('public/video', $file_name);
//                 $img=$file_name;
//               }
//              $data=array(
                
//                 'booking_id'=>$request->booking_id,
//                 'video'=>$img
//                 );
//              $d=DB::table('video_book')->insert($data);
//             if($d>0)
//             {
//                 $arr['status']=1;
//                 $arr['message']='Success';
//                 $arr['data']=true;    
//             }
//             else
//             {
//                 $arr['status']=0;
//                 $arr['message']='video is not success';
//                 $arr['data']=NULL;
//             }
      
//      }    
    
//     catch(\Exception $e){
//                 $arr['status']=0;
//                 $arr['message']=$e->getMessage();
//                 $arr['data']=NULL;
//             }
//         return response()->json($arr,200);
    
//   }
    /*******************user video end**********************/ 
  
    
    /*****************************Ongoing ride********************************/
    public function ongoing_booking_lists()
    {
        try
        {
            $driver_id = Auth::id();
            
            $data=DB::table('bookings')->where('driver_id',$driver_id)->where('booking_status',4)->get()->toArray();
            if(!empty($data))
            {
             
                $arr['status']=1;
                $arr['message']="Successfully...";
                $arr['data']=$data;
            }
            else
            {
                $arr['status']=0;
                $arr['message']="Data not Found";
                $arr['data']=NULL;
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    /******************************Ongoing Ride end*******************************/
    
    
   
   
   /*******************************cancel_booking Ride start***************************************/
   public function cancel_booking_lists()
    {
        try
        {
            $driver_id = Auth::id();
            
            $data=DB::table('bookings')->where('driver_id',$driver_id)->where('booking_status',3)->get()->toArray();
            if(!empty($data))
            {
             
                $arr['status']=1;
                $arr['message']="Successfully...";
                $arr['data']=$data;
            }
            else
            {
                $arr['status']=0;
                $arr['message']="Data not Found";
                $arr['data']=NULL;
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
   /*******************************cancel_booking Ride end***************************************/
   
   /*******************************Driver Trip Start***************************************/
   public function driver_start_trip(Request $request)
    { 
        
        \Log::info('driver_start_trip : ');
        \Log::info($request->all());
        
        $driverId=Auth::id();
        
            $validation=Validator::make($request->all(),['booking_id'=>'required','driver_lat'=>'required','driver_long'=>'required']);
            if($validation->fails()==true)
            {
                $arr['status']=0;
                $arr['message']=$validation->errors()->first();
                $arr['data']=NULL;
            }
            else
            {
                //$userid = Auth::id();
                $bookingId=$request->booking_id;
                $count=DB::table('bookings')->where('booking_id',$bookingId)->where('driver_id',$driverId)->count();
                
                if($count>0)
                {
                     $driverlat=$request->driver_lat;
                    $driverlong=$request->driver_long;
                    $userData=DB::table('bookings')->where('booking_id',$bookingId)->where('driver_id',$driverId)->first();
                    $userid =$userData->user_id??'';
                     $result = DB::table('users')->where('id',$userid)
                                       ->update(['longtitude'=>$driverlong,'latitude'=>$driverlat]);
                                       
                                     
                    $bookingdetails=DB::table('bookings')->where('booking_id',$bookingId)->where('driver_id',$driverId)->first();
                    $pickuplat =$bookingdetails->picup_lat??'';
                    $pickuplong =$bookingdetails->picup_long??'';
                    // $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$driverlat.",".$driverlong.
                    //"&destinations=".$bookingdetails->picup_lat.",".$bookingdetails->picup_long."&departure_time=now&key=AIzaSyD5gY5yoLsLO7lQZxze7nJL_dijCiaNnAA";
                     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$driverlat.",".$driverlong.
                     "&destinations=".$pickuplat.",".$pickuplong."&departure_time=now&key=".get_option_data('google_map_api_key');
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($ch);
                   
                    curl_close($ch);
                    $response_a = json_decode($response, true);
                     $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
                      
                     $time = $response_a['rows'][0]['elements'][0]['duration']['text'];   
                     $distance = $response_a['rows'][0]['elements'][0]['distance']['value'];
                    
                     $dis = $distance/1000;
                     if($dis<=1)
                     {
                         $d=DB::table('bookings')->where('booking_id',$bookingId)->where('driver_id',$driverId)->first();
                         
                        $arr['status']=0;
                         $arr['message']=" You Can Start the Ride";
                         $arr['data']=NULL;
                     }
                     else
                     {
                        $upcoming_booking = DB::table('bookings')
                             ->select('bookings.*','bookings.distance as total_dis','users.email','users.mobile_no','users.profile_image','users.name',
                             DB::raw('CONCAT("' . url('storage/app/profile_photo') . '","/",profile_image)  as profile_image'))
                             
                            ->join('users', 'users.id', '=', 'bookings.user_id')
                            ->where('bookings.booking_id',$request->booking_id)
                            ->where('bookings.booking_status',2)
                            ->where('bookings.driver_id',$driverId)
                            ->first(); 
                         
                          $d=DB::table('bookings')->where('booking_id',$bookingId)->where('driver_id',$driverId)->first();
                         $arr['status']=1;
                         $arr['message']="You Cannot Start the Ride Untill you pick the user";
                         $arr['data']=$upcoming_booking;
                         
                     }
                    
                }
                else
                {
                    $arr['status']=0;
                    $arr['message']="No Booking Id Found";
                    $arr['data']=NULL;
                }
                
            }
        return response()->json($arr,200);
    }
   /*******************************Driver Trip End***************************************/
   
   
   
   
   /*******************************complete Ride list start***************************************/
    public function complete_list_ride(Request $request)
    {
        try
        {
            $driver_id = Auth::id();
            
            // dd($driver_id);
            
            $result = DB::table('bookings')->where('driver_id',$driver_id)->where('booking_status',5)->get();
            if(!empty($result))
            {
                $arr['status']=1;
                $arr['message']="Past Rides Successfully...";
                $arr['data']=$result;
            }
            else
            {
                $arr['status']=0;
                $arr['message']="Data not update";
                $arr['data']=NULL;
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
   /*******************************complete Ride list end***************************************/
   
   
   public function user_schedule_ride(Request $request){
       
       \Log::info('user_schedule_ride : ');
        \Log::info($request->all());
      try
        {
            $user_id = Auth::id();
            $currentDate = Carbon::today()->format('Y-m-d'); // Use Y-m-d for correct comparison
            $currentTime = Carbon::now()->format('H:i');     // Keep the time as HH:mm
            
            $result = DB::table('bookings as b')
                ->select(
                    'b.id', 'b.booking_id', 'b.picup_location', 'b.drop_location', 
                    'b.booking_status', 'b.picup_lat', 'b.picup_long', 'b.drop_lat', 
                    'b.drop_long', 'b.booking_date', 'b.booking_time', 'bs.name as booking_status_name', 
                    'b.fare', DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile) as profile_image'), 
                    'd.id as driver_id', 'd.name as driver_name', 'd.latitude', 'd.longtitude', 
                    'b.picup_lat', 'b.picup_long', 'detail.vehicle_number', 'detail.vehicle_model', 
                    'd.mobile_no'
                )
                ->leftJoin('users as d', 'd.id', '=', 'b.driver_id')
                ->leftJoin('booking_status as bs', 'bs.id', '=', 'b.booking_status')
                ->leftJoin('driver_details_basic_details as detail', 'detail.driver_id', '=', 'b.driver_id')
                ->where('b.user_id', $user_id)
                ->where('b.booking_type', '2')
                ->where(function($query) use ($currentDate, $currentTime) {
                    $query->where(DB::raw("STR_TO_DATE(b.booking_date, '%d-%m-%Y')"), '>', $currentDate)
                          ->orWhere(function($query) use ($currentDate, $currentTime) {
                              $query->where(DB::raw("STR_TO_DATE(b.booking_date, '%d-%m-%Y')"), '=', $currentDate)
                                    ->where('b.booking_time', '>', $currentTime);
                          });
                })
                ->orderBy('b.id', 'DESC')
                ->get();

            if(!empty($result))
            {
                $arr['status']=1;
                $arr['message']="User All Schedule Booking...";
                $arr['data']=$result;
            }
            else
            {
                $arr['status']=0;
                $arr['message']="Data not update";
                $arr['data']=NULL;
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
   }
  
   
   /*******************************booking trip start***************************************/
   public function driver_end_trip(Request $request)
    {
       // Not Implemet this api in application
        
        $driverId=Auth::id();
        
        // try
        // {
            $validation=Validator::make($request->all(),['booking_id'=>'required','driver_lat'=>'required','driver_long'=>'required']);
            if($validation->fails()==true)
            {
                $arr['status']=0;
                $arr['message']=$validation->errors()->first();
                $arr['data']=NULL;
            }
            else
            {
                $userid = Auth::id();
                $bookingId=$request->booking_id;
                $count=DB::table('bookings')->where('booking_id',$bookingId)->count();
    
                if($count>0)
                {
                     $driverlat=$request->driver_lat;
                    $driverlong=$request->driver_long;
                    
                     $result = DB::table('users')->where('id',$userid)
                                       ->update(['longtitude'=>$driverlong,'latitude'=>$driverlat]);
                                       
                                       
                    $bookingdetails=DB::table('bookings')->where('booking_id',$bookingId)->first();
                    
                    // $driverDetails=DB::table('driver_vehicle_details')->where('driver_status',1)->where('user_id',$driverId)->first();
                   
                    
                    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$driverlat.",".$driverlong."&destinations=".$bookingdetails->drop_lat.",".$bookingdetails->drop_long."&departure_time=now&key=".get_option_data('google_map_api_key');
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    $response = curl_exec($ch);
                    
                    curl_close($ch);
                    $response_a = json_decode($response, true);
                    
                     $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
                     $time = $response_a['rows'][0]['elements'][0]['duration']['text'];   
                     $distance = $response_a['rows'][0]['elements'][0]['distance']['value'];
                    
                     $dis = $distance/1000;
                   
                     if($dis<=5)
                     {
                         $d=DB::table('bookings')->where('booking_id',$bookingId)->update(['booking_status'=>'5']);
                         
                        $arr['status']=1;
                         $arr['message']="Ride Completed Successfully!!";
                         $arr['data']=NULL;
                     }
                     else
                     {
                         
                         $arr['status']=0;
                         $arr['message']="You did not Complete the ride untill you drop the User";
                         $arr['data']=null;
                         
                     }
                    
                }
                else
                {
                    $arr['status']=0;
                    $arr['message']="No Booking Id Found";
                    $arr['data']=NULL;
                }
                
            }
        // }
        // catch(\Exception $e)
        // {
        //     $arr['status']=0;
        //     $arr['message']="Sorry!! Something Went Wrong";
        //     $arr['data']=NULL;
        // }
        return response()->json($arr,200);
    }
   /*******************************booking trip end***************************************/
   
   public function get_cupon_list(Request $request){
        try
        {
            $user_id = Auth::id();

            $result = DB::table('cupons')->where('status',1)->where('used',0)->get()->toArray();
            if($result)
            {
                foreach($result as $res){
                    $res->valid_date = date('d M Y', strtotime($res->valid_date));
                }
                
                $arr['status']=1;
                $arr['message']="User All Cupons.";
                $arr['data']=$result;
            }
            else
            {
                $arr['status']=0;
                $arr['message']="No Cupon Found !";
                $arr['data']=NULL;
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
   }
   
   public function apply_cupon(Request $request){
        $typevalidate=Validator::make($request->all(),[   
            'cupon_id'=>'required',
            'vehicle_fare_id'=>'required',
          ]);
        if($typevalidate->fails())
        {
            $arr['status']=0;
            $arr['message']= $typevalidate->errors()->first();
            $arr['data']=NULL;
           
            return response()->json($arr,200);
        }
        try
        {
            $user_id = Auth::id();
            $result = DB::table('cupons')->where('user_id',$user_id)->where('id', $request->cupon_id)->first();
            
            if(!empty($result))
            {
                $current_date = date('Y-m-d H:i:s');
                if($result->valid_date < $current_date){
                    $arr['status']=0;
                    $arr['message']=" Cupon has been Expired !";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                
                $data = DB::table('vehicle_fare')->where('id', $request->vehicle_fare_id)->first();
                if(empty($data)){
                    $arr['status']=0;
                    $arr['message']=" No Vehicle Found !";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                
                $total_fare = $data->total_fare;
                $discount_percent = $result->discount_percent;
                $discount = ($total_fare*$discount_percent)/100;
                $new_fare = ($total_fare-$discount);
                
                DB::table('vehicle_fare')->where('id', $data->id)->update(['fare'=>$new_fare, 'discount'=>$discount, 'applied_cupon_id'=>$result->id]);
                DB::table('cupons')->where('id',$result->id)->update(['used'=>'1', 'used_datetime'=>$current_date]);
                
                $arr['status']=1;
                $arr['message']="Appiled Cupon Successfull .";
                $arr['data']=NULL;
            }
            else
            {
                $arr['status']=0;
                $arr['message']="No Cupon Found !";
                $arr['data']=NULL;
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
   }
   
   private function send_msg($otp,$name,$mobile){
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://control.msg91.com/api/v5/flow/',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
          "template_id":"65eabb57d6fc0517601e98a2",
          "recipients": [
            {
              "mobiles": "91'.$mobile.'",
              "var": "'.$name.'",
              "var1": "'.$otp.'"
            }
          ]
        }',
          CURLOPT_HTTPHEADER => array(
            'authkey: 412583AqWHZIjl659260c1P1',
            'Content-Type: application/json',
            'Cookie: PHPSESSID=a8lb8mnfqt7oknl38vgds3p842'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        // echo $response;
        return $response;
    }
   
   
   
   public function add_voice_chat(Request $request){
        $validate=Validator::make($request->all(),[
            'reciever_id' =>'required',
            'attachment_file' =>'required',
        ]);
        
        if($validate->fails()){
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        
        try
        {
            $filename = "";
            if ($request->hasfile('attachment_file')) {
                // $imageName = time().'.'.$request->attachment_file->extension();
                // $request->attachment_file->move(public_path('/recording/'), $imageName);
                // $filename = $imageName;
                
                if(imageCompres($_FILES["attachment_file"]["tmp_name"], $_FILES["attachment_file"]["name"], $_FILES["attachment_file"]["size"], public_path('/recording/'))){
                    $filename = $_FILES["attachment_file"]["name"];
                 }else{
                      $arr['status']=0;
                      $arr['message']="Only jpg, png, jpeg, gif, doc, pdf is Allowed !";
                      $arr['data']=NULL;
                      return response()->json($arr,200);
                 }
                
             }
            $senderId = Auth::id();

            $insert = [
                'sender_id' =>$senderId,
                'receiver_id' =>$request->reciever_id,
                'attachment' =>$filename, 
                'status' =>2,
                'post_type'=>2,
                ]; 
                       
            $id = DB::table('chats')->insertGetId($insert); 
            if($id){

                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']=NULL;
                
            }else{
                $arr['status']=0;
                $arr['message']='Not Data Found !';
                $arr['data']=NULL;
            }
            
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
   }
   
   
   

  public function user_add_money_in_wallet(Request $request){
  
    $validate=Validator::make($request->all(),[
            'add_ammount' =>'required'
        ]);
        
        if($validate->fails()){
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
   try{
        $senderId = Auth::id();
        $amount=$request->add_ammount;
        
        $order_id="add".rand(100000000000,1);
        
        $insert_payment=[
            "user_id"=>$senderId,
            "amount"=>$amount,
            "transaction_type"=>'1',
            "order_id"=>$order_id,
            'status'=>2,
            "transaction_date"=>date('Y-m-d')
            ];
        
        $check_insert= db::table('my_wallet')->insert($insert_payment);
        
        $redirect_url='money_add_confirm';
        
        if($check_insert){
       $payment_status['url']=$this->ccavenue_payment($amount,$redirect_url,$order_id);
        }else{
            $payment_status='please try again';
        }
        
        
         if($check_insert){

                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']=$payment_status;
                
            }else{
                $arr['status']=0;
                $arr['message']='Not Data Found !';
                $arr['data']=NULL;
            }
        
        
        
   }catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
   }
   
public function user_received_money_authentication(Request $request){
        
        try{
    
        // dump($request->encResp);
    
        $workingKey = get_option_data('working_key'); //Working Key should be provided here.
        $encResponse = $request->encResp;

        $rcvdString = $this->decryptCC($encResponse, $workingKey);        //Crypto Decryption used as per the specified working key.
        $order_status = "";
        $decryptValues = explode('&', $rcvdString);
        $dataSize = sizeof($decryptValues);
        $new_array=[];
        
        foreach($decryptValues as $key=> $val){
             $inside_array = explode('=', $val);
             $new_array[$inside_array[0]]=$inside_array[1];
        }
        
        if($new_array['order_status']=='Success'){
            
         $update_payment=db::table('my_wallet')->where('order_id',$new_array['order_id'])->update(['status'=>1,'transaction_id'=>$new_array['tracking_id'],'response'=>$new_array, 'payment_type'=>'wallet']);
         
         $arr['status']=1;
         $arr['message']='Success';
         $arr['data']='Transaction is Successful';

        }else{
            $arr['status']=0;
            $arr['message']='failed';
            $arr['data']=NULL; 
        }

        }catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
    
    
        
        
    }
    
public function user_pay_driver_by_wallet(Request $request){
        $typevalidate=Validator::make($request->all(),[ 
            'booking_id'=>'required',
        ]);
        
        if($typevalidate->fails())
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        try 
        {
            $driverId=Auth::id();
            $booking_data = DB::table('bookings')->where('booking_id', $request->booking_id)->first();
        
            if($booking_data){
                $get_data = DB::table('list_subscription')->where([['booking_id', $booking_data->booking_id],['status',1]])->first();
                if(empty($get_data)){
                    
                    $real_money_inwallet= $this->calculate_wallet_amount($driverId);
                    
                     if( floatval($real_money_inwallet) <= floatval($booking_data->fare) ){
                            $arr['status']=0;
                            $arr['message']="Your Wallet Ammount is not Sufficent !";
                            $arr['data']='low wallet ammount';
                            return response()->json($arr,200);
                      }
                      
                      $payfair=[
                         "user_id"=>$driverId,
                         "amount"=>$booking_data->fare,
                         "transaction_type"=>'2',
                         "transaction_date"=>date('Y-m-d'),
                         "booking_id"=>$request->booking_id
                     ];
                     
                     db::table('my_wallet')->insert($payfair);       
                       
                    
                    $payment_type = '2';
                    
                     $actual_fare = ($booking_data->fare)??1;
                     
                     $admin_per = $area_partner_per = $service_provider_per = $user_percentage = 0;
                     
                     $percentage_calculator= db::table('mlm_percentage')->select('user_name','percent')->where('status','1')->get();
                     foreach($percentage_calculator as $val){
                        if($val->user_name=='admin'){
                            $admin_per=$val->percent;
                        }
                        if($val->user_name=='area partner'){
                            $area_partner_per=$val->percent;
                        }
                        if($val->user_name=='sponsor'){
                            $service_provider_per=$val->percent;
                        }
                        
                        if($val->user_name=='User'){
                            $user_percentage=$val->percent;
                        }
                    }
                    
                    $sp_details = DB::table('sp_driver_table')->where('driver_id', $booking_data->driver_id)->first();
                    $payment_staus = [];
                    if($sp_details){
                        $ap_details = DB::table('area_sp_table')->where('service_partner_id', $sp_details->service_provide_id)->first();
                        if($ap_details){
                            
                            $admint_amount = ($actual_fare*$admin_per)/100;
                            $service_provider_amount = ($actual_fare*$service_provider_per)/100;
                            $area_partner_amount = ($actual_fare*$area_partner_per)/100;
                            
                            $user_amount = ($actual_fare*$user_percentage)/100;
                            
                            $total_other_pay=$admint_amount+$service_provider_amount+$area_partner_amount+$user_amount;
                            $driver_amount = ($actual_fare-$total_other_pay);
                            
                            // area partner wallet add money
                             $commison1 = [
                                     "user_id"=>$ap_details->area_id,
                                     "amount"=>$area_partner_amount,
                                     "transaction_type"=>'1',
                                     "transaction_date"=>date('Y-m-d'),
                                     "booking_id"=>$request->booking_id
                                 ];
                                 db::table('my_wallet')->insert($commison1);
                             
                             // service partner wallet add money    
                             $commison2 = [
                                     "user_id"=>$ap_details->service_partner_id,
                                     "amount"=>$service_provider_amount,
                                     "transaction_type"=>'1',
                                     "transaction_date"=>date('Y-m-d'),
                                     "booking_id"=>$request->booking_id
                                 ];
                                db::table('my_wallet')->insert($commison2); 
                             
                             // driver wallet add money  
                             $commison3 = [
                                     "user_id"=>$booking_data->driver_id,
                                     "amount"=>$driver_amount,
                                     "transaction_type"=>'1',
                                     "transaction_date"=>date('Y-m-d'),
                                     "booking_id"=>$request->booking_id
                                 ];
                             db::table('my_wallet')->insert($commison3); 
                             
                             if($user_percentage > 0){
                                 $commison4 = [
                                     "user_id"=>$booking_data->user_id,
                                     "amount"=>$user_amount,
                                     "transaction_type"=>'1',
                                     "transaction_date"=>date('Y-m-d'),
                                     "booking_id"=>$booking_data->booking_id
                                 ];
                                db::table('my_wallet')->insert($commison4); 
                             }
                             
                             // transaction record
                             $transaction = [
                                     'booking_id'=>$booking_data->booking_id,
                                     'amount'=>$booking_data->fare,
                                     'payment_type'=>$payment_type,
                                     'admin_commision_percentage'=>$admin_per,
                                     'admin_amount'=>$admint_amount,
                                     'area_partner_id'=>$ap_details->area_id,
                                     'service_partner_id'=>$ap_details->service_partner_id,
                                     'service_partner_percentage'=>$service_provider_per,
                                     'service_partner_amount'=>$service_provider_amount,
                                     'area_partner_percentage'=>$area_partner_per,
                                     'area_partner_amount'=>$area_partner_amount,
                                     'driver_amount'=>$driver_amount,
                                     
                                     'cashback_user_id'=>$booking_data->user_id,
                                     'cashback_user_per'=>$user_percentage,
                                     'cashback_user_amount'=>$user_amount,
                                 ];
                             $payment_staus= DB::table('list_subscription')->insert($transaction);
                             DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
                        }
                    }else{
                        
                        $total_percentage = $admin_per+$area_partner_per+$service_provider_per;
                        $admint_amount = ($actual_fare*$total_percentage)/100;
                        
                        $user_amount = ($actual_fare*$user_percentage)/100;
                        $driver_amount = ($actual_fare-($admint_amount+$user_amount));
                    
                        // driver wallet add money  
                         $commison3 = [
                                 "user_id"=>$booking_data->driver_id,
                                 "amount"=>$driver_amount,
                                 "transaction_type"=>'1',
                                 "transaction_date"=>date('Y-m-d'),
                                 "booking_id"=>$request->booking_id
                             ];
                         db::table('my_wallet')->insert($commison3); 
                         
                         if($user_percentage > 0){
                             $commison4 = [
                                 "user_id"=>$booking_data->user_id,
                                 "amount"=>$user_amount,
                                 "transaction_type"=>'1',
                                 "transaction_date"=>date('Y-m-d'),
                                 "booking_id"=>$booking_data->booking_id
                             ];
                            db::table('my_wallet')->insert($commison4); 
                         }   
                         
                         // transaction record
                         $transaction = [
                                 'booking_id'=>$booking_data->booking_id,
                                 'amount'=>$booking_data->fare,
                                 'payment_type'=>$payment_type,
                                 'admin_commision_percentage'=>$total_percentage,
                                 'admin_amount'=>$admint_amount,
                                 'driver_amount'=>$driver_amount,
                                 
                                 'cashback_user_id'=>$booking_data->user_id,
                                 'cashback_user_per'=>$user_percentage,
                                 'cashback_user_amount'=>$user_amount,
                         ];
                         $payment_staus= DB::table('list_subscription')->insert($transaction);
                         DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
                    }
                    if($payment_staus){
        
                        $arr['status']=1;
                        $arr['message']='Success';
                        $arr['data']=$payment_staus;
                        
                    }else{
                        $arr['status']=0;
                        $arr['message']='failed';
                        $arr['data']=NULL;
                    }
                }else{
                    $arr['status']=1;
                    $arr['message']="Order Already paid";
                    $arr['data']=NULL;
                }
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL;
            }

        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    public function total_wallet_amount(Request $request){
        try 
        {
            $driverId=Auth::id();
            $wallet_amount = $this->calculate_wallet_amount($driverId);

            $arr['status']=1;
            $arr['message']="success !";
            $arr['data']=number_format((float)$wallet_amount, 2, '.', '');  
           
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    }
    
    
    

    public function user_pay_driver_by_online(Request $request){
        
        $typevalidate=Validator::make($request->all(),[ 
            'booking_id'=>'required',
        ]);
        
        if($typevalidate->fails())
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        try 
        {
            $driverId=Auth::id();
            $booking_data = DB::table('bookings')->where('booking_id', $request->booking_id)->first();
            if($booking_data){
                $get_data = DB::table('list_subscription')->where('booking_id', $booking_data->booking_id)->first();
                if(empty($get_data)){
                  $amount=$booking_data->fare;
                  
                //   dd($amount);
                //   $amount= "1";
                   $redirect_url='driver_online_payment';
                   $payment_link['url']=$this->ccavenue_payment($amount,$redirect_url,$request->booking_id);
                   if($payment_link){
                        $arr['status']=1;
                        $arr['message']='Success';
                        $arr['data']=$payment_link;
                        
                    }else{
                        $arr['status']=0;
                        $arr['message']='failed';
                        $arr['data']=NULL;
                    }

                }else{
                    $arr['status']=1;
                    $arr['message']="Order Already paid";
                    $arr['data']=NULL;
                }
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL;
            }

        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    public function return_user_pay_online_with_bookingid(Request $request){
        $rawData = $request->getContent();
        \Log::info('qr_call_back_log : ' . $rawData);
        
        $response = $rawData;
        
        // Decrypt the encrypted payload
        $filePath = public_path('decryption_key/carmy.in.key');
        $privateKey = file_get_contents($filePath);
        
        // Encrypted payload (base64 encoded)
        $encPayload = $response;
        
        // Base64 decode the encrypted payload
        $encryptedPayload = base64_decode($encPayload);

        // Decrypt the encrypted payload using RSA/ECB/PKCS1Padding
        $decryptedPayload = '';
        openssl_private_decrypt($encryptedPayload, $decryptedPayload, $privateKey, OPENSSL_PKCS1_PADDING);
        
        \Log::info('decrypt_data : ' . $decryptedPayload);
        
        $response_data = json_decode($decryptedPayload, true);
        // dd();
        try {
            if(isset($response_data) && isset($response_data['TxnStatus']) && $response_data['TxnStatus'] == "SUCCESS"){
                
                if(isset($response_data['merchantTranId']) && $response_data['merchantTranId'] != " "){
                    $booking_id = $response_data['merchantTranId'];
                    
                    // Add Wallet Amount By using QR
                    $wallet_data = DB::table('my_wallet')->where('order_id', $booking_id)->first();
                    if($wallet_data){
                        DB::table('my_wallet')->where('id',$wallet_data->id)->update(['status'=>1,'transaction_id'=>time(), 'payment_type'=>'wallet']);
                    }

                    $booking_data = DB::table('bookings')->where('booking_qr', $booking_id)->first();
                    if(empty($booking_data)){
                        $book_data_id = explode('_', $booking_id);
                        $book_id = (isset($book_data_id[0]))?$book_data_id[0]:"";
                        $booking_data = DB::table('bookings')->where('booking_id', $book_id)->first();
                    }
                    
                    if($booking_data){
                        $get_data = DB::table('list_subscription')->where('booking_id', $booking_data->booking_id)->first();
    
                        if(empty($get_data)){
                            
                            // dd($get_data);
    
                             $payment_type = '5';
                             
                             $actual_fare_new = ($booking_data->fare)??1;
                             
                             $gst_percentage = (get_option_data('gst_percentage'))??"0";
                             
                             $gst_amount = ($booking_data->gst_amount)??1;
                             
                             $actual_fare = ($actual_fare_new-$gst_amount);
                             
                             $admin_per = $area_partner_per = $service_provider_per = $user_percentage = 0;
                             
                             $percentage_calculator= db::table('mlm_percentage')->select('user_name','percent')->where('status','1')->get();
                             foreach($percentage_calculator as $val){
                                if($val->user_name=='admin'){
                                    $admin_per=$val->percent;
                                }
                                if($val->user_name=='area partner'){
                                    $area_partner_per=$val->percent;
                                }
                                if($val->user_name=='sponsor'){
                                    $service_provider_per=$val->percent;
                                }
                                if($val->user_name=='User'){
                                    $user_percentage=$val->percent;
                                }
                            }
                            
                            $sp_details = DB::table('sp_driver_table')->where('driver_id', $booking_data->driver_id)->first();
                            $payment_staus = [];
                            if($sp_details){
                                $ap_details = DB::table('area_sp_table')->where('service_partner_id', $sp_details->service_provide_id)->first();
                                if($ap_details){
                                    
                                    $admint_amount = ($actual_fare*$admin_per)/100;
                                    $service_provider_amount = ($actual_fare*$service_provider_per)/100;
                                    $area_partner_amount = ($actual_fare*$area_partner_per)/100;
                                    
                                    $user_amount = ($actual_fare*$user_percentage)/100;
                                    
                                    $total_other_pay=$admint_amount+$service_provider_amount+$area_partner_amount+$user_amount;
                                    $driver_amount = ($actual_fare-$total_other_pay);
                                    
                                    $payfair=[
                                         "user_id"=>$booking_data->driver_id,
                                         "amount"=>$driver_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id
                                     ];
                                     
                                     db::table('my_wallet')->insert($payfair);  
                                    
                                    // area partner wallet add money
                                     $commison1 = [
                                             "user_id"=>$ap_details->area_id,
                                             "amount"=>$area_partner_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                         db::table('my_wallet')->insert($commison1);
                                     
                                     // service partner wallet add money    
                                     $commison2 = [
                                             "user_id"=>$ap_details->service_partner_id,
                                             "amount"=>$service_provider_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                        db::table('my_wallet')->insert($commison2); 
                                        
                                    if($user_percentage > 0){
                                         $commison4 = [
                                             "user_id"=>$booking_data->user_id,
                                             "amount"=>$user_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                        db::table('my_wallet')->insert($commison4); 
                                     }    
                                     
                                     // transaction record
                                     $transaction = [
                                             'booking_id'=>$booking_data->booking_id,
                                             'amount'=>$booking_data->fare,
                                             'payment_type'=>$payment_type,
                                             'admin_commision_percentage'=>$admin_per,
                                             'admin_amount'=>$admint_amount,
                                             'area_partner_id'=>$ap_details->area_id,
                                             'service_partner_id'=>$ap_details->service_partner_id,
                                             'service_partner_percentage'=>$service_provider_per,
                                             'service_partner_amount'=>$service_provider_amount,
                                             'area_partner_percentage'=>$area_partner_per,
                                             'area_partner_amount'=>$area_partner_amount,
                                             'driver_amount'=>$driver_amount,
                                             'gst_percentage'=>$gst_percentage,
                                             'gst_amount'=>$gst_amount,
                                             
                                             'cashback_user_id'=>$booking_data->user_id,
                                             'cashback_user_per'=>$user_percentage,
                                             'cashback_user_amount'=>$user_amount,
                                         ];
                                     $payment_staus= DB::table('list_subscription')->insert($transaction);
                                     DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
                                }
                            }else{
                                
                                $total_percentage = $admin_per+$area_partner_per+$service_provider_per;
                                $admint_amount = ($actual_fare*$total_percentage)/100;
                                $user_amount = ($actual_fare*$user_percentage)/100;
                                $driver_amount = ($actual_fare-($admint_amount+$user_amount));
                            
                                // driver wallet debit commision  
                                $payfair=[
                                         "user_id"=>$booking_data->driver_id,
                                         "amount"=>$driver_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id,
                                     ];
                                     
                                     db::table('my_wallet')->insert($payfair);  
                                     
                                if($user_percentage > 0){
                                     $commison4 = [
                                         "user_id"=>$booking_data->user_id,
                                         "amount"=>$user_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id
                                     ];
                                    db::table('my_wallet')->insert($commison4); 
                                 }     
                                 
                                 // transaction record
                                 $transaction = [
                                         'booking_id'=>$booking_data->booking_id,
                                         'amount'=>$booking_data->fare,
                                         'payment_type'=>$payment_type,
                                         'admin_commision_percentage'=>$total_percentage,
                                         'admin_amount'=>$admint_amount,
                                         'driver_amount'=>$driver_amount,
                                         'gst_percentage'=>$gst_percentage,
                                         'gst_amount'=>$gst_amount,
                                         
                                         'cashback_user_id'=>$booking_data->user_id,
                                         'cashback_user_per'=>$user_percentage,
                                         'cashback_user_amount'=>$user_amount,
                                     ];
                                 $payment_staus= DB::table('list_subscription')->insert($transaction);
                                 DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
                            }
                            
                            $title = "Ride payment ";
                            $message = "Your Ride (". $booking_data->booking_id .") payment has been successfull !";
                            send_firebase_notification($title, $message, $booking_data->user_id);
                            send_firebase_notification($title, $message, $booking_data->driver_id);
                            
                            \Log::info('Payment Successfull !');
                            return response()->json(['all_data' => $response_data,]);
                            
                        }
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
            \Log::info('Something Went Wrong !');
            return response()->json($arr,200); 
        } 

    }
    
    
    
    /////////////////////////  TEST Return Bank QR Code  ///////////////////////
    
    
    public function return_online_with_bookingid_test(Request $request){

        try {

                    $booking_id = $request->booking_id;
                    
                    

                    $booking_data = DB::table('bookings')->where('booking_id', $booking_id)->first();

                    
                    if($booking_data){
                        $get_data = DB::table('list_subscription')->where('booking_id', $booking_data->booking_id)->first();

                        if(empty($get_data)){
                            
                            // dd($get_data);
    
                             $payment_type = '5';
                             
                             $actual_fare_new = ($booking_data->fare)??1;
                             
                             $gst_percentage = (get_option_data('gst_percentage'))??"0";
                             
                             $gst_amount = ($booking_data->gst_amount)??1;
                             
                             $actual_fare = ($actual_fare_new-$gst_amount);
                             
                             $admin_per = $area_partner_per = $service_provider_per = $user_percentage = 0;
                             
                             $percentage_calculator= db::table('mlm_percentage')->select('user_name','percent')->where('status','1')->get();
                             foreach($percentage_calculator as $val){
                                if($val->user_name=='admin'){
                                    $admin_per=$val->percent;
                                }
                                if($val->user_name=='area partner'){
                                    $area_partner_per=$val->percent;
                                }
                                if($val->user_name=='sponsor'){
                                    $service_provider_per=$val->percent;
                                }
                                if($val->user_name=='User'){
                                    $user_percentage=$val->percent;
                                }
                            }
                            
                            $sp_details = DB::table('sp_driver_table')->where('driver_id', $booking_data->driver_id)->first();
                            $payment_staus = [];
                            if($sp_details){
                                $ap_details = DB::table('area_sp_table')->where('service_partner_id', $sp_details->service_provide_id)->first();
                                if($ap_details){
                                    
                                    $admint_amount = ($actual_fare*$admin_per)/100;
                                    $service_provider_amount = ($actual_fare*$service_provider_per)/100;
                                    $area_partner_amount = ($actual_fare*$area_partner_per)/100;
                                    
                                    $user_amount = ($actual_fare*$user_percentage)/100;
                                    
                                    $total_other_pay=$admint_amount+$service_provider_amount+$area_partner_amount+$user_amount;
                                    $driver_amount = ($actual_fare-$total_other_pay);
                                    
                                    $payfair=[
                                         "user_id"=>$booking_data->driver_id,
                                         "amount"=>$driver_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id
                                     ];
                                     
                                     db::table('my_wallet')->insert($payfair);  
                                    
                                    // area partner wallet add money
                                     $commison1 = [
                                             "user_id"=>$ap_details->area_id,
                                             "amount"=>$area_partner_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                         db::table('my_wallet')->insert($commison1);
                                     
                                     // service partner wallet add money    
                                     $commison2 = [
                                             "user_id"=>$ap_details->service_partner_id,
                                             "amount"=>$service_provider_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                        db::table('my_wallet')->insert($commison2); 
                                        
                                    if($user_percentage > 0){
                                         $commison4 = [
                                             "user_id"=>$booking_data->user_id,
                                             "amount"=>$user_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                        db::table('my_wallet')->insert($commison4); 
                                     }    
                                     
                                     // transaction record
                                     $transaction = [
                                             'booking_id'=>$booking_data->booking_id,
                                             'amount'=>$booking_data->fare,
                                             'payment_type'=>$payment_type,
                                             'admin_commision_percentage'=>$admin_per,
                                             'admin_amount'=>$admint_amount,
                                             'area_partner_id'=>$ap_details->area_id,
                                             'service_partner_id'=>$ap_details->service_partner_id,
                                             'service_partner_percentage'=>$service_provider_per,
                                             'service_partner_amount'=>$service_provider_amount,
                                             'area_partner_percentage'=>$area_partner_per,
                                             'area_partner_amount'=>$area_partner_amount,
                                             'driver_amount'=>$driver_amount,
                                             'gst_percentage'=>$gst_percentage,
                                             'gst_amount'=>$gst_amount,
                                             
                                             'cashback_user_id'=>$booking_data->user_id,
                                             'cashback_user_per'=>$user_percentage,
                                             'cashback_user_amount'=>$user_amount,
                                         ];
                                     $payment_staus= DB::table('list_subscription')->insert($transaction);
                                     DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
                                }
                            }else{
                                
                                $total_percentage = $admin_per+$area_partner_per+$service_provider_per;
                                $admint_amount = ($actual_fare*$total_percentage)/100;
                                $user_amount = ($actual_fare*$user_percentage)/100;
                                $driver_amount = ($actual_fare-($admint_amount+$user_amount));
                            
                                // driver wallet debit commision  
                                $payfair=[
                                         "user_id"=>$booking_data->driver_id,
                                         "amount"=>$driver_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id,
                                     ];
                                     
                                     db::table('my_wallet')->insert($payfair);  
                                     
                                if($user_percentage > 0){
                                     $commison4 = [
                                         "user_id"=>$booking_data->user_id,
                                         "amount"=>$user_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id
                                     ];
                                    db::table('my_wallet')->insert($commison4); 
                                 }     
                                 
                                 // transaction record
                                 $transaction = [
                                         'booking_id'=>$booking_data->booking_id,
                                         'amount'=>$booking_data->fare,
                                         'payment_type'=>$payment_type,
                                         'admin_commision_percentage'=>$total_percentage,
                                         'admin_amount'=>$admint_amount,
                                         'driver_amount'=>$driver_amount,
                                         'gst_percentage'=>$gst_percentage,
                                         'gst_amount'=>$gst_amount,
                                         
                                         'cashback_user_id'=>$booking_data->user_id,
                                         'cashback_user_per'=>$user_percentage,
                                         'cashback_user_amount'=>$user_amount,
                                     ];
                                 $payment_staus= DB::table('list_subscription')->insert($transaction);
                                 DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
                            }
                            
                            return response()->json(['all_data' => $response_data,]);
                            
                        }
                    }


        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
            \Log::info('Something Went Wrong !');
            return response()->json($arr,200); 
        } 

    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    
    
// ccavenue payment gateway code

public function ccavenue_payment($amount,$redirect_url,$order_id){
    $senderId = Auth::id();
   
    $user_details=db::table('users')
      ->leftjoin('user_addressess','users.id','user_addressess.user_id')
      ->where('users.id',$senderId)->first();
      
    //     $working_key = "A4DDD9211D9486BEF90472C21E79A87F";
    // 	$access_code = "AVDT05LC60BE16TDEB";
    // 	$testurl= "https://test.ccavenue.com "; 
      
	$working_key = get_option_data('working_key'); // Testing A4DDD9211D9486BEF90472C21E79A87F
	$access_code = get_option_data('access_code');  // Testing AVDT05LC60BE16TDEB
	$testurl= get_option_data('ccavenue_url');  // Testing https://test.ccavenue.com 
	
    // 	dd($testurl);
    
    $name=($user_details->name)?$user_details->name:'';
    $address=($user_details->address)?$user_details->address:'';
    $state=($user_details->state)?$user_details->state:'';
    $pincode=($user_details->pincode)?$user_details->pincode:'';
    $city=($user_details->city)?$user_details->city:'';
    $mobile=($user_details->mobile_no)?$user_details->mobile_no:'';
    $email=($user_details->email)?$user_details->email:'';


    $input['amount'] = $amount;
    $input['order_id'] = $order_id;
    $input['currency'] = "INR";
    $input['redirect_url'] = route($redirect_url);
    $input['cancel_url'] = route($redirect_url);
    $input['language'] = "EN";
    $input['merchant_id'] = get_option_data('merchant_id'); // Testing 1734948

    $merchant_data = "";
    
    $input['billing_name']=$name;
    $input['billing_address']=$address;
    $input['billing_state']=$state;
    $input['billing_zip']=$pincode;
    $input['billing_city']=$city;
    $input['billing_country']="INDIA";
    $input['billing_tel']=$mobile;
    $input['billing_email']=$email;
  
    $input['merchant_param1'] = "some-custom-inputs"; // optional parameter
    $input['merchant_param2'] = "some-custom-inputs"; // optional parameter
    $input['merchant_param3'] = "some-custom-inputs"; // optional parameter
    $input['merchant_param4'] = "some-custom-inputs"; // optional parameter
    $input['merchant_param5'] = "some-custom-inputs"; // optional parameter
    
    foreach ($input as $key => $value) {
        $merchant_data .= $key . '=' . $value . '&';
    }
    
    $encrypted_data = $this->encryptCC($merchant_data, $working_key);
    
    $url = $testurl.'/transaction/transaction.do?command=initiateTransaction&encRequest=' . $encrypted_data . '&access_code=' . $access_code;

    return $url;
        
    }
    
    public function encryptCC($plainText, $key){
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }
    public function decryptCC($encryptedText, $key){
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }
    public function pkcs5_padCC($plainText, $blockSize){
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }
    public function hextobin($hexString){
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }
    
            $count += 2;
        }
        return $binString;
    }
// ccavenue payment gateway code End here 


    public function terms_conditions(Request $request){
        try 
        {
            $user_id=Auth::id();
            $data = DB::table('options')->select('id', 'key as type', 'value as description')->where('key', 'terms_condition')->first();
            // $data = DB::table('terms_conditions')->where('type', '1')->first();
            if($data){
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$data; 
                
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL; 
            }
                       
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    }
    
    public function terms_conditions_driver(Request $request){
        try 
        {
            $user_id=Auth::id();

            // $data = DB::table('terms_conditions')->where('type', '2')->first();
            $data = DB::table('options')->select('id', 'key as type', 'value as description')->where('key', 'terms_condition_driver')->first();
            if($data){
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$data; 
                
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL; 
            }
                       
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    }
    
    
     public function privacy_policy_user(Request $request){
        try 
        {
            $user_id=Auth::id();

            $data = DB::table('options')->select('id', 'key as type', 'value as description')->where('key', 'privacy_policy')->first();

            if($data){
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$data; 
                
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL; 
            }
                       
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    }
    
    public function privacy_policy_driver(Request $request){
        try 
        {
            $user_id=Auth::id();

            $data = DB::table('options')->select('id', 'key as type', 'value as description')->where('key', 'privacy_policy_driver')->first();
            
            if($data){
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$data; 
                
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL; 
            }
                       
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    }
    
    public function privacy_policy_sponser(Request $request){
        try 
        {
            $user_id=Auth::id();

            $data = DB::table('privacy_policy')->where('type', '3')->first();
            if($data){
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$data; 
                
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL; 
            }
                       
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    }
    
    public function privacy_policy_area_partner(Request $request){
        try 
        {
            $user_id=Auth::id();

            $data = DB::table('privacy_policy')->where('type', '4')->first();
            if($data){
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$data; 
                
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL; 
            }
                       
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    }
    
    public function get_google_map_api_key(){
        $arr['status']=1;
        $arr['message']="success !";
        $arr['api_key']=get_option_data('google_map_api_key'); 
        return response()->json($arr,200);
    }

    public function invoice_details(Request $request){
        
         \Log::info('invoice_details : ');
        \Log::info($request->all());
        
        $typevalidate=Validator::make($request->all(),[ 
            'booking_id'=>'required',
        ]);
        
        if($typevalidate->fails())
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        try 
        {
            $driverId=Auth::id();
            $booking_data = DB::table('bookings as b')
                            ->select('b.booking_id', 'b.user_id', 'b.driver_id', 'b.picup_location'
                            , 'b.drop_location', 'l.payment_type', 'b.booking_status','b.fare as total_fare', 
                            'l.admin_commision_percentage', 'l.admin_amount', 'l.driver_amount',
                            'l.area_partner_percentage', 'l.service_partner_percentage', 'l.service_partner_amount', 'l.area_partner_amount', 'l.tds_percentage'
                            , 'l.tds_amount', 'u.name as user_name', 'd.name as driver_name', 'a.name as area_partner_name', 's.name as sponser_name'
                            , 'a.referral_code as areapartner_referal', 's.referral_code as servicepartner_referal', 'b.distance', 'd.email as driver_email'
                            , 'd.mobile_no as driver_mobile', 'st.state_name as driver_state_name', 'ct.name as driver_city_name')
                            ->leftJoin('list_subscription as l','l.booking_id','=','b.booking_id')
                            ->leftJoin('users as u','u.id','=','b.user_id')
                            ->leftJoin('users as d','d.id','=','b.driver_id')
                            ->leftJoin('users as a','a.id','=','l.area_partner_id')
                            ->leftJoin('users as s','s.id','=','l.service_partner_id')
                            ->leftJoin('states  as st','st.id','=','d.state')
                            ->leftJoin('cities as ct','ct.id','=','d.city')
                            ->where('b.booking_id', $request->booking_id)->first();
            if($booking_data){
                $arr['status']=1;
                $arr['message']="Success !";
                $arr['data']=$booking_data;
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL;
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    public function driver_online_payment(Request $request){      
        // try 
        // {
            
            \Log::info('driver_online_payment : ');
            \Log::info($request->all());
    
            $workingKey = get_option_data('working_key'); //Working Key should be provided here.
            $encResponse = $request->encResp;
    
            $rcvdString = $this->decryptCC($encResponse, $workingKey);        //Crypto Decryption used as per the specified working key.
            $order_status = "";
            $decryptValues = explode('&', $rcvdString);
            $dataSize = sizeof($decryptValues);
            $new_array=[];
            
            foreach($decryptValues as $key=> $val){
                 $inside_array = explode('=', $val);
                 $new_array[$inside_array[0]]=$inside_array[1];
            }
            
            $booking_id=$new_array['order_id'];
            
            if($new_array['order_status']=='Success'){
               
                $booking_data = DB::table('bookings')->where('booking_id',$booking_id)->first();
                
                if($booking_data){
                    $get_data = DB::table('list_subscription')->where([['booking_id', $booking_data->booking_id],['status',1]])->first();
                    
                    if(empty($get_data)){
               
                             $payment_type = $booking_data->payment_mode;
                             
                             $actual_fare_new = ($booking_data->fare)??1;
                             
                             $gst_percentage = (get_option_data('gst_percentage'))??"0";
                             
                             $gst_amount = ($booking_data->gst_amount)??0;
                             
                             $actual_fare = ($actual_fare_new-$gst_amount);
                             
                             $admin_per = $area_partner_per = $service_provider_per = $user_percentage = 0;
                             
                             $percentage_calculator= db::table('mlm_percentage')->select('user_name','percent')->where('status','1')->get();
                             foreach($percentage_calculator as $val){
                                if($val->user_name=='admin'){
                                    $admin_per=$val->percent;
                                }
                                if($val->user_name=='area partner'){
                                    $area_partner_per=$val->percent;
                                }
                                if($val->user_name=='sponsor'){
                                    $service_provider_per=$val->percent;
                                }
                                 if($val->user_name=='User'){
                                    $user_percentage=$val->percent;
                                }
                            }
                            
                            $sp_details = DB::table('sp_driver_table')->where('driver_id', $booking_data->driver_id)->first();
                            $payment_staus = [];
                            if($sp_details){
                                $ap_details = DB::table('area_sp_table')->where('service_partner_id', $sp_details->service_provide_id)->first();
                                if($ap_details){
                                    
                                    $admint_amount = ($actual_fare*$admin_per)/100;
                                    $service_provider_amount = ($actual_fare*$service_provider_per)/100;
                                    $area_partner_amount = ($actual_fare*$area_partner_per)/100;
                                    
                                    $user_amount = ($actual_fare*$user_percentage)/100;
                                    
                                    $total_other_pay=$admint_amount+$service_provider_amount+$area_partner_amount+$user_amount;
                                    $driver_amount = ($actual_fare-$total_other_pay);
                                    
                                    $payfair=[
                                         "user_id"=>$booking_data->driver_id,
                                         "amount"=>$total_other_pay,
                                         "transaction_type"=>'2',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id,
                                     ];
                                     
                                     db::table('my_wallet')->insert($payfair);  
                                    
                                    // area partner wallet add money
                                     $commison1 = [
                                             "user_id"=>$ap_details->area_id,
                                             "amount"=>$area_partner_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                         db::table('my_wallet')->insert($commison1);
                                     
                                     // service partner wallet add money    
                                     $commison2 = [
                                             "user_id"=>$ap_details->service_partner_id,
                                             "amount"=>$service_provider_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                        db::table('my_wallet')->insert($commison2); 
                                        
                                    if($user_percentage > 0){
                                         $commison4 = [
                                             "user_id"=>$booking_data->user_id,
                                             "amount"=>$user_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$booking_data->booking_id
                                         ];
                                        db::table('my_wallet')->insert($commison4); 
                                     }    
                                     
                                     // transaction record
                                     $transaction = [
                                             'booking_id'=>$booking_data->booking_id,
                                             'amount'=>$booking_data->fare,
                                             'payment_type'=>$payment_type,
                                             'admin_commision_percentage'=>$admin_per,
                                             'admin_amount'=>$admint_amount,
                                             'area_partner_id'=>$ap_details->area_id,
                                             'service_partner_id'=>$ap_details->service_partner_id,
                                             'service_partner_percentage'=>$service_provider_per,
                                             'service_partner_amount'=>$service_provider_amount,
                                             'area_partner_percentage'=>$area_partner_per,
                                             'area_partner_amount'=>$area_partner_amount,
                                             'driver_amount'=>$driver_amount,
                                             'gst_percentage'=>$gst_percentage,
                                             'gst_amount'=>$gst_amount,
                                             
                                             'cashback_user_id'=>$booking_data->user_id,
                                             'cashback_user_per'=>$user_percentage,
                                             'cashback_user_amount'=>$user_amount,
                                         ];
                                     $payment_staus= DB::table('list_subscription')->insert($transaction);
                                     DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
                                }
                            }else{
                                
                                $total_percentage = $admin_per+$area_partner_per+$service_provider_per;
                                $admint_amount = ($actual_fare*$total_percentage)/100;
                                $user_amount = ($actual_fare*$user_percentage)/100;
                                $driver_amount = ($actual_fare-($admint_amount+$user_amount));
                            
                                // driver wallet debit commision  
                                $payfair=[
                                         "user_id"=>$booking_data->driver_id,
                                         "amount"=>$admint_amount,
                                         "transaction_type"=>'2',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id
                                    ];
                                    
                                    // dd($payfair);
                                     
                                     db::table('my_wallet')->insert($payfair);  
                                     
                                if($user_percentage > 0){
                                     $commison4 = [
                                         "user_id"=>$booking_data->user_id,
                                         "amount"=>$user_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$booking_data->booking_id
                                     ];
                                    db::table('my_wallet')->insert($commison4); 
                                 }      
                                 
                                   // transaction record
                                   $transaction = [
                                         'booking_id'=>$booking_data->booking_id,
                                         'amount'=>$booking_data->fare,
                                         'payment_type'=>$payment_type,
                                         'admin_commision_percentage'=>$total_percentage,
                                         'admin_amount'=>$admint_amount,
                                         'driver_amount'=>$driver_amount,
                                         'gst_percentage'=>$gst_percentage,
                                         'gst_amount'=>$gst_amount,
                                         
                                         'cashback_user_id'=>$booking_data->user_id,
                                         'cashback_user_per'=>$user_percentage,
                                         'cashback_user_amount'=>$user_amount,
                                    ];
                                 $payment_staus= DB::table('list_subscription')->insert($transaction);
                                 DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
                            }
                            
                        $booking_data = DB::table('bookings')->where('booking_id', $booking_data->booking_id)->first();
                        
                        return view('payment_success', compact('booking_data'));
                            
                        $arr['status']=1;
                        $arr['message']="success !";
                        $arr['data']=NULL;
                        return response()->json($arr,200);
                    }else{
                        $arr['status']=1;
                        $arr['message']="Order Already paid";
                        $arr['data']=NULL;
                        return response()->json($arr,200);
                    }
                }
            }else{
                
                 $booking_data = DB::table('bookings')->where('booking_id', $booking_id)->first();
                 return view('payment_failed', compact('booking_data'));
                
            }

        // }
        // catch(\Exception $e)
        // {
        //     $arr['status']=0;
        //     $arr['message']="Something went Wrong !";
        //     $arr['data']=NULL;
        // }
        return response()->json($arr,200);

    }
    
    public function check_payment_status(Request $request){
        $typevalidate=Validator::make($request->all(),[ 
            'booking_id'=>'required',
        ]);
        
        if($typevalidate->fails())
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        try 
        {
            $data = DB::table('bookings as b')
                    ->select('b.id', 'b.booking_id', 'b.payment_status')
                    ->where('b.booking_id', $request->booking_id)
                    ->first();
                    
            if($data){
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$data; 
                
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL; 
            }
                       
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200); 
    }
    
    public function composute_amount(Request $request){
        $typevalidate=Validator::make($request->all(),[ 
            'amount'=>'required',
        ]);
        
        if($typevalidate->fails())
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        try {
           $userid = Auth::id();
           $withdraw_id = "2";

        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']=$e->getMessage();
            $arr['data']=NULL;
            return response()->json($arr,200); 
        }
    }
    
    public function user_add_money_in_wallet_by_qr(Request $request){
          
        $validate=Validator::make($request->all(),[
            'add_ammount' =>'required'
        ]);
            
        if($validate->fails()){
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
         try{
             
            // $booking_id = "qr1726577961"; 
            // // Add Wallet Amount By using QR
            // $wallet_data = DB::table('my_wallet')->where('order_id', $booking_id)->first();
            // if($wallet_data){
            //     DB::table('my_wallet')->where('id',$wallet_data->id)->update(['status'=>1,'transaction_id'=>time(), 'payment_type'=>'wallet']);
            // }
            
            // die("+++");
             
            $senderId = Auth::id();
            $amount=$request->add_ammount;
            
            $order_id="qr".time();
            
            $insert_payment=[
                "user_id"=>$senderId,
                "amount"=>$amount,
                "transaction_type"=>'1',
                "order_id"=>$order_id,
                'status'=>2,
                "transaction_date"=>date('Y-m-d')
                ];
            
            $check_insert= db::table('my_wallet')->insert($insert_payment);
            
            if($check_insert){
                $arr['status']=1;
                $arr['message']='Success';
                $arr['order_id'] = $order_id;
            }else{
                $arr['status']=0;
                $arr['message']='Not Data Found !';
                $arr['data']=NULL;
            }
        
        }catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
    }
    
    
    public function check_wallet_paymet_status(Request $request){
          
        $validate=Validator::make($request->all(),[
            'order_id' =>'required'
        ]);
            
        if($validate->fails()){
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
         try{

            $senderId = Auth::id();
            $data= DB::table('my_wallet')->where('order_id', $request->order_id)->first();
            
            if($data){
                
                if($data->status == "2"){
                    $payment_ststus = "pending";
                }else{
                    $payment_ststus = "success";
                }
                
                $all_data = [
                    'payment_status'=> $payment_ststus,
                    'payment_code'=> $data->status,   
                ];
                
                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']= $all_data;
            }else{
                $arr['status']=0;
                $arr['message']='Not Data Found !';
                $arr['data']=NULL;
            }
        
        }catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
    }
    
    public function send_testing_notification(Request $request){
        $validate=Validator::make($request->all(),[
            'title' =>'required',
            'message' =>'required'
        ]);
            
        if($validate->fails()){
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        // try{

            $senderId = Auth::id();
            $response = send_firebase_notification($request->title, $request->message, $senderId);

            print_r($response);
            die;
        
        // }catch(\Exception $e)
        // {
        //     $arr['status']=0;
        //     $arr['message']='Sorry!! Something Went Wrong';
        //     $arr['data']= NULL;
        // }

        return response()->json($arr,200);
    }
    
    
    public function rental_rate_apply_km(Request $request){

        try{

            $response = get_option_data("rental_rate_apply_km");

            $arr['status']=1;
            $arr['message']='Success';
            $arr['data']= ['rental_km' => $response];
            return response()->json($arr,200);
        
        }catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }
        return response()->json($arr,200);
    }
    
    
    
    
    
}