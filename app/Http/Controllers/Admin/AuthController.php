<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Rules\MatchOldPassword;
use Validator;
use Config;
use Session;
use Cache;
use Image;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class AuthController extends Controller
{
    
    public function qr_code_samples(Request $request){
        
        $data = DB::table('qr_code_sample')->where('status', '1')->get();
        foreach($data as $res){
            $url = url('/qr_code_details')."/".$res->id;
            $res->qr_code = QrCode::size(200)->generate($url);
        }
        
        return view('website.qr_samples', compact('data'));
    }
    
    public function qr_code_details(Request $request, $id){
        
        $data = DB::table('qr_code_sample')->where('id', $id)->first();
        return view('website.qr_details', compact('data'));
    }
    
    public function qr_code_submit(Request $request){
        
        $update_id = $request->update_id;
        
        $data = [
            'username' => $request->userName,
            'mobile' => $request->mobile,
            'otp_verify' => "1",
        ];
        
        DB::table('qr_code_sample')->where('id', $update_id)->update($data);
        
        return redirect()->route('qr.details', ['id' => $update_id]);
        
    }
    
    
    public function getChartDataAjax(Request $request){
        
        $filter = $request->get('filter', 'today');
        $data = $this->getChartData($filter);
        return response()->json($data);
        
    }
    
    
    private function getChartData($filter)
    {
        $rides = [];
        $revenue = [];
        $bookings = [];
        $categories = [];

        switch ($filter) {
            case 'today':
                // Get hourly data for today
                for ($i = 0; $i < 24; $i++) {
                    $startTime = Carbon::today()->addHours($i);
                    $endTime = Carbon::today()->addHours($i + 1);
                    
                    $hourlyBookings = DB::table('bookings')->whereBetween('created_at', [$startTime, $endTime]);
                    
                    $rides[] = $hourlyBookings->count();
                    $revenue[] = (float) $hourlyBookings->where('booking_status', '5')->sum('fare');
                    $bookings[] = $hourlyBookings->where('booking_status', '5')->count();
                    $categories[] = $startTime->toIso8601String();
                }
                break;

            case 'month':
                // Get daily data for the current month
                $daysInMonth = Carbon::now()->daysInMonth;
                
                for ($i = 1; $i <= $daysInMonth; $i++) {
                    $date = Carbon::now()->startOfMonth()->addDays($i - 1);
                    $startDate = $date->copy()->startOfDay();
                    $endDate = $date->copy()->endOfDay();
                    
                    $dailyBookings = DB::table('bookings')->whereBetween('created_at', [$startDate, $endDate]);
                    
                    $rides[] = $dailyBookings->count();
                    $revenue[] = (float) $dailyBookings->where('booking_status', '5')->sum('fare');
                    $bookings[] = $dailyBookings->where('booking_status', '5')->count();
                    $categories[] = $startDate->toIso8601String();
                }
                break;

            case 'year':
                // Get monthly data for the current year
                for ($i = 1; $i <= 12; $i++) {
                    $startDate = Carbon::now()->month($i)->startOfMonth();
                    $endDate = Carbon::now()->month($i)->endOfMonth();
                    
                    $monthlyBookings = DB::table('bookings')->whereBetween('created_at', [$startDate, $endDate]);
                    
                    $rides[] = $monthlyBookings->count();
                    $revenue[] = (float) $monthlyBookings->where('booking_status', '5')->sum('fare');
                    $bookings[] = $monthlyBookings->where('booking_status', '5')->count();
                    $categories[] = $startDate->toIso8601String();
                }
                break;
        }

        return [
            'rides' => $rides,
            'revenue' => $revenue,
            'bookings' => $bookings,
            'categories' => $categories
        ];
    }
    
    
    public function index(Request $request){
         return view('website.index');
    }
    
    public function web_privacy_policy(Request $request){
        $data = get_option_all_data('privacy_policy');
        return view('website.privacy-policy', compact('data'));
    }
    
    public function web_terms_conditions(Request $request){
        $data = get_option_all_data('terms_condition');
        return view('website.term-condition', compact('data'));
    }
    
    public function web_cancel_policy(Request $request){
        return view('website.cancel-policy');
    }
    
    public function web_refund_policy(Request $request){
        return view('website.refund-policy');
    }
    
    public function login(Request $request)
    {
        if($request->isMethod('post'))
        {  
            
            $validator = Validator::make($request->all(),[
                'email'      => 'required|',
                'password'   =>'required',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
                
             }else{
                 
                $userData = Admin::where('email', $request->email)->orWhere('username', $request->email)->first();

                if($userData == null){
                    return response()->json([
                        "status" => "false",
                        "msg" => ['email'=>['Email or Username is not registered with us !']],
                    ]);
                }
                
                if(!(Hash::check(request('password'), $userData->password))){
                    return response()->json([
                        "status" => "false",
                        "msg" => ['password'=>['Your password is incorrect']],
                    ]);
                }
                
                $cred['email'] = $userData->email;
                $cred['password'] = $request->password;
                
                if (Auth::attempt($cred))
                {
                    return response()->json([
                        "status" => "true",
                        "msg" => 'You Have Successfully Signup...!',
                        "return_url" => url('admin/dashboard'),
                    ]);
                }
                else
                {
                    return response()->json([
                        "status" => "false",
                        "msg" => ['email'=> ['Invalid Credentials !']],
                    ]);
                }
            }
        }
        
        if(auth()->user()){
            return redirect('admin/dashboard');
        }
        return view('admin.login');
    }
    
    public function profile(Request $request){
        
        $admin_id = auth()->user()->id;
        
        if ($request->isMethod('post')) {
            
            if($request->update_type == "profile_update"){
                
                $validator=Validator::make($request->all(),[
                    'name' => 'required',
                    'email' => 'required',
                    'mobile' => 'required',
                ]);
        
                if($validator->fails())
                {
                    return response()->json([
                        "status" => "false",
                        "msg" => $validator->errors(),
                    ]);
                }
    
                $update_data = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                ];
                
                if ($request->hasFile('profile')) {
                    $image = $request->file('profile');
                    $img_name = time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/assets/admin_profile/');
                    $image->move($destinationPath, $img_name);
                    $update_data['profile'] = $img_name;
                }
                
                if($request->password != ""){
                    $update_data['password'] = bcrypt($request->password);
                }
                
            }else{
                
                $validator=Validator::make($request->all(),[
                    'password' => 'required',
                    'newpassword' => 'required',
                    'renewpassword' => 'required',
                ]);
        
                if($validator->fails())
                {
                    return response()->json([
                        "status" => "false",
                        "msg" => $validator->errors(),
                    ]);
                }
                
                $userData = Admin::where('id', $admin_id)->first();

                if(($request->newpassword != $request->renewpassword)){
                    return response()->json([
                        "status" => "false",
                        "msg" => ['renewpassword'=>['Your New Password and Renew Passworde not Matched !']],
                    ]);
                }
                
                if(!(Hash::check(request('password'), $userData->password))){
                    return response()->json([
                        "status" => "false",
                        "msg" => ['password'=>['Your password is incorrect']],
                    ]);
                }
    
                $update_data = [
                    'password' => Hash::make($request->newpassword),
                ];
                
            }
            
            DB::table('admins')->where('id', $admin_id)->update($update_data);
            
            return response()->json([
                "status" => "true",
                "msg" => "Updated successfully !",
                "return_url" => "",
            ]);
    
        }
        
        $admin_data = DB::table('admins')->where('id', $admin_id)->first();
        
        return view('admin.profile', compact('admin_data'));
        
    }

   
    /********************Logout *************************/
    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        Cache::flush();
        return redirect('admin/login');
        
    }
    
}
