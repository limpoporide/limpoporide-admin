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

class SmartrideController extends Controller
{
    
    public function language_list(Request $request)
    {
       try
        {
            $result = DB::table('languages')->select('id','name')->get();
           
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
    
    public function language_search(Request $request)
    {
       $typevalidate=Validator::make($request->all(),[
            'language'           =>'required',
        ]);
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
      try {
          $search = $request->language;
         
         $language_list = DB::table('languages')->select('id','name')->where('name', 'LIKE', "%$search%")->get();
         
         if($language_list){
                $arr['status']=1;
                $arr['message']='Successfully!!';
                $arr['data']=$language_list;
            } else {
                $arr['status']=0;
                $arr['message']='No Language Found';
                $arr['data']=NULL;
            }               
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=NULL;
       }
       return response()->json($arr,200);
   }

   public function get_riders_details()
    {
        
       
      try {
          
        $id = $id = Auth::id();
        $result = DB::table('riders_details')->select('id','user_id','fullname','mobile','email')->where('user_id',$id)->get();
       
         
         if($result){
                $arr['status']=1;
                $arr['message']='Successfully!!';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='No Language Found';
                $arr['data']=NULL;
            }               
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=NULL;
       }
       return response()->json($arr,200);
   }
    
    
    public function add_saved_places(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'name'           =>'required',
            'latitude'           =>'required',
            'longitute'           =>'required',
            'address'           =>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
      try {
          
         $id = $id = Auth::id();
           $name = $request->name;
           $latitude = $request->latitude;
           $longitude = $request->longitute;
           $address = $request->address;
           $data = [
                    'user_id'  => $id,
                    'name'      => $name,
                    'latitude' => $latitude,
                    'longitute'   => $longitude,
                    'address'   => $address,
                    
                ];
                
                
          $result = DB::table('saved_places')->insert($data);
         
        //  $language_list = DB::table('languages')->select('id','name')->where('name', 'LIKE', "%$search%")->get();
         
         if($result){
                $arr['status']=1;
                $arr['message']='Successfully!!';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='No Language Found';
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
   
    public function get_saved_place_list()
    {
        
       
      try {
          
        $id = $id = Auth::id();
        $result = DB::table('saved_places')->select('id','user_id','name','latitude','longitute', 'address')->where('user_id',$id)->get();
       
         
         if($result){
                $arr['status']=1;
                $arr['message']='Successfully!!';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='No Language Found';
                $arr['data']=NULL;
            }               
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=NULL;
       }
       return response()->json($arr,200);
   }
   public function delete_saved_place(Request $request)
    {
         $typevalidate=Validator::make($request->all(),[
            'id'           =>'required'
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
       
      try {
          
        $user_id = $id = Auth::id();
        $id = $request->id;
        $result = DB::table('saved_places')->where('user_id',$user_id)->where('id',$id)->delete();
       
         
         if($result){
                $arr['status']=1;
                $arr['message']='Successfully!!';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='Something Went Wrong';
                $arr['data']=NULL;
            }               
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=NULL;
       }
       return response()->json($arr,200);
   }
    public function my_profile()
    {
        
       
      try {
          
        $id = $id = Auth::id();
        
        $result = DB::table('users')->select('*','profile as image')
        ->where('id',$id)->get();
        
       if($result){
           foreach($result as $val){
              
                $val->total_distance = get_total_distance($val->id);
                $val->profile = ($val->image)?url("public/profile_image")."/".$val->image :"";
                $val->latest_version = get_option_data('apk_current_version');
           }
       }
         if($result){
                $arr['status']=1;
                $arr['message']='Successfully!!';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='No Language Found';
                $arr['data']=NULL;
            }               
      }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=NULL;
       }
       return response()->json($arr,200);
   }
   
    public function user_bank_details(Request $request){
       
       try {
          
         $id = $id = Auth::id();
        
         $result = DB::table('users')->select('id','bank_ac_holder_name', 'bank_name', 'bank_account', 'branch', 'code as ifsc')->where('id',$id)->first();
        
         if($result){
                $arr['status']=1;
                $arr['message']='Successfully!!';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='No Language Found';
                $arr['data']=NULL;
            }               
       }
       catch(\Exception $e)
       {
           $arr['status']=0;
           $arr['message']='Sorry!! Something Went Wrong';
           $arr['data']=NULL;
       }
       return response()->json($arr,200);
       
    }
    
    
    public function change_password(Request $request)
    { 
        $typevalidate=Validator::make($request->all(),[
            'previous_password'           =>'required',
            'password'                    =>'required|min:6',
            'confirm_password'            =>'required|min:6',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        } 
         
        
      try {
          
       $id = Auth::id();
       $previous_password = $request->previous_password;
       $password = $request->password;
       $confirm_password = $request->confirm_password;
         
         
         $users = User::where('id',$id)->first();  
        // dd($users);
         
         if (Hash::check($previous_password, $users->password))
                {
                    
                    if($password!=$confirm_password){
                                $arr['status']=0;
                                $arr['message']='confirm password should same as password';
                                $arr['data']=NULL;
          
                            }
                            else{
                                 $hashpass= Hash::make($password);              
                                $data_user = array(
                                'password'    => $hashpass
                                );
                
                             $data =   DB::table('users')->where('id',$id)->update($data_user);
                           if($data){
                                $arr['status']=1;
                                $arr['message']='Successfully!!';
                                $arr['data']=$data;
                            } else {
                                $arr['status']=0;
                                $arr['message']='no data found';
                                $arr['data']=NULL;
                            }   
                    
             
                            }
                    
                    // The passwords match...
                }else{
                            $arr['status']=0;
                                $arr['message']="pervious password is wrong !";
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
     public function about_us(Request $request)
    {
      $id = $id = Auth::id();
       try
        {
            // $result = DB::table('about_us')->select('details',DB::raw('CONCAT("' .url("public") .'","/",img)  as img'))->where('type',1)->first();
            $result = DB::table('options')->select('id', 'key as type', 'value as details')->where('key', 'about_us')->first();
           
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
    
    
    public function cancellation_privacy_policy(Request $request)
    {
      $id = $id = Auth::id();
       try
        {
            $result = DB::table('options')->select('id', 'key as type', 'value as details')->where('key', 'cancellation_privacy_policy')->first();
           
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
    
    public function customer_support(Request $request)
    {
       try
        {
            $result = DB::table('customer_supports')->select('id','email','mobile')->first();
           
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
    
    public function faq(Request $request)
    {
       try
        {
            $result = DB::table('faqs')->select('id', 'type','questions','answers')->get()->toArray();
           
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
    
    public function get_vehicles(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'total_distance'           =>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
      
      try
        {
            
        $total_distance = $request->total_distance;    
            
        $result = DB::table('vehicle_type')->select('id','name','price',DB::raw('CONCAT("' .url("public") .'","/vehicle_image/vehicle_type_image/",image)  as image'))->where('status',1)->get()->toArray();
        
        foreach($result as $val){
            $val->price  = $val->price * $total_distance;
        };
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
    
    public function get_coupon_codes(Request $request)
    {
       try
        {
           
            $result = DB::table('coupon_codes')->get()->toArray();
           
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
    
    public function apply_coupon_code(Request $request)
    {
        
        $typevalidate=Validator::make($request->all(),[
            'vehicle_fare_id' =>'required',
            'coupon_code' =>'required',
        ]);
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }  
        try
        {
            $current_date = date('Y-m-d');
            $code = $request->coupon_code;
            $result = DB::table('coupon_codes')->where('code', $code)->first();
           
            if($result)
            {
                $result_fare = DB::table('vehicle_fare')->where('id', $request->vehicle_fare_id)->first();
                if($result_fare){
                    
                    $coupon_type = $result->discount_type;
                    $coupon_amount = $result->discount_value;
                    
                    if($coupon_type == "percentage"){
                        $applied_amount = ($result_fare->actual_price * $coupon_amount)/100;
                        $final_amount = ($result_fare->actual_price - $applied_amount);
                    }else{
                        $final_amount = ($result_fare->actual_price - $coupon_amount);
                    }
                    
                    DB::table('vehicle_fare')->where('id', $request->vehicle_fare_id)->update(['fare' => $final_amount, 'applied_cupon_id' => $result->id]);
                    
                }
                
                $result_fare = DB::table('vehicle_fare')->where('id', $request->vehicle_fare_id)->first();
                
                $arr['status']=1;
                $arr['message']='Coupon Code Applied';
                $arr['data']=$result_fare;
            }
            else
            {
                $arr['status']=0;
                $arr['message']='Invalid Coupon Code';
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
    

    
    public function add_review(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            // 'id'           =>'required',
            'star'           =>'required',
            'review'           =>'required'
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
       
    try
        {
            $id = Auth::id();
            $result = DB::table('reviews')->insert(['user_id' => $id,'star' => $request->star,'review' => $request->review,'booking_id'=>$request->booking_id]);
            
            if($result)
            {
                $arr['status']=1;
                $arr['message']='Successfully';
            }
            else
            {
                $arr['status']=0;
                $arr['message']='Unsuccessfully';
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
    
    public function get_dodont_list(Request $request)
    {
       try
        {
           
            $result = DB::table('dodont')->select('do','dont')->where('status',1)->get()->toArray();
           
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
    
    public function contact_us(Request $request)
    {
       try
        {
            $result = DB::table('contact_us')->select('email','mobile','whatsapp')->get()->toArray();
           
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
    
     /****************************************** Contact Add *****************************************/
     public function add_contact(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'first_name'           =>'required',
            'last_name'            =>'required',
            'mobile'               =>'required'
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
        
      try {
        $user_id =  Auth::id();
        $first_name = $request->first_name;
        $last_name  = $request->last_name;
        $mobile     = $request->mobile;
        $all_contact = DB::table('contact')->where('user_id', $user_id)->count();
        if($all_contact<=5){
            $result = DB::table('contact')->insert([
                'user_id'     => $user_id,
                'first_name'  => $first_name,
                'last_name'   => $last_name,
                'mobile'      => $mobile,
            ]);
           if($result){
                $arr['status']=1;
                $arr['message']='Add Contact Successfully!!';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='Data Not Found';
                $arr['data']=NULL;
            } 
        }else{
            $arr['status']=0;
            $arr['message']='You Can not add Emergency Contact more thane 5 !';
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
   
   /******************************************End Contact Add *****************************************/
   
   
   /******************************************Emergency Contact list *****************************************/
     public function emergency_contact_list(Request $request)
    {
        $user_id =  Auth::id();
        
      try {
        
        $result = DB::table('contact')->where('user_id', $user_id)->orderBy('id','desc')->get()->toarray();
         if($result){
                $arr['status']=1;
                $arr['message']='Contact List';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='Data Not Found';
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
   
   /******************************************End Emergency Contact list *****************************************/
   
   /******************************************Emergency Contact list *****************************************/
     public function emergency_contact_id(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'id'  =>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        } 
        try {
        $user_id =  Auth::id();
        $id      = $request->id;
        $result = DB::table('contact')->where('id', $id)->first();
         if($result){
                $arr['status']=1;
                $arr['message']='Emergency Contact';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='Data Not Found';
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
   
   /******************************************End Emergency Contact list *****************************************/
   
   /******************************************Emergency Contact Delete *****************************************/
     public function emergency_contact_delete(Request $request)
    {
      $typevalidate=Validator::make($request->all(),[
            'id'  =>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }   
      try {
        $user_id =  Auth::id();
        $id      = $request->id;
        $result = DB::table('contact')->where('id', $id)->delete();
         if($result){
                $arr['status']=1;
                $arr['message']='Emergency Contact Delete Successfully';
                $arr['data']=$result;
            } else {
                $arr['status']=0;
                $arr['message']='Data Not Found';
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
   
   /******************************************End Emergency Contact Delete *****************************************/
   
    public function smart_ride_safery(Request $request)
    {
       try
        {
            $result = DB::table('smart_ride_safery')->select('details')->where('status',1)->get()->toArray();
           
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
    
    public function get_trip_issue(Request $request)
    {
       try
        {
            $result = DB::table('trip_issue')->select('details')->where('status',1)->get()->toArray();
           
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
    
    public function get_safety_issue(Request $request)
    {
       try
        {
            $result = DB::table('safety_issue')->select('details')->where('status',1)->get()->toArray();
           
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
    
    
    
    public function calculate_wallet_amount($user_id){
        if($user_id != ""){
          $wallet_data = DB::table('my_wallet')
                        ->select(DB::raw('sum(case when transaction_type = "1" then amount else -amount end) balance '))
                        ->where('user_id', $user_id)
                        ->where('status','1')
                        ->first();
                        
          if($wallet_data){
              $wallet_amount = ($wallet_data->balance)? number_format((float)$wallet_data->balance, 2, '.', '') :"0.00";
          } else{
              $wallet_amount = "0";
          } 
          return $wallet_amount;
        }
    }
    
    public function get_ongoing_ride(Request $request)
    {
        
      \Log::info('get_ongoing_ride : ');
      \Log::info($request->all());
    
        
      try
        {
            $user_id = Auth::id();

            $user_data = DB::table('users')->where('id', $user_id)->first();
            if(isset($user_data) && $user_data->type == "2"){
                
                // $result = DB::table('bookings as b')
                //         ->select('b.*', 'u.mobile_no as user_mobile', 'd.mobile_no as driver_mobile')
                //         ->leftjoin('users as u','u.id','=','b.user_id')
                //         ->leftjoin('users as d','d.id','=','b.driver_id')
                //         ->where('b.driver_id',$user_id)
                //         ->where('b.booking_type', '1')
                //         ->whereIn('b.booking_status',['4', '2'])
                //         ->orderBy('b.id', 'DESC')->first();
                        
                $result = DB::table('bookings as b')
                        ->select('e.name as username','e.mobile_no as usermobile', 'e.gender','b.id', 'b.booking_id', 'b.distance', 'b.fare', 'vt.type', 'vt.name as vehicle_type_name', 'vt.image as vehicle_type_image', 'b.trip_type', 'b.is_rental', 'b.rental_km', 'b.rental_hour', 'b.picup_location', 'b.drop_location', 'd.latitude as driver_latitude',
                        'd.longtitude as driver_longitude', 'b.ride_start_otp as otp', DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile)  as profile_image'),
                        'd.id as driver_id', 'd.name as driver_name', 'd.profile as driver_profile', 'd.latitude', 'd.longtitude', 'b.picup_lat', 'b.picup_long', 'b.drop_lat', 'b.drop_long', 'detail.vehicle_number',
                        'm.name as vehicle_model', 'd.mobile_no', 'b.booking_status', 'b.payment_status')
                        ->leftjoin('users as d','d.id','=','b.driver_id')
                        ->leftjoin('users as e','e.id','=','b.user_id')
                        ->leftjoin('driver_details_basic_details as detail','detail.driver_id','=','b.driver_id')
                        ->leftjoin('vehicle_models_name as m','m.id','=','detail.vehicle_model')
                        ->leftjoin('vehicle_type as vt','vt.id','=','detail.vehicle_type')
                        ->where('b.driver_id',$user_id)
                        ->where('b.booking_type', '1')
                        ->whereIn('b.booking_status',['4', '2'])
                        ->orderBy('b.id', 'DESC')->first();        
                
            }else{
                
                // $result = DB::table('bookings as b')
                //         ->select('b.*', 'u.mobile_no as user_mobile', 'd.mobile_no as driver_mobile')
                //         ->leftjoin('users as u','u.id','=','b.user_id')
                //         ->leftjoin('users as d','d.id','=','b.driver_id')
                //         ->where('b.user_id',$user_id)
                //         ->where('b.booking_type', '1')
                //         ->whereIn('b.booking_status',['2', '4'])
                //         ->orderBy('b.id', 'DESC')->first();
                        
                $result = DB::table('bookings as b')
                        ->select('e.name as username','e.mobile_no as usermobile', 'e.gender','b.id', 'b.booking_id', 'b.distance', 'b.fare', 'vt.type', 'vt.name as vehicle_type_name', 'vt.image as vehicle_type_image', 'b.trip_type', 'b.is_rental', 'b.rental_km', 'b.rental_hour', 'b.picup_location', 'b.drop_location', 'd.latitude as driver_latitude',
                        'd.longtitude as driver_longitude', 'b.ride_start_otp as otp', DB::raw('CONCAT("' . url('public/profile_image') . '","/",d.profile)  as profile_image'),
                        'd.id as driver_id', 'd.name as driver_name', 'd.profile as driver_profile', 'd.latitude', 'd.longtitude', 'b.picup_lat', 'b.picup_long', 'b.drop_lat', 'b.drop_long', 'detail.vehicle_number',
                        'm.name as vehicle_model', 'd.mobile_no', 'b.booking_status', 'b.payment_status')
                        ->leftjoin('users as d','d.id','=','b.driver_id')
                        ->leftjoin('users as e','e.id','=','b.user_id')
                        ->leftjoin('driver_details_basic_details as detail','detail.driver_id','=','b.driver_id')
                        ->leftjoin('vehicle_models_name as m','m.id','=','detail.vehicle_model')
                        ->leftjoin('vehicle_type as vt','vt.id','=','detail.vehicle_type')
                        ->where('b.user_id',$user_id)
                        ->where('b.booking_type', '1')
                        ->whereIn('b.booking_status',['2', '4'])
                        ->orderBy('b.id', 'DESC')->first();        
                
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
                $arr['data']="Searching";
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
    
    public function book_ride(Request $request)
    {
        $typevalidate=Validator::make($request->all(),[
            'user_id'            =>'required',
            'assign_driver_id'   =>'required',
            'driver_vehicle_id'  =>'required',
            'amount'             =>'required',
            'pickup_location'    =>'required',
            'pickup_lat'         =>'required',
            'pickup_long'        =>'required',
            'drop_location'      =>'required',
            'dropup_lat'         =>'required',
            'dropup_long'        =>'required',
            'pickup_time'        =>'required',
        ]);
        
        if($typevalidate->fails()){
            $arr['status']=0;
            $arr['message']=$typevalidate->errors()->first();
            $arr['data']=NULL;
            return response()->json($arr,200);
        }
       
      try
        {
            $user_id = $request->user_id;
            $assign_driver_id = $request->assign_driver_id;
            $driver_vehicle_id = $request->driver_vehicle_id;
            $amount = $request->amount;
            $pickup_location = $request->pickup_location;
            $pickup_lat = $request->pickup_lat;
            $pickup_long = $request->pickup_long;
            $drop_location = $request->drop_location;
            $dropup_lat = $request->dropup_lat;
            $dropup_long = $request->dropup_long;
            $coupon_code = $request->coupon_code;
            
            
            $data=array(
                'user_id'=>$request->user_id,
                'assign_driver_id'=>$request->assign_driver_id,
                'driver_vehicle_id'=>$request->driver_vehicle_id,
                'amount'=>$request->amount,
                'pickup_location'=>$request->pickup_location,
                'pickup_lat'=>$request->pickup_lat,
                'pickup_long'=>$request->pickup_long,
                'drop_location'=>$request->drop_location,
                'dropup_lat'=>$request->dropup_lat,
                'dropup_long'=>$request->dropup_long,
                'coupon_code'=>$request->coupon_code,
                'trip_code'=>rand(0000,9999),
                'booking_date'=>date('Y-m-d'),
                'pickup_time'=>$request->pickup_time,
            );
            
            
            $card = DB::table('ride')->insert($data);
            
           
            // $result = DB::table('ride as t1')
            // ->leftjoin('riders_details as t2','t1.assign_driver_id','=','t2.id')
            // ->select('t2.fullname','t1.pickup_time','t1.amount','t1.pickup_location','t1.drop_location')
            // ->where('t1.user_id',$user_id)->get()->toArray();
            
           
            if($card)
            {
                $arr['status']=1;
                $arr['message']='Success';
                $arr['data']=$data;
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
    
    public function country_list(Request $request){
        try 
        {
            $get_data = DB::table('country')->select('id', 'name')->where('status', '1')->get()->toArray();
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
    
    public function year_list(Request $request){
        try 
        {
            
            $years_to_show = 10;
            
            $start_year = date('Y');
            $years = [];
            for($offset=0; $offset<$years_to_show; $offset++)
            {
                $year = $start_year - $offset;
                $year_data['id'] = "$year";
                $year_data['name'] = "$year";
                array_push($years, $year_data);
            }
            
            if($years)
            {
                $arr['status']=1;
                $arr['message']="successfully";
                $arr['data']=$years;
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
    
    public function testing_api(Request $request){

$url = "http://app.accent.in/HRPAppService/WebService.asmx";
$soapAction = "http://tempuri.org/GetShortLeavePeriod";

// Create the XML payload
$xmlPayload = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetShortLeavePeriod xmlns="http://tempuri.org/">
      <DomainName>string</DomainName>
      <EmpId>string</EmpId>
      <UserName>string</UserName>
      <Password>string</Password>
    </GetShortLeavePeriod>
  </soap:Body>
</soap:Envelope>
XML;

// Initialize cURL session
$ch = curl_init();

// Set the cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: text/xml; charset=utf-8",
    "Content-Length: " . strlen($xmlPayload),
    "SOAPAction: " . $soapAction
]);

        // Execute the cURL request and get the response
        $response = curl_exec($ch);
        dd($response);
        // Check for cURL errors
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            die("cURL Error: $error");
        }
        
        // Close the cURL session
        curl_close($ch);
        
        // Output the response
        echo $response;

    }
    
}