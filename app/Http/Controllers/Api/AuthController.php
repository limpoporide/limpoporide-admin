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
use Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    
    /************Create User*************/
    private function send_msg($otp,$name,$mobile){
        
        return true; // Bypass MSG 91
        
        // dd($mobile);
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
        // dd($response);
        curl_close($curl);
        // echo $response;
        return $response;
    }
    
   
    public function user_login(Request $request)
    {

        \Log::info('user_login');
        \Log::info($request->all());

        $typevalidate=Validator::make($request->all(),[
            'email_mobile'=>'required',
            'password'=>'required',
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
 
            $users = User::where(function($query) use ($request) {
                $query->where('mobile_no', $request->email_mobile)
                      ->orWhere('email', $request->email_mobile);
            })
            ->where('type', '1')
            ->first();
            
            
            if($users){
                
                if($users->status == "2"){
                    $arr['status']=0;
                    $arr['message']= "You are Blocked !";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                
                if($users->otp_verify == "no"){
                    $arr['status']=0;
                    $arr['message']= "Otp is not Verified. Please register again !";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                
                if(Hash::check($request->password, $users->password))
                {
                    $token=  $users->createToken('limpopo_ride')->accessToken;
               
                    User::where('id',$users->id)->update(['remember_token'=>$token,'api_token'=>$token, 'device_token'=>($request->device_token)??""]);
                    
                    $res = User::where('id',$users->id)->first();
                    
                    $res->vehicle_name = check_driver_vehicle($res->id);
                    
                    $res->profile = !empty($res->profile)?url('public/profile_image/'.$res->profile):profileImg();
                    
                    
                    $arr['status']=1;
                    $arr['message']='Success';
                    $arr['data']=$res;
                    return response()->json($arr,200);
                    
                }else{
                    $arr['status']=0;
                    $arr['message']='Password is Invalid !';
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                
            }else{
                $arr['status']=0;
                $arr['message']='User Not Found !';
                $arr['data']=NULL;
                return response()->json($arr,200);
            }

        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    public function user_signup(Request $request)
    {
        $typevalidate = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required|regex:/[0-9]{9}/',
            'email' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
    
        if ($typevalidate->fails()) {
            $arr['status'] = 0;
            $arr['message'] = $typevalidate->errors()->first();
            $arr['data'] = NULL;
            return response()->json($arr, 200);
        }
    
        try {
            // Check if any record with same email or mobile exists
            $existing_user = User::where(function($query) use ($request) {
                $query->where('mobile_no', $request->mobile)
                      ->orWhere('email', $request->email);
            })
            ->where('type', '1')
            ->first();
    
            $data = [
                'type' => '1',
                'name' => $request->name,
                'mobile_no' => $request->mobile,
                'gender' => $request->gender??"",
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => rand(1111, 9999),
                'otp_verify' => "no",
            ];
    
            if ($existing_user) {
                if ($existing_user->otp_verify == "no") {
                    // Update existing unverified user
                    DB::table('users')->where('id', $existing_user->id)->update($data);
    
                    $user = DB::table('users')
                        ->select('id', 'name', 'otp')
                        ->where('id', $existing_user->id)
                        ->first();
    
                    $arr['status'] = 1;
                    $arr['message'] = "Existing unverified user updated successfully.";
                    $arr['data'] = $user;
                    return response()->json($arr, 200);
    
                } elseif ($existing_user->otp_verify == "yes") {
                    // Verified user exists — reject
                    $arr['status'] = 0;
                    $arr['message'] = "Email or Mobile already registered and verified!";
                    $arr['data'] = NULL;
                    return response()->json($arr, 200);
                }
            }
    
            // No existing record found — insert new user
            $user_id = DB::table('users')->insertGetId($data);
    
            $user = DB::table('users')->select('id', 'name', 'otp')->where('id', $user_id)->first();
            
            send_otp_sms($request->mobile, $user->otp, $user->name);
    
            $arr['status'] = 1;
            $arr['message'] = "New user registered successfully.";
            $arr['data'] = $user;
            return response()->json($arr, 200);
    
        } catch (\Exception $e) {
            $arr['status'] = 0;
            $arr['message'] = "Something went wrong: " . $e->getMessage();
            $arr['data'] = NULL;
            return response()->json($arr, 200);
        }
    }
    

    public function deiver_signup(Request $request)
    {
        $typevalidate = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required|regex:/[0-9]{9}/',
            'email' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
    
        if ($typevalidate->fails()) {
            $arr['status'] = 0;
            $arr['message'] = $typevalidate->errors()->first();
            $arr['data'] = NULL;
            return response()->json($arr, 200);
        }
    
        try {
            $existing_user = User::where(function($query) use ($request) {
                $query->where('mobile_no', $request->mobile)
                      ->orWhere('email', $request->email);
            })
            ->where('type', '2')
            ->first();
    
            $data = [
                'type' => '2',
                'name' => $request->name,
                'mobile_no' => $request->mobile,
                'gender' => $request->gender??"",
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp' => rand(1111, 9999),
                'otp_verify' => "no",
            ];
    
            if ($existing_user) {
                if ($existing_user->otp_verify == "no") {
                    DB::table('users')->where('id', $existing_user->id)->update($data);
    
                    $user = DB::table('users')
                        ->select('id', 'name', 'otp')
                        ->where('id', $existing_user->id)
                        ->first();
    
                    $arr['status'] = 1;
                    $arr['message'] = "Existing unverified Driver updated successfully.";
                    $arr['data'] = $user;
                    return response()->json($arr, 200);
    
                } elseif ($existing_user->otp_verify == "yes") {
                    $arr['status'] = 0;
                    $arr['message'] = "Email or Mobile already registered and verified!";
                    $arr['data'] = NULL;
                    return response()->json($arr, 200);
                }
            }
    
            // No existing record found — insert new user
            $user_id = DB::table('users')->insertGetId($data);
            
            DB::table('driver_details_basic_details')->insert(['driver_id'=>$user_id]);
    
            $user = DB::table('users')->select('id', 'name', 'mobile_no', 'otp')->where('id', $user_id)->first();
            
            // send_otp_sms($user->mobile_no, $user->otp, $user->name);
    
            $arr['status'] = 1;
            $arr['message'] = "New Driver registered successfully.";
            $arr['data'] = $user;
            return response()->json($arr, 200);
    
        } catch (\Exception $e) {
            $arr['status'] = 0;
            $arr['message'] = "Something went wrong: " . $e->getMessage();
            $arr['data'] = NULL;
            return response()->json($arr, 200);
        }
    }
    
    
    public function driver_login(Request $request)
    {
        // \Log::info('driver_login');
        // \Log::info($request->all());
        
        $typevalidate=Validator::make($request->all(),[
            'email_mobile'=>'required',
            'password'=>'required',
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
            
            $checkMobile = User::where(function($query) use ($request) {
                $query->where('mobile_no', $request->email_mobile)
                      ->orWhere('email', $request->email_mobile);
            })
            ->where('type', '2')
            ->first();
            
            // dd($checkMobile);
            
            if(empty($checkMobile)){
                
                $arr['status']=0;
                $arr['message'] = "Driver not register !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            if($checkMobile->otp_verify == "no"){
                
                $arr['status']=0;
                $arr['message'] = "Otp is not Verified. Please register again !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            // if($checkMobile->vehicle_register == "no"){
            //      $arr['status']=0;
            //      $arr['message']='Please register your vehicle !';
            //      $arr['redirect']="vehicle_register"; 
            //      $arr['token']= $checkMobile->api_token; 
            //      return response()->json($arr,200);
            // }
            // if($checkMobile->complete_profile == "no"){
            //      $arr['status']=0;
            //      $arr['message']='Please complete your profile !';
            //      $arr['redirect']="complete_profile"; 
            //      $arr['token']= $checkMobile->api_token;
            //      return response()->json($arr,200);
            // }
            // if($checkMobile->complete_bank_details == "no"){
            //      $arr['status']=0;
            //      $arr['message']='Please complete your Bank Details !';
            //      $arr['redirect']="complete_bank_details"; 
            //      $arr['token']= $checkMobile->api_token;
            //      return response()->json($arr,200);
            // }

            
            $vehicle_data_details =DB::table('driver_details_basic_details')->where('driver_id',$checkMobile->id)->first(); 
            
            if(empty($vehicle_data_details)){
                 $arr['status']=0;
                 $arr['message']='You have no assign any vehicle please contact support !';
                 $arr['data']=NULL; 
                 return response()->json($arr,200);
            }

            if (Hash::check($request->password, $checkMobile->password))
            {
                $token=$checkMobile->createToken('limpopo_ride')->accessToken;
                $otp = rand(1111,9999);
                $result = User::where('id',$checkMobile->id)->update(['status'=>1,'login_status'=>1,'remember_token'=>$token,'api_token'=>$token,'otp'=>$otp, 'device_type'=>$request->device_type, 'device_token'=>$request->device_token]); 
                $user = User::where('id',$checkMobile->id)->first();
                if($user){ 
                    
                    $user->driver_id = "DRIVER".$user->id;
                    $user->vehicle_number = $vehicle_data_details->vehicle_number??"";
                    $user->profile = !empty($user->profile)?url('public/profile_image/'.$user->profile):profileImg();
                    $user->vehicle_name = check_driver_vehicle($user->id);
                    
                    $arr['status']=1;
                    $arr['message']="You're login Successfully";
                    $arr['data']=$user;
                }
                else{
                    
                    $arr['status']=0;
                    $arr['message']='Wrong Id Password';
                    $arr['data']=NULL;
                }
            }else{
                $arr['status']=0;
                $arr['message']="Your password doesn't match";
                $arr['data']=NULL;
            }

        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="This mobile number is not registered";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }

  
    public function user_verify_otp(Request $request)
    {
        $arr=[];
        $typevalidate=Validator::make($request->all(),[
            'type'=>'required',
            'id'=>'required',
            'otp'=>'required',
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
            $data= User::where('id',$request->id)->where('type',$request->type)->first();
            
            if(empty($data)){
                $arr['status']=0;
                $arr['message']='User not found!';
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $data=User::where('id',$request->id)->where('otp', $request->otp)->where('type',$request->type)->first();
            if($data)
            {
                $token =  $data->createToken('limpopo_ride')->accessToken;
               
                User::where('id', $data->id)->update(['remember_token'=>$token,'api_token'=>$token, 'otp_verify' => 'yes', 'device_token'=>($request->device_token)??""]);
                
                $user_data = User::where('id',$request->id)->first();
                $user_data->profile = !empty($user_data->profile)?url('public/profile_image/'.$user_data->profile):profileImg();
                $user_data->vehicle_name = check_driver_vehicle($user_data->id);
                
                $arr['status']=1;
                $arr['message']='OTP Verify Successfully !';
                $arr['data']=$user_data;
            }
            else
            {
                $arr['status']=0;
                $arr['message']='OTP Invalid';
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
    
    public function reset_otp_send(Request $request){
        
        \Log::info('reset_otp_send');
        \Log::info($request->all());
        
        $arr=[];
        $typevalidate=Validator::make($request->all(),[
            'type'=>'required',
            'email_mobile'=>'required',
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
            
            $existing_user = User::where(function($query) use ($request) {
                $query->where('mobile_no', $request->email_mobile)
                      ->orWhere('email', $request->email_mobile);
            })
            ->where('type', $request->type)
            ->first();
            
            if(empty($existing_user)){
                $arr['status']=0;
                $arr['message']='User not found!';
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            DB::table('users')->where('id', $existing_user->id)->update(['otp' => rand(1111, 9999)]);
            
            $user = DB::table('users')->select('id', 'name', 'mobile_no', 'otp')->where('id', $existing_user->id)->first();
            
            send_otp_sms($user->mobile_no, $user->otp, $user->name);
    
            $arr['status'] = 1;
            $arr['message'] = "OTP Send Successfully !";
            $arr['data'] = $user;
            return response()->json($arr, 200);
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
    }
    
    public function get_banners(Request $request)
    {

        try
        { 
           $banners = DB::table('banners')->where('status', '1')->get();
           
           if($banners)
           {
               
               foreach($banners as $res){
                   $res->image = url('public/banners')."/".$res->image;
               }
               
                $arr['status']=1;
                $arr['message']='Success !';
                $arr['data']=$banners;
           }
           else
           {
                $arr['status']=0;
                $arr['message']="No Data found !";
                $arr['data']=null;
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
    
    public function user_logout(Request $request)
    {
        $user_id = Auth::id(); 
        $time = Carbon\Carbon::now();
        $logouttime = $time->toDateTimeString();
      
        $arr=[];
         
        try
        { 
            $user = User::select('id')->where('id',$user_id)->first();  
        if($user){
            Session::flush();
            Cache::flush(); 
            $userLogout = User::where('id',$user_id)->update(['device_token'=>'','api_token' =>'','remember_token'=>'','updated_at'=>$logouttime]);
           
           if($userLogout)
           {
                $arr['status']=1;
                $arr['message']='User logout successfully!!';
                $arr['data']=$userLogout;
           }
           else
           {
                $arr['status']=0;
                $arr['message']="Can't logout!!";
                $arr['data']=null;
           }
            
        }
        else
        {
                $arr['status']=0;
                $arr['message']="User is not login";
                $arr['data']=null;
            
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
  
    
    public function signup_save_profile(Request $request)
    {
        $validate=Validator::make($request->all(),[
            'id'=>'required',
            'name'=>'required',
            'email'=>'required|email|unique:users',
        ]);

        if($validate->fails())
        {
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();
            $arr['data']=NULL;

            return response()->json($arr,200);
        }
       try
        {
            $sdata['name']      = $request->name;
            $sdata['gender']    = $request->gender;
            $sdata['email']       = $request->email;
            $sdata['password']   = Hash::make($request->password);
            // $sdata['confirmpassword']   = $request->confirmpassword;
            // $sdata['birth_place']   = $request->birth_place;
            if($request->password != $request->confirmpassword){
                
                $arr['status']=0;
                $arr['message']='Password Not Matched';
                $arr['data']=NULL;
                 return response()->json($arr,200);
            }
            $save = DB::table('users')->where('id',$request->id)->update($sdata);

            if($save)
            {
                $result = DB::table('users')->select('id','name','gender','email')->where('id',$request->id)->first();
           
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
            $arr['message']=$e->getMessage();
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
    }
    
    public function sponser_areapartner_login(Request $request){
        
        Log::info('sponser_areapartner_login');
        Log::info($request->all());
        
        $typevalidate=Validator::make($request->all(),[
            'type'=>'required',
            'mobile_no'=>'required',
            'password'=>'required',
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
            
            $checkUser = User::where('type', $request->type)
                        ->where(function ($query) use ($request) {
                            $query->where('mobile_no', $request->mobile_no)
                                  ->orWhere('email', $request->mobile_no);
                        })->first(); 
                        
        //   dd($checkUser);                
            
            if($checkUser){
             
                $users = User::where('id',$checkUser->id)->first(); 

                if (Hash::check($request->password, $users->password))
                {
                    $token=$users->createToken('limpopo_ride')->accessToken;
                    $otp = rand(1111,9999);
                    $result = User::where('id',$users->id)->update(['status'=>1,'login_status'=>1,'remember_token'=>$token,'api_token'=>$token,'otp'=>$otp, 'device_type'=>$request->device_type, 'device_token'=>$request->device_token]); 
                    $user = User::where('id',$users->id)->first();
                    
                    if($user){ 
                        
                        $user->profile = !empty($user->profile)?url('public/file/area_sponsor/'.$user->profile):profileImg();
                        $user->vehicle_name = check_driver_vehicle($user->id);
                        
                        $arr['status']=1;
                        $arr['message']="You're login Successfully";
                        $arr['data']=$user;
                        
                    }else{
                        
                        $arr['status']=0;
                        $arr['message']='Wrong Id Password';
                        $arr['data']=NULL;
                        
                    }
                }else{
                    $arr['status']=0;
                    $arr['message']="Your password doesn't match";
                    $arr['data']=NULL;
                }
            
            }else{
                $arr['status']=0;
                $arr['message']="Invalid credentials";
                $arr['data']=NULL; 
            }

        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
   
    # Astrologer
    public function user_loginwith_pass(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
                'type'=>'required',
                'mobile_no'=>'required|numeric|min:10',
                'password'=>'required',
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
            
            $data=$request->all();
            if($request->type==1){
                 $checkMobile = User::where('mobile_no',$request->mobile_no)->where('type',$request->type)->first();
            }else{
               
                $checkMobile = User::where('mobile_no',$request->mobile_no)->where('type',$request->type)->first();  
                
                if($checkMobile){
                    if($checkMobile->vehicle_register == "no"){
                         $arr['status']=0;
                         $arr['message']='Please register your vehicle !';
                         $arr['redirect']="vehicle_register"; 
                         $arr['token']= $checkMobile->api_token; 
                         return response()->json($arr,200);
                    }
                    if($checkMobile->complete_profile == "no"){
                         $arr['status']=0;
                         $arr['message']='Please complete your profile !';
                         $arr['redirect']="complete_profile"; 
                         $arr['token']= $checkMobile->api_token;
                         return response()->json($arr,200);
                    }
                    if($checkMobile->complete_bank_details == "no"){
                         $arr['status']=0;
                         $arr['message']='Please complete your Bank Details !';
                         $arr['redirect']="complete_bank_details"; 
                         $arr['token']= $checkMobile->api_token;
                         return response()->json($arr,200);
                    }
                }
                
                $vehicle_data_details =DB::table('driver_details_basic_details')->where('driver_id',$checkMobile->id)->first(); 
                
                if(($vehicle_data_details->doc_status1 == 0) || ($vehicle_data_details->doc_status2 == 0) || ($vehicle_data_details->doc_status3 == 0) || ($vehicle_data_details->doc_status4 == 0) || ($vehicle_data_details->doc_status5 == 0)){
                     $arr['status']=0;
                     $arr['message']='Please wait for document verification';
                     $arr['data']=NULL; 
                     return response()->json($arr,200);
                }

            }
            if($checkMobile){
            $users = User::where('mobile_no',$request->mobile_no)->where('type',$request->type)->first(); 

                if (Hash::check($request->password, $users->password))
                {
                    $token=$users->createToken('limpopo_ride')->accessToken;
                    $otp = rand(1111,9999);
                    $result = User::where('id',$users->id)->update(['status'=>1,'login_status'=>1,'remember_token'=>$token,'api_token'=>$token,'otp'=>$otp, 'device_type'=>$request->device_type, 'device_token'=>$request->device_token]); 
                    $user = User::where('id',$users->id)->first();
                    if($user){ 
                        
                        $user->vehicle_name = check_driver_vehicle($user->id);
                        
                        $arr['status']=1;
                        $arr['message']="You're login Successfully";
                        $arr['data']=$user;
                    }
                    else{
                        
                        
                        $arr['status']=0;
                        $arr['message']='Wrong Id Password';
                        $arr['data']=NULL;
                    }
                    // The passwords match...
                }else{
                        $arr['status']=0;
                        $arr['message']="Your password doesn't match";
                        $arr['data']=NULL;
                }
            
            }else{
                $arr['status']=0;
                $arr['message']="This mobile number is not registered";
                $arr['data']=NULL; 
            }

        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="This mobile number is not registered";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    
    
    public function recovery_password(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
                                'type'=>'required',
                                'mobile_no'=>'required'
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
            
            $checkMobile = User::where('mobile_no',$request->mobile_no)->where('type',$request->type)->first();
            if($checkMobile){
                
            $otp = rand(1111,9999);
            $data['otp'] = $otp;
            
            $result = User::where('id',$checkMobile->id)->update($data);
            // $data=$this->send_msg($otp,$checkMobile->name,$checkMobile->mobile_no);
            //dd($data);
                    if($result){
                        $res = DB::table('users')->select('id','name','gender','email','otp')->where('id',$checkMobile->id)->first();
                        $arr['status']=1;
                        $arr['message']='Success';
                        $arr['data']=$res;
                    }
                    else{
                        
                        
                        $arr['status']=0;
                        $arr['message']='No Data Found';
                        $arr['data']=NULL;
                    }
            
            }else{
                $arr['status']=0;
                $arr['message']='Your Mobile no not register';
                $arr['data']=NULL; 
            }
                
            
              
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
  
    public function recovery_password_verify_otp(Request $request)
    {
        $arr=[];

        $typevalidate=Validator::make($request->all(),[
            'type'=>'required',
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
        try
        {
            $data= User::where('id',$request->id)->where('type',$request->type)->first();
            if(empty($data)){
                $arr['status']=0;
                $arr['message']='User not found!';
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $data=User::where('id',$request->id)->where('otp', $request->otp)->where('type',$request->type)->first();
            if($data)
            {
                //$token=$data->createToken('limpopo_ride')->accessToken;
                
                $result = User::where('id',$data->id)->update(['otp'=>'', 'otp_verify'=>'yes']);
                if($result){
                    $res = User::where('id',$data->id)->first();
                    
                    $res->vehicle_name = check_driver_vehicle($res->id);
                    
                    $arr['status']=1;
                    $arr['message']='Success';
                    $arr['data']=$res;
                }else{
                    $arr['status']=0;
                    $arr['message']='Sorry something went wrong!';
                    $arr['data']=NULL;
                }
            }
            else
            {
                $arr['status']=0;
                $arr['message']='OTP Invalid';
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
    
    public function update_user_password(Request $request){
        $arr=[];
        $validate=Validator::make($request->all(),[
              'old_password'=>'required', 
              'new_password'=>'required',
              'confirm_password'=>'required'
          ]);

        if($validate->fails())
        {
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();;
            $arr['data']=NULL;
            return response()->json($arr,200);
        }

        try
        {
            $id = Auth::id();
            $data= User::where('id',$id)->first();

            if($data)
            {
                
                
                if($request->new_password != $request->confirm_password){
                    $arr['status']=0;
                    $arr['message']='Password Didnt Match With Confirm Password';
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                
                
               if(Hash::check($request->old_password, $data->password)){
                    $hashpass= Hash::make($request->new_password);
                    $result = User::where('id',$data->id)->update(['password'=>$hashpass]);
                       
                    if($result){
                        $res = User::where('id',$data->id)->first();
                        $arr['status']=1;
                        $arr['message']='Success';
                        $arr['data']=$res;
                    }else{
                        $arr['status']=0;
                        $arr['message']='Data Not Update!';
                        $arr['data']=NULL;
                    }
                    return response()->json($arr,200);
               }
               else{
                    $arr['status']=0;
                    $arr['message']='Old password is Invalid !';
                    $arr['data']=NULL;
                    return response()->json($arr,200);
               }
 
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
    
    public function update_notification_status(Request $request){
        $arr=[];

        try
        {
            $id = Auth::id();
            $data= User::where('id',$id)->first();

            if($data)
            {
                $status = ($data->notification_status == "1")?"0":"1";
                
                $result = User::where('id',$data->id)->update(['notification_status'=>$status]);
                
                $res = User::where('id',$data->id)->first();
                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']=$res;
                return response()->json($arr,200);
 
            }else{
                $arr['status']=0;
                $arr['message']='No data found !';
                $arr['data']=NULL;
                return response()->json($arr,200);
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
    
    public function resend_otp(Request $request){
        
        $arr=[];
        $validate=Validator::make($request->all(),[
             'id'=>'required',
        ]);

        if($validate->fails())
        {
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();;
            $arr['data']=NULL;

            return response()->json($arr,200);
        }
        
         try
        {
            
            $otp = rand(1111,9999);
            $result = User::where('id',$request->id)->update(['otp'=>$otp]); 
            
            $user = User::where('id',$request->id)->first();

            $arr['status']=1;
            $arr['message']="OTP Resend successfuly !";
            $arr['data']=$user;

            
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);

        
    }
    
    public function update_password(Request $request)
    {
        $arr=[];
        $validate=Validator::make($request->all(),[
              'id'=>'required',
              'password' => 'required',
              'confirm_password' => 'required|same:password',
        ]);

        if($validate->fails())
        {
            $arr['status']=0;
            $arr['message']=$validate->errors()->first();;
            $arr['data']=NULL;

            return response()->json($arr,200);
        }

        try
        {
            $data= User::where('id',$request->id)->first();
            if(empty($data)){
                $arr['status']=0;
                $arr['message']='User not found!';
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            if($data)
            {
                
                $hashpass= Hash::make($request->password);
                $result = User::where('id',$data->id)->update(['password'=>$hashpass]);
                   
                if($result){
                    $res = User::where('id',$data->id)->first();
                    $arr['status']=1;
                    $arr['message']='Success';
                    $arr['data']=null;
                }else{
                    $arr['status']=0;
                    $arr['message']='Sorry something went wrong!';
                    $arr['data']=NULL;
                }
 
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
    
    public function delete_account(){
        try
        {
            $user_id = Auth::id(); 
            
            User::where('id', $user_id)->delete();

            $arr['status']=1;
            $arr['message']='Account delete Sucessfully !';
            $arr['data']=NULL;
            return response()->json($arr,200);

        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
    }
    
    
    public function user_recent_search_rides(Request $request){
        
        try
        {
            $user_id = Auth::id(); 
            
            $bookings = DB::table('bookings')->where('user_id', $user_id)->where('booking_status', '5')->orderBy('id', 'DESC')->limit(3)->get();

            $arr['status']=1;
            $arr['message']='Success !';
            $arr['data']=$bookings;
            return response()->json($arr,200);

        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
        
    }
    
    
    public function get_payout_data(Request $request){
        
        try
        {
            $user_id = Auth::id(); 
            
            $pending_payout = DB::table('weekly_payouts')->where('user_id', $user_id)->where('payment_status', '1')->orderBy('id', 'DESC')->get();
            $completed_payout = DB::table('weekly_payouts')->where('user_id', $user_id)->where('payment_status', '2')->orderBy('id', 'DESC')->get();
            
            $pending_payout_amount = DB::table('weekly_payouts')->where('user_id', $user_id)->where('payment_status', '1')->sum('amount');
            $completed_payout_amount = DB::table('weekly_payouts')->where('user_id', $user_id)->where('payment_status', '2')->sum('amount');
            
            $all_data = [
                "pending_payout_amount" => $pending_payout_amount,
                "completed_payout_amount" => $completed_payout_amount,
                "pending_payout" => $pending_payout,
                "completed_payout" => $completed_payout,
            ];

            $arr['status']=1;
            $arr['message']='Success !';
            $arr['data']=$all_data;
            return response()->json($arr,200);

        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']='Sorry!! Something Went Wrong';
            $arr['data']= NULL;
        }

        return response()->json($arr,200);
        
    }
    
    
}