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
use Auth;
use Session;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\WebSocketService;

class ApiController extends Controller
{
    
    protected $webSocketService;

    public function __construct(WebSocketService $webSocketService) {
        $this->webSocketService = $webSocketService;
    }
    
    // Web Socket Apis
    
    public function healthCheck()
    {
        $response = $this->webSocketService->checkHealth();

        return response()->json([
            'success' => true,
            'websocket_server' => $response
        ]);
    }
    
    public function getOnlineUsers()
    {
        $response = $this->webSocketService->getOnlineUsers();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }
    
    public function checkUsersOnline(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $response = $this->webSocketService->checkUsersOnline($request->user_ids);

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }
   
    public function sendNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer',
            'title' => 'required|string|max:100',
            'message' => 'required|string|max:255',
            'data' => 'array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $response = $this->webSocketService->sendNotification(
            $request->user_ids,
            $request->title,
            $request->message,
            $request->data ?? []
        );

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }
    
    public function get_wallet_list(Request $request)
    {
      try
        {
            $id = Auth::id();
    
            $result = DB::table('my_wallet as t1')
            ->select('t1.payment_type as fullname','t1.transaction_date','t1.amount','t1.transaction_type', 't1.status', 't1.booking_id', 't1.withdraw_token')
            ->where('t1.user_id',$id)->orderBy('t1.id', 'DESC')->get()->toArray();
            
            if($result)
            {
                foreach($result as $res){
                    if($res->fullname == "wallet"){
                        $res->fullname = "Wallet Amount Add";
                    }
                    if($res->fullname == "booking"){
                        $res->fullname = "Booking (".$res->booking_id.")";
                    }
                    if($res->fullname == "withdraw"){
                        $res->fullname = "Withdraw (".$res->withdraw_token.")";
                    }
                    $res->amount = number_format((float)$res->amount, 2, '.', ''); 
                }
                
                $arr['status']=1;
                $arr['message']='Success';
                $arr['TotalAmount']= get_wallet_balance($id);
                $arr['data']=$result;
            }
            else
            {
                $arr['status']=0;
                $arr['message']='No Data Found';
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
    
    public function add_wallet_money(Request $request){
        
        $typevalidate=Validator::make($request->all(),[ 
            'amount'=>'required',
            'transaction_id'=>'required',
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

            $data = [
                'user_id' => $userid,
                'amount' => $request->amount,
                'transaction_type' => '1',
                'transaction_date' => date('d-m-Y'),
                'transaction_id' => $request->transaction_id,
                'status' => "1",
                'payment_type' => "wallet",
            ];
            
            DB::table('my_wallet')->insert($data);
        
            
            $arr['status']=1;
            $arr['message']='Amount added successfully..';
            $arr['data']=NULL;
           
            return response()->json($arr,200);
                
        } 
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
        
    }
    
    public function notification_list(Request $request)
    {
     
            $user_id =  Auth::id();
            
            $get_notification = DB::table('notifications')->where('user_id', $user_id)->get()->toArray();
            
            
            if(!empty($get_notification)){
                
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$get_notification;
               
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
      
            
        
    }
    
    public function help(Request $request){
        
        try{

            $senderId = Auth::id();
            
            $user_data = DB::table('users')->where('id', $senderId)->first();
            
            $type = ($user_data->type == "1")?"1":"2";
            
            $data= DB::table('faqs')->where('type', $type)->where('status', '1')->get();
            
            if($data){
                
                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']= $data;
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
    
    public function user_profile(Request $request){
        
        try {

            $userid = Auth::id();

            $user_data = DB::table('users')->where('id', $userid)->first();
            if($user_data->type == "2"){
                
                $vehicle_data_details =DB::table('driver_details_basic_details')->where('driver_id',$user_data->id)->first(); 
                
                $user_data->driver_id = "DRIVER".$user_data->id;
                $user_data->vehicle_number = $vehicle_data_details->vehicle_number??"";
                $user_data->vehicle_name = check_driver_vehicle($user_data->id);
                
            }
            $user_data->profile = !empty($user_data->profile)?url('public/profile_image/'.$user_data->profile):profileImg();
        
            $arr['status']=1;
            $arr['message']='Success !';
            $arr['data']=$user_data;
           
            return response()->json($arr,200);
                
        } 
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
        
    }
    
    public function update_profile(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'name'=>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        } 
      try {
          
            $id = Auth::id();
          
            $data_user = array(
                'name' => $request->name,
            );
            
            if(isset($request->email) && $request->email != ""){
                $data_user['email'] = $request->email;
            }
            
            if(isset($request->gender) && $request->gender != ""){
                $data_user['gender'] = $request->gender;
            }
            
            if(isset($request->mobile) && $request->mobile != ""){
                $data_user['mobile_no'] = $request->mobile;
            }
            
            if ($request->hasFile('profile')) {
    
                $profile = $request->file('profile');
                $ext = strtolower($profile->getClientOriginalExtension());
                $allowedExt = ["jpg", "png", "jpeg", "gif"];
    
                if (!in_array($ext, $allowedExt)) {
                    return response()->json([
                        'status'  => 0,
                        'message' => "Only jpg, png, jpeg, gif formats are allowed!",
                        'data'    => null
                    ], 200);
                }
    
                // Store new image
                $filename = $this->compressImage($profile, public_path('/profile_image/'));
                $data_user['profile'] = $filename;
                
            }
            
            $data =   DB::table('users')->where('id',$id)->update($data_user);
         
            $user_data = DB::table('users')->where('id',$id)->first();
            $user_data->profile = !empty($user_data->profile)?url('public/profile_image/'.$user_data->profile):profileImg();

            $arr['status']=1;
            $arr['message']='Successfully!!';
            $arr['data']=$user_data;            
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=$e->getMessage();
       }
       return response()->json($arr,200);
    }
    
    public function update_profile_picture(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'profile'=>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        } 
      try {
          
            $id = Auth::id();
            
            if ($request->hasFile('profile')) {
    
                $profile = $request->file('profile');
                $ext = strtolower($profile->getClientOriginalExtension());
                $allowedExt = ["jpg", "png", "jpeg", "gif"];
    
                if (!in_array($ext, $allowedExt)) {
                    return response()->json([
                        'status'  => 0,
                        'message' => "Only jpg, png, jpeg, gif formats are allowed!",
                        'data'    => null
                    ], 200);
                }
    
                // Store new image
                $filename = $this->compressImage($profile, public_path('/profile_image/'));
                $updateData['profile'] = $filename;
    
                // Remove old image
                if (!empty($user->profile)) {
                    $oldImage = public_path('/profile_image/') . $user->profile;
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
                
                DB::table('users')->where('id',$id)->update($updateData);
                
            }
          
            $user_data = DB::table('users')->select('id', 'name', 'profile')->where('id',$id)->first();
            $user_data->profile= !empty($user_data->profile)?url('public/profile_image/'.$user_data->profile):profileImg();

            $arr['status']=1;
            $arr['message']='Successfully!!';
            $arr['data']=$user_data;            
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=$e->getMessage();
       }
       return response()->json($arr,200);
    }
    
    private function compressImage($file, $destinationPath, $quality = 5)
    {
        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));

        $compressedFileName = rand(111111111, 99999999) . '.jpg';
        $destination = $destinationPath . $compressedFileName;

        // Compress the image
        imagejpeg($image, $destination, $quality);

        // Destroy the image resource to free up memory
        imagedestroy($image);

        return $compressedFileName;
    }
    
    public function update_email(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'email'=>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        } 
      try {
          
            $id = Auth::id();
          
            $data_user = array(
                'email' => $request->email,
            );
            $data =   DB::table('users')->where('id',$id)->update($data_user);
         
            $user_data = DB::table('users')->select('id', 'name', 'email')->where('id',$id)->first();

            $arr['status']=1;
            $arr['message']='Successfully!!';
            $arr['data']=$user_data;            
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=$e->getMessage();
       }
       return response()->json($arr,200);
    }
    
    public function send_email_otp(Request $request){
        
        $typevalidate=Validator::make($request->all(),[ 
            'type'=>'required',
            'email'=>'required',
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
            
            $check_email = DB::table('users')->where('email', $request->email)->where('type', $request->type)->first();
            if($check_email){
                
                $arr['status']=0;
                $arr['message']="This Email already Exist. Try Another !";
                $arr['data']=NULL;
                return response()->json($arr,200);
                
            }

            $user_data = DB::table('users')->where('id', $userid)->update(['otp' => rand(1111, 9999)]);
            
            $data = DB::table('users')->select('id', 'otp')->where('id', $userid)->first();
        
            $arr['status']=1;
            $arr['message']='Success !';
            $arr['data']=$data;
           
            return response()->json($arr,200);
                
        } 
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
        
    }
    
    public function email_otp_verify(Request $request){
        
        $typevalidate=Validator::make($request->all(),[ 
            'id'=>'required',
            'otp'=>'required',
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
            
            $check_email = DB::table('users')->where('id', $request->id)->where('otp', $request->otp)->first();
            if($check_email){
                
                $arr['status']=1;
                $arr['message']="Otp verify Successfully !";
                $arr['data']=NULL;
                return response()->json($arr,200);
                
            }else{
                
                $arr['status']=0;
                $arr['message']="Invalid OTP !";
                $arr['data']=NULL;
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
    
    public function send_phone_otp(Request $request){
        
        $typevalidate=Validator::make($request->all(),[ 
            'type'=>'required',
            'mobile'=>'required',
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
            
            $check_email = DB::table('users')->where('mobile_no', $request->mobile)->where('type', $request->type)->first();
            if($check_email){
                
                $arr['status']=0;
                $arr['message']="This Mobile already Exist. Try Another !";
                $arr['data']=NULL;
                return response()->json($arr,200);
                
            }

            $user_data = DB::table('users')->where('id', $userid)->update(['otp' => rand(1111, 9999)]);
            
            $data = DB::table('users')->select('id', 'otp')->where('id', $userid)->first();
        
            $arr['status']=1;
            $arr['message']='Success !';
            $arr['data']=$data;
           
            return response()->json($arr,200);
                
        } 
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
        
    }
    
    public function phone_otp_verify(Request $request){
        
        $typevalidate=Validator::make($request->all(),[ 
            'id'=>'required',
            'otp'=>'required',
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
            
            $check_email = DB::table('users')->where('id', $request->id)->where('otp', $request->otp)->first();
            if($check_email){
                
                $arr['status']=1;
                $arr['message']="Otp verify Successfully !";
                $arr['data']=NULL;
                return response()->json($arr,200);
                
            }else{
                
                $arr['status']=0;
                $arr['message']="Invalid OTP !";
                $arr['data']=NULL;
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
    
    public function update_phone(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'mobile'=>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        } 
      try {
          
            $id = Auth::id();
          
            $data_user = array(
                'mobile_no' => $request->mobile,
            );
            $data =   DB::table('users')->where('id',$id)->update($data_user);
         
            $user_data = DB::table('users')->select('id', 'name', 'mobile_no')->where('id',$id)->first();

            $arr['status']=1;
            $arr['message']='Successfully!!';
            $arr['data']=$user_data;            
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=$e->getMessage();
       }
       return response()->json($arr,200);
    }
    
    public function vehicle_types(Request $request){
        try 
        {
            $get_data = DB::table('vehicle_type')->select('id', 'name', 'image')->where('status', '1')->get()->toArray();
            if($get_data)
            {
                foreach($get_data as $res){
                    $res->image = !empty($res->image)?url('public/vehicle_image/vehicle_type_image/'.$res->image):profileImg();
                }
                
                $arr['status']=1;
                $arr['message']="successfully";
                $arr['data']=$get_data;
            }else{
                $arr['status']=0;
                $arr['message']='No data found ...';
                $arr['data'] =NULL;
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
    
    public function search_vehicle(Request $request)
    {
        \Log::info('search_vehicle : ');
        \Log::info($request->all());
       
        $typevalidate=Validator::make($request->all(),[   
            'pick_up_location'=>'required',
            'pick_up_lat'=>'required',
            'pick_up_long'=>'required',
            'drop_location'=>'required',
            'drop_lat'=>'required',
            'drop_long'=>'required',
          ]);
        try
        {
            if($typevalidate->fails())
            {
                $arr['status']=0;
                $arr['message']= $typevalidate->errors()->first();
                $arr['data']=NULL;
               
                return response()->json($arr,200);
            }
            
            date_default_timezone_set("Asia/Calcutta");  
            
            $user_id =  Auth::id();
            
            $trip_type= "1";
            
            $drop_location = $request->drop_location;
            $drop_lat = $request->drop_lat;
            $drop_long = $request->drop_long;
            
            $latlong = $request->pick_up_lat.",".$request->pick_up_long;
            $city_name = $this->get_city_name($latlong);
            // dd($city_name);
            $googe_ride = calculate_total_distance_time($request->pick_up_lat, $request->pick_up_long, $request->drop_lat, $request->drop_long);
            $dis = $googe_ride['dist'];
            $lenth = $googe_ride['lenth'];
            $net_mintue = $googe_ride['time_val'];
            $time = $googe_ride['time'];
            

            $booking_type = 1;
            if($request->booking_date != "" && $request->booking_date != "Invalid date"){
                 $ck = date('H:i');
                 $endtime = date('H:i', strtotime("15 minutes", strtotime($ck)));
                if(($request->booking_date != date('d-m-Y')) || ($request->booking_date == date('d-m-Y') && ($request->booking_time > $endtime))){
                    $booking_type = 2;
                }
            }
            
            $get_vehicle_list = DB::table('vehicle_type')->select('*',DB::raw("'$dis' as total_distance"),DB::raw("'$time' as total_time")
                                ,DB::raw('CONCAT("' .url("public") .'","/vehicle_image/vehicle_type_image/",image)  as image'))->where('status',1)
                                ->get()->toArray();
                                        
             $all_type_price = [];  
             $all_type_fare = []; 
             $max = 0;
             $delete = DB::table('vehicle_fare')->where('user_id',$user_id)->delete();
             
             foreach($get_vehicle_list as $key=>$val){

                if($dis>0){
                    $total_km = $dis;
                }else{
                    $total_km = 1; 
                }
                $night_rate= $wating_charge = "0";
                
                date_default_timezone_set("Asia/Calcutta"); 
                  
                $picup_date = ($request->booking_date != "" && $request->booking_date != "Invalid date")? $request->booking_date : date('d-m-Y');
                $picup_time = ($request->booking_time)?date('H:i', strtotime($request->booking_time)):date('H:i');
                $ride_time = date("Y-m-d", strtotime($picup_date)).' '.$picup_time.':00'; 
                
                $night_charge = is_night_ride($ride_time, $request->pick_up_lat, $request->pick_up_long);
                
                $outstation = "0";

                $data_fare = calculace_vehicle_fare($val->id, $city_name, $total_km, $night_charge, $outstation, $trip_type, $picup_time);

                $price = $val->price;
                
                $per_km_charge = $price;

                if(isset($data_fare['price']) && $data_fare['price'] != ""){
                    $fare = $data_fare['price'];
                    $wating_charge = $data_fare['wating'];
                    $price_per_km = $data_fare['price_per_km'];
                    $gst = $data_fare['gst_amount'];
                }else{
                    
                    $fare = ($total_km*$price)+$val->base_fare;
                    //   dd($fare);
                    $wating_charge = $val->wating_charge;
                    $price_per_km = $per_km_charge;
                    $gst_percentage = get_option_data('gst_percentage');
                    if(isset($night_charge) && $night_charge == "Night"){
                        $night_fare = DB::table('vehicle_charger')->where('vehicle_name', $val->id)->where('status', 1)->first();
                        if($night_fare){
                            $night_rate = $night_fare->price;
                            $fare = $fare+$night_fare->price;
                        }
                    }
                    if($gst_percentage){
                        $gst = ($fare*$gst_percentage)/100;
                        $fare = $fare+$gst;
                    }
                }
                
                $fare = number_format((float)$fare, 2, '.', '');

                $discount_price = 0; 
                $total_f = $fare;
                $val->fare = $fare;

                $total_time = $val->total_time;
                $val->time_duration = date("h:i A")." - ".$total_time." away";

                date_default_timezone_set("Asia/Calcutta"); 
                
                $data_vehicle=array(
                    'user_id'       =>$user_id,
                    'v_id'          =>$val->id,
                    'rider_id'      =>($request->rider_id)??"",
                    'outstation'    =>$outstation,
                    'booking_type'  =>$booking_type,
                    'pick_up_location'=>($request->pick_up_location)??"",
                    'pick_up_lat'   =>($request->pick_up_lat)??"",
                    'pick_up_long'  =>($request->pick_up_long)??"",
                    'drop_location' =>$drop_location,
                    'drop_lat'      =>$drop_lat,
                    'drop_long'     =>$drop_long,
                    'total_distance'=>$val->total_distance,
                    'total_time'    =>$total_time,
                    'fare'          =>$total_f,
                    'gst_amount'    =>$gst,
                    'discount'      =>$discount_price,
                    'total_fare'    =>$total_f,
                    'base_fare'     =>$val->base_fare,
                    'night_charge'  =>$night_rate,
                    'actual_price'  =>$total_f,
                    'booking_date'  =>($request->booking_date != "" && $request->booking_date != "Invalid date")? $request->booking_date : date('d-m-Y'),
                    'booking_time'  =>($request->booking_time)??date('H:i'),
                    'wating_charge' =>$wating_charge,
                    'price_per_km'  =>$price_per_km,
                );
                
                DB::table('vehicle_fare')->insert($data_vehicle);
                
            }
             
            if(!empty($get_vehicle_list)){

                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$get_vehicle_list;
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
    
    
    public function search_vehicle_id(Request $request)
    {
      $typevalidate=Validator::make($request->all(),[   
             'id'=>'required',
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
            $user_id =  Auth::id();
            
            $vehicle_data = DB::table('vehicle_fare as vf')
                            ->select('vf.*', 'vehicle_type.image','vehicle_type.name as vehicle_name', 'coupon_codes.code as applied_cupon', 'vf.total_fare as before_discount_fare')
                            ->leftjoin('vehicle_type','vehicle_type.id','=','vf.v_id')
                            ->leftjoin('coupon_codes','coupon_codes.id','=','vf.applied_cupon_id')
                            ->where('vf.user_id', $user_id)->where('vf.v_id', $request->id)->first();
                            
            if(!empty($vehicle_data)){
                $vehicle_data->time_duration = date("h:i A")." - ".$vehicle_data->total_time." away";
                if($request->id == "0"){
                    $vehicle_data->image = url("public") ."/vehicle_image/vehicle_type_image/all_vehicle_image.png"; 
                }else{
                    $vehicle_data->image = ($vehicle_data->image)?url("public") ."/vehicle_image/vehicle_type_image/".$vehicle_data->image:"";
                }
                $vehicle_data->before_discount_fare = number_format((float)$vehicle_data->before_discount_fare, 2, '.', '');
                $vehicle_data->total_fare = number_format((float)$vehicle_data->total_fare, 2, '.', '');
                
                $vehicle_data->payment_method = ($vehicle_data->payment_method == "" || $vehicle_data->payment_method == "0")?"2":$vehicle_data->payment_method;
                
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=$vehicle_data;
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
            }
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']=$e->getMessage();
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
 
    }
    
    public function payment_modes(Request $request){
        
        try 
        {
            $user_id=Auth::id();
            
            $data = DB::table('payment_modes')->where('status', '1')->get();
            if($data){
                
                foreach($data as $key => $result){
                    $result->image = url("public").'/payment_modes/'.$result->image;
                }
                
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
    
    public function add_payment_method(Request $request){
        
        \Log::info('add_payment_method : ');
        \Log::info($request->all());
        
        $typevalidate=Validator::make($request->all(),[ 
            'id'=>'required',
            'type'=>'required',
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
            $user_id=Auth::id();
            // dd($this->calculate_wallet_amount($user_id));
            $price = $request->price;
            $payment_method = $request->type;
            
            $data = DB::table('vehicle_fare')->where('id', $request->id)->first();
            if($data){
                
                if($payment_method == "1"){
                    $wallet_amount = $this->calculate_wallet_amount($user_id);
                    if($wallet_amount < $data->total_fare){
                        $arr['status']=0;
                        $arr['message']= "Your Wallet Amount is Not Sufficent. Please Add Amount !";
                        $arr['data']=NULL;
                        return response()->json($arr,200);
                    }
                    
                }
                
                DB::table('vehicle_fare')->where('id', $data->id)->update(['payment_method'=>$payment_method]);
                            
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=NULL; 
                
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
    
    public function add_ride_details(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'id'           =>'required',
            'fulname'           =>'required',
            'mobile'           =>'required',
            'email'           =>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        
       
      try {
          
       $id = Auth::id();
       $fullname = $request->fulname;
       $mobile = $request->mobile;
       $email = $request->email;
       
       $result = DB::table('riders_details')->insertGetId([
            'user_id'  => $id,
            'fullname' => $fullname,
            'mobile'   => $mobile,
            'email'    => $email,
        ]);
        
        $data = DB::table('vehicle_fare')->where('id', $request->id)->update(['rider_id' => $result]);
        
        // dd($data);
         
        $arr['status']=1;
        $arr['message']='Successfully!!';
        $arr['data']=NULL; 
        
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=NULL;
       }
       return response()->json($arr,200);
   }
   
   public function get_ride_details(Request $request){
       
        $driverId=Auth::id();
        try 
        {
            $get_data = DB::table('riders_details')->where('user_id', $driverId)->get();
            if($get_data)
            {
                $arr['status']=1;
                $arr['message']="successfully";
                $arr['data']=$get_data;
            }else{
                $arr['status']=0;
                $arr['message']='No data found ...';
                $arr['data'] =NULL;
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
   
   public function book_a_ride(Request $request){
        $typevalidate=Validator::make($request->all(),[   
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
                $user_id =  Auth::id();
                
                $check_booking = DB::table('bookings')->where('user_id', $user_id)->where('booking_date', date('d-m-Y'))->where('booking_status', '4')->first();
                if($check_booking){
                    $arr['status']=0;
                    $arr['message']= "You Can not Book ride because you are ongoing !";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                
                
                $fare_data = DB::table('vehicle_fare')->where('id', $request->vehicle_fare_id)->first();
                if($fare_data){
                    
                      $booking_id = 'LIMPOPO'.time().rand(100,9999);
                      $data_booking['user_id']        = $user_id;
                      $data_booking['trip_type']      = $fare_data->trip_type;
                      $data_booking['vechicle_id']    = $fare_data->v_id;
                      $data_booking['book_any']       = ($fare_data->v_id == "0")?"1":"0";
                      $data_booking['booking_id']     = $booking_id;
                      $data_booking['outstation']     = $fare_data->outstation;
                      $data_booking['is_rental']      = $fare_data->is_rental;
                      $data_booking['rental_hour']    = $fare_data->rental_hour;
                      $data_booking['rental_km']      = $fare_data->rental_km;
                      $data_booking['booking_type']   = $fare_data->booking_type??1;
                      $data_booking['picup_location'] = $fare_data->pick_up_location;
                      $data_booking['picup_lat']      = $fare_data->pick_up_lat;
                      $data_booking['picup_long']     = $fare_data->pick_up_long;
                      $data_booking['drop_location']  = $fare_data->drop_location;
                      $data_booking['drop_lat']       = $fare_data->drop_lat;
                      $data_booking['drop_long']      = $fare_data->drop_long;
                      $data_booking['fare']           = $fare_data->fare;
                      $data_booking['gst_amount']     = $fare_data->gst_amount;
                      $data_booking['distance']       = $fare_data->total_distance;
                      $data_booking['time_duration']  = $fare_data->total_time;
                      $data_booking['booking_date']   = ($fare_data->booking_date)??date('Y-m-d');
                      $data_booking['booking_time']   = ($fare_data->booking_time)??date('h:i:s');
                      $data_booking['payment_mode']   = $fare_data->payment_method;
                      $data_booking['rider_id']       = $fare_data->rider_id??"";
                      
                      $data_booking['first_stop_location']  = $fare_data->first_stop_location??"";
                      $data_booking['first_stop_lat']       = $fare_data->first_stop_lat??"";
                      $data_booking['first_stop_long']      = $fare_data->first_stop_long??"";
                      $data_booking['second_stop_location'] = $fare_data->second_stop_location??"";
                      $data_booking['second_stop_lat']      = $fare_data->second_stop_lat??"";
                      $data_booking['second_stop_long']     = $fare_data->second_stop_long??"";
                     
                      
                      $data_inst = DB::table('bookings')->insert($data_booking);
                      $lastid =  DB::getPdo()->lastInsertId();
                      $getData = DB::table('bookings')->where('id',$lastid)->first();
                      if($getData){
                          
                             date_default_timezone_set('Africa/Johannesburg');
                             DB::table('booking_activity_log')->insert(['booking_id' => $getData->booking_id, 'booked_datetime' => date('d-m-Y H:i:s'), 'user_id' => $getData->user_id]);
                          
                             if($fare_data->rider_id != ""){
                                  $rider_data = DB::table('riders_details')->where('id', $fare_data->rider_id)->first();
                                  if($rider_data){
                                      DB::table('riders_booking')->insert(['user_id'=>$rider_data->user_id,'rider_id'=>$fare_data->rider_id,'booking_id'=>$booking_id]);
                                  }
                              }
                          
                            DB::table('vehicle_fare')->where('user_id', $user_id)->delete();
                          
                            $arr['status']=1;
                            $arr['message']="Success";
                            $arr['data']=$getData;
                            
                      }else{
                          $arr['status']=0;
                          $arr['message']="No Booking !";
                          $arr['data']=NULL;   
                      }
                }else{
                    $arr['status']=0;
                    $arr['message']="No Data Found";
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
     
     
    public function search_ride(Request $request)
    {
        \Log::info('search_ride : ');
        \Log::info($request->all());
        
         $typevalidate=Validator::make($request->all(),[   
            'booking_id'=>'required',
         ]);
          
        if($typevalidate->fails())
        {
            $arr['status']=0;
            $arr['message']= $typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }  
        // try
        // {
               $user_id =  Auth::id();

               $radious_data = DB::table('radius')->first();

               if($radious_data){
                 $radius = $radious_data->radius_range;
               }else{
                   $radius=100;
               }
               $book_status = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
               
               $all_vehicles = DB::table('vehicle_type')->where('status', '1')->whereNotIn('name', ['Bike', 'Auto'])->get()->toArray();
               $all_type_vehicle = [];
               if($all_vehicles){
                   $all_type_vehicle = array_column($all_vehicles, 'id');
               }

               if($book_status){
                    //   if($book_status->booking_type == "2"){
                    //         $arr['status']=0;
                    //         $arr['message']= "Booking has been Scheduled !";
                    //         $arr['data']=NULL;
                    //         return response()->json($arr,200);
                    //   }       
                   $latitude=$book_status->picup_lat;
                   $longtitude=$book_status->picup_long;
                   $booking_id=$request->booking_id; 
                   $driver_id = $request->driver_id;
                   $booking_status = $request->booking_status;
                   $booking_for_type = ($book_status->rider_id)?"1":"0";
                   $data=DB::table("users as u")
                       ->leftjoin('driver_details_basic_details as dd','dd.driver_id','=','u.id')
                       ->leftjoin('vehicle_type as vt','vt.id','=','dd.vehicle_type')
                        ->select("vt.*", "u.latitude as driver_latitude", "u.longtitude as driver_longitude"
                        ,"u.id as driver_id","u.name as DriverName","u.email as DriverEmail","u.mobile_no","u.gender",
                        "dd.vehicle_type as vehicle_type_id"
                        ,DB::raw('CONCAT("' .url("public") .'","/vehicle_image/vehicle_type_image/",image)  as image')
                        ,DB::raw("round(6371 * acos(cos(radians(" . $latitude . ")) 
                        * cos(radians(u.latitude)) 
                        * cos(radians(u.longtitude) - radians(" . $longtitude . ")) 
                        + sin(radians(" .$latitude. ")) 
                        * sin(radians(u.latitude)))) AS distance"))
                        ->having("distance", "<", $radius)
                        // ->where('u.id', '2676')
                        ->where("u.type",2);
                        // if($booking_status == 6 && $driver_id != ""){
                        //     $data->where("u.id", "!=", $driver_id);
                        // }
                        
                      if($book_status->vechicle_id == "0"){
                         $data = $data->whereIN('dd.vehicle_type',$all_type_vehicle)->where('u.is_live',1)->get()->toArray(); 
                      }else{
                         $data = $data->where('dd.vehicle_type',$book_status->vechicle_id)->where('u.is_live',1)->get()->toArray(); 
                      }    
                      
                // dd($data);      
                
                if($data){
                     $delete = DB::table('user_schedule_booking')->where('user_id',$user_id)->delete();
                     $vid = $book_status->vechicle_id;

                     foreach($data as $val){
                         $invoice_number = 'Invoice'.rand(10000,100000);
                         $val->user_id = $user_id;
                         $val->trip_type =$book_status->trip_type;
                         $val->pick_up_location =$book_status->picup_location;
                         $val->pick_up_lat =$book_status->picup_lat;
                         $val->pick_up_long =$book_status->picup_long;
                         $val->drop_location =$book_status->drop_location;
                         $val->drop_lat =$book_status->drop_lat;
                         $val->drop_long =$book_status->drop_long;
                         $val->fare =$book_status->fare;
                         
                         if($vid == "0"){
                            $driver_details = DB::table('driver_details_basic_details')->where('driver_id', $val->driver_id)->first();
                            $vid = $driver_details->vehicle_type;
                         }
                         
                          $data_booking['user_id']        = $user_id;
                          $data_booking['driver_id']      = $val->driver_id;
                          $data_booking['vechicle_id']    = $vid;
                          $data_booking['is_rental']      = $book_status->is_rental;
                          $data_booking['rental_hour']    = $book_status->rental_hour;
                          $data_booking['rental_km']    = $book_status->rental_km;
                          $data_booking['trip_type']      = $book_status->trip_type;
                          $data_booking['booking_id']     = $booking_id;
                          $data_booking['booking_type']   = $book_status->booking_type;
                          $data_booking['invoice_number'] = $invoice_number;
                          $data_booking['picup_location'] = $book_status->picup_location;
                          $data_booking['picup_lat']      = $book_status->picup_lat;
                          $data_booking['picup_long']     = $book_status->picup_long;
                          $data_booking['drop_location']  = $book_status->drop_location;
                          $data_booking['drop_lat']       = $book_status->drop_lat;
                          $data_booking['drop_long']      = $book_status->drop_long;
                          $data_booking['distance']       = $val->distance;
                          $data_booking['fare']           = $book_status->fare;
                          $data_booking['booking_status'] = 1;
                          $data_booking['payment_mode']  = $book_status->payment_mode;
                          $data_booking['booking_for_type']  = $booking_for_type;
                          
                          $data_booking['booking_date']       = $book_status->booking_date;
                          $data_booking['booking_time']      = $book_status->booking_time;
                          
                          $data_booking['first_stop_location']  = $book_status->first_stop_location;
                          $data_booking['first_stop_lat']       = $book_status->first_stop_lat;
                          $data_booking['first_stop_long']      = $book_status->first_stop_long;
                          $data_booking['second_stop_location'] = $book_status->second_stop_location;
                          $data_booking['second_stop_lat']      = $book_status->second_stop_lat;
                          $data_booking['second_stop_long']     = $book_status->second_stop_long;
                          
                        //   $user_status = DB::table('bookings')->where('driver_id', $val->driver_id)->whereIn('booking_status', ['2','4'])->where('booking_date', date('d-m-Y'))->first();

                        //   if(empty($user_status)){
                              
                        //       $title = "New Ride Request: Ready to Roll?";
                        //       $message = "You have a new ride request. Please proceed to the pickup location at ".$book_status->picup_location." to pick up your passenger. Thank you for driving with Carmy!";
                        //       send_firebase_notification($title, $message, $val->driver_id);
                              
                        //       $data_inst = DB::table('user_schedule_booking')->insert($data_booking);
                        //   }
                        
                        
                        
                        // $user_status = DB::table('bookings')
                        //     ->where('driver_id', $val->driver_id)
                        //     ->whereIn('booking_status', ['2', '4'])
                        //     ->where('booking_date', date('d-m-Y'))
                        //     ->first();
                        
                        // if (empty($user_status)) {
                        
                        //     $title = "New Ride Request: Ready to Roll?";
                        //     $message = "You have a new ride request. Please proceed to the pickup location at " . $book_status->picup_location . " to pick up your passenger. Thank you for driving with Ready Rider!";
                        //     send_firebase_notification($title, $message, $val->driver_id);
                        
                        //     send_websocket_notification($val->driver_id, $title, $message, "search_user_ride");
                        
                        //     DB::table('user_schedule_booking')->insert($data_booking);
                            
                        // } else {
                        
                        //     if ($user_status->booking_type == "2" && $user_status->booking_status == "2") {
                    
                        //         $bookingDateTime = Carbon::createFromFormat(
                        //             'd-m-Y H:i',
                        //             $user_status->booking_date . ' ' . $user_status->booking_time
                        //         );
                        
                        //         $now = Carbon::now();
                                
                        //         $title = "New Ride Request: Ready to Roll?";
                        //         $message = "You have a new ride request. Please proceed to the pickup location at " . $book_status->picup_location . " to pick up your passenger. Thank you for driving with Ready Rider!";
                        //         send_firebase_notification($title, $message, $val->driver_id);
                                
                        //         send_websocket_notification($val->driver_id, $title, $message, "search_user_ride");
                                
                        //         DB::table('user_schedule_booking')->insert($data_booking);
                        
                        //     }
                        // }
                        
                        
                        $user_status = DB::table('bookings')
                            ->where('driver_id', $val->driver_id)
                            ->whereIn('booking_status', ['2', '4'])
                            ->where('booking_date', date('d-m-Y'))
                            ->first();
                            

                        if (empty($user_status)) {
                        
                            $title = "New Ride Request: Ready to Roll?";
                            $message = "You have a new ride request. Please proceed to the pickup location at " . $book_status->picup_location . " to pick up your passenger. Thank you for driving with Ready Rider!";
                            send_firebase_notification($title, $message, $val->driver_id);
                        
                            send_websocket_notification($val->driver_id, $title, $message, "search_user_ride");
                        
                            DB::table('user_schedule_booking')->insert($data_booking);
                            
                        } else {
                            
                           
                            if ($user_status->booking_type == "2" && $user_status->booking_status == "2") {

                                $dateTime = trim($user_status->booking_date) . ' ' . trim($user_status->booking_time);
                            
                                // Try both formats
                                if (Carbon::hasFormat($dateTime, 'd-m-Y H:i')) {
                                    $bookingDateTime = Carbon::createFromFormat('d-m-Y H:i', $dateTime);
                                } elseif (Carbon::hasFormat($dateTime, 'd-m-Y H:i:s')) {
                                    $bookingDateTime = Carbon::createFromFormat('d-m-Y H:i:s', $dateTime);
                                } else {
                                    \Log::error('Invalid booking date/time format.', [
                                        'booking_date' => $user_status->booking_date,
                                        'booking_time' => $user_status->booking_time,
                                        'combined'     => $dateTime,
                                    ]);
                            
                                    // Skip this record
                                    continue;
                                }
                            
                                $now = Carbon::now();
                            
                                // Example: Send notification only if booking time has arrived
                                // if ($bookingDateTime->lessThanOrEqualTo($now)) {
                            
                                    $payload_data = [
                                        'notification_type' => 'request',
                                    ];
                            
                                    $title = "New Ride Request: Ready to Roll?";
                                    $message = "You have a new ride request. Please proceed to the pickup location at {$book_status->picup_location} to pick up your passenger. Thank you for driving with Ready Rider!";
                            
                                    send_firebase_notification($title, $message, $val->driver_id);
                            
                                    send_websocket_notification(
                                        $val->driver_id,
                                        $title,
                                        $message,
                                        "search_user_ride"
                                    );
                            
                                    DB::table('user_schedule_booking')->insert($data_booking);
                                // }
                            }
                        }
                        
                        
                     }
                     
                     $arr['status']=1;
                     $arr['message']="Success";
                     $arr['booking_id']=$booking_id;
                     $arr['data']=$data;
                     
                }else{
                     $arr['status']=0;
                     $arr['message']="No Data Found";
                     $arr['data']=NULL;   
                }            
            }else{
                $arr['status']=0;
                $arr['message']="Booking Not Found !";
                $arr['data']=NULL;   
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
    
    public function calculate_wallet_amount($user_id){
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
          return $wallet_amount;
      }
    }
    
    public function check_booking_status(Request $request){
        $driverId=Auth::id();
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
            $get_data = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
            if($get_data)
            {
                
                if($get_data->booking_status == "1")
                {
                    $booking_status = "pending";
                }elseif($get_data->booking_status == "1"){
                    $booking_status = "pending";
                }elseif($get_data->booking_status == "2"){
                    $booking_status = "accepted";
                }elseif($get_data->booking_status == "3"){
                    $booking_status = "cancel";
                }elseif($get_data->booking_status == "4"){
                    $booking_status = "Ongoing";
                }elseif($get_data->booking_status == "5"){
                    $booking_status = "Completed";
                }elseif($get_data->booking_status == "6"){
                    $booking_status = "Driver cancel";
                }elseif($get_data->booking_status == "7"){
                    $booking_status = "Refunded";
                }elseif($get_data->booking_status == "8"){
                    $booking_status = "Cancel";
                }
                else{
                   $booking_status = "upcoming"; 
                }
                
                
                $arr['status']=1;
                $arr['message']="successfully";
                $arr['booking_status']=$booking_status;
            }else{
                $arr['status']=0;
                $arr['message']='No data found ...';
                $arr['booking_status'] =NULL;
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
    
    public function user_booking_trip_info(Request $request){
       
        // \Log::info('user_booking_trip_info : ');
        // \Log::info($request->all());
       
        $typevalidate=Validator::make($request->all(),[   
            'booking_id'=>'required',
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
            $result = DB::table('bookings as b')
            ->select('e.name as username','e.mobile_no as usermobile', 'e.gender','b.id', 'b.booking_id', 'b.distance', 'b.fare', 'vt.type', 'vt.name as vehicle_type_name', 'vt.image as vehicle_type_image', 'b.trip_type', 'b.is_rental', 'b.rental_km', 'b.rental_hour', 'b.picup_location', 'b.drop_location', 'd.latitude as driver_latitude',
            'd.longtitude as driver_longitude', 'b.ride_start_otp as otp', DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile)  as profile_image'),
            'd.id as driver_id', 'd.name as driver_name', 'd.profile as driver_profile', 'd.latitude', 'd.longtitude', 'b.picup_lat', 'b.picup_long', 'b.drop_lat', 'b.drop_long', 'detail.vehicle_number', 'detail.seating_capicity',
            'm.name as vehicle_model', 'd.mobile_no', 'b.booking_status', 'b.payment_status')
            ->leftjoin('users as d','d.id','=','b.driver_id')
            ->leftjoin('users as e','e.id','=','b.user_id')
            ->leftjoin('driver_details_basic_details as detail','detail.driver_id','=','b.driver_id')
            ->leftjoin('vehicle_models_name as m','m.id','=','detail.vehicle_model')
            ->leftjoin('vehicle_type as vt','vt.id','=','detail.vehicle_type')
            ->where('b.booking_id',$request->booking_id)
            ->first();
            // dd($result);
            if($result)
            {
                
                $driver_lat = $result->driver_latitude;
                $driver_long = $result->driver_longitude;
                
                $drop_lat = $result->picup_lat;
                $drop_long = $result->picup_long;
                
                // $driver_lat = $result->picup_lat;
                // $driver_long = $result->picup_long;
                // $drop_lat = $result->drop_lat;
                // $drop_long = $result->drop_long;
                
                
                $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$driver_lat.",".$driver_long."&destinations=".$drop_lat.",".$drop_long."&departure_time=now&key=".get_option_data('google_map_api_key');
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
                if(isset($response_a['rows'][0]['elements'][0]['duration']['text'])){
                    $time_val = $response_a['rows'][0]['elements'][0]['duration']['text'];
                }else{
                    $time_val = "0 min";
                }

                $complete_ride = DB::table('bookings')->where('driver_id', $result->driver_id)->where('booking_status', '5')->count();
                $result->ride_complete = (isset($complete_ride))?$complete_ride:0;
                $result->vehicle_type_image = !empty($result->vehicle_type_image) ? url('public/vehicle_image/vehicle_type_image/' . $result->vehicle_type_image) : profileImg();
                $result->driver_profile = !empty($result->driver_profile) ? url('public/profile_image/' . $result->driver_profile) : profileImg();
                
                $result->eta_picup = $time_val;
                $result->rating = get_driver_avg_rating($result->driver_id);
                
                //$data=$this->send_msg($result->otp,$result->username,$result->usermobile);
                
                // \Log::info($result);
                
                $arr['status']=1;
                $arr['message']=" Success !";
                $arr['data']=$result;
            }
            else
            {
                $arr['status']=0;
                $arr['message']="No data Found !";
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
   
   public function ride_complete_booking(Request $request) 
    {
        \Log::info('ride_complete_booking : ');
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
           $ride = DB::table('bookings')->where('booking_id',$request->booking_id)->where('booking_status', '5')->first();
            
          if($ride)
          {
               $ride_complete = DB::table('bookings as b')->select('b.id', 'b.time_duration', 'b.booking_id','b.is_rental', 'b.rental_km', 'b.distance', 'b.rental_hour', 'booking_type', 'b.user_id', 'b.driver_id', 'b.rating', 'b.review', 'b.vechicle_id',
               'b.picup_location', 'b.drop_location', 'b.fare', 'b.payment_mode', 'b.payment_status', 'b.booking_status','b.picup_datetime', 'b.drop_datetime','u.name','u.profile',
               'u.mobile_no as user_mobile', 'd.name as driver_name', 'd.profile as driver_profile', 'b.first_stop_location', 'b.first_stop_lat', 'b.first_stop_long', 'b.second_stop_location',
               'b.second_stop_lat', 'b.second_stop_long', 'm.name as payment_mode', 'vt.name as vehicle_type_name', 'vt.image as vehicle_type_image')
                ->leftjoin('users as u','u.id','=','b.user_id')
                ->leftjoin('payment_modes as m','m.id','=','b.payment_mode')
                ->leftjoin('users as d','d.id','=','b.driver_id')
                ->leftjoin('driver_details_basic_details as detail','detail.driver_id','=','b.driver_id')
                ->leftjoin('vehicle_type as vt','vt.id','=','detail.vehicle_type')
                ->where('b.id',$ride->id)
                ->first();
                
                $ride_complete->profile = !empty($ride_complete->profile) ? url('public/profile_image/' . $ride_complete->profile) : profileImg();
                $ride_complete->driver_profile = !empty($ride_complete->driver_profile) ? url('public/profile_image/' . $ride_complete->driver_profile) : profileImg();
                $ride_complete->picup_date = date('d M y', strtotime($ride_complete->picup_datetime));
                $ride_complete->picup_time = date('h:i A', strtotime($ride_complete->picup_datetime));
                $ride_complete->drop_date = date('d M y', strtotime($ride_complete->drop_datetime));
                $ride_complete->drop_time = date('h:i A', strtotime($ride_complete->drop_datetime));
                $ride_complete->rating_status = check_booking_rating_status($ride->booking_id, $driver_id);
                
                $ride_complete->user_rating = get_booking_rating_review($request->booking_id, $ride->user_id);
                $ride_complete->driver_rating = get_booking_rating_review($request->booking_id, $ride->driver_id);
                
                $ride_complete->vehicle_type_image = !empty($ride_complete->vehicle_type_image) ? url('public/vehicle_image/vehicle_type_image/' . $ride_complete->vehicle_type_image) : profileImg();
                $ride_complete->rating = get_driver_avg_rating($ride_complete->driver_id);
                $ride_complete->invoice_link = url('/api') . '/download_invoice?id=' . $ride_complete->id;

                $arr['status'] = 1;
                $arr['message'] = 'Successfully...';
                $arr['data'] = $ride_complete;     
                
          }
          else
          {
              $arr['status'] = 0;
              $arr['message'] = 'No data found';
              $arr['data'] =[]; 
              
          }
        } 
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);    
    }
   
   public function booking_status_reject(Request $request){
          
             $typevalidate=Validator::make($request->all(),[
                                    'booking_id'=>'required',
                                    'status'=>'required',
                                    ]);
            try
            {
                if($typevalidate->fails())
                {
                    $arr['status']=0;
                    $arr['message']=$typevalidate->errors()->first();;
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                    
                      $user_id = Auth::id();
                      $today_date = Carbon::now();
                      $rideStatus = DB::table('bookings')
                                      ->select('user_id','driver_id','booking_id','booking_status')
                                      //->where('user_id',$user_id)
                                      ->where('booking_id',$request->booking_id)
                                      ->first();
                                     
                                      
    
                if(!empty($rideStatus)){
                     $data = DB::table('bookings')->where('booking_id',$request->booking_id)->update(['booking_status'=>$request->status,'updated_at'=>$today_date]);
                    $arr['status']=1;
                    $arr['message']='booking rejected sucessfully.';
                    $arr['data']=null;
                   
                    return response()->json($arr,200);
                }else{
                    $arr['status']=0;
                    $arr['message']='Booking not Found';
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
    
    public function booking_status_accepted(Request $request){
          
          \Log::info('booking_status_accepted : ');
          \Log::info($request->all());
          
            $typevalidate=Validator::make($request->all(),[
                'booking_id'=>'required',
                'status'=>'required',
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
                    
                     $user_id = Auth::id();
                     
                    //  dd($user_id);
                     
                     $today_date = carbon::now();
                     $rideStatus = DB::table('bookings')
                                      ->where('booking_id',$request->booking_id)
                                      ->first();
                                      
                if(!empty($rideStatus)){
                    
                    
                    if($request->status == "1"){
                        
                        $get_data = DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)->where('driver_id', $user_id)->first();
                        DB::table('user_schedule_booking')->where('id', $get_data->id)->update(['booking_status'=>"0"]);
                        $arr['status']=0;
                        $arr['message']="Booking Reject Successfull !";
                        $arr['data']=NULL;
                        return response()->json($arr,200);
                        
                    }
                    
                    
                    if($rideStatus->booking_status == "2"){
                        $arr['status']=0;
                        $arr['message']= "This Ride already Booked !";
                        $arr['data']=NULL;
                        return response()->json($arr,200);
                    }
                    
                    $otp = rand(1231,7879);
                    $update_data = ['booking_status'=>$request->status, 'accepted_date_time' => date('d-m-Y'), 'driver_id'=>$user_id, 'updated_at'=>$today_date, 'ride_start_otp'=>$otp];
                    
                    if($rideStatus->book_any == "1"){
                        
                        $latlong = $rideStatus->picup_lat.",".$rideStatus->picup_long;
                        $city_name = $this->get_city_name($latlong);
                        
                        $user_data = DB::table('driver_details_basic_details')->where('driver_id', $user_id)->first();
                        
                        $vehicle_type = DB::table('vehicle_type')->where('id', $user_data->vehicle_type)->first();
                        
                        $picup_date = ($rideStatus->booking_date != "" && $rideStatus->booking_date != "Invalid date")? $rideStatus->booking_date : date('d-m-Y');
                        $picup_time = ($rideStatus->booking_time)?date('H:i', strtotime($rideStatus->booking_time)):date('H:i');
                        $ride_time = date("Y-m-d", strtotime($picup_date)).' '.$picup_time.':00'; 
                        $night_charge = is_night_ride($ride_time, $rideStatus->picup_lat, $rideStatus->picup_long);
        
                        $data_fare = calculace_vehicle_fare($user_data->vehicle_type, $city_name, $rideStatus->distance, $night_charge, $rideStatus->outstation, $rideStatus->trip_type, $picup_time);

                        $price = ($rideStatus->outstation == "1" && $rideStatus->trip_type == "1")?$vehicle_type->outstation_rate:$vehicle_type->price;
                        // dd($rideStatus->outstation);
                        $per_km_charge = $price;
                        if($rideStatus->trip_type == "2"){
                          $price = $price*2; 
                        }
                        
                        if(isset($data_fare['price']) && $data_fare['price'] != ""){
                            $fare = $data_fare['price'];
                            $wating_charge = $data_fare['wating'];
                            $price_per_km = $data_fare['price_per_km'];
                        }else{
                          $fare = ($rideStatus->distance*$price)+$vehicle_type->base_fare;
                        //   dd($fare);
                          $wating_charge = $vehicle_type->wating_charge;
                          $price_per_km = $per_km_charge;
                          $gst_percentage = get_option_data('gst_percentage');
                            if(isset($night_charge) && $night_charge == "Night"){
                                $night_fare = DB::table('vehicle_charger')->where('vehicle_name', $vehicle_type->id)->where('status', 1)->first();
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
                        
                        // dd($new_fare);

                        $update_data['fare'] = $new_fare;
                        $update_data['vechicle_id'] = $user_data->vehicle_type;

                    }
                    
                    $data = DB::table('bookings')->where('booking_id',$request->booking_id)->update($update_data);
                    
                    $title = "Your Booking ride accepted successfully !";
                    $message = "Your Booking Id ".$request->booking_id." accepted successfully !";
                    send_firebase_notification($title, $message, $rideStatus->user_id);
                    
                    send_websocket_notification(
                        $rideStatus->user_id,
                        $title,
                        $message,
                        'booking_status_accept'
                    );
                    
                    $msg_title = "Booking Accepted by another person";
                    $msg_driver = "Booking Accepted by another person";
                    
                    $all_requests = DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)->where('driver_id', '!=', $user_id)->get();
                    
                    foreach ($all_requests as $res) {
                        send_websocket_notification(
                            $res->driver_id,
                            $msg_title,
                            $msg_driver,
                            'user_cancel_ride'
                        );
                        \Log::info('Cancel websocket - driver: ' . $res->driver_id);
                    }
                    
                    DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)->delete();

                    $data_booking = DB::table('bookings as b')
                            ->select('e.name as username','e.mobile_no as usermobile', 'e.gender','b.id', 'b.booking_id', 'b.distance', 'b.fare', 'vt.type', 'vt.name as vehicle_type_name', 'vt.image as vehicle_type_image', 'b.trip_type', 'b.is_rental', 'b.rental_km', 'b.rental_hour', 'b.picup_location', 'b.drop_location', 'd.latitude as driver_latitude',
                            'd.longtitude as driver_longitude', 'b.ride_start_otp as otp', DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile)  as profile_image'),
                            'd.id as driver_id', 'd.name as driver_name', 'd.profile as driver_profile', 'd.latitude', 'd.longtitude', 'b.picup_lat', 'b.picup_long', 'b.drop_lat', 'b.drop_long', 'detail.vehicle_number',
                            'm.name as vehicle_model', 'd.mobile_no', 'b.booking_status', 'b.payment_status')
                            ->leftjoin('users as d','d.id','=','b.driver_id')
                            ->leftjoin('users as e','e.id','=','b.user_id')
                            ->leftjoin('driver_details_basic_details as detail','detail.driver_id','=','b.driver_id')
                            ->leftjoin('vehicle_models_name as m','m.id','=','detail.vehicle_model')
                            ->leftjoin('vehicle_type as vt','vt.id','=','detail.vehicle_type')
                            ->where('b.booking_id',$request->booking_id)
                            ->first();
                            
                    $complete_ride = DB::table('bookings')->where('driver_id', $data_booking->driver_id)->where('booking_status', '5')->count();
                    $data_booking->ride_complete = (isset($complete_ride))?$complete_ride:0;
                    $data_booking->vehicle_type_image = !empty($data_booking->vehicle_type_image) ? url('public/vehicle_image/vehicle_type_image/' . $data_booking->vehicle_type_image) : profileImg();
                    $data_booking->driver_profile = !empty($data_booking->driver_profile) ? url('public/profile_image/' . $data_booking->driver_profile) : profileImg();
                    
                    $data_booking->rating = get_driver_avg_rating($data_booking->driver_id);        

                    
                    date_default_timezone_set('Africa/Johannesburg');
                    DB::table('booking_activity_log')
                    ->where('booking_id', $rideStatus->booking_id)
                    ->update([
                        'accept_driver_id' => $user_id,
                        'accept_datetime'  => date('d-m-Y H:i:s'),
                    ]);
                       
                    $arr['status']=1;
                    $arr['message']='booking Accepted sucessfully.';
                    $arr['data']=$data_booking;
                   
                    return response()->json($arr,200);
                }else{
                    $arr['status']=0;
                    $arr['message']='Booking not Found';
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
      
    public function get_reason_cancel_list(Request $request)
    {
       try
        {
            $result = DB::table('reason_cancel')->select('id','reason')->where('type', '1')->where('status',1)->get()->toArray();
            if($result)
            {
                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']=$result;
            }
            else
            {
                $arr['status']=0;
                $arr['message']='No Data Found';
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
    
    public function get_driver_reason_cancel_list(Request $request)
    {
       try
        {
            $result = DB::table('reason_cancel')->select('id','reason')->where('type', '2')->where('status',1)->get()->toArray();
            if($result)
            {
                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']=$result;
            }
            else
            {
                $arr['status']=0;
                $arr['message']='No Data Found';
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
    
    public function get_city_name($latlong){
        
        if($latlong != ""){
            $googleMapsUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlong . "&language=ar&key=".get_option_data('google_map_api_key');
            
            $response = file_get_contents($googleMapsUrl);
            // dd($response);
            $response = json_decode($response, true);
            $results = $response["results"];
            $addressComponents = $results[0]["address_components"];
            
            $cityName = "";
            $stateName = "";
            $countryName = "";
            
            foreach ($addressComponents as $component) {
                $types = $component["types"];
                
                // Get locality (city)
                if (in_array("locality", $types) && in_array("political", $types)) {
                    $cityName = $component["long_name"];
                }
                
                // Get state
                if (in_array("administrative_area_level_1", $types) && in_array("political", $types)) {
                    $stateName = $component["long_name"];
                }
                
                // Get country
                if (in_array("country", $types) && in_array("political", $types)) {
                    $countryName = $component["long_name"];
                }
            }
            
            // Combine the parts
            $locationParts = array_filter([$cityName, $stateName]);
            return implode(", ", $locationParts);
        }
    }
    
    public function user_cancel_ride(Request $request){
        
        \Log::info('user_cancel_ride : ');
        \Log::info($request->all());
        
        $typevalidate=Validator::make($request->all(),[   
            'booking_id'=>'required',
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
            if((empty($request->reason_id) || $request->reason_id == "[]") &&  $request->reason_text == ""){
                $arr['status']=0;
                $arr['message']= "reason_id or reason_text fiels required !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $user_id = Auth::id();
            $reason_id = ($request->reason_id)??"";
            $reason_text = ($request->reason_text)??"";
            $data=DB::table('bookings')->where('booking_id',$request->booking_id)->first();
            if($data)
            {
                DB::table('bookings')->where('booking_id',$request->booking_id)->update(['booking_status'=>8, 'booking_type'=>1, 'cancel_resion_id'=>$reason_id, 'cancel_reason_text'=>$reason_text]);
                DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)->delete();
                
                date_default_timezone_set('Africa/Johannesburg');
                DB::table('booking_activity_log')->where('booking_id', $request->booking_id)->update(['cancel_type' => "User", 'cancel_datetime' => date('d-m-Y H:i:s'), 'cancel_user_id' => $user_id]);
                
                $title = "Ride Cancelled by User !";
                $message = "Your Booking Id ".$request->booking_id." has been cancelled by User !";
                send_firebase_notification($title, $message, $data->driver_id);
                
                send_websocket_notification($data->driver_id, $title, $message, "user_cancel_ride");
                
                date_default_timezone_set("Africa/Johannesburg");
                $current_time = strtotime(date('y-m-d H:i'));
                $accepted_time = strtotime($data->accepted_date_time);
                
                $user_cancel_time_duration = get_option_data('user_cancel_time_duration');
                
                $diff_in_minutes = ($current_time - $accepted_time) / 60;
                
                if ($diff_in_minutes >= $user_cancel_time_duration) {
                
                    $cancel_amount = get_option_data('schedule_ride_cancel_user_charges');
                    
                    if($cancel_amount > 0){
                        
                        $insert = [
                          'user_id' => $data->user_id,
                          'amount' => $cancel_amount,
                          'transaction_type' => '2',
                          'transaction_date' => date('d-m-Y H:i'),
                          'transaction_id' => $request->trxref??"",
                          'booking_id' => $data->booking_id,
                          'status' => '1',
                          'payment_type' => 'wallet'
                        ];
                        
                        DB::table('my_wallet')->insert($insert);
                        
                    }
                    
                }
                
                $arr['status']=1;
                $arr['message']="Your booking with order id ". $request->booking_id ." has been cancelled successfully.";
                $arr['data']=NULL;
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
    
    public function add_chats(Request $request){
      
        $senderId = Auth::id(); 
        
        $validate=Validator::make($request->all(),[
            'receiver_id'=>'required',
            'message'=>'required',
            'booking_id'=>'required',
            ]); 
            
        try 
        {
            if($validate->fails()){
                
                $arr['status']=0;
                $arr['message']=$validate->errors()->first();
                $arr['data']=NULL;
               
                return response()->json($arr,200);
            }
            
            $insert = [
                'sender_id' =>$senderId,
                'receiver_id' =>$request->receiver_id,
                'message' =>$request->message, 
                'booking_id' =>$request->booking_id, 
                'status' =>2,
                ]; 
                       
            $id = DB::table('chats')->insertGetId($insert); 
            
            if($id){
                
                $title = "Ride Mesage";
                $message = $request->message;
                send_firebase_notification($title, $message, $request->receiver_id);
                
                send_websocket_notification($request->receiver_id, $title, $message, "send_message");
                
                $result = DB::table('chats')->where('id',$id)->get();
                $arr['status']=1;
                $arr['message']="successfully !!!";
                $arr['data']=$result;
            }
            else{
                $arr['status']=0;
                $arr['message']='Data not found';
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
    
    public function chat_with_user(Request $request){ 
        
        $validate=Validator::make($request->all(),[
            'receiver_id'=>'required',
            'booking_id'=>'required'
        ]);
        try
        {
            if($validate->fails()){
            
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();
            $arr['data']=NULL;
           
            return response()->json($arr,200); 
            }
            
            $senderId = Auth::id();
            $receiverId =$request->receiver_id; 
            $bookingId =$request->booking_id; 
            
            $reciver_user = DB::table('users')->select('users.*', DB::raw('CONCAT("' . url('public/profile_image') . '","/",users.profile)  as profile_image'))->where('id', $receiverId)->first();
        
            $chats = DB::table('chats')
            ->leftjoin('users as sender', 'chats.sender_id', '=', 'sender.id')
            ->leftjoin('users as receiver', 'chats.receiver_id', '=', 'receiver.id')
            ->select('chats.*', 'sender.name as sender_name', 'receiver.name as receiver_name')
            ->where('booking_id', $bookingId)
            ->get(); 

            // Organize the chat messages into left and right sections
            $organizedChats = [];
    
            foreach ($chats as $chat) {
                if ($chat->sender_id == $senderId) {
                    $chat->chat_type = "right";
                } else {
                    $chat->chat_type = "left";
                }
                if($chat->post_type == "2"){
                    $chat->attachment = !empty($chat->attachment) ? url('public/attachments').'/'.$chat->attachment : "";
                }
                $chat->time = date('h:i A', strtotime($chat->created_at));
            }
            
            if($chats){
                
                $arr['status']=1;
                $arr['message']="chat list";
                $arr['ricever_name']=$reciver_user->name;
                $arr['ricever_profie']=$reciver_user->profile_image;
                $arr['data']=$chats;
            }
            else{
                $arr['status']=0;
                $arr['ricever_name']=$reciver_user->name;
                $arr['ricever_profie']=$reciver_user->profile_image;
                $arr['message']='No chat available';
                $arr['data']=[];
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
    
    public function add_booking_rating(Request $request){
        
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
            $user_id = Auth::id();
            
            $rating = ($request->rating)??"0";
            $review = $request->review;
            
            $booking_detail = DB::table('booking_ratings')->where('booking_id', $request->booking_id)->where('user_id', $user_id)->first();
            if($booking_detail){
                $data = ['rating'=>$rating, 'message'=>$review]; 
                $ride_data = DB::table('booking_ratings')->where('id', $booking_detail->id)->update($data);
            }else{
                $data = ['user_id'=>$user_id, 'booking_id'=>$request->booking_id, 'rating'=>$rating, 'message'=>$review]; 
                $ride_data = DB::table('booking_ratings')->insert($data); 
            }

            $arr['status']=1;
            $arr['message']='successfully..';
            $arr['data']=NULL;
        }
        
        catch(\Exception $e){
                $arr['status']=0;
                $arr['message']=$e->getMessage();
                $arr['data']=NULL;
            }
        return response()->json($arr,200); 
        
    }
    
    public function add_driver_rating(Request $request){
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
            $user_id = Auth::id();
            
            $rating = ($request->rating)??"0";
            $review = $request->review;
            
            $booking_detail = DB::table('bookings')->where('booking_id', $request->booking_id)->where('user_id', $user_id)->first();
            if(empty($booking_detail)){
                $arr['status']=0;
                $arr['message']= "Booking Not Found !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            // $rating = 0;
            DB::table('bookings')->where('id', $booking_detail->id)->update(['rating'=>$rating, 'review'=>$review]);
            $ride_detail = DB::table('ride_rating')->where('booking_id', $booking_detail->booking_id)->first();
            if($ride_detail){
                $data = ['rating'=>$request->rating, 'message'=>$request->review]; 
                $ride_data = DB::table('ride_rating')->where('id', $ride_detail->id)->update($data);
            }else{
               $data = ['user_id'=>$user_id, 'driver_id'=>$booking_detail->driver_id, 'booking_id'=>$booking_detail->booking_id, 'rating'=>$rating, 'message'=>$review]; 
                $ride_data = DB::table('ride_rating')->insert($data); 
            }
            $arr['status']=1;
            $arr['message']='successfully..';
            $arr['data']=NULL;
        }
        catch(\Exception $e){
                $arr['status']=0;
                $arr['message']=$e->getMessage();
                $arr['data']=NULL;
            }
        return response()->json($arr,200); 
    }
    
    public function vehicle_register(Request $request)
    {

        $typevalidate=Validator::make($request->all(),[
            'driver_id'=>'required',
            'vehicle_number'=>'required',
            'vehicle_manufacture'=>'required',
            'vehicle_model'=>'required',
            'vehicle_type'=>'required',
            'vehicle_owner_name'=>'required',
            'vehicle_reg_year'=>'required',
        ]);
        try {  
            
            if($typevalidate->fails()==true)
            {
                $arr['registration_status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            else
            {    
                $driverId = $request->driver_id;
                
                $vehicle_number       = $request->vehicle_number;
                $vehicle_manufacture  = $request->vehicle_manufacture;
                $vehicle_model        = $request->vehicle_model;
                $vehicle_type         = $request->vehicle_type;
                $vehicle_reg_year     = $request->vehicle_reg_year;
                $vehicle_owner_name   = $request->vehicle_owner_name;


                $data=array(
                    'vehicle_number'     =>$vehicle_number,
                    'vehicle_manufacture'=>$vehicle_manufacture,
                    'vehicle_model'      =>$vehicle_model,
                    'vehicle_type'       =>$vehicle_type,
                    'vehicle_reg_year'   =>$vehicle_reg_year,
                    'vehicle_owner_name' =>$vehicle_owner_name,
                );
                    
                $vehicle_data =DB::table('driver_details_basic_details')->where('driver_id',$driverId)->update($data);
                
                $get_date =DB::table('users')->where('id',$driverId)->first();

                
                DB::table('users')->where('id', $driverId)->update(['vehicle_register'=>'yes']);
            
                $arr['status']=1;
                $arr['message']="Vehicle Registration Successfully!!";
                $arr['data']=$get_date;
   
            }
            
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
        }    
           
        return response()->json($arr,200);
    }

    
    public function ride_start_verfiy_otp(Request $request)
    {
         $user_id = Auth::id();

        try
        {     
            $typevalidate=Validator::make($request->all(),[   
                'booking_id'=>'required',
                'otp'=>'required'
            ]);
                      
       
            if($typevalidate->fails())
            {
                $arr['status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
               
                return response()->json($arr,200);
            }
            
            date_default_timezone_set("Asia/Calcutta");   //India time (GMT+5:30)
            $date = date('Y-m-d H:i:s');
            
            $bookingid = $request->booking_id;
            $otp = $request->otp;
            $userid = Auth::id();
            
            $user_status = DB::table('bookings')
                            ->where('driver_id', $userid)
                            ->whereIn('booking_status', ['4'])
                            ->where('booking_date', date('d-m-Y'))
                            ->first();
                        
            if ($user_status) {
                
                $arr['status']=0;
                $arr['message']= "You have already Ongoing ride !";
                $arr['data']=NULL;
                return response()->json($arr,200);
                
            }
                        

            $chk_otp =  DB::table('bookings')->where('booking_id',$bookingid)->where('ride_start_otp',$otp)->first();
            
            if(!empty($chk_otp))
            {
                DB::table('bookings')->where('booking_id',$bookingid)->update(['booking_status'=>'4', 'booking_type'=>'1', 'picup_datetime' => $date]);
                $d=DB::table('bookings')->where('booking_id',$bookingid)->first();
                
                $title = "Ride Otp verification";
                $message = "Your Booking Id ".$d->booking_id." Ride Otp verification has been successfull !";
                send_firebase_notification($title, $message, $d->user_id);
                
                send_websocket_notification($d->user_id, $title, $message, 'ride_start_otp_verify');
                
                date_default_timezone_set('Africa/Johannesburg');
                DB::table('booking_activity_log')
                    ->where('booking_id', $bookingid)
                    ->update([
                        'otp_driver_id' => $user_id,
                        'otp_datetime'  => date('d-m-Y H:i:s'),
                    ]);

                $arr['status']=1;
                $arr['message']='OTP Matched Successfully!!';
                $arr['data']=$d;
                
            }
            
            else{
                 $arr['status']=0;
                $arr['message']='OTP not match..';
                $arr['data']=null;
            }
        
            
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    
    public function driver_complete_ride(Request $request) 
    {
        
        \Log::info('driver_complete_ride : ');
        \Log::info($request->all());
        
        $typevalidate=Validator::make($request->all(),[
            'booking_id' =>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        } 
        try
        {
            
                $driver_id = Auth::id();
                $today_date = Carbon::now();
                
                date_default_timezone_set("Asia/Calcutta");
                $date = date('Y-m-d H:i');
                
                $driver_data = DB::table('users')->where('id', $driver_id)->first();
                
                $driver_latitude = ($request->latitude)??$driver_data->latitude;
                $driver_longitude = ($request->longitude)??$driver_data->longtitude;
                
                $booking_info = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
                if(empty($booking_info)){
                    $arr['status']=0;
                    $arr['message']="Booking not found !";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                
               
                DB::table('bookings')->where('booking_id',$request->booking_id)->update(['drop_lat'=>$driver_latitude, 'drop_long'=>$driver_longitude,'booking_status'=>'5','updated_at'=>$today_date, 'drop_datetime'=>$date]);
                
                $booking_data = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
 
                if(empty($booking_data)){
                    $arr['status']=0;
                    $arr['message']= "No data Found !";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }

                if($booking_data->payment_mode == "1" && $booking_data->payment_status != "1"){
                    
                    $get_data = DB::table('list_subscription')->where('booking_id', $request->booking_id)->first();
                    
                    if(empty($get_data)){

                         $payfair=[
                             "user_id"=>$booking_data->user_id,
                             "amount"=>$booking_data->fare,
                             "transaction_type"=>'2',
                             "transaction_date"=>date('Y-m-d'),
                             "booking_id"=>$request->booking_id
                         ];
                         
                         db::table('my_wallet')->insert($payfair);       
                           
                        
                        $payment_type = '2';
                        
                         $actual_fare_new = ($booking_data->fare)??1;
                         
                         $gst_percentage = (get_option_data('gst_percentage'))??"0";
                         
                         $gst_amount = ($booking_data->gst_amount)??0;
                         
                         $actual_fare = ($actual_fare_new-$gst_amount);
                         
                         $admin_per = 0;
                         
                         $percentage_calculator= db::table('mlm_percentage')->select('user_name','percent')->where('status','1')->get();
                         foreach($percentage_calculator as $val){
                            if($val->user_name=='admin'){
                                $admin_per=$val->percent;
                            }
                        }
                        
                        $payment_staus = [];
                            
                            $total_percentage = $admin_per;
                            $admint_amount = ($actual_fare*$total_percentage)/100;

                            $driver_amount = ($actual_fare-$admint_amount);
                        
                            // driver wallet add money  
                             $commison3 = [
                                     "user_id"=>$booking_data->driver_id,
                                     "amount"=>$driver_amount,
                                     "transaction_type"=>'1',
                                     "transaction_date"=>date('Y-m-d'),
                                     "booking_id"=>$request->booking_id
                                 ];
                             db::table('my_wallet')->insert($commison3); 
                             
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
                             ];
                             $payment_staus= DB::table('list_subscription')->insert($transaction);
                             DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
            
                        
                        $title = "Ride payment ";
                        $message = "Your Ride (". $booking_data->booking_id .") payment has been successfull !";
                        send_firebase_notification($title, $message, $booking_data->user_id);
                        send_firebase_notification($title, $message, $booking_data->driver_id);
                        
                        send_websocket_notification($booking_data->user_id, $title, $message, 'driver_complete_ride');
                        
                        date_default_timezone_set('Africa/Johannesburg');
                        DB::table('booking_activity_log')->where('booking_id', $booking_data->booking_id)->update(['driver_amount' => $driver_amount, 'amount_driver_id' => $booking_data->driver_id]); 
                        
                    }
                }
                
                $booking_data->payment_mode = ($booking_data->payment_mode == "")?"2":$booking_data->payment_mode;
                $arr['status']=1;
                $arr['message']="Successfully...";
                $arr['data']=$booking_data;
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    public function user_all_rides(Request $request)
    {
        \Log::info('user_all_rides : ');
        \Log::info($request->all());
    
        $typevalidate = Validator::make($request->all(), [
            'status' => 'required', // 1-ongoing, 2-past, 3-schedule
        ]);
    
        if ($typevalidate->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => $typevalidate->errors()->first(),
                'data'    => NULL
            ], 200);
        }
    
        try {
            $user_id = Auth::id();
    
            // 🔹 Pagination Params
            $page    = $request->page ?? 1;
            $perPage = $request->per_page ?? 10;
            $offset  = ($page - 1) * $perPage;
    
            $query = DB::table('bookings as b')
                ->select(
                    'b.id', 'b.booking_type', 'b.booking_id', 'b.is_rental',
                    'b.payment_status', 'b.rental_km', 'b.rental_hour',
                    'b.picup_location', 'b.drop_location', 'b.booking_status',
                    'b.trip_type', 'b.picup_lat', 'b.picup_long',
                    'b.drop_lat', 'b.drop_long',
                    'bs.name as booking_status_name',
                    'b.booking_date', 'b.booking_time', 'b.fare',
                    DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile) as profile_image'),
                    'd.id as driver_id', 'd.name as driver_name',
                    'd.latitude', 'd.longtitude',
                    'detail.vehicle_number',
                    'vm.name as vehicle_model',
                    'd.mobile_no',
                    'b.first_stop_location', 'b.first_stop_lat', 'b.first_stop_long',
                    'b.second_stop_location', 'b.second_stop_lat', 'b.second_stop_long'
                )
                ->leftJoin('users as d', 'd.id', '=', 'b.driver_id')
                ->leftJoin('booking_status as bs', 'bs.id', '=', 'b.booking_status')
                ->leftJoin('driver_details_basic_details as detail', 'detail.driver_id', '=', 'b.driver_id')
                ->leftJoin('vehicle_models_name as vm', 'vm.id', '=', 'detail.vehicle_model')
                ->where('b.user_id', $user_id);
    
            // 🔹 Status Conditions
            if ($request->status == 1) {
                $query->where('b.booking_type', 1)
                      ->whereIn('b.booking_status', ['0', '2', '4']);
            } elseif ($request->status == 2) {
                $query->whereIn('b.booking_status', ['1', '3', '5', '6', '7', '8']);
            } elseif ($request->status == 3) {
                $query->where('b.booking_type', 2);
            }
    
            // 🔹 Total Count
            $totalRecords = $query->count();
            $totalPages   = ceil($totalRecords / $perPage);
    
            // 🔹 Fetch Paginated Data
            $result = $query
                ->orderBy('b.id', 'DESC')
                ->offset($offset)
                ->limit($perPage)
                ->get();
    
            if ($result->count() > 0) {
    
                foreach ($result as $res) {
                    $res->booking_date = date('d-m-Y', strtotime($res->booking_date));
                    $res->booking_time = date('h:i A', strtotime($res->booking_time));
                    $res->invoice_link = url('/api') . '/download_invoice?id=' . $res->id;
                }
    
                return response()->json([
                    'status'         => 1,
                    'message'        => "Rides fetched successfully",
                    'current_page'   => (int) $page,
                    'per_page'       => (int) $perPage,
                    'total_records'  => $totalRecords,
                    'total_pages'    => $totalPages,
                    'data'           => $result,
                ], 200);
            }
    
            return response()->json([
                'status'  => 0,
                'message' => "No data found",
                'data'    => NULL
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json([
                'status'  => 0,
                'message' => "Sorry!! Something Went Wrong",
                'data'    => NULL
            ], 200);
        }
    }
    
        
    // public function user_all_rides(Request $request){

    //     \Log::info('user_all_rides : ');
    //     \Log::info($request->all());

    //     $typevalidate=Validator::make($request->all(),[ 
    //         'status'=>'required', // 1-ongoing, 2- past, 3- schedule
    //     ]);
    //     if($typevalidate->fails())
    //     {
    //         $arr['status']=0;
    //         $arr['message']=$typevalidate->errors()->first();
    //         $arr['data']=NULL;
    //         return response()->json($arr,200);
    //     } 
    //   try
    //     {
    //         $user_id = Auth::id();
    //         $currentDate = Carbon::today()->format('Y-m-d'); // Use Y-m-d for correct comparison
    //         $currentTime = Carbon::now()->format('H:i'); 
            
    //         // $result = DB::table('bookings')->where('user_id',$user_id)->where('booking_status',5)->get();
            
    //         $result = DB::table('bookings as b')
    //         ->select('b.id', 'b.booking_type', 'b.booking_id','b.is_rental', 'b.payment_status', 'b.rental_km', 'b.rental_hour', 'b.picup_location', 'b.drop_location', 'b.booking_status', 'b.trip_type', 'b.picup_lat', 'b.picup_long', 'b.drop_lat',
    //         'b.drop_long', 'bs.name as booking_status_name', 'b.booking_date', 'b.booking_time', 'b.fare', DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile)  as profile_image'), 'd.id as driver_id',
    //         'd.name as driver_name', 'd.latitude', 'd.longtitude', 'b.picup_lat', 'b.picup_long', 'detail.vehicle_number', 'vm.name as vehicle_model', 'd.mobile_no', 'b.first_stop_location', 'b.first_stop_lat', 'b.first_stop_long',
    //         'b.second_stop_location', 'b.second_stop_lat', 'b.second_stop_long')
    //         ->leftjoin('users as d','d.id','=','b.driver_id')
    //         ->leftjoin('booking_status as bs','bs.id','=','b.booking_status')
    //         ->leftjoin('driver_details_basic_details as detail','detail.driver_id','=','b.driver_id')
    //         ->leftjoin('vehicle_models_name as vm','vm.id','=','detail.vehicle_model')
    //         ->where('b.user_id',$user_id);
    //         if($request->status == 1){
    //             $result->where('b.booking_type', 1)->whereIn('b.booking_status',['0','4', '2']);
    //         }else if($request->status == 2){
    //             $result->whereIn('b.booking_status', ['3', '6', '7', '8', '5', '1']);
    //         }else if($request->status == 3){
    //             $result->whereIn('b.booking_type', [2]);
    //         }else{
    //             $result->whereIn('b.booking_status', ['0']);
    //         }
    //         $result = $result->orderBy('b.id', 'DESC')->get();

    //         if(!empty($result))
    //         {
    //             foreach($result as $res){
    //                 $res->booking_date = date('d-m-Y', strtotime($res->booking_date));
    //                 $res->booking_time = date('h:i A', strtotime($res->booking_time));
    //                 $res->invoice_link = url('/api').'/download_invoice?id='.$res->id;
    //             }
                
    //             $arr['status']=1;
    //             $arr['message']="Past Rides Successfully...";
    //             $arr['data']=$result;
    //         }
    //         else
    //         {
    //             $arr['status']=0;
    //             $arr['message']="Data not update";
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
    
    // public function driver_all_rides(Request $request){
        
    //     \Log::info('driver_all_rides : ');
    //     \Log::info($request->all());
        
    //     $typevalidate=Validator::make($request->all(),[ 
    //         'status'=>'required', // 1-ongoing, 2- past, 3- schedule
    //     ]);
    //     if($typevalidate->fails())
    //     {
    //         $arr['status']=0;
    //         $arr['message']=$typevalidate->errors()->first();
    //         $arr['data']=NULL;
    //         return response()->json($arr,200);
    //     } 
    //   try
    //     {
    //         $user_id = Auth::id();
    //         $currentDate = Carbon::today()->format('Y-m-d');
    //         $currentTime = Carbon::now()->format('H:i'); 
            
    //         $page      = $request->page ?? 1;         // current page
    //         $perPage   = $request->per_page ?? 20;    // records per page
    //         $offset    = ($page - 1) * $perPage;
            
    //         $result = DB::table('bookings as b')
    //         ->select('b.id', 'b.booking_type', 'b.booking_id','b.is_rental', 'b.payment_status', 'b.rental_km', 'b.rental_hour', 'b.picup_location', 'b.drop_location', 'b.booking_status', 'b.trip_type', 'b.picup_lat', 'b.picup_long', 'b.drop_lat', 'b.drop_long', 'bs.name as booking_status_name', 'b.booking_date', 'b.booking_time', 'b.fare', DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile)  as profile_image'), 'd.id as driver_id', 'd.name as driver_name', 'd.latitude', 'd.longtitude', 'b.picup_lat', 'b.picup_long', 'detail.vehicle_number', 'vm.name as vehicle_model', 'd.mobile_no', 'b.first_stop_location', 'b.first_stop_lat', 'b.first_stop_long', 'b.second_stop_location', 'b.second_stop_lat', 'b.second_stop_long')
    //         ->leftjoin('users as d','d.id','=','b.driver_id')
    //         ->leftjoin('booking_status as bs','bs.id','=','b.booking_status')
    //         ->leftjoin('driver_details_basic_details as detail','detail.driver_id','=','b.driver_id')
    //         ->leftjoin('vehicle_models_name as vm','vm.id','=','detail.vehicle_model')
    //         ->where('b.driver_id',$user_id);
            
    //         if($request->status == 1){
    //             $result->where('b.booking_type', 1)->whereIn('b.booking_status',['0','4', '2']);
    //         }else if($request->status == 2){
    //             $result->whereIn('b.booking_status', ['3', '6', '7', '8', '5', '1']);
    //         }else if($request->status == 3){
    //             $result->whereIn('b.booking_type', [2]);
    //         }else{
    //             $result->whereIn('b.booking_status', ['0']);
    //         }
    //         $result = $result->orderBy('b.id', 'DESC')->get();

    //         if(!empty($result))
    //         {
    //             foreach($result as $res){
    //                 $res->booking_date = date('d-m-Y', strtotime($res->booking_date));
    //                 $res->booking_time = date('h:i A', strtotime($res->booking_time));
    //                 // $res->invoice_link = url('/api').'/download_invoice?id='.$res->id;
    //             }
                
    //             $arr['status']=1;
    //             $arr['message']="Past Rides Successfully...";
    //             $arr['data']=$result;
    //         }
    //         else
    //         {
    //             $arr['status']=0;
    //             $arr['message']="Data not update";
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
    
    public function driver_all_rides(Request $request)
    {
        \Log::info('driver_all_rides : ');
        \Log::info($request->all());
    
        $typevalidate = Validator::make($request->all(), [
            'status' => 'required', // 1-ongoing, 2- past, 3- schedule
        ]);
    
        if ($typevalidate->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => $typevalidate->errors()->first(),
                'data'    => NULL
            ], 200);
        }
    
        try {
            $user_id = Auth::id();
    
            $page    = $request->page ?? 1;
            $perPage = $request->per_page ?? 10;
            $offset  = ($page - 1) * $perPage;
    
            $query = DB::table('bookings as b')
                ->select(
                    'b.id', 'b.booking_type', 'b.booking_id', 'b.is_rental',
                    'b.payment_status', 'b.rental_km', 'b.rental_hour',
                    'b.picup_location', 'b.drop_location', 'b.booking_status',
                    'b.trip_type', 'b.picup_lat', 'b.picup_long',
                    'b.drop_lat', 'b.drop_long',
                    'bs.name as booking_status_name',
                    'b.booking_date', 'b.booking_time', 'b.fare',
                    DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile) as profile_image'),
                    'd.id as driver_id', 'd.name as driver_name',
                    'd.latitude', 'd.longtitude',
                    'detail.vehicle_number',
                    'vm.name as vehicle_model',
                    'd.mobile_no',
                    'b.first_stop_location', 'b.first_stop_lat', 'b.first_stop_long',
                    'b.second_stop_location', 'b.second_stop_lat', 'b.second_stop_long'
                )
                ->leftJoin('users as d', 'd.id', '=', 'b.driver_id')
                ->leftJoin('booking_status as bs', 'bs.id', '=', 'b.booking_status')
                ->leftJoin('driver_details_basic_details as detail', 'detail.driver_id', '=', 'b.driver_id')
                ->leftJoin('vehicle_models_name as vm', 'vm.id', '=', 'detail.vehicle_model')
                ->where('b.driver_id', $user_id);
    
            if ($request->status == 1) {
                $query->where('b.booking_type', 1)
                      ->whereIn('b.booking_status', ['0', '2', '4']);
            } elseif ($request->status == 2) {
                $query->whereIn('b.booking_status', ['1', '3', '5', '6', '7', '8']);
            } elseif ($request->status == 3) {
                $query->where('b.booking_type', 2);
            }
    
            // ðŸ”¹ Total Records Count
            $totalRecords = $query->count();
            $totalPages   = ceil($totalRecords / $perPage);
    
            // ðŸ”¹ Apply Pagination
            $result = $query
                ->orderBy('b.id', 'DESC')
                ->offset($offset)
                ->limit($perPage)
                ->get();
    
            if ($result->count() > 0) {
                foreach ($result as $res) {
                    $res->booking_date = date('d-m-Y', strtotime($res->booking_date));
                    $res->booking_time = date('h:i A', strtotime($res->booking_time));
                }
    
                return response()->json([
                    'status'         => 1,
                    'message'        => "Rides fetched successfully",
                    'current_page'   => (int) $page,
                    'per_page'       => (int) $perPage,
                    'total_records'  => $totalRecords,
                    'total_pages'    => $totalPages,
                    'data'           => $result,
                ], 200);
            }
    
            return response()->json([
                'status'  => 0,
                'message' => "No data found",
                'data'    => NULL
            ], 200);
    
        } catch (\Exception $e) {
            \Log::error($e);
            return response()->json([
                'status'  => 0,
                'message' => "Sorry!! Something Went Wrong",
                'data'    => NULL
            ], 200);
        }
    }

   
   
    public function user_pay_online(Request $request){
        
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
        // try 
        // {
            $driverId=Auth::id();
            $booking_data = DB::table('bookings')->where('booking_id', $request->booking_id)->first();
            if($booking_data){
                
                $get_data = DB::table('list_subscription')->where('booking_id', $booking_data->booking_id)->first();
                
                    if(empty($get_data)){

                         $payment_type = '2';
                        
                         $actual_fare_new = ($booking_data->fare)??1;
                         
                         $gst_percentage = (get_option_data('gst_percentage'))??"0";
                         
                         $gst_amount = ($booking_data->gst_amount)??0;
                         
                         $actual_fare = ($actual_fare_new-$gst_amount);
                         
                         $admin_per = 0;
                         
                         $percentage_calculator= db::table('mlm_percentage')->select('user_name','percent')->where('status','1')->get();
                         foreach($percentage_calculator as $val){
                            if($val->user_name=='admin'){
                                $admin_per=$val->percent;
                            }
                        }
                        
                        $payment_staus = [];
                        
                        $total_percentage = $admin_per;
                        $admint_amount = ($actual_fare*$total_percentage)/100;

                        $driver_amount = ($actual_fare-$admint_amount);
                    
                        // driver wallet add money  
                         $commison3 = [
                                 "user_id"=>$booking_data->driver_id,
                                 "amount"=>$driver_amount,
                                 "transaction_type"=>'1',
                                 "transaction_date"=>date('Y-m-d'),
                                 "booking_id"=>$request->booking_id
                             ];
                         db::table('my_wallet')->insert($commison3); 
                         
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
                         ];
                         $payment_staus= DB::table('list_subscription')->insert($transaction);
                         DB::table('bookings')->where('booking_id', $booking_data->booking_id)->update(['payment_status'=>'1']);
            
                        
                        $title = "Ride payment ";
                        $message = "Your Ride (". $booking_data->booking_id .") payment has been successfull !";
                        send_firebase_notification($title, $message, $booking_data->user_id);
                        send_firebase_notification($title, $message, $booking_data->driver_id);
                        
                        $arr['status']=1;
                        $arr['message']="Booking payment successfully !";
                        $arr['data']=NULL;
                        return response()->json($arr,200);
                        
                    }else{
                        
                    $arr['status']=1;
                    $arr['message']="Booking Already paid";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
            }else{
                $arr['status']=0;
                $arr['message']="No Data Found !";
                $arr['data']=NULL;
                return response()->json($arr,200);
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
    
    public function faq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first()
            ], 400);
        }

        $response = DB::table('faqs')->where('type', '1')->get();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }
    
}