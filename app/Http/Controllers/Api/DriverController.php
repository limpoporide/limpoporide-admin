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
use App\Models\PreUser;
use App\Models\TimeSlot;
use Auth; 
use PDF;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

// require_once __DIR__ . '/../../../../vendor/autoload.php';
// use chillerlan\QRCode\QRCode;
// use chillerlan\QRCode\Output\QRImage;
// use App\QRGdImage; 

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;


class DriverController extends Controller
{
    
    public function update_driver_cordinates(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'driver_id'=>'required',
            'latitude'=>'required',
            'longtitude'=>'required',
        ]);
                        
        try
        {
            if($typevalidate->fails())
            {
                $arr['registration_status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $driver_id = $request->driver_id;
            $data['latitude'] = $request->latitude;
            $data['longtitude'] = $request->longtitude;
            
            $user = User::where('id',$driver_id)->first();
            
            if(!empty($user))
            {
                $cordinateupdate = User::where('id',$driver_id)->update($data);
                
                    $arr['status']=1;
                    $arr['message']="Cordinates updated Successfully!!";
                    $arr['data']= $cordinateupdate;
            }
            else
            {
                    $arr['status']=0;
                    $arr['message']="couldn't update cordinates successfully!!";
                    $arr['data']=null;
            }
            
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
        }   
           
        return response()->json($arr,200);
      
    }
    
  
    
    // Driver registration start 
    
    public function driver_register(Request $request)
    {
        
        $typevalidate=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email',
            'mobile'=>'required|numeric|digits:10,mobile_no',
            // 'gender'=>'required',
            'password'=>'required|min:6',
            'confirm_password'=>'min:6|required_with:password|same:password'
          ],
          [
            'confirm_password.required_with'=> 'Confirm Password is required !', // custom message
            'confirm_password.same'=> 'Password Not Match', // custom message
           ]
        );
        
        if($typevalidate->fails()==true)
        {
            $arr['registration_status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }          
        // try {  

            $mobile_exist = DB::table('users')->where('mobile_no', $request->mobile)->where('type', '2')->count();
            if($mobile_exist)
            {
                $arr['registration_status']=0;
                $arr['message']="Mobile Number Already exist !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $mobile_exist = DB::table('users')->where('email', $request->email)->where('type', '2')->count();
            if($mobile_exist)
            {
                $arr['registration_status']=0;
                $arr['message']="Email Already exist !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            $referral_code = "";
            $data_referral_code = [];
            if(isset($request->referral_code) && $request->referral_code != ""){
                $data_referral_code = DB::table('users')->where('type',4)->where('referral_code', $request->referral_code)->first();
                
                if(empty($data_referral_code)){
                    $arr['status']=0;
                    $arr['message']="Incorrect Referral code";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                $referral_code = $data_referral_code->referral_code;
                
                
            }
            

            $name=$request->name;
            $mobile=$request->mobile;
            $email=$request->email;
            $gender=($request->gender)??"";
            $state=$request->state;
            $city =$request->city;
            // $deviceId=$request->device_id;
            // $deviceType=$request->device_type;
            // $deviceName=$request->device_name;
            // $deviceToken=$request->device_token;
            $vehicle_type_id=$request->vehicle_type_id;
            $latitude      =$request->latitude;
            $longtitude    =$request->longtitude;
            $password= Hash::make($request->password);
            
            $data=array(
                'type'=>2,
                'name'=>$name,
                'email'=>$email,
                'gender'=>$gender,
                'mobile_no'=>$mobile,
                'password'=>$password,
                'state'=>$state,
                'city'=>$city,
                'vehicle_type_id'=>$vehicle_type_id,
                'latitude'=>$latitude,
                'longtitude'=>$longtitude,
                'referral_code' => $referral_code, 
                'device_name' => $request->device_name??"", 
                'device_token' => $request->device_token??"", 
                );
                
         
            $user=User::create($data);
            $queryId = DB::getPdo()->lastInsertId();
            
            DB::table('driver_details_basic_details')->insert(['driver_id'=>$queryId]);
            
            $token = $user->createToken('CARMY')->accessToken;
            // dd($token);
            
            DB::table('users')->where('id',$user->id)->update(['api_token'=>$token,'remember_token'=>$token]);
            $fulldata = DB::table('users')->select('*')->Where('id',$queryId)->first();
            // dd($fulldata);
            if(!empty($data_referral_code)){
               $spdata=array( 
                'service_provide_id'=>$data_referral_code->id,
                'driver_id'=>$queryId,
                ); 
              DB::table('sp_driver_table')->insert($spdata);
            }

            
            $arr['registration_status']=1;
            $arr['message']="Registration Successfully!!";
            $arr['data']=$fulldata;

        // }
        // catch(\Exception $e){
            
        //     $arr['status']=0;
        //     $arr['message']="Something went wrong";
        //     $arr['data']=NULL;
        // }   
           
        return response()->json($arr,200);
    }
    
    public function sponser_areapartner_register(Request $request)
    {
        
        $typevalidate=Validator::make($request->all(),[
            'type'=>'required',
            'name'=>'required',
            'email'=>'required|email',
            'mobile'=>'required|numeric|digits:10,mobile_no',
            'password'=>'required|min:6',
            'confirm_password'=>'min:6|required_with:password|same:password'
        ]);
          
        if($typevalidate->fails()==true)
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        } 
        
        try {  

            $mobile_exist = DB::table('users')->where('mobile_no', $request->mobile)->where('type', $request->type)->count();
            if($mobile_exist)
            {
                $arr['registration_status']=0;
                $arr['message']="Mobile Number Already exist !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $mobile_exist = DB::table('users')->where('email', $request->email)->where('type', $request->type)->count();
            if($mobile_exist)
            {
                $arr['registration_status']=0;
                $arr['message']="Email Already exist !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            $referral_code = "";
            $data_referral_code = [];
            if(isset($request->referral_code) && $request->referral_code != ""){
                $data_referral_code = DB::table('users')->where('type',4)->where('referral_code', $request->referral_code)->first();
                
                if(empty($data_referral_code)){
                    $arr['status']=0;
                    $arr['message']="Incorrect Referral code";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                $referral_code = $data_referral_code->referral_code;
 
            }
            

            $name=$request->name;
            $mobile=$request->mobile;
            $email=$request->email;
            $gender=($request->gender)??"";
            $state=$request->state;
            $city =$request->city;
            // $deviceId=$request->device_id;
            // $deviceType=$request->device_type;
            // $deviceName=$request->device_name;
            // $deviceToken=$request->device_token;
            $vehicle_type_id=$request->vehicle_type_id;
            $latitude      =$request->latitude;
            $longtitude    =$request->longtitude;
            $password= Hash::make($request->password);
            
            $data=array(
                'type'=>2,
                'name'=>$name,
                'email'=>$email,
                'gender'=>$gender,
                'mobile_no'=>$mobile,
                'password'=>$password,
                'state'=>$state,
                'city'=>$city,
                'vehicle_type_id'=>$vehicle_type_id,
                'latitude'=>$latitude,
                'longtitude'=>$longtitude,
                'referral_code' => $referral_code, 
                );
                
         
            $user=User::create($data);
            $queryId = DB::getPdo()->lastInsertId();
            
            DB::table('driver_details_basic_details')->insert(['driver_id'=>$queryId]);
            
            $token = $user->createToken('CARMY')->accessToken;
            // dd($token);
            
            DB::table('users')->where('id',$user->id)->update(['api_token'=>$token,'remember_token'=>$token]);
            $fulldata = DB::table('users')->select('*')->Where('id',$queryId)->first();
            // dd($fulldata);
            if(!empty($data_referral_code)){
               $spdata=array( 
                'service_provide_id'=>$data_referral_code->id,
                'driver_id'=>$queryId,
                ); 
              DB::table('sp_driver_table')->insert($spdata);
            }

            
            $arr['registration_status']=1;
            $arr['message']="Registration Successfully!!";
            $arr['data']=$fulldata;

        }
        catch(\Exception $e){
            
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
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
    
    
    public function driver_update_profile(Request $request)
    {
        $id = Auth::id();
    
        // ========== Validation ==========
        $validator = Validator::make($request->all(), [
            'driver_name'      => 'required',
            'age'              => 'required',
            'dob'              => 'required',
            'licence_number'   => 'required',
            'state'            => 'required',
            'city'             => 'required',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => $validator->errors()->first(),
                'data'    => null
            ], 200);
        }
    
        try {
    
            $user = DB::table('users')->where('id', $id)->first();
    
            // ---------- Prepare Data ----------
            $updateData = [
                'name'            => $request->driver_name,
                'age'             => $request->age,
                'dob'             => $request->dob,
                'state'           => $request->state,
                'city'            => $request->city,
                'licence_number'  => $request->licence_number,
            ];
    
            // ---------- Profile Upload ----------
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
            }
    
            // ---------- Update ----------
            $updated = DB::table('users')->where('id', $id)->update($updateData);
    
            if ($updated) {
                return response()->json([
                    'status'  => 1,
                    'message' => "Driver profile updated successfully!",
                    'data'    => $updateData
                ], 200);
            } else {
                return response()->json([
                    'status'  => 0,
                    'message' => "No changes found",
                    'data'    => null
                ], 200);
            }
    
        } catch (\Exception $e) {
    
            return response()->json([
                'status'  => 0,
                'message' => "Sorry!! Something Went Wrong",
                'data'    => $e->getMessage()
            ], 200);
        }
    }

    
   /*******************************vehicle registration start***************************************/
     
    
     
     public function driver_profile(Request $request) 
    {
        $driver_id = Auth::id(); 
      try 
        {
            $result = DB::table('driver_details_basic_details')->select('driver_details_basic_details.id','driver_details_basic_details.driver_id','driver_details_basic_details.driving_licence'
                    ,'driver_details_basic_details.driving_licence2','driver_details_basic_details.id_proof','driver_details_basic_details.id_proof2','driver_details_basic_details.photo1','driver_details_basic_details.photo2','driver_details_basic_details.certificate1','driver_details_basic_details.certificate2'
                    ,'driver_details_basic_details.vehicle_audit_photo1','driver_details_basic_details.vehicle_audit_photo2','users.id','users.name','users.email','users.mobile_no','users.profile','users.dob','users.licence_number', 'users.age', 'cities.name as city_name', 'cities.id as city_id', 'states.state_name', 'states.id as state_id', 'users.user_current_version')
                    ->join('users', 'users.id', '=', 'driver_details_basic_details.driver_id')  
                    ->leftjoin('cities', 'cities.id', '=', 'users.city') 
                    ->leftjoin('states', 'states.id', '=', 'users.state')  
                    ->where('users.id',$driver_id) 
                    ->first();  
                    // if(!empty($result->dob))
                    // {    
                    // $result->age = \Carbon\Carbon::parse($result->dob)->age;
                    // }
                    // if(($result->dob == '0000-00-00') || ($result->dob == '')) 
                    // { 
                    //     $result->age='';  
                    // }
                    $result->profile= !empty($result->profile)?url('public/profile_image/'.$result->profile):profileImg();
                    $result->driving_licence= !empty($result->driving_licence)?url('public/vehicle_image/driving_licence'.'/'.$result->driving_licence):profileImg();
                    $result->driving_licence2= !empty($result->driving_licence2)?url('public/vehicle_image/driving_licence'.'/'.$result->driving_licence2):profileImg();
                    $result->id_proof= !empty($result->id_proof)?url('public/vehicle_image/id_proof'.'/'.$result->id_proof):profileImg();
                    $result->id_proof2= !empty($result->id_proof2)?url('public/vehicle_image/id_proof'.'/'.$result->id_proof2):profileImg(); 
                    $result->photo1= !empty($result->photo1)?url('public/vehicle_image/vehicle_photo'.'/'.$result->photo1):profileImg(); 
                    $result->photo2= !empty($result->photo2)?url('public/vehicle_image/vehicle_photo'.'/'.$result->photo2):profileImg(); 
                    $result->certificate1= !empty($result->certificate1)?url('public/vehicle_image/vehicle_certificate'.'/'.$result->certificate1):profileImg(); 
                    $result->certificate2= !empty($result->certificate2)?url('public/vehicle_image/vehicle_certificate'.'/'.$result->certificate2):profileImg(); 
                    $result->vehicle_audit_photo1 = !empty($result->vehicle_audit_photo1)?url('public/vehicle_image/vehicle_audit_photo'.'/'.$result->vehicle_audit_photo1):profileImg(); 
                    $result->vehicle_audit_photo2 = !empty($result->vehicle_audit_photo2)?url('public/vehicle_image/vehicle_audit_photo'.'/'.$result->vehicle_audit_photo2):profileImg();
                    
                    $result->vehicle_name = check_driver_vehicle($result->driver_id);
                    
                    $distance = DB::table('bookings')
                            ->where('driver_id', $driver_id)
                            ->where('booking_status', 5)
                            ->sum('distance');
                            
            $result->total_distance = get_total_distance($driver_id);
            $result->latest_version = get_option_data('apk_current_version');
           
           
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
    
    
    public function vehicle_details(Request $request) 
    {
        $driver_id = Auth::id(); 
        
       try
        {
            $result = DB::table('driver_details_basic_details as d')
                    ->select('d.*', 'u.city', 'u.state', 'c.name as country_operating_name')
                    ->leftjoin('users as u','u.id','=','d.driver_id')
                    ->leftjoin('country as c','c.id','=','d.country_operating')
                    ->where('d.driver_id',$driver_id)
                    ->first(); 
            
            $result->driving_licence= !empty($result->driving_licence)?url('public/vehicle_image/driving_licence'.'/'.$result->driving_licence):profileImg();
            $result->driving_licence2= !empty($result->driving_licence2)?url('public/vehicle_image/driving_licence'.'/'.$result->driving_licence2):profileImg();
            $result->id_proof= !empty($result->id_proof)?url('public/vehicle_image/id_proof'.'/'.$result->id_proof):profileImg();
            $result->id_proof2= !empty($result->id_proof2)?url('public/vehicle_image/id_proof'.'/'.$result->id_proof2):profileImg(); 
            $result->photo1= !empty($result->photo1)?url('public/vehicle_image/vehicle_photo'.'/'.$result->photo1):profileImg(); 
            $result->photo2= !empty($result->photo2)?url('public/vehicle_image/vehicle_photo'.'/'.$result->photo2):profileImg(); 
            $result->certificate1= !empty($result->certificate1)?url('public/vehicle_image/vehicle_photo'.'/'.$result->certificate1):profileImg(); 
            $result->certificate2= !empty($result->certificate2)?url('public/vehicle_image/vehicle_photo'.'/'.$result->certificate2):profileImg(); 
            $result->vehicle_audit_photo1 = !empty($result->vehicle_audit_photo1)?url('public/vehicle_image/vehicle_photo'.'/'.$result->vehicle_audit_photo1):profileImg(); 
            $result->vehicle_audit_photo2 = !empty($result->vehicle_audit_photo2)?url('public/vehicle_image/vehicle_photo'.'/'.$result->vehicle_audit_photo2):profileImg();
            
            $result->licence_disk= !empty($result->licence_disk)?url('public/vehicle_document/driving_licence'.'/'.$result->licence_disk):profileImg(); 
            $result->permit_disk= !empty($result->permit_disk)?url('public/vehicle_document/permit_disk'.'/'.$result->permit_disk):profileImg(); 
            $result->car_audit_document= !empty($result->car_audit_document)?url('public/vehicle_document/car_audit_document'.'/'.$result->car_audit_document):profileImg(); 
            $result->car_front_photo = !empty($result->car_front_photo)?url('public/vehicle_document/car_front_photo'.'/'.$result->car_front_photo):profileImg(); 
            $result->car_web_photo = !empty($result->car_web_photo)?url('public/vehicle_document/car_web_photo'.'/'.$result->car_web_photo):profileImg();
            
            $result->vehicle_type_id = $result->vehicle_type;
            $result->vehicle_reg_year_id = $result->vehicle_reg_year;
            $vehicle_data = DB::table('vehicle_type')->where('id',$result->vehicle_type)->first();
            
            $vehiclles = DB::table('cars_names')->where('id',$result->vehicle_manufacture)->first();
            $modles = DB::table('vehicle_models_name')->where('id',$result->vehicle_model)->first();
            
             $result->vehicle_model_id = $result->vehicle_model;
             $result->vehicle_manufacture_id = $result->vehicle_manufacture;
            
            if($vehiclles){
                $result->vehicle_manufacture = $vehiclles->name;
            }
            if($modles){
                $result->vehicle_model = $modles->name;
            }
            
            
            if($vehicle_data){
                $result->vehicle_type = $vehicle_data->name;
            }else{
                $result->vehicle_type = "";
            }
            
            $vehicle_fuel_data = DB::table('vehicle_varients')->where('id',$result->vehicle_fuel_type)->first();
            if($vehicle_fuel_data){
                $result->vehicle_fuel_type = $vehicle_fuel_data->varient_name;
                $result->vehicle_fuel_id = $vehicle_fuel_data->id;
            }else{
                $result->vehicle_fuel_type = "";
                $result->vehicle_fuel_id = "";
            }
           
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
    
    
    public function upload_rejected_reupload_document(Request $request){
        
        $typevalidate=Validator::make($request->all(),[
            'driver_id'=>'required',
            'slug'=>'required',
            'reupload_document'=>'required',
        ]);
        if($typevalidate->fails()==true)
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        try {
            $column_name = "";

            if($request->slug == "doc1_reupload"){
                $document_name = public_path('/vehicle_image/driving_licence');
                $column_name = "driving_licence";
            }
            else if($request->slug == "doc2_reupload"){
                $document_name = public_path('/vehicle_image/driving_licence');
                $column_name = "driving_licence2";
            }
            else if($request->slug == "doc3_reupload"){
                $document_name = public_path('/vehicle_image/id_proof');
                $column_name = "id_proof";
            }
            else if($request->slug == "doc4_reupload"){
                $document_name = public_path('/vehicle_image/id_proof');
                $column_name = "id_proof2";
            }
            else if($request->slug == "doc5_reupload"){
                $document_name = public_path('/vehicle_image/vehicle_photo');
                $column_name = "photo1";
            }
            else if($request->slug == "doc6_reupload"){
                $document_name = public_path('/vehicle_image/vehicle_photo');
                $column_name = "photo2";
            }
            else if($request->slug == "doc7_reupload"){
                $document_name = public_path('/vehicle_image/vehicle_photo');
                $column_name = "certificate1";
            }
            else if($request->slug == "doc8_reupload"){
                $document_name = public_path('/vehicle_image/vehicle_photo');
                $column_name = "certificate2";
            }
            else if($request->slug == "doc9_reupload"){
                $document_name = public_path('/vehicle_image/vehicle_photo');
                $column_name = "vehicle_audit_photo1";
            }
            else{
                $document_name = public_path('/vehicle_image/vehicle_photo');
                $column_name = "vehicle_audit_photo2";
            }
            
            if($request->hasFile('reupload_document')){
            
                 if(imageCompres($_FILES["reupload_document"]["tmp_name"], $_FILES["reupload_document"]["name"], $_FILES["reupload_document"]["size"], $document_name)){
                      $imageName1 = $_FILES["reupload_document"]["name"];
                 }else{
                      $arr['status']=0;
                      $arr['message']="Only png is Allowed !";
                      $arr['data']=NULL;
                      return response()->json($arr,200);
                 }
            
                $data_user1 = array(
                    $request->slug => '1',
                    $column_name => $imageName1,
                    
                );
                
                $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
           }
           
           $arr['status']=1;
           $arr['message']='Update Successfully !';
           $arr['data']=NULL;
           return response()->json($arr,200);
        
        } 
        catch(\Exception $e)
        {
          $arr['status']=0;
          $arr['message']='Sorry!! Something Went Wrong';
          $arr['data']=$e->getMessage();
        }
        return response()->json($arr,200);
        
    }
    
    public function document_upload(Request $request){
    
        \Log::info('document_upload : ');
        \Log::info($request->all());
        
        $typevalidate = Validator::make($request->all(),[
            'driver_id'=>'required',
        ]);
        
        if($typevalidate->fails() == true)
        {
            $arr['status'] = 0;
            $arr['message'] = $typevalidate->errors()->first();
            $arr['data'] = NULL;
            return response()->json($arr, 200);
        }
        
        // try {
            
            $driver_id = $request->driver_id;
    
            // Document 1 - Driving Licence
             if ($request->hasFile('document1')) {
                
                $path = 'vehicle_image/driving_licence/';
                $image = $request->file('document1');
                $destinationPath = public_path($path);
                $driving_licence = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $driving_licence);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['driving_licence' => $driving_licence]);
                
            }
            
            // Document 2 - Driving Licence 2
            if ($request->hasFile('document2')) {
                
                $path = 'vehicle_image/driving_licence/';
                $image = $request->file('document2');
                $destinationPath = public_path($path);
                $driving_licence2 = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $driving_licence2);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['driving_licence2' => $driving_licence2]);
                
            }
            
            // Document 3 - ID Proof
            if ($request->hasFile('document3')) {
                
                $path = 'vehicle_image/id_proof/';
                $image = $request->file('document3');
                $destinationPath = public_path($path);
                $id_proof = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $id_proof);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['id_proof' => $id_proof]);
                
            }
            
            // Document 4 - ID Proof 2
            if ($request->hasFile('document4')) {
                
                $path = 'vehicle_image/id_proof/';
                $image = $request->file('document4');
                $destinationPath = public_path($path);
                $id_proof2 = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $id_proof2);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['id_proof2' => $id_proof2]);
                
            }
            
            // Document 5 - Vehicle Photo 1
            if ($request->hasFile('document5')) {
                
                $path = 'vehicle_image/vehicle_photo/';
                $image = $request->file('document5');
                $destinationPath = public_path($path);
                $photo1 = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $photo1);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['photo1' => $photo1]);
                
            }
            
            // Document 6 - Vehicle Photo 2
            if ($request->hasFile('document6')) {
                
                $path = 'vehicle_image/vehicle_photo/';
                $image = $request->file('document6');
                $destinationPath = public_path($path);
                $photo2 = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $photo2);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['photo2' => $photo2]);
                
            }
            
            // Document 7 - Certificate 1
            if ($request->hasFile('document7')) {
                
                $path = 'vehicle_image/vehicle_photo/';
                $image = $request->file('document7');
                $destinationPath = public_path($path);
                $certificate1 = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $certificate1);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['certificate1' => $certificate1]);
                
            }
            
            // Document 8 - Certificate 2
            if ($request->hasFile('document8')) {
                
                $path = 'vehicle_image/vehicle_photo/';
                $image = $request->file('document8');
                $destinationPath = public_path($path);
                $certificate2 = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $certificate2);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['certificate2' => $certificate2]);
                
            }
            
            // Document 9 - Vehicle Audit Photo 1
            if ($request->hasFile('document9')) {
                
                $path = 'vehicle_image/vehicle_photo/';
                $image = $request->file('document9');
                $destinationPath = public_path($path);
                $vehicle_audit_photo1 = rand(1111, 9999).time() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $vehicle_audit_photo1);
                
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['vehicle_audit_photo1' => $vehicle_audit_photo1]);
                
            }
            
            // Document 10 - Vehicle Audit Photo 1
            if ($request->hasFile('profile') && $request->file('profile')->isValid()) {
            
                $file = $request->file('profile');
                $fileName = uniqid().'_'.time().'.'.$file->getClientOriginalExtension();
            
                $vehiclePath = public_path('vehicle_image/vehicle_photo/');
                $profilePath = public_path('profile_image/');
            
                if (!file_exists($vehiclePath)) {
                    mkdir($vehiclePath, 0755, true);
                }
            
                if (!file_exists($profilePath)) {
                    mkdir($profilePath, 0755, true);
                }
            
                $file->move($vehiclePath, $fileName);
            
                copy($vehiclePath.$fileName, $profilePath.$fileName);
            
                DB::table('driver_details_basic_details')->where('driver_id', $driver_id)->update(['vehicle_audit_photo2' => $fileName]);
                DB::table('users')->where('id', $driver_id)->update(['profile' => $fileName]);

            }
           
            $arr['status'] = 1;
            $arr['message'] = 'Update Successfully !';
            $arr['data'] = NULL;
            return response()->json($arr, 200);
        
        // } catch(\Exception $e) {
        //     $arr['status'] = 0;
        //     $arr['message'] = 'Sorry!! Something Went Wrong';
        //     $arr['data'] = $e->getMessage();
        //     return response()->json($arr, 200);
        // }
    }
        
    // public function document_upload(Request $request){
        
    //     \Log::info('document_upload : ');
    //     \Log::info($request->all());
        
    //      $typevalidate=Validator::make($request->all(),[
    //         'driver_id'=>'required',
    //     ]);
    //     if($typevalidate->fails()==true)
    //     {
    //         $arr['status']=0;
    //         $arr['message']=$typevalidate->errors()->first();
    //         $arr['data']=NULL;
    //         return response()->json($arr,200);
    //     }
    //     // try {
    //         $column_name = "";
            
    //         if($request->hasFile('profile')){
                
    //             $document_name = public_path('/profile_image');

    //             if(imageCompres($_FILES["profile"]["tmp_name"], $_FILES["profile"]["name"], $_FILES["profile"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["profile"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user_profile = array(
    //                 "profile" => $imageName1,
    //             );
                
    //             $data = DB::table('users')->where('id',$request->driver_id)->update($data_user_profile); 
                
    //             $document_name = public_path('/vehicle_image/vehicle_photo');

    //             if(imageCompres($_FILES["profile"]["tmp_name"], $_FILES["profile"]["name"], $_FILES["profile"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["profile"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user_document = array(
    //                 "vehicle_audit_photo2" => $imageName1,
    //                 // "doc_status1" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user_document); 

    //         }

    //         if($request->hasFile('document1')){
                
    //             $document_name = public_path('/vehicle_image/driving_licence');

    //             if(imageCompres($_FILES["document1"]["tmp_name"], $_FILES["document1"]["name"], $_FILES["document1"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document1"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 'driving_licence' => $imageName1,
    //                 // "doc_status1" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 

    //         }
            
    //         if($request->hasFile('document2')){
                
    //             $document_name = public_path('/vehicle_image/driving_licence');
                
    //             if(imageCompres($_FILES["document2"]["tmp_name"], $_FILES["document2"]["name"], $_FILES["document2"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document2"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 'driving_licence2' => $imageName1,
    //                 // "doc_status2" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
                
    //         }
            
    //         if($request->hasFile('document3')){
                
    //             $document_name = public_path('/vehicle_image/id_proof');
                
    //             if(imageCompres($_FILES["document3"]["tmp_name"], $_FILES["document3"]["name"], $_FILES["document3"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document3"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 "id_proof" => $imageName1,
    //                 // "doc_status2" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
                
    //         }
            
    //         if($request->hasFile('document4')){
                
    //             $document_name = public_path('/vehicle_image/id_proof');
                
    //             if(imageCompres($_FILES["document4"]["tmp_name"], $_FILES["document4"]["name"], $_FILES["document4"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document4"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 "id_proof2" => $imageName1,
    //                 // "doc_status3" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
                
    //         }
            
    //         if($request->hasFile('document5')){
                
    //             $document_name = public_path('/vehicle_image/vehicle_photo');
                
    //             if(imageCompres($_FILES["document5"]["tmp_name"], $_FILES["document5"]["name"], $_FILES["document5"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document5"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 "photo1" => $imageName1,
    //                 // "doc_status3" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
                
    //         }
            
    //         if($request->hasFile('document6')){
                
    //             $document_name = public_path('/vehicle_image/vehicle_photo');

    //             if(imageCompres($_FILES["document6"]["tmp_name"], $_FILES["document6"]["name"], $_FILES["document6"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document6"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 "photo2" => $imageName1,
    //                 // "doc_status4" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
                
    //         }
            
    //         if($request->hasFile('document7')){
                
    //             $document_name = public_path('/vehicle_image/vehicle_photo');
                
    //             if(imageCompres($_FILES["document7"]["tmp_name"], $_FILES["document7"]["name"], $_FILES["document7"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document7"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 "certificate1" => $imageName1,
    //                 // "doc_status4" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
                
    //         }
            
    //         if($request->hasFile('document8')){
                
    //             $document_name = public_path('/vehicle_image/vehicle_photo');
                
    //             if(imageCompres($_FILES["document8"]["tmp_name"], $_FILES["document8"]["name"], $_FILES["document8"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document8"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 "certificate2" => $imageName1,
    //                 // "doc_status5" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
                
    //         }
            
    //         if($request->hasFile('document9')){
                
    //             $document_name = public_path('/vehicle_image/vehicle_photo');
                
    //             if(imageCompres($_FILES["document9"]["tmp_name"], $_FILES["document9"]["name"], $_FILES["document9"]["size"], $document_name)){
    //                   $imageName1 = $_FILES["document9"]["name"];
    //              }else{
    //                   $arr['status']=0;
    //                   $arr['message']="Only png is Allowed !";
    //                   $arr['data']=NULL;
    //                   return response()->json($arr,200);
    //              }
            
    //             $data_user1 = array(
    //                 "vehicle_audit_photo1" => $imageName1,
    //                 // "doc_status5" => "0",
    //             );
                
    //             $data = DB::table('driver_details_basic_details')->where('driver_id',$request->driver_id)->update($data_user1); 
                
    //         }
           
    //       $arr['status']=1;
    //       $arr['message']='Update Successfully !';
    //       $arr['data']=NULL;
    //       return response()->json($arr,200);
        
    //     // } 
    //     // catch(\Exception $e)
    //     // {
    //     //   $arr['status']=0;
    //     //   $arr['message']='Sorry!! Something Went Wrong';
    //     //   $arr['data']=$e->getMessage();
    //     // }
    //     return response()->json($arr,200);
        
    // }
    
    
     public function vehicle_details_update(Request $request)   
    {

        $typevalidate=Validator::make($request->all(),[
            'vehicle_number'=>'required',
            'vehicle_manufacture'=>'required',
            'vehicle_model'=>'required',
            'vehicle_type'=>'required',
            'vehicle_reg_year'=>'required',
            'vehicle_owner_name'=>'required',
            'vehicle_fuel_type'=>'required',
        ]);
        if($typevalidate->fails()==true)
        {
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
      try {
          
           $data= $request->all(); 
           $id = Auth::id(); 
           $driver_data = DB::table('driver_details_basic_details')->where('driver_id',$id)->first();
           
           if($driver_data)
           {
               
              $data_user = array(
                'vehicle_number'=>$request->vehicle_number,
                'vehicle_manufacture'   =>$request->vehicle_manufacture,
                'vehicle_model'   =>$request->vehicle_model,
                'vehicle_type'   =>$request->vehicle_type,
                'vehicle_reg_year'=> $request->vehicle_reg_year,
                'vehicle_owner_name' => $request->vehicle_owner_name,
                'vehicle_fuel_type' => $request->vehicle_fuel_type,
                );

            $data = DB::table('driver_details_basic_details')->where('driver_id',$id)->update($data_user); 
           
           
           if($request->hasFile('insurance1')){
            
             if(imageCompres($_FILES["insurance1"]["tmp_name"], $_FILES["insurance1"]["name"], $_FILES["insurance1"]["size"], public_path('/vehicle_image/vehicle_photo'))){
                    $imageName1 = $_FILES["insurance1"]["name"];
             }else{
                  $arr['status']=0;
                  $arr['message']="Only png is Allowed !";
                  $arr['data']=NULL;
                  return response()->json($arr,200);
             }
            
                $data_user1 = array(
                'photo1'=>$imageName1,
                );
                
                $data = DB::table('driver_details_basic_details')->where('driver_id',$id)->update($data_user1); 
           }
            
           
            if($request->hasFile('insurance2')){
            
            if(imageCompres($_FILES["insurance2"]["tmp_name"], $_FILES["insurance2"]["name"], $_FILES["insurance2"]["size"], public_path('/vehicle_image/vehicle_photo'))){
                    $imageName2 = $_FILES["insurance2"]["name"];
             }else{
                  $arr['status']=0;
                  $arr['message']="Only png is Allowed !";
                  $arr['data']=NULL;
                  return response()->json($arr,200);
             }
            
                 $data_user2 = array(
                'photo2'=>$imageName2,
                );
                
                $data = DB::table('driver_details_basic_details')->where('driver_id',$id)->update($data_user2); 
          }
           
           
          if($request->hasFile('certificate1')){

             if(imageCompres($_FILES["certificate1"]["tmp_name"], $_FILES["certificate1"]["name"], $_FILES["certificate1"]["size"], public_path('/vehicle_image/vehicle_photo'))){
                    $imageName3 = $_FILES["certificate1"]["name"];
             }else{
                  $arr['status']=0;
                  $arr['message']="Only png is Allowed !";
                  $arr['data']=NULL;
                  return response()->json($arr,200);
             }
            
                $data_user3 = array(
                'certificate1'=>$imageName3,
                );
                
                $data = DB::table('driver_details_basic_details')->where('driver_id',$id)->update($data_user3); 
          }
           
           
          if($request->hasFile('certificate2')){

            if(imageCompres($_FILES["certificate2"]["tmp_name"], $_FILES["certificate2"]["name"], $_FILES["certificate2"]["size"], public_path('/vehicle_image/vehicle_photo'))){
                    $imageName4 = $_FILES["certificate2"]["name"];
             }else{
                  $arr['status']=0;
                  $arr['message']="Only png is Allowed !";
                  $arr['data']=NULL;
                  return response()->json($arr,200);
             }

                 $data_user4 = array(
                'certificate2'=>$imageName4,
                );
                
                $data = DB::table('driver_details_basic_details')->where('driver_id',$id)->update($data_user4);
            }
          
          
          if($request->hasFile('vehicle_audit_photo1')){

            if(imageCompres($_FILES["vehicle_audit_photo1"]["tmp_name"], $_FILES["vehicle_audit_photo1"]["name"], $_FILES["vehicle_audit_photo1"]["size"], public_path('/vehicle_image/vehicle_photo'))){
                    $imageName5 = $_FILES["vehicle_audit_photo1"]["name"];
             }else{
                  $arr['status']=0;
                  $arr['message']="Only png is Allowed !";
                  $arr['data']=NULL;
                  return response()->json($arr,200);
             }
            
                 $data_user5 = array(
                'vehicle_audit_photo1'=>$imageName5,
                );
                
                $data = DB::table('driver_details_basic_details')->where('driver_id',$id)->update($data_user5);
          }
          
          
          if($request->hasFile('vehicle_audit_photo2')){

             if(imageCompres($_FILES["vehicle_audit_photo2"]["tmp_name"], $_FILES["vehicle_audit_photo2"]["name"], $_FILES["vehicle_audit_photo2"]["size"], public_path('/vehicle_image/vehicle_photo'))){
                    $imageName6 = $_FILES["vehicle_audit_photo2"]["name"];
             }else{
                  $arr['status']=0;
                  $arr['message']="Only png is Allowed !";
                  $arr['data']=NULL;
                  return response()->json($arr,200);
             }

            $data_user6 = array(
                'vehicle_audit_photo2'=>$imageName6,
            );
            $data = DB::table('driver_details_basic_details')->where('driver_id',$id)->update($data_user6); 
          }
                
     

            $d= DB::table('driver_details_basic_details')->where('driver_id',$id)->first();
            $d->photo1= !empty($d->photo1)?url('public/vehicle_image/vehicle_photo/'.$d->photo1):profileImg();
            $d->photo2= !empty($d->photo2)?url('public/vehicle_image/vehicle_photo'.'/'.$d->photo2):profileImg();
            $d->certificate1= !empty($d->certificate1)?url('public/vehicle_image/vehicle_photo'.'/'.$d->certificate1):profileImg();
            $d->certificate2= !empty($d->certificate2)?url('public/vehicle_image/vehicle_photo'.'/'.$d->certificate2):profileImg();
            $d->vehicle_audit_photo1= !empty($d->vehicle_audit_photo1)?url('public/vehicle_image/vehicle_photo'.'/'.$d->vehicle_audit_photo1):profileImg();
            $d->vehicle_audit_photo2= !empty($d->vehicle_audit_photo2)?url('public/vehicle_image/vehicle_photo'.'/'.$d->vehicle_audit_photo2):profileImg();
            
            $arr['status']=1;
            $arr['message']='vehicle details updated successfully!!';
            $arr['data']=$d;

            
           }
           
           else
           {
               $arr['status']=0;
               $arr['message'] = 'No such data available';
               $arr['data'] = NULL;
           }
      } 
      catch(\Exception $e)
      {
          $arr['status']=0;
          $arr['message']='Sorry!! Something Went Wrong';
          $arr['data']=$e->getMessage();
      }
       return response()->json($arr,200);
   }
   /*******************************vehicle registration End***************************************/
   
   /*******************************Driver Profile start***************************************/
    public function driver_profile_register(Request $request)
    {
        \Log::info('driver_profile_register : ');
        \Log::info($request->all());
        
        $driverId=Auth::id();
        $typevalidate=Validator::make($request->all(),[
            // 'vehicle_id'=>'required',
             'name' =>'required',
             'dob' =>'required',
             'age' =>'required',
             'licence_number' =>'required',
            //  'licence_exp_date' =>'required',
             'profile' =>'required',
             'driving_licence' =>'required',
             'driving_licence2' =>'required',
             'id_proof' =>'required',
             'id_proof2' =>'required',
             'state' =>'required',
             'city' =>'required',
            
          ]);
          if($typevalidate->fails()==true)
            {
                $arr['registration_status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
        try {  
            
                // $vehicle_id       = $request->vehicle_id;
                $name              = $request->name;
                $dob              = $request->dob;
                $age             = $request->age;
                $licence_number  = $request->licence_number;
                $licence_exp_date  = $request->licence_exp_date;
                
              $driver_details = DB::table('driver_details_basic_details')->where('driver_id',$driverId)->first();
              $driver_data = DB::table('users')->where('id',$driverId)->first();
              if(empty($driver_details)){
                    $arr['registration_status']=0;
                    $arr['message']= "driver details not found !";
                    $arr['data']=NULL;
                    return response()->json($arr,200);
              }
               
              if (!empty($request->file('profile'))) 
              {
                $file_name1 = date('dmy').rand(1,4).$request->file('profile')->getClientOriginalName();
                $store=$request->file('profile')->move('public/profile_image', $file_name1);
                $img1=$file_name1;
               } else{
                  $img1 = $driver_data->profile; 
               }        
                if (!empty($request->file('driving_licence'))) 
              {
                $file_name2 = date('dmy').rand(1,4).$request->file('driving_licence')->getClientOriginalName();
                $store=$request->file('driving_licence')->move('public/vehicle_image/driving_licence', $file_name2);
                $img2=$file_name2;
               }else{
                  $img2 = $driver_details->driving_licence; 
               }
                 if (!empty($request->file('driving_licence2'))) 
              {
                $file_name3 = date('dmy').rand(1,4).$request->file('driving_licence2')->getClientOriginalName();
                $store=$request->file('driving_licence2')->move('public/vehicle_image/driving_licence', $file_name3);
                $img3=$file_name3;
               }else{
                  $img3= $driver_details->driving_licence2; 
               }
                if (!empty($request->file('id_proof'))) 
              {
                $file_name4 = date('dmy').rand(1,4).$request->file('id_proof')->getClientOriginalName();
                $store=$request->file('id_proof')->move('public/vehicle_image/id_proof', $file_name4);
                $img4=$file_name4;
               }else{
                  $img4= $driver_details->id_proof; 
               }
               if (!empty($request->file('id_proof2'))) 
              {
                $file_name5 = date('dmy').rand(1,4).$request->file('id_proof2')->getClientOriginalName();
                $store=$request->file('id_proof2')->move('public/vehicle_image/id_proof', $file_name5);
                $img5=$file_name5;
               }else{
                  $img5= $driver_details->id_proof2;  
               }
               
                    $data=array(
                        // 'vehicle_type_id' => $vehicle_id,
                        'name'            => $name,
                        'dob'             => $dob,
                        'age'             => $age,
                        'licence_number'  => $licence_number,
                        'licence_exp_date'=> $licence_exp_date,
                        'profile'         => $img1,
                        'state'           => ($request->state)??"",
                        'city'            => ($request->city)??"",
                        );
                    $data_doc =array(
                        'driving_licence' => $img2,
                        'driving_licence2'=> $img3,
                        'id_proof'        => $img4,
                        'id_proof2'       => $img5
                        );    
                    
                    $vehicle_data_details =DB::table('driver_details_basic_details')
                    ->where('driver_id',$driverId)->update($data_doc);
                     
    
                    $vehicle_data =DB::table('users')->where('id',$driverId)->update($data);
                    
                    DB::table('users')->where('id', $driverId)->update(['complete_profile'=>'yes']);
                    
                    $arr['status']=1;
                    $arr['message']="Update Profile Successfully!!";
                    $arr['data']=null;

        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
        }  
           
        return response()->json($arr,200);
    }
   /*******************************Driver Profile End***************************************/
   
   
   /*******************************Driver Bank details start***************************************/
     public function bank_details(Request $request)
    {
        $driverId=Auth::id();
        $typevalidate=Validator::make($request->all(),[
            'bank_name'   =>'required',
            // 'bank_ac_holder_name' =>'required',
            'bank_account'=>'required',
            'branch'      =>'required',
             'code'       =>'required',
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
                $bank_name                 = $request->bank_name;
                $bank_ac_holder_name       = $request->bank_ac_holder_name;
                $bank_account              = $request->bank_account;
                $branch                    = $request->branch;
                $code                      = $request->code;
        
       
                $data=array(
                    'bank_name'                 => $bank_name,
                    'bank_ac_holder_name'       => $bank_ac_holder_name,
                    'bank_account'              => $bank_account,
                    'branch'                    => $branch,
                    'code'                      => $code
                );
                
                
                $bank_data =DB::table('users')->where('id',$driverId)->update($data);
                
                DB::table('users')->where('id', $driverId)->update(['complete_bank_details'=>'yes']);
                 
                $arr['status']=1;
                $arr['message']="Update Bank Details Successfully !";
                $arr['data']=null;
            
            }
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
        }  
           
        return response()->json($arr,200);
    }
    
    
    
    public function bank_account_update(Request $request)   
    {
        
        \Log::info('bank_account_update : ');
        \Log::info($request->all());
        
        $typevalidate=Validator::make($request->all(),[
            'driver_id'=>'required',
            'dob'=>'required',
            'bank_ac_holder_name'=>'required',
            'bank_account'=>'required', 
            'bank_name'   =>'required',
            'branch'   =>'required',
            'code'   =>'required',
        ]);
            
      try {
          
          $id = $request->driver_id;
          
          if($typevalidate->fails()==true)
            {
                $arr['status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
         
         $user_data = DB::table('users')->where('id',$id)->first();
          
          if($user_data)
          {
                 $data_user = array(
                    'dob'    => $request->dob,
                    'bank_ac_holder_name'    => $request->bank_ac_holder_name,
                    'bank_name'   =>$request->bank_name,
                    'bank_account' =>$request->bank_account,
                    'branch'   =>$request->branch,
                    'code'=> $request->code,
                    'bank_status'=> '1',
                 );
                 
                 $data = DB::table('users')->where('id',$id)->update($data_user); 

                 $driver = DB::table('users')->select('id','name','bank_ac_holder_name','bank_name','bank_account','branch','code')->where('id',$id)->first();
             
                 DB::table('users')->where('id', $id)->update(['complete_bank_details'=>'yes']);
             
                $arr['status']=1;
                $arr['message']='Bank details updated successfully!!';
                $arr['data']=$driver;
  
          }
          else
          {
                $arr['status']=0;
                $arr['message']='No such user available';
                $arr['data']=NULL;
          }
      } 
      catch(\Exception $e)
      {
          $arr['status']=0;
          $arr['message']='Sorry!! Something Went Wrong';
          $arr['data']=$e->getMessage();
      }
       return response()->json($arr,200);
   }
   
   
   public function get_bank_details(Request $request) 
    {
        $driver_id = Auth::id(); 
         
       try
        {
            $bank_details = DB::table('users')->select('id','name','bank_ac_holder_name','bank_name','bank_account','branch','code')->where('id',$driver_id)->first();
             
            if($bank_details)
            {
                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']=$bank_details;
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
    
   /*******************************Driver Bank details End***************************************/
    
   /*******************************all booking Ride start***************************************/
   public function all_booking_lists(Request $request)
    {
        try
        {
            $driver_id = Auth::id();
            
            $data=DB::table('bookings')->where('driver_id',$driver_id)->get()->toArray();
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
   /*******************************all booking Ride end***************************************/
   
   
   /*******************************driver chat start***************************************/
   public function chat_with_driver(Request $request){
        $validate=Validator::make($request->all(),[
                    'receiver_id'=>'required'
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
        
            $chats = DB::table('chats')
            ->leftjoin('users as sender', 'chats.sender_id', '=', 'sender.id')
            ->leftjoin('users as receiver', 'chats.receiver_id', '=', 'receiver.id')
            ->select('chats.*', 'sender.name as sender_name', 'receiver.name as receiver_name')
            ->where(function ($query) use ($senderId, $receiverId) {
                $query->where('sender_id', $senderId)
                    ->where('receiver_id', $receiverId);
            })
            ->orWhere(function ($query) use ($senderId, $receiverId) {
                $query->where('sender_id', $receiverId)
                    ->where('receiver_id', $senderId);
            })
            ->get(); 

            // Organize the chat messages into left and right sections
            $organizedChats = [];
    
            foreach ($chats as $chat) {
                if ($chat->sender_id == $senderId) {
                    $organizedChats['right'][] = $chat;
                } else {
                    $organizedChats['left'][] = $chat;
                }
            }
            
            if($chats){
                
                $arr['status']=1;
                $arr['message']="driver chat list";
                $arr['data']=$chats;
            }
            else{
                $arr['status']=0;
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
   /*******************************driver chat end***************************************/
   
   /*******************************Reject booking start***************************************/
        
   /*******************************Reject booking End***************************************/
   
   /*******************************Accepted booking start***********************************/
        
   /*******************************Accepted booking End***************************************/
   
   /*******************************Driver Booking Cancel***************************************/
//   public function driver_cancel_ride(Request $request)
//     {
//         $driver_id = Auth::id();
//         $typevalidate=Validator::make($request->all(),[ 
//                 'booking_id'=>'required',
//                 'reason'=>'required',
//                 'user_id'=>'required',
//                 'driver_id'=>'required',
//             ]);
//         try
//         {
            
//         $reason_type = $request->reason_type;
        
//         $checkCancelRide =  DB::table('cancel_resions')->where('booking_id',$request->booking_id)->first(); 
//         if(!$checkCancelRide){
            
//             $today_date=Carbon::now(); 
//             $cancelReason = DB::table('cancel_resions')->insert(['reason_type'=>$reason_type,'booking_id'=>$request->booking_id,'user_id'=>$request->user_id,
//             'driver_id'=>$driver_id,'reason_message'=>$request->reason]);
            
//             $can_booking = DB::table('bookings')->where('booking_id',$request->booking_id)->update(['booking_status'=>3,'updated_at'=>$today_date]); 
//             $bookingid = $request->booking_id;
             
            
//             if(!empty($cancelReason)){
                
//                 $arr['status']=1;
//                 $arr['message']='Your booking with order id '.$request->booking_id.' has been Cancelled Successfully';
//                 $arr['bookingId'] =$bookingid;
//                 return response()->json($arr,200);
//             }else{
//                 $arr['status']=0;
//                 $arr['message']='Cancel un successfully..';
//                 $arr['OTP']=null;
//                 return response()->json($arr,200); 
//             }
//         } else {
            
//                 $arr['status']=0;
//                 $arr['message']='This ride already cancelled..';
//                 $arr['bookingId'] =null;
//                 return response()->json($arr,200);
//         }    
            
//             }
//             catch(\Exception $e){
//                 $arr['status']=0;
//                 $arr['message']=$e->getMessage();
//                 $arr['data']=NULL;
//             }
//         return response()->json($arr,200);
//     }



    public function driver_cancel_ride(Request $request){
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
                if($data->booking_status == "8"){
                     $arr['status']=0;
                     $arr['message']="User already Cancel this ride !";
                     $arr['data']=NULL;
                     return response()->json($arr,200);
                }
                DB::table('bookings')->where('booking_id',$request->booking_id)->update(['booking_status'=>6, 'booking_type'=>1, 'cancel_resion_id'=>$reason_id, 'cancel_reason_text'=>$reason_text]);
                DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)->delete();
                
                $title = "Ride Cancelled by driver !";
                $message = "Your Booking Id ".$request->booking_id." has been cancelled by driver !";
                send_firebase_notification($title, $message, $data->user_id);
                
                send_websocket_notification($data->user_id, $title, $message, "driver_cancel_ride");
                
                date_default_timezone_set('Africa/Johannesburg');
                DB::table('booking_activity_log')
                    ->where('booking_id', $request->booking_id)
                    ->update([
                        'cancel_type'     => "Driver",
                        'cancel_datetime' => date('d-m-Y H:i:s'),
                        'cancel_user_id'  => $user_id
                    ]);
                    
                $cancel_time_duration = get_option_data("driver_cancel_time_duration");
                $cancel_charges       = get_option_data("driver_cancel_charge");
            
                $givenTime   = strtotime($data->accepted_date_time);
                $currentTime = time();
            
                if ($currentTime > ($givenTime + ($cancel_time_duration * 60))) {
                    if ($cancel_charges > 0) {
                        DB::table('my_wallet')->insert([
                            "user_id"          => $data->driver_id,
                            "amount"           => $cancel_charges,
                            "transaction_type" => 2,
                            "transaction_date" => date('Y-m-d'),
                            "booking_id"       => $data->booking_id
                        ]);
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
    
    
    public function driver_ride_start_verfiy_otp(Request $request)
    {
         $driver_id = Auth::id();
         $typevalidate=Validator::make($request->all(),[   
            'booking_id'=>'required',
            'otp'=>'required'
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
            
            $bookingid = $request->booking_id;
            $booking =  DB::table('bookings')->where('booking_id',$bookingid)->first();
            if($booking)
            { 
            $otp = $request->otp;
            $chk_otp =  DB::table('bookings')->where('booking_id',$bookingid)->where('ride_start_otp',$otp)->first();
            if(!empty($chk_otp))
            {
                $date = date('Y-m-d H:i');
                DB::table('bookings')->where('booking_id',$bookingid)->update(['booking_status'=>'4', 'picup_datetime'=>$date]);
                $d=DB::table('bookings')->where('booking_id',$bookingid)->first();
                
                $title = "Ride Otp verification";
                $message = "Your Booking Id ".$d->booking_id." Ride Otp verification has been successfull !";
                send_firebase_notification($title, $message, $d->user_id);
                
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
            else{
                 $arr['status']=0;
                $arr['message']='No such booking id available...';
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
   /*******************************recoding_list start***************************************/
    
    public function audio_recording_store(Request $request) 
    {
        $typevalidate=Validator::make($request->all(),[
                'booking_id'=>'required',
                'user_type'=>'required',
                'recording' => 'required|mimes:mp3,mp4,wav', 
            ]);
                    
        try {
            
            if($typevalidate->fails()){ 
                $arr['status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
            return response()->json($arr,200); 
            } 
    
            if ($request->hasFile('recording')) {
                // $audio = $request->file('recording');
                // $audioName = time() . '.' .'audio'.'.'. $audio->getClientOriginalExtension();
                // $audio->move(public_path('/recording/'), $audioName);
                
                 if(imageCompres($_FILES["recording"]["tmp_name"], $_FILES["recording"]["name"], $_FILES["recording"]["size"], public_path('/recording/'))){
                    $audioName = $_FILES["recording"]["name"];
                 }else{
                      $arr['status']=0;
                      $arr['message']="Only png is Allowed !";
                      $arr['data']=NULL;
                      return response()->json($arr,200);
                 }
                
                $recording_data = DB::table('recording')->where('booking_id', $request->booking_id)->where('user_type', $request->user_type)->first();
                if($recording_data){
                    $data['recording'] = $audioName;
                    DB::table('recording')->where('id', $recording_data->id)->update($data);
                    $recording = $recording_data->id;
                }else{
                    $data['booking_id'] = $request->booking_id;
                    $data['recording'] = $audioName;
                    $data['user_type'] = $request->user_type;
                    $data['status']  = 1;
                    $recording = DB::table('recording')->insertGetId($data);
                }
                
                if(!empty($recording))
                {
                    $recording_data = DB::table('recording')->where('id', $recording)->first(); 
                    
                    $arr['status']=1;
                    $arr['message']='Audio recording uploaded successfully..';
                    $arr['data']=$recording_data; 
                }
                else
                {
                    $arr['status']=1;
                    $arr['message']="can't upload the given audio...";
                    $arr['data']=null;
                }
        
               
            }
        }
      catch(\Exception $e){
                    $arr['status']=0;
                    $arr['message']=$e->getMessage();
                    $arr['data']=NULL;
        }
        return response()->json($arr,200);
    
    }
   
    public function recording_list(Request $request) 
    {
             $typevalidate=Validator::make($request->all(),[
                                    'booking_id'=>'required',
                                    ]);
            try
            {
                if($typevalidate->fails())
                {
                    $arr['status']=0;
                    $arr['message']=$validate->errors()->first();
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                    
                    $user_id = Auth::id();
                    $recordings = DB::table('recording')
                                      ->select('*'
                                      ,DB::raw('CONCAT("' . url('public/recording') . '","/",recording)  as recording'))
                                      ->where('booking_id',$request->booking_id)
                                      ->get();
                                      
    
                if(!empty($recordings)){
                        
                    $arr['status']=1;
                    $arr['message']='Audio recording list';
                    $arr['data']=$recordings;
                   
                    return response()->json($arr,200);
                }else{
                    $arr['status']=0;
                    $arr['message']='No recording available on this booking id';
                    $arr['data']=[];
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
   /*******************************recoding_list End***************************************/
   
   
   /*******************************video list start***************************************/
    public function video_list(Request $request){
          
             $typevalidate=Validator::make($request->all(),[
                        'booking_id'=>'required',
                        ]);
            try
            {
                if($typevalidate->fails())
                {
                    $arr['status']=0;
                    $arr['message']=$validate->errors()->first();
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }
                    
                $user_id = Auth::id();
                $video_list = DB::table('video_book')
                                      ->select('*'
                                      ,DB::raw('CONCAT("' . url('public/video') . '","/",video)  as video'))
                                      ->where('booking_id',$request->booking_id)
                                      ->get();
                                      
    
                if(!empty($video_list)){
                        
                    $arr['status']=1;
                    $arr['message']='Video recording list.';
                    $arr['data']=$video_list;
                   
                    return response()->json($arr,200);
                }else{
                    $arr['status']=0;
                    $arr['message']='No video recording available on this booking id';
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
       
      
    public function video_recording_store(Request $request)  
    {
        $typevalidate=Validator::make($request->all(),[ 
            'booking_id'=>'required',
            'user_type'=>'required',
            'video' => 'required|mimes:mp4,mov,avi,mkv',
        ]);
                     
        try {
            
            if($typevalidate->fails()){ 
                $arr['status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
            return response()->json($arr,200); 
            } 
    
        if ($request->hasFile('video')) {
            // $video = $request->file('video');
            // $videoName = time().'.'.'video'.'.'.$video->getClientOriginalExtension();
            // $video->move(public_path('/video/'), $videoName);
            
             if(imageCompres($_FILES["video"]["tmp_name"], $_FILES["video"]["name"], $_FILES["video"]["size"], public_path('/video/'))){
                $videoName = $_FILES["video"]["name"];
             }else{
                  $arr['status']=0;
                  $arr['message']="Only png is Allowed !";
                  $arr['data']=NULL;
                  return response()->json($arr,200);
             }
            
            $recording_data = DB::table('video_book')->where('booking_id', $request->booking_id)->where('user_type', $request->user_type)->first();
                if($recording_data){
                    $data['video'] = $videoName;
                    DB::table('video_book')->where('id', $recording_data->id)->update($data);
                    $recording = $recording_data->id;
                }else{
                    $data['booking_id'] = $request->booking_id;
                    $data['video'] = $videoName;
                    $data['user_type'] = $request->user_type;
                    $data['status']  = 1;
                    $recording = DB::table('video_book')->insertGetId($data);
                }
            
            // $data['booking_id'] = $request->booking_id;
            // $data['video'] = $videoName;
            // $data['status']  = 1;
            
            // $video_recording = DB::table('video_book')->insert($data);
            
            if(!empty($recording))
            {
                $video_data = DB::table('video_book')->where('id', $recording)->first(); 
                
                $arr['status']=1;
                $arr['message']='Video recording uploaded successfully..';
                $arr['data']=$video_data; 
            }
            else
            {
                $arr['status']=1;
                $arr['message']="can't upload the given video...";
                $arr['data']=null;
            }
    
           
        }
        }
           catch(\Exception $e){ 
                        $arr['status']=0;
                        $arr['message']=$e->getMessage();
                        $arr['data']=NULL;
            }
            return response()->json($arr,200);
    
        return response()->json(['message' => 'Failed to upload audio recording'], 400);
    }
   /*******************************video_list End***************************************/
   //Driver request booking id
    public function driver_request_booking(Request $request)
    {
        $driverid =  Auth::id();
       
        $typevalidate=Validator::make($request->all(),[
                                                        
                                                        'booking_id'=>'required',
                                                        'status' =>'required',
                                                      ]);
        try {  
              
                 $rideStart_otp = rand(1231,7879);    
                if($typevalidate->fails()==true)
                { 
                    $arr['registration_status']=0;
                    $arr['message']=$typevalidate->errors()->first();
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }else{
                    
                    $bookingdetails=DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)
                    ->where('driver_id',$driverid)->where('booking_status',1)->first();
                         if($bookingdetails){
                          $data_booking['user_id']        = $bookingdetails->user_id??''; 
                          $data_booking['driver_id']      = $bookingdetails->driver_id??''; 
                          $data_booking['vechicle_id']    = $bookingdetails->vechicle_id??''; 
                          $data_booking['booking_id']     = $bookingdetails->booking_id??''; 
                          $data_booking['invoice_number'] = $bookingdetails->invoice_number??''; 
                          $data_booking['picup_location'] = $bookingdetails->picup_location??''; 
                          $data_booking['picup_lat']      = $bookingdetails->picup_lat??''; 
                          $data_booking['picup_long']     = $bookingdetails->picup_long??''; 
                          $data_booking['drop_location']  = $bookingdetails->drop_location??''; 
                          $data_booking['drop_lat']       = $bookingdetails->drop_lat??''; 
                          $data_booking['drop_long']      = $bookingdetails->drop_long??''; 
                          $data_booking['distance']       = $bookingdetails->distance??''; 
                          $data_booking['fare']           = $bookingdetails->fare??''; 
                          $data_booking['booking_status'] = $request->status??'';  
                          $data_booking['ride_start_otp'] = $rideStart_otp; 
                          
                          $checkbookings = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
                           $data_inst = DB::table('bookings')->insert($data_booking);
                           $update = DB::table('user_schedule_booking')->where('driver_id',$bookingdetails->driver_id)
                           ->where('booking_id',$bookingdetails->booking_id)->update(['booking_status'=>2]);
                            
                            $delete = DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)->delete();
               
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=null;
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
                         } 
                         
                
              
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
        }    
           
        return response()->json($arr,200);
    }
    /*******************************complete Ride start***************************************/
    
   /*******************************complete Ride end***************************************/
   
   public function change_drop_location(Request $request){
       $typevalidate=Validator::make($request->all(),[
            'booking_id'           =>'required',
            'latitude'             =>'required',
            'longitude'            =>'required',
            'location'            =>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        
        // try{
            date_default_timezone_set("Asia/Calcutta"); 
            $driver_id = Auth::id();
            $today_date = Carbon::now();
            $drop_latitude = $request->latitude;
            $drop_longitude = $request->longitude;
            
            $booking_info = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
            if(empty($booking_info)){
                $arr['status']=0;
                $arr['message']="Booking not found !";
                $arr['data']=NULL;
                return response()->json($arr,200);
            }

            ///////////////////////////////////////////////
            
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$booking_info->picup_lat.",".$booking_info->picup_long."&destinations=".$drop_latitude.",".$drop_longitude."&departure_time=now&key=".get_option_data('google_map_api_key');

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

            $latlong = $drop_latitude.",".$drop_longitude;
            
            $city_name = $this->get_city_name($latlong);
            
            $get_vehicle_data = DB::table('vehicle_type')->where('id', $booking_info->vechicle_id)->first();
            
            $data_fare = $this->calculace_vehicle_fare($booking_info->vechicle_id, $city_name, $dis);
                
            $price = $get_vehicle_data->price;
            
            if($data_fare != ""){
                $fare = $data_fare; 
            }else{
                $fare = ($total_km*$price);  
            }
            $new_fare = number_format((float)$fare, 2, '.', ''); 
            
            DB::table('bookings')->where('booking_id',$request->booking_id)->update(['fare'=>$new_fare, 'drop_lat'=>$drop_latitude,
            'drop_long'=>$drop_longitude, 'distance'=>$total_km, 'time_duration'=>$time,
            'drop_location'=>$request->location,'updated_at'=>$today_date]);
            
            $data = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
            
            $arr['status']=1;
            $arr['message']="Success !";
            $arr['data']=$data;
            
        // }
        // catch(\Exception $e)
        // {
        //     $arr['status']=0;
        //     $arr['message']="Sorry!! Something Went Wrong";
        //     $arr['data']=NULL;
        // }
        return response()->json($arr,200);
   }
   
    public function get_city_name($latlong){
        
        if($latlong != ""){
            $googleMapsUrl = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latlong . "&language=ar&key=".get_option_data('google_map_api_key');

            $response = file_get_contents($googleMapsUrl);
            $response = json_decode($response, true);
            $results = $response["results"];
            $addressComponents = $results[0]["address_components"];
            $cityName = "";
        
            foreach ($addressComponents as $component) {
                // echo $component;
                $types = $component["types"];
                if (in_array("locality", $types) && in_array("political", $types)) {
                    $cityName = $component["long_name"];
                }
            }
            
            return $cityName;
        }
    }

    public function calculace_vehicle_fare($vehicle_id, $city_name, $distance){
        $total_fare = "";
        if($vehicle_id != "" && $city_name != "" && $distance != ""){
           $get_city_data = DB::table('locations')->where('name',$city_name)->first();
           if($get_city_data){
               $fare_data = DB::table('manage_fare')->where('vehicle_id',$vehicle_id)->where('location_id',$get_city_data->id)->first();
               if($fare_data){
                    $total_fare = ($fare_data->per_km*$distance)+$fare_data->base_fare;
               }
           }
        }
        return $total_fare;
    }
   
   
    /*******************************Scheduled Ride start***************************************/
    public function scheduled_list_ride(Request $request)
    {
        $today_date = Carbon::now(); 
        $dateToday = $today_date->toDateTimeString();
        try
        {
            $driver_id = Auth::id(); 
            
            $result = DB::table('bookings as sb')->select('sb.id','sb.booking_id','sb.user_id','sb.driver_id','sb.vechicle_id','sb.picup_location'
            ,'sb.picup_lat','sb.picup_long','sb.drop_location','sb.drop_lat','sb.drop_long','sb.fare','sb.distance','sb.booking_status','sb.booking_date','sb.booking_time','sb.taxi_type','users.name as user_name')
            ->join('users','users.id','=','sb.user_id')
            ->where('sb.booking_type', 2)->where('sb.driver_id', $driver_id)
            ->get();
            
            if(!empty($result))
            {
                $arr['status']=1;
                $arr['message']="Scheduled Rides List ";
                $arr['data']=$result;
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
   /*******************************Scheduled Ride end***************************************/
   
   
   /*******************************scheduled ride details start***************************************/
   
    public function scheduled_ride_details(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'id'           =>'required',
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
            $id = $request->id;
            $result = DB::table('schedule_booking')->select('id','booking_id','user_id','driver_id','vechicle_id','picup_location'
            ,'picup_lat','picup_long','drop_location','drop_lat','drop_long','fare','distance','booking_status','booking_date','booking_time','taxi_type')
            ->where('driver_id',$driver_id)->where('id',$id)->first();
            if(!empty($result))
            {
                $arr['status']=1;
                $arr['message']="Scheduled Rides Successfully...";
                $arr['data']=$result;
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
   /*******************************scheduled ride details end***************************************/
   
   /*******************driver booking cancel start**********************/
    public function driver_schedule_booking_cancel(Request $request){
      
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
                    
                $get_booking = DB::table('schedule_booking')->where('booking_id',$request->booking_id)->first();
                if(!empty($get_booking)){
                    
                    $data_update = DB::table('schedule_booking')->where('booking_id',$request->booking_id)->update($data);
                    
                    $title = "Ride Cancelled by driver !";
                    $message = "Your Booking Id ".$request->booking_id." has been cancelled by driver !";
                    send_firebase_notification($title, $message, $get_booking->user_id);
                    
                    $arr['status']=1;
                    $arr['message']='Driver Cancel successfully..';
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
    /*******************driver booking cancel end**********************/
    
    //Driver request booking id
    public function driver_schedule_request_booking(Request $request)
    {
        $driverid =  Auth::id();
       
        $typevalidate=Validator::make($request->all(),[
                                                        
                                                        'booking_id'=>'required',
                                                        'status' =>'required',
                                                      ]);
        try {  
              
                 $rideStart_otp = rand(1231,7879);    
                if($typevalidate->fails()==true)
                { 
                    $arr['registration_status']=0;
                    $arr['message']=$typevalidate->errors()->first();
                    $arr['data']=NULL;
                    return response()->json($arr,200);
                }else{
                    
                    $bookingdetails=DB::table('schedule_booking')->where('booking_id',$request->booking_id)
                    ->where('driver_id',$driverid)->where('booking_status',1)->first();
                         if($bookingdetails){
                          $data_booking['user_id']        = $bookingdetails->user_id??''; 
                          $data_booking['driver_id']      = $bookingdetails->driver_id??''; 
                          $data_booking['vechicle_id']    = $bookingdetails->vechicle_id??''; 
                          $data_booking['booking_id']     = $bookingdetails->booking_id??''; 
                          $data_booking['invoice_number'] = $bookingdetails->invoice_number??''; 
                          $data_booking['picup_location'] = $bookingdetails->picup_location??''; 
                          $data_booking['picup_lat']      = $bookingdetails->picup_lat??''; 
                          $data_booking['picup_long']     = $bookingdetails->picup_long??''; 
                          $data_booking['drop_location']  = $bookingdetails->drop_location??''; 
                          $data_booking['drop_lat']       = $bookingdetails->drop_lat??''; 
                          $data_booking['drop_long']      = $bookingdetails->drop_long??''; 
                          $data_booking['distance']       = $bookingdetails->distance??''; 
                          $data_booking['fare']           = $bookingdetails->fare??''; 
                          $data_booking['booking_status'] = $request->status??''; 
                          $data_booking['ride_start_otp'] = $rideStart_otp; 
                          
                          $checkbookings = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
                           $data_inst = DB::table('bookings')->insert($data_booking);
                           $delete = DB::table('schedule_booking')->where('driver_id',$bookingdetails->driver_id)
                           ->where('booking_id',$bookingdetails->booking_id)->update(['booking_status'=>2]);
               
               
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['data']=null;
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='Data not found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
                         } 
                         
                
              
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Something went wrong";
            $arr['data']=NULL;
        }    
           
        return response()->json($arr,200);
    }
    
    /*******************driver recent booking start**********************/

    public function recent_booking(Request $request)
    {  
        \Log::info('recent_booking : ');
        \Log::info($request->all());
        
        try {
            
            $driver_id = Auth::id(); 

            $data=DB::table('bookings as b')
                ->select('b.id', 'b.booking_id','b.is_rental', 'b.rental_km', 'b.rental_hour', 'b.payment_mode', 'b.picup_location', 'b.picup_lat', 'b.picup_long', 'b.drop_location', 'b.drop_lat', 'b.drop_long', 'b.fare', 'b.rating', 'b.review', 'b.distance', 'b.booking_date', 'b.booking_time', 'u.name as user_name', 'u.profile', 'bs.name as booking_status_name', 'u.mobile_no as user_mobile')
                ->leftjoin('booking_status as bs','bs.id','=','b.booking_status')
                ->leftjoin('users as u','u.id','=','b.user_id')
                ->where('b.driver_id', $driver_id)
                ->where('b.booking_status', 5)
                ->orderBy('b.id', 'DESC')
                ->first(); 
            
            if(!empty($data))
            {
                $data->profile = !empty($data->profile)?url('public/profile_image/'.$data->profile):profileImg();
                $data->booking_date = date('d M Y', strtotime($data->booking_date));
                $data->booking_time = date('h:i A', strtotime($data->booking_time));
                $data->payment_mode = ($data->payment_mode == "")?"2":$data->payment_mode;
                
                $arr['status']=1;
                $arr['message']="Successfully...";
                $arr['data']=$data;
            }else{
                $arr['status'] = 0;
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
    
    
    
    
    
    /*******************driver recent booking end**********************/
     /*******************************driver booking Ride start***************************************/
     
    public function generate_pdf(Request $request){
        if(isset($request->id) && $request->id != ""){
            $id = $request->id;
            $get_booking_details = DB::table('bookings')
                ->select('bookings.*','bookings.id as book_id','u.*','d.name as driver_name','d.email as driver_email',
                'd.mobile_no as driver_mobile','vf.name as vehicle_name','states.state_name','cities.name as city_name')
                ->leftJoin('users as u','u.id','=','bookings.user_id')
                ->leftJoin('users as d','d.id','=','bookings.driver_id')
                ->leftJoin('vehicle_type as vf','vf.id','=','bookings.vechicle_id')
                ->leftJoin('states','states.id','=','d.state')
                ->leftJoin('cities','cities.id','=','d.city')
                ->where('bookings.id',$id)
                ->first();
                                        
            $pdf = PDF::loadView('admin.invoice_pdf', compact('get_booking_details'));  
            return $pdf->download("invoice.pdf");
        }
    }
    
    public function download_invoice(Request $request){
        if(isset($request->id) && $request->id != ""){
            $get_booking_details = DB::table('bookings as b')
                ->select('b.booking_id', 'b.user_id', 'b.driver_id', 'b.picup_location'
                , 'b.drop_location', 'b.created_at', 'l.payment_type', 'b.booking_status','b.fare as total_fare', 'b.actual_price', 'b.night_charge', 'b.base_fare', 'b.wating_charge','b.actual_drop_location',
                'l.admin_commision_percentage', 'l.admin_amount', 'l.driver_amount as amount_driver',
                'l.area_partner_percentage', 'l.service_partner_percentage', 'l.service_partner_amount', 'l.area_partner_amount', 'l.tds_percentage','l.gst_percentage', 'b.gst_amount'
                , 'l.tds_amount', 'u.name as user_name', 'd.name as driver_name', 'a.name as area_partner_name', 's.name as sponser_name'
                , 'a.referral_code as areapartner_referal', 's.referral_code as servicepartner_referal', 'b.distance', 'd.email as driver_email'
                , 'd.mobile_no as driver_mobile', 'st.state_name as driver_state_name', 'ct.name as driver_city_name', 'v.name as vehicle_name', 'v.image as vehicle_image', 'b.fare', 'v.base_fare')
                ->leftJoin('list_subscription as l','l.booking_id','=','b.booking_id')
                ->leftJoin('users as u','u.id','=','b.user_id')
                ->leftJoin('users as d','d.id','=','b.driver_id')
                ->leftJoin('users as a','a.id','=','l.area_partner_id')
                ->leftJoin('users as s','s.id','=','l.service_partner_id')
                ->leftJoin('states  as st','st.id','=','d.state')
                ->leftJoin('cities as ct','ct.id','=','d.city')
                ->leftJoin('vehicle_type as v','v.id','=','b.vechicle_id')
                ->where('b.id', $request->id)->first();
                
            $invoice_name = ($get_booking_details->booking_id)."_invoice.pdf";    
 
            // return view('admin.download_invoice', compact('get_booking_details'));    
            $pdf = PDF::loadView('admin.download_invoice', compact('get_booking_details'));  
            return $pdf->download($invoice_name); 
        }
    }
     
         
   public function driver_booking_lists(Request $request) 
    { 
        \Log::info('driver_booking_lists : ');
        \Log::info($request->all());
        
        try 
        {  
            $driver_id = Auth::id();
            $data=DB::table('bookings as b')
                ->select('b.id', 'b.booking_id','b.is_rental', 'b.rental_km', 'b.rental_hour', 'b.picup_location', 'b.picup_lat', 'b.picup_long', 'b.drop_location', 'b.drop_lat', 'b.drop_long', 'b.booking_status', 'b.fare', 'b.rating', 'b.booking_date', 'b.booking_time', 'b.first_stop_location', 'b.first_stop_lat', 'b.first_stop_long', 'b.second_stop_location', 'b.second_stop_lat', 'b.second_stop_long', 'u.name as user_name', 'u.profile', 'bs.name as booking_status_name')
                ->leftjoin('booking_status as bs','bs.id','=','b.booking_status')
                ->leftjoin('users as u','u.id','=','b.user_id')
                ->orderBy('b.id', 'DESC')->where('b.driver_id', $driver_id)->get(); 
            
            if(!empty($data))
            {
                foreach($data as $res){
                    $res->profile = !empty($res->profile)?url('public/profile_image/'.$res->profile):profileImg();
                    $res->booking_date = date('d M Y', strtotime($res->booking_date));
                    $res->booking_time = date('h:i A', strtotime($res->booking_time));
                    $res->invoice_link = url('/api').'/download_invoice?id='.$res->id;
                }
                
                $arr['status']=1;
                $arr['message']="Successfully...";
                $arr['data']=$data;
            }
            else
            {
                $arr['status']=0;
                $arr['message']="Data not Found";
                $arr['data']=[];
            }
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Data not Found";
            $arr['data']=null;
        }
        return response()->json($arr,200);
    }
   /*******************************driver booking Ride end***************************************/
   
   public function upcoming_booking(Request $request){
       try 
        { 
            
            // \Log::info('upcoming_booking : ');
            // \Log::info($request->all());
            
            $driver_id = Auth::id();
            // dd($driver_id);
            $get_bookings = DB::table('user_schedule_booking as up')
                            ->select('up.*', 'u.profile', 'u.name as user_name', 'u.mobile_no as user_mobile', 'ud.mobile_no as driver_mobile')
                            ->leftjoin('users as u','u.id','=','up.user_id')
                            ->leftjoin('users as ud','ud.id','=','up.driver_id')
                            ->where('up.driver_id',$driver_id)
                            ->where('up.booking_status','1')
                            ->whereDate('up.created_at', Carbon::today())
                            ->orderBy('up.id', 'ASC')
                            ->first();  
                            
            if(!empty($get_bookings))
            {
                
                $get_bookings->profile = !empty($get_bookings->profile)?url('public/profile_image/'.$get_bookings->profile):profileImg();
                $get_bookings->payment_mode = ($get_bookings->payment_mode)??"Cash";
                $get_bookings->schedule_later_booking = date('d M', strtotime($get_bookings->created_at))." | ".date('h:i A', strtotime($get_bookings->created_at));
             
                $arr['status']=1;
                $arr['message']="Successfully...";
                $arr['data']=$get_bookings;
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
            $arr['data']=null;
        }
        return response()->json($arr,200);
   }
   
   
    // public function upcoming_booking(Request $request)
    // {
    //         $driv_id = Auth::id();
    //         $typevalidate=Validator::make($request->all(),[
    //           'latitude'=>'required',
    //             'longtitude'=>'required',
    //         ]);

    //         if($typevalidate->fails())
    //         {
    //             $arr['status']=0;
    //             $arr['message']=$typevalidate->errors()->first();
    //             $arr['data']=NULL;
    //             return response()->json($arr,200);
    //         }
    //         $lat=$request->latitude;
    //         $long=$request->longtitude;

    //         $get_bookings = DB::table('bookings')->where('driver_id',$driv_id)->where('booking_status',[0,1])->get()->toArray(); 
    //         // $get_bookings = DB::table('user_schedule_booking')->where('driver_id',$driv_id)->get()->toArray(); 
            
    //         // echo "<pre>";
    //         // print_r($get_bookings);
    //         // die("++++++");
            
    //         $pikup_lat = $pikip_long = ""; 
    //         if(!empty($get_bookings)){   
    //             foreach($get_bookings as $val){
    //             $reject_driver = DB::table('driver_rejected_requests')->select('driver_id')->where('booking_id',$val->booking_id)->get()->toArray();
                
    //             $pikup_lat = $val->picup_lat;
    //             $pikip_long = $val->picup_long;
                
    //                 if(!empty($reject_driver)){
                       
    //                   foreach($reject_driver as $key=>$val){
                         
    //                       $driver_id[$key]= $val->driver_id;
                          
    //                   }
                      
    //                   $drivers_id = $driver_id;
                    
    //                 }
                
    //             }
                
                    
    //             if($val)
    //             {
    //                 $user_id = $val->user_id;
    //                 $driver_id = $val->driver_id;
                    
    //                 $val_book = DB::table('bookings')->select('bookings.*','users.id','users.name','users.email','users.mobile_no','users.profile')
    //                 ->leftjoin('users', 'users.id', '=', 'bookings.user_id')
    //                 ->where('bookings.driver_id',$driver_id)
    //                 ->where('users.id',$user_id)
    //                 ->where('booking_status',[0,1]) 
    //                 ->get(); 
                  
    //                 // echo "<pre>";
    //                 // print_r($val_book);
    //                 // die("++++++");
                     
    //                 $val_book[0]->profile= !empty($val_book[0]->profile)?url('public/profile_image/'.$val_book[0]->profile):profileImg();   
                    
                
    //                 $arr['status']=1;
    //                 $arr['message']='success!!';
    //                 $arr['data']=$val_book;
    //                 return response()->json($arr,200);  
    //             }
          
    //         } 
    //         else{
    //           $arr['status']=0;
    //             $arr['message']='Data not found!!';
    //             $arr['data']=null;
    //             return response()->json($arr,200); 
    //         }
    
    //         $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$pikup_lat.",".$pikip_long."&destinations=".$lat.",".$long."&departure_time=now&key=AIzaSyDbxkz20fFy5zjI0_a-1YVF4acoi12Z6wg";
    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, $url);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //         curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //         $response = curl_exec($ch);
    //         curl_close($ch);
    //         $response_a = json_decode($response, true);
    //         $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    //         $time = $response_a['rows'][0]['elements'][0]['duration']['text'];    
    //         $distance = $response_a['rows'][0]['elements'][0]['distance']['value'];
    //         $dis = round($distance/1000, 2);
         
      
    //         DB::table('users')->where('id',$driv_id)->update(['latitude'=>$lat,'longtitude'=>$long]);
    //         $driver_lat_long = DB::table('users')->where('id',$driv_id)->first();
           
    //         $driverInfo = DB::table('driver_details_basic_details')
    //                     ->join('users','users.id','=','driver_details_basic_details.driver_id')        
    //                     ->where('driver_details_basic_details.driver_id',$driv_id)
    //                     ->where('users.driveractive_status',1)
    //                     ->first();
                      
    //     $upcoming_booking = "";  
    //     if(!empty($driverInfo->vehicle_ids) || !empty($driverInfo->vehicle_id)){
    //         $get_v_idd = explode(",",$driverInfo->vehicle_ids);
    //         $vid = explode(",",$driverInfo->vehicle_id);
           
    //         $get_v_id = array_merge($get_v_idd,$vid);
           
    //         $radius_distance = DB::table('radius')->where('id',1)->first();
    //         $radius_range =$radius_distance->radius_range??0;
                 
    //         $upcoming_booking = DB::table('bookings')
    //                          ->select('bookings.*','bookings.distance as total_dis','users.email','users.mobile_no','users.profile','users.name',DB::raw("'$dis' as pickup_km"),DB::raw('CONCAT("' . url('storage/app/profile_photo') . '","/",profile)  as profile')
    //                          ,DB::raw("round(6371 * acos(cos(radians(" . $driver_lat_long->latitude . ")) 
    //                             * cos(radians(bookings.picup_lat)) 
    //                             * cos(radians(bookings.picup_long) - radians(" . $driver_lat_long->longtitude . ")) 
    //                             + sin(radians(" .$driver_lat_long->latitude. ")) 
    //                             * sin(radians(bookings.picup_lat)))) AS distance"))
    //                              ->having("distance", "<", $radius_range)
    //                         ->join('users', 'users.id', '=', 'bookings.user_id')
    //                         ->where('bookings.booking_status',1)
    //                         ->whereIn('bookings.vechicle_id',$get_v_id)
    //                         ->first();  
                        
    //         if(!empty($upcoming_booking)){          
                            
    //           if($upcoming_booking->noti_stop == 1){              
    //               $title = "New Booking..";
    //               $message = "New Booking..";
                  
    //               $dvice_token = DB::table('users')->where('id',$userid)->first();
                  
    //               $firebaseToken[] =  $dvice_token->device_token;
      
    //                 $SERVER_API_KEY = 'Gsdsds:APA91bGeYv-MdLOW-1QtP8YG32hNaslqKSssBewds-u9Tc9TwHFFX8NuMQDJy7MCNojAmNRHG5Mcd9VcuiFSahU7yQNaWE-DlZo1ISrN85DkDQxeAzc3QXVNO-G2Sh_rAbJyQcqgdI0s';
              
    //                 $data = [
    //                     "registration_ids" => $firebaseToken,
    //                     "data" => [
    //                         "title" => $title,
    //                         "body" => $message,
    //                         "type" => 'upcomming',
    //                     ]
    //                 ];
    //                 $dataString = json_encode($data);
    //                 $headers = [
    //                     'Authorization: key=' . $SERVER_API_KEY,
    //                     'Content-Type: application/json',
    //                 ];
                
    //                 $url = 'https://fcm.googleapis.com/fcm/send';
    //                  $ch = curl_init();
                  
    //                 curl_setopt($ch, CURLOPT_URL, $url);
    //                 curl_setopt($ch, CURLOPT_POST, true);
    //                 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //                 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //                 curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    //                 // Disabling SSL Certificate support temporarly
    //                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
    //                 curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    //                 // Execute post
    //                 $result = curl_exec($ch);
    //                  curl_close($ch);
                
    //             $datanoti['noti_stop']  = 2;
                
    //             $notiupdate = DB::table('bookings')->where('booking_id',$upcoming_booking->booking_id)->update($datanoti);
                     
                     
    //           }
    //         }
    
    //     }
        
    //     if(!empty($upcoming_booking)){
    //         $dk=DB::table('bookings')->where('booking_id',$upcoming_booking->booking_id)->first();
    //         $val->distance=$dk->distance;
           
    //         $arr['status']=1;
    //         $arr['message']='successfully..';
    //         $arr['data']=$upcoming_booking;
           
    //         return response()->json($arr,200);
      
    //     }
    //     else{
    //         $arr['status']=0;
    //         $arr['message']='Data not found a!!';
    //         $arr['data']=null;
    //         return response()->json($arr,200); 
    //      }
    // }
    
    
    
    
    // rating get for driver
     public function driver_rating(Request $request){  
                  
        try
        {
            $driver_id = Auth::id();
           
            $rating = 0;
            $totalRating = DB::table('ride_rating as t1')->select('t1.rating','t1.message','t2.name as user_name', 'profile', 't2.created_at')
            ->join('users as t2','t2.id','=','t1.user_id')->where('t1.driver_id',$driver_id)->get();
             
            if(count($totalRating)>0){
              $totalRatingCount = DB::table('ride_rating')->where('driver_id',$driver_id)->count(); 
              $rating = $totalRating->sum('rating')/$totalRatingCount;
            }
            
            $avg_rating = $rating > 0 ? number_format((float)$rating, 1, '.', '') : "0";
            //Avarage Rating End
            $total_rating =$totalRating->count();

            if(!empty($totalRating)){
                
                foreach($totalRating as $res){
                    $res->profile = !empty($res->profile)?url('public/profile_image/'.$res->profile):profileImg();
                    $res->time = date('d-m-Y', strtotime($res->created_at));
                }
                
                $arr['status']=1;
                $arr['message']='successfully..';
                $arr['avg_rating']=$avg_rating;
                $arr['total_rating']=$total_rating;
                 $arr['data']=$totalRating;
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='No data found';
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
    
    
    // driver rating according user id
     public function driver_user_rating(Request $request)
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
            
            $id = $request->id;
            $driver_id = Auth::id();
            // $rating = 0;
            $DriverRating = DB::table('ride_rating as t1')->select('t1.rating','t1.message','t2.name as user_name','t2.profile')
            ->join('users as t2','t2.id','=','t1.user_id')->where('t1.id',$id)->where('t1.driver_id',$driver_id)->first(); 
            
            if(!empty($DriverRating)){
                $DriverRating->profile = $DriverRating->profile= !empty($DriverRating->profile)?url('public/profile_image/'.$DriverRating->profile):profileImg();
                $arr['status']=1;
                $arr['message']='successfully..';
                 $arr['data']=$DriverRating;
                return response()->json($arr,200);
            }else{
                $arr['status']=0;
                $arr['message']='No data found';
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
    
    
    
    // payment request 
    public function withdrawal_amount_request(Request $request){
        $typevalidate=Validator::make($request->all(),[ 
            'amount'=>'required|numeric',
        ]);
        // try
        // {
            if($typevalidate->fails())
            {
                $arr['status']=0;
                $arr['message']=$typevalidate->errors()->first();
                $arr['data']=NULL;
                return response()->json($arr,200);
            }
            
            $withdraw_token = "W".time();
            $driver_id = Auth::id();
            $driver_data = User::select('bank_ac_holder_name','bank_name','bank_account','branch','code')->where('id',$driver_id)->first();
            if(empty($driver_data)){
                $arr['status']=0;
                $arr['message']='Driver Bank Detail Not Found !!';
                $arr['data']=null;
                return response()->json($arr,200); 
            }
            
            $wallet_amount = get_wallet_balance($driver_id);
            
            if($wallet_amount >= $request->amount){
                $remaning_amount = ($wallet_amount - $request->amount);

                DB::table('my_wallet')->insert(['user_id'=>$driver_id, 'transaction_type'=>"2", 'transaction_date'=>date('Y-m-d'), 'amount'=>$request->amount, 'withdraw_token' => $withdraw_token, 'payment_type' => 'withdraw']);
                
                $data['driver_id'] = Auth::id();
                $data['withdraw_token'] = $withdraw_token;
                $data['amount'] = $request->amount;
                $data['remaning_wallet_amount'] = number_format((float)$remaning_amount, 2, '.', '');
                $data['payment_status'] = "1";
                $data['bank_name'] = $driver_data->bank_name;
                $data['bank_branch'] = $driver_data->branch;
                $data['account_number'] = $driver_data->bank_account;
                $data['reaccount_number'] = $driver_data->bank_account;
                $data['account_number_name'] = ($driver_data->bank_ac_holder_name)??"";
                
                $data_inst = DB::table('withdrawal_request')->insertGetId($data); 
                
                // $message = "Confirmation of Withdrawal Request (". $withdraw_token .") Received";
                // DB::table('platform_notification')->insert(['user_id'=>$driver_id, 'title'=>"Withdraw Request", 'message'=>$message]);
                
                // Deduct Driver wallet Amount
    
                if(!empty($data_inst)){
                    
                    $arr['status']=1;
                    $arr['message']='Request send successfully..';
                    $arr['data']=$data_inst;
                }else{
                    $arr['status']=0;
                    $arr['message']='Data not found !!';
                    $arr['data']=null;
                }
            }else{
                $arr['status']=0;
                $arr['message']='Wallet Amount is not sufficient !!';
                $arr['data']=null;
            }
    // }    
    // catch(\Exception $e){
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

    public function my_payment(Request $request)
    {
        try {
            $driver_id = Auth::id(); 

            $withdraw_history = DB::table('withdrawal_request as w')
                            ->select('w.id', 'w.driver_id', 'w.amount', 'w.withdraw_token', 'w.created_at')
                            ->where('driver_id', $driver_id)
                            ->get()->toArray(); 
               
            if($withdraw_history){
                foreach($withdraw_history as $res){
                    $res->created_at = date('d M Y', strtotime($res->created_at));
                }
                $withdraw = $withdraw_history;
            } else{
                $withdraw = [];
            } 
            
            $total_income = DB::table('list_subscription  as l')
                         ->leftjoin('bookings as b','b.booking_id','=','l.booking_id')
                         ->where('b.driver_id',$driver_id)
                         ->sum('driver_amount'); 
                     
            $today_income = DB::table('list_subscription  as l')
                         ->leftjoin('bookings as b','b.booking_id','=','l.booking_id')
                         ->where('b.driver_id',$driver_id)
                         ->whereDate('b.created_at', date('Y-m-d'))
                         ->sum('driver_amount');          

            $arr['status'] = 1;
            $arr['message'] = 'Success.';
            $arr['today_income'] = number_format((float)$today_income, 2, '.', '');
            $arr['total_income'] = number_format((float)$total_income, 2, '.', '');
            $arr['wallet_amount'] = get_wallet_balance($driver_id);
            $arr['withdraw'] = $withdraw;
            
        } catch (\Exception $e) {
            $arr['status'] = 0;
            $arr['message'] = "Sorry!! Something Went Wrong";
            $arr['data'] = null;
        }
        return response()->json($arr, 200);
    } 



  public function today_income(Request $request) 
  {
        try 
        {  
            $driver_id = Auth::id();

            $today_income = DB::table('my_wallet as w')
                        ->select(DB::raw('sum(case when w.transaction_type = "1" then w.amount end) total_amount '), DB::raw('SUM(b.admin_amount) as total_tax'))
                        ->leftJoin('list_subscription as b','b.booking_id','=','w.booking_id')
                        ->where('transaction_date', date('Y-m-d'))
                        ->where('user_id',$driver_id)
                        ->first();           
                     
            if(!empty($today_income)){
                $arr['status']=1;
                $arr['message']='successfully';
                $arr['today_income'] =($today_income->total_amount)?number_format($today_income->total_amount, 2 ):0;
                $arr['total_tax'] =($today_income->total_tax)?number_format($today_income->total_tax, 2 ):0;
                $arr['payment_due'] = "0";
            }else{
                $arr['status']=1;
                $arr['message']='successfully';
                $arr['today_income'] = "0"; 
                $arr['total_tax'] ="0"; 
                $arr['payment_due'] = "0";
            }
        }
        catch(\Exception $e){
            $arr['status']=0;
            $arr['message']="Sorry!! Something Went Wrong";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);    

    } 
    
    public function payment_history(Request $request) 
    {
        try 
        {  
            $driver_id = Auth::id();
            $payment_history = DB::table('list_subscription  as l')
                     ->select('l.id', 'b.booking_id', 'l.driver_amount as amount', 'l.status', 'l.created_at', 'u.name as user_name')
                     ->leftjoin('bookings as b','b.booking_id','=','l.booking_id')
                     ->leftjoin('users as u','u.id','=','b.driver_id')
                     ->where('b.driver_id',$driver_id)
                     ->get()->toArray(); 
                     
            if(!empty($payment_history)){
                
                foreach($payment_history as $res){
                    $res->amount = number_format((float)$res->amount, 2, '.', '');
                    $res->payment_date = date('d M Y', strtotime($res->created_at));
                    $res->payment_time = date('h:i A', strtotime($res->created_at));
                    if($res->status == "1"){
                        $msg = "Received (". $res->booking_id .")";
                    }else{
                        $msg = "Deduct (". $res->booking_id .")";
                    }
                    $res->status = $msg;
                }
                
                $arr['status']=1;
                $arr['message']='successfully';
                $arr['data'] = $payment_history;
            }else{
                $arr['status']=0;
                $arr['message']='No Data Found !';
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
    
    public function driver_payment_history(Request $request) 
    {
        try 
        {  
            $driver_id = Auth::id();

            $payment_history = DB::table('bookings as b')
                     ->select('t.id', 't.amount', 'u.name as user_name', 't.status', 't.created_at')
                     ->join('list_subscription as t','t.booking_id','=','b.booking_id')
                     ->join('users as u','u.id','=','b.user_id')
                     ->where('b.booking_status', '5')
                     ->where('b.driver_id',$driver_id)
                     ->get();
                     
            if(!empty($payment_history)){
                
                foreach($payment_history as $res){
                    $res->amount = number_format((float)$res->amount, 2, '.', '');
                    $res->payment_date = date('d M Y', strtotime($res->created_at));
                    $res->payment_time = date('h:i A', strtotime($res->created_at));
                    $res->status = ($res->status == "1")?"Recived":"Payment Due";
                }
                
                $arr['status']=1;
                $arr['message']='successfully';
                $arr['data'] = $payment_history;
            }else{
                $arr['status']=0;
                $arr['message']='No Data Found !';
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
    
    
    
    public function onoff(Request $request)
    {
        $driverId=Auth::id();
        $typevalidate=Validator::make($request->all(),[ 
                        'status'=>'required',
                        'latitude'=>'required',
                        'longtitude'=>'required',
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
            
            
            $status=$request->status;
            $lat=$request->latitude;
            $long=$request->longtitude;
            
            $abc = DB::table('users')->where('id',$driverId)->update(['driveractive_status'=>$status,'latitude'=>$lat,'longtitude'=>$long]);
            $get_status = DB::table('users')->where('id',$driverId)->first();
            if($get_status->driveractive_status =="1")
            {
                $arr['status']=1;
                $arr['message']="You Are Online Now";
                $arr['data']=$get_status->driveractive_status;
            }
            
            if($get_status->driveractive_status =="2")
            {
                $arr['status']=1;
                $arr['message']="You Are Offline Now";
                $arr['data']=$get_status->driveractive_status;
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
    
    public function change_booking_status(Request $request){
        $driverId=Auth::id();
        $typevalidate=Validator::make($request->all(),[ 
            'booking_id'=>'required',
            'status'=>'required',
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
                DB::table('bookings')->where('id',$get_data->id)->update(['booking_status'=>$request->status]);
                $get_data = DB::table('bookings')->where('booking_id',$request->booking_id)->first();
                
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
    

    
    public function driver_online_offline(Request $request){
        $driverId=Auth::id();
        $typevalidate=Validator::make($request->all(),[ 
            'status'=>'required',
            'latitude'=>'required',
            'longitude'=>'required',
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
            $get_data = DB::table('users')->where('id',$driverId)->update(['is_live'=>$request->status, 'latitude'=>$request->latitude, 'longtitude'=>$request->longitude ]);
            $get_data = DB::table('users')->where('id',$driverId)->first();
                $arr['status']=1;
                $arr['message']="update successfully";
                $arr['data']=$get_data;

        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
        
    }
    
    
    
    public function vehicle_fuel_types(Request $request){
        try 
        {
            $get_data = DB::table('vehicle_varients')->select('id', 'varient_name')->get()->toArray();
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
            $arr['message']="data not found..";
            $arr['data']=NULL;
        }
        return response()->json($arr,200);
    }
    
    public function driver_online_offline_status(Request $request){
        try 
        {
            $driverId=Auth::id();
            $get_data = DB::table('users')->where('id', $driverId)->first();
            if($get_data)
            {
                $arr['status']=1;
                $arr['message']="successfully";
                $arr['is_online']=$get_data->is_live;
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
    
    public function driver_reject_ride(Request $request){
        
        \Log::info('driver_reject_ride : ');
        \Log::info($request->all());
        
        $userId=Auth::id();
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
            $get_data = DB::table('user_schedule_booking')->where('booking_id',$request->booking_id)->where('driver_id', $driverId)->first();
            if($get_data){
                DB::table('user_schedule_booking')->where('id', $get_data->id)->update(['booking_status'=>"0"]);
                $arr['status']=0;
                $arr['message']="Booking Reject Successfull !";
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
    
    public function driver_change_location(Request $request){
        
        \Log::info("driver_change_location");
        \Log::info($request->all());
        
        $typevalidate=Validator::make($request->all(),[ 
            'langitude'=>'required',
            'latitude'=>'required',
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
            
            // Testing
            // $googe_ride = calculate_total_distance_time($request->current_lat, $request->current_long, $request->latitude, $request->langitude);
            
            $driverId=Auth::id();
            $heading = ($request->heading)??"";
            $get_data = DB::table('users')->where('id', $driverId)->first();
            if($get_data){
                $old_latitude = $get_data->latitude;
                $old_longitude = $get_data->longtitude;
                if($request->latitude != "" && $request->langitude != ""){
                    $driver_lat = $request->latitude;
                    $driver_long = $request->langitude;
                    
                    $current_time = date('Y-m-d H:i');
                    $latest_boking = DB::table('bookings')->where('driver_id', $driverId)->where('booking_status', '4')->orderBy('id', 'DESC')->first();
                    // if($latest_boking){
                    //     $booking_info = DB::table('booking_locations')->where('booking_id', $latest_boking->booking_id)->first();
                        
                    //     \Log::info("driver_change_location booking id:- ");
                    //     \Log::info($latest_boking->booking_id);
                        
                    //     if($booking_info){
                    //         $last_id = $booking_info->id;
                    //     }else{
                    //         $last_id = DB::table('booking_locations')->insertGetId(['booking_id'=>$latest_boking->booking_id, 'current_lat'=>$driver_lat, 'current_long'=>$driver_long, 'time_duration'=>$current_time]);
                    //     }
                    //     $booking_all_data = DB::table('booking_locations')->where('id', $last_id)->where('is_ride_complete', '0')->first();
                    //     if($booking_all_data){
                    //         $time_duration = $booking_all_data->time_duration;
                    //         $get_minutes = $this->get_total_minute($time_duration, $current_time);
                    //         if($get_minutes > 10){
                    //             $wating = ($booking_all_data->total_wating)+($get_minutes-10);
                    //             DB::table('booking_locations')->where('id', $last_id)->update(['total_wating' => $wating]);
                    //         }
                            
                    //         if($get_minutes > 1){
                    //             $googe_ride = calculate_total_distance_time($booking_all_data->current_lat, $booking_all_data->current_long, $driver_lat, $driver_long);
                    //             $user_current_location = $googe_ride['current_location'];
                    //             // dd($user_current_location);
                    //             $dis = ($googe_ride['dist']+$booking_all_data->total_km);
                    //             $total_km_value = ($googe_ride['dist_value']+$booking_all_data->total_km_value);
                    //             $net_mintue = ($googe_ride['time_val']+$booking_all_data->total_time);
                    //             DB::table('booking_locations')->where('id', $last_id)->update(['total_km' => $dis, 'total_km_value' => $total_km_value, 'total_time'=>$net_mintue, 'current_lat'=>$driver_lat, 'current_long'=>$driver_long, 'time_duration'=>$current_time]);
                                
                    //             // Track Booking record
                    //             track_booking_record($latest_boking->booking_id, $driver_lat, $driver_long, $user_current_location, $googe_ride['dist'], $googe_ride['time_val'], $googe_ride['dist_value']);
                    //         }
                    //     }
                    // }
                    DB::table('users')->where('id', $get_data->id)->update(['latitude'=>$driver_lat, 'longtitude'=>$driver_long, 'driver_heading'=>$heading, 'location_change_time'=>date('d-m-Y H:i')]);

                }
                
                $data = DB::table('users')->select('id', 'name', 'latitude', 'longtitude')->where('id', $get_data->id)->first();
                
                $arr['status']=0;
                $arr['message']="Location Update successfully !";
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
    
    public function get_total_minute($first_time, $second_time){
        $date1 = new DateTime($first_time);
        $date2 = new DateTime($second_time);
        $interval = $date1->diff($date2);
        $minutesDifference = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
        return $minutesDifference;
    }
    
    public function get_driver_lat_long(Request $request){
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
                $get_data = DB::table('users')->where('id', $booking_data->driver_id)->first();
                if(!empty($get_data) &&  $get_data->latitude != "" && $get_data->longtitude != ""){
                    $data = ['driver_latitude'=>$get_data->latitude, 'driver_longitude'=>$get_data->longtitude, 'driver_heading'=>$get_data->driver_heading, 'trip_type'=>$booking_data->trip_type];
                    $arr['status']=1;
                    $arr['message']="success !";
                    $arr['data']=$data;
                }else{
                    $arr['status']=0;
                    $arr['message']="Driver lat long is empty !";
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
    
    public function driver_collect_amount(Request $request){
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
                
                $get_data = DB::table('list_subscription')->where('booking_id', $request->booking_id)->first();

                    if(empty($get_data)){
                        
                         $payment_type = '1';
                         
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
                        
                        $total_percentage = $admin_per;
                        $admint_amount = ($actual_fare*$total_percentage)/100;
                        
                        $driver_amount = ($actual_fare-($admint_amount));
                    
                        // driver wallet debit commision  
                        $payfair=[
                             "user_id"=>$booking_data->driver_id,
                             "amount"=>($admint_amount+$gst_amount),
                             "transaction_type"=>'2',
                             "transaction_date"=>date('Y-m-d'),
                             "booking_id"=>$request->booking_id
                        ];
                         
                        DB::table('my_wallet')->insert($payfair);  
                         
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
                        
                    }
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
   
    
    public function get_state_list(Request $request){
        try 
        {
            $state_list = DB::table('states')->get();
                        
            if($state_list){
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$state_list;  
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
    
    public function get_city_list(Request $request){
        $typevalidate=Validator::make($request->all(),[ 
            'state_id'=>'required',
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
        $getCityList = DB::table('cities')
                        ->select('id','name')
                        ->where('state_id',$request->state_id)
                        ->get();
                        
            if($getCityList){
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$getCityList;  
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
    
    public function withdrawal_history(Request $request){
        try 
        {
            $driverId=Auth::id();
            $getwithdraw = DB::table('withdrawal_request as r')
                        ->select('r.id', 'r.withdraw_token', 'r.amount', 'r.payment_status', 'r.created_at')
                        ->where('r.driver_id', $driverId)
                        ->orderBy('r.id', 'DESC')
                        ->get();
                        
            if($getwithdraw){
                
                foreach($getwithdraw as $val){
                    $val->created_at = date('d M Y', strtotime($val->created_at));
                    if($val->payment_status == "1"){
                        $val->payment_status = "Pending";
                    }else if($val->payment_status == "2"){
                        $val->payment_status = "Accepted";
                    }else{
                        $val->payment_status = "Denied";
                    }
                }
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$getwithdraw;  
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
    
    public function vehicle_company(Request $request){
        try 
        {
            $driverId=Auth::id();
            $company_model = DB::table('cars_names')
                        ->select('id', 'name')
                        ->where('status', '1')
                        ->get();
                        
            if($company_model){
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$company_model;  
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
    
    public function vehicle_model(Request $request){
        $typevalidate=Validator::make($request->all(),[ 
            'vehicle_compny_id'=>'required',
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
            $company_model = DB::table('vehicle_models_name as v')
                        ->select('v.id', 'v.car_id', 'v.name')
                        ->where('v.status', '1')
                        ->where('v.car_id', $request->vehicle_compny_id)
                        ->get();
                        
            if($company_model){
                
                $arr['status']=1;
                $arr['message']="success !";
                $arr['data']=$company_model;  
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
    
    public function check_reupload_document(Request $request){
        $typevalidate=Validator::make($request->all(),[ 
            'driver_id'=>'required',
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

            $driver_details = DB::table('driver_details_basic_details')
                        ->where('driver_id', $request->driver_id)
                        ->first();
                        
            if($driver_details){
                
                $document_name = "";
                $doc_status = "0";
                $slug = "";
                
                if($driver_details->doc1_reupload == "0"){
                    $document_name = "Driving Licence";
                    $doc_status = "1";
                    $slug = "doc1_reupload";
                }
                else if($driver_details->doc2_reupload == "0"){
                    $document_name = "Driving Licence Back";
                    $doc_status = "1";
                    $slug = "doc2_reupload";
                }
                else if($driver_details->doc3_reupload == "0"){
                    $document_name = "Driver Id Proof";
                    $doc_status = "1";
                    $slug = "doc3_reupload";
                }
                else if($driver_details->doc4_reupload == "0"){
                    $document_name = "Driver Id Proof Back";
                    $doc_status = "1";
                    $slug = "doc4_reupload";
                }
                else if($driver_details->doc5_reupload == "0"){
                    $document_name = "RC Front Image";
                    $doc_status = "1";
                    $slug = "doc5_reupload";
                }
                else if($driver_details->doc6_reupload == "0"){
                    $document_name = "RC Back Image";
                    $doc_status = "1";
                    $slug = "doc6_reupload";
                }
                else if($driver_details->doc7_reupload == "0"){
                    $document_name = "Pollution certificate Front";
                    $doc_status = "1";
                    $slug = "doc7_reupload";
                }
                else if($driver_details->doc8_reupload == "0"){
                    $document_name = "Pollution certificate Back";
                    $doc_status = "1";
                    $slug = "doc8_reupload";
                }
                else if($driver_details->doc9_reupload == "0"){
                    $document_name = "Vehicle Audit Front";
                    $doc_status = "1";
                    $slug = "doc9_reupload";
                }
                else if($driver_details->doc10_reupload == "0"){
                    $document_name = "Vehicle Audit Back";
                    $doc_status = "1";
                    $slug = "doc10_reupload";
                }
                
                if($doc_status == "1"){
                    
                    $data = [
                        'driver_id'=>$driver_details->driver_id,
                        'document_name'=>$document_name,
                        'slug'=>$slug,
                    ];
                    
                    $arr['status']=1;
                    $arr['message']="Re Upload Document Found !";
                    $arr['data']=$data; 
                    
                }else{
                    
                    $arr['status']=1;
                    $arr['message']="No Re upload documents !";
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
    
    
    public function ccavenue_payment(Request $request){
    
  
	$working_key = env('CCAVENUE_ENCRYPTION_KEY');
	$access_code = env('CCAVENUE_ACCESS_CODE');
	$testurl= env('CCAVENUE_URL');

  $input = $request->all();

    $input['amount'] = '1000';
    $input['order_id'] = "ORDER001";
    $input['currency'] = "INR";
    $input['redirect_url'] = route('cc-response');
    $input['cancel_url'] = route('cc-response');
    $input['language'] = "EN";
    $input['merchant_id'] = env('CCAVENUE_MERCHANT_ID');

    $merchant_data = "";
    
    $input['billing_name']="manoj";
    $input['billing_address']="new delhi sector 2";
    $input['billing_state']="delhi";
    $input['billing_zip']="110001";
    $input['billing_city']="delhi";
    $input['billing_country']="IND";
    $input['billing_tel']="9013409547";
    $input['billing_email']="test123@gmail.com";
  
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

    return redirect($url);
        
    }
    
    public function ccavenue_payment_redirect(Request $request){
    
        dump($request->encResp);
    
        $workingKey = env('CCAVENUE_ENCRYPTION_KEY'); //Working Key should be provided here.
        $encResponse = $request->encResp;

        $rcvdString = $this->decryptCC($encResponse, $workingKey);        //Crypto Decryption used as per the specified working key.
        $order_status = "";
        $decryptValues = explode('&', $rcvdString);
        $dataSize = sizeof($decryptValues);
        
    dump($decryptValues);
    dump($dataSize);
    
    }
    
  
    
    public function encryptCC($plainText, $key)
    {
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }
    
    public function decryptCC($encryptedText, $key)
    {
        $key = $this->hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = $this->hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }
    
    public function pkcs5_padCC($plainText, $blockSize)
    {
        $pad = $blockSize - (strlen($plainText) % $blockSize);
        return $plainText . str_repeat(chr($pad), $pad);
    }
    
    public function hextobin($hexString)
    {
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
    
    public function generate_qr_code(Request $request){

    }
    

    public function collect_pay(Request $request){
        try {
            $client = new Client();
    
            $orderId = uniqid();
            $merchantId = get_option_data('merchant_id');
            $accessCode = get_option_data('access_code');
            $encryptionKey = get_option_data('working_key');
            $amount = "100";
            
            // Load ICICI public key
            $filePath = public_path('encryption_key/rsa_apikey.txt');
            $icici_public_key = file_get_contents($filePath);
            
            // Sample data to encrypt
            $payload = [
                 "payerVa" => "testo@icici",  
                 "amount" => "5.00",  
                 "note" => "collect-pay-request",  
                 "collectByDate" => "10/05/2024 01:30 PM",  
                 "merchantId" => $merchantId,  
                 "merchantName" => "Testmerchant",  
                 "subMerchantId" => "",  
                 "subMerchantName" => "Test",  
                 "terminalId" => "5411",  
                 "merchantTranId" => "p0nillp0k9lqlp091p17",  
                 "billNumber" => "sdf1po111b",  
                 "validatePayerAccFlag" => "Y",  
                 "payerAccount"=> "0405012740",  
                 "payerIFSC"=> "ICI00012345", 
            ];
            $payload = json_encode($payload);
            
            // Encrypt the payload using RSA/ECB/PKCS1
            openssl_public_encrypt($payload, $encrypted_payload, $icici_public_key, OPENSSL_PKCS1_PADDING);
            
            // Base64 encode the encrypted payload
            $encrypted_payload_base64 = base64_encode($encrypted_payload);
            
            $headers = [
                'Content-Type: application/json',
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://apibankingonesandbox.icicibank.com/api/MerchantAPI/UPI/v0/CollectPay3/593815');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encrypted_payload_base64);
            $response = curl_exec($ch);
            $a=json_decode($response);
            
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

            echo $decryptedPayload ;

        
        }
        catch(\Exception $e)
        {
            $arr['status']=0;
            $arr['message']="Something went Wrong !";
            $arr['data']=NULL;
            return response()->json($arr,200); 
        }
    }
    
    public function call_back_transaction(Request $request){
        
        $rawData = $request->getContent();
        \Log::info('Call_back_log : ' . $rawData);
        
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

        try {
            if(isset($response_data) && isset($response_data['TxnStatus']) && $response_data['TxnStatus'] == "SUCCESS"){
                if(isset($response_data['merchantTranId']) && $response_data['merchantTranId'] != " "){
                    $booking_id = $response_data['merchantTranId'];
                    \Log::info('booking_id : ' . $booking_id);
                    
                    // Add Wallet Amount By using QR
                    $wallet_data = DB::table('my_wallet')->where('order_id', $booking_id)->first();
                    if($wallet_data){
                        DB::table('my_wallet')->where('id',$wallet_data->id)->update(['status'=>1,'transaction_id'=>time(), 'payment_type'=>'wallet']);
                    }
                    
                    $booking_data = DB::table('bookings')->where('booking_id', $booking_id)->first();
                    if($booking_data){
                        $get_data = DB::table('list_subscription')->where('booking_id', $booking_data->booking_id)->first();
    
                        if(empty($get_data)){
    
                             $payment_type = '1';
                             
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
                                         "amount"=>$driver_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$request->booking_id
                                     ];
                                     
                                     db::table('my_wallet')->insert($payfair);  
                                    
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
                                        
                                     if($user_percentage > 0){
                                         $commison4 = [
                                             "user_id"=>$booking_data->user_id,
                                             "amount"=>$user_amount,
                                             "transaction_type"=>'1',
                                             "transaction_date"=>date('Y-m-d'),
                                             "booking_id"=>$request->booking_id
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
                                         "booking_id"=>$request->booking_id
                                     ];
                                     
                                     db::table('my_wallet')->insert($payfair);  
                                     
                                if($user_percentage > 0){
                                     $commison4 = [
                                         "user_id"=>$booking_data->user_id,
                                         "amount"=>$user_amount,
                                         "transaction_type"=>'1',
                                         "transaction_date"=>date('Y-m-d'),
                                         "booking_id"=>$request->booking_id
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
                            
                        }
                    }
                    
                }
            }
            \Log::info('Payment Successfull !');
            return response()->json(['all_data' => $response_data,]);
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
    
    public function get_total_time_duration($driverId){
        
        $rides = DB::table('bookings')
                ->where('driver_id', $driverId)
                ->where('booking_status', 5)
                ->whereDate('created_at', Carbon::today())
                ->pluck('time_duration');
        
        $totalMinutes = 0;
        
        foreach ($rides as $duration) {
        
            $minutes = 0;
        
            // MATCH "X hour Y mins"
            if (preg_match('/(\d+)\s*hour[s]?\s*(\d+)\s*min[s]?/i', $duration, $m)) {
                $minutes += ($m[1] * 60) + $m[2];
            }
            // MATCH "X hour"
            else if (preg_match('/(\d+)\s*hour[s]?/i', $duration, $m)) {
                $minutes += ($m[1] * 60);
            }
            // MATCH "Y mins"
            else if (preg_match('/(\d+)\s*min[s]?/i', $duration, $m)) {
                $minutes += $m[1];
            }
        
            $totalMinutes += $minutes;
        }
        
        // Convert minutes to hour:minute format
        $totalHours = floor($totalMinutes / 60);
        $remainingMinutes = $totalMinutes % 60;
        
        $totalTimeFormatted = $totalHours . ' hours ' . $remainingMinutes . ' mins';
        
        return $totalTimeFormatted;
        
    }
    
    public function driver_dashboard(Request $request)
    {
        try
        {
            $driverId = Auth::id();
    
            // Today Earning
            $today_earning = DB::table('bookings')
                ->where('driver_id', $driverId)
                ->where('payment_status', '1')
                ->whereDate('created_at', Carbon::today())
                ->sum('fare');
    
            // Weekly Earning
            $weekly_earning = DB::table('bookings')
                ->where('driver_id', $driverId)
                ->where('payment_status', '1')
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->sum('fare');
    
            // Monthly Earning
            $monthly_earning = DB::table('bookings')
                ->where('driver_id', $driverId)
                ->where('payment_status', '1')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('fare');
    
            // Custom / Total Earning
            $customEarningQuery = DB::table('bookings')
                ->where('driver_id', $driverId)
                ->where('payment_status', '1');
    
            if ($request->filled('from_date') && $request->filled('to_date')) {
    
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $toDate   = Carbon::parse($request->to_date)->endOfDay();
    
                $customEarningQuery->whereBetween('created_at', [$fromDate, $toDate]);
            }
    
            // If dates are not passed, this returns total earning
            $custom_earning = $customEarningQuery->sum('fare');
    
            // Today Rides
            $today_rides = DB::table('bookings')
                ->where('driver_id', $driverId)
                ->where('booking_status', '5')
                ->whereDate('created_at', Carbon::today())
                ->count();
    
            // Ongoing Ride
            $ongoing_ride = DB::table('bookings as b')
                ->select('b.*', 'u.name as user_name', 'u.profile')
                ->leftJoin('users as u', 'u.id', '=', 'b.user_id')
                ->where('b.driver_id', $driverId)
                ->where('b.booking_type', '1')
                ->whereIn('b.booking_status', [2, 4])
                ->first();
    
            if ($ongoing_ride) {
                $ongoing_ride->profile = !empty($ongoing_ride->profile)
                    ? url('public/profile_image/' . $ongoing_ride->profile)
                    : profileImg();
            }
    
            $data = [
                'today_rides' => $today_rides,
                'today_working_hours' => $this->get_total_time_duration($driverId),
                'today_earning' => $today_earning,
                'weekly_earning' => $weekly_earning,
                'monthly_earning' => $monthly_earning,
                'custom_earning' => $custom_earning,
                'wallet_amount' => get_wallet_balance($driverId),
                'weekly_goals' => get_option_data('minimum_payout_generate'),
                'ongoing_ride' => $ongoing_ride,
            ];
    
            $arr = [
                'status' => 1,
                'message' => 'Success!',
                'data' => $data
            ];
        }
        catch (\Exception $e)
        {
            $arr = [
                'status' => 0,
                'message' => 'Something went wrong!',
                'data' => null
            ];
        }
    
        return response()->json($arr, 200);
    }
    
    
    public function driver_earning(Request $request){
        
        try 
        {
            $driverId = Auth::id();
            
            $today_earning = DB::table('bookings')
                            ->where('driver_id', $driverId)
                            ->where('payment_status', '1')
                            ->whereDate('created_at', Carbon::today())
                            ->get();
                            
            $weekly_earning = DB::table('bookings')
                            ->where('driver_id', $driverId)
                            ->where('payment_status', '1')
                            ->whereBetween('created_at', [
                                Carbon::now()->startOfWeek(),
                                Carbon::now()->endOfWeek()
                            ])
                            ->get(); 
                            
            $monthly_earning = DB::table('bookings')
                            ->where('driver_id', $driverId)
                            ->where('payment_status', '1')
                            ->whereYear('created_at', Carbon::now()->year)
                            ->whereMonth('created_at', Carbon::now()->month)
                            ->get();    
                            
            // Custom / Total Earning
            $customEarningQuery = DB::table('bookings')
                ->where('driver_id', $driverId)
                ->where('payment_status', '1');
    
            if ($request->filled('from_date') && $request->filled('to_date')) {
    
                $fromDate = Carbon::parse($request->from_date)->startOfDay();
                $toDate   = Carbon::parse($request->to_date)->endOfDay();
    
                $customEarningQuery->whereBetween('created_at', [$fromDate, $toDate]);
            }
    
            // If dates are not passed, this returns total earning
            $custom_earning = $customEarningQuery->sum('fare');                
                            
           
            $data = [
                'custom_earning' => $custom_earning,
                'today_earning' => $today_earning,
                'weekly_earning' => $weekly_earning,
                'monthly_earning' => $monthly_earning,
                'wallet_amount' => get_wallet_balance($driverId),
            ];            
                        
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
    
    
}