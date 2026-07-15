<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash;
use DataTables;
use Toastr;
use Validator;
use Carbon\Carbon;
use App\Models\{User};

use Illuminate\Database\Query\Builder;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    
    
    //////////////  Dashboard Section Start ///////////////
    
    public function index(Request $request)
    {
        // ── Row 1 stats ──
        $all_users      = DB::table('users')->where('type', '1')->count();
        $all_drivers    = DB::table('users')->where('type', '2')->count();
        $all_earnings   = DB::table('list_subscription')->sum('admin_amount');
        $complete_bookings = DB::table('bookings')->where('booking_status', '5')->count();
 
        $today_rides    = DB::table('bookings')->whereDate('created_at', Carbon::today())->count();
        $today_earnings = DB::table('bookings')
                            ->whereDate('created_at', Carbon::today())
                            ->sum('fare');
 
        // ── Row 2 stats ──
        $drivers_online   = DB::table('users')->where('type', '2')->where('is_live', 1)->count();
        $pending_rides    = DB::table('bookings')->where('booking_status', '1')->count();
        $active_sos       = DB::table('sos_alerts')->where('status', 'active')->count();
        $scheduled_today  = DB::table('bookings')
                              ->where('booking_type', '2')
                              ->whereDate('booking_date', Carbon::today())
                              ->count();
 
        // ── Rides by category ──
        $bike_rides  = DB::table('bookings as b')
                        ->leftJoin('vehicle_type as t', 't.id', '=', 'b.vechicle_id')
                        ->where('b.booking_status', '5')
                        ->where('t.type', 'bike')->count();
                        
        $auto_rides = DB::table('bookings as b')
                        ->leftJoin('vehicle_type as t', 't.id', '=', 'b.vechicle_id')
                        ->where('b.booking_status', '5')
                        ->where('t.type', 'auto')->count();
                        
        $car_rides  = DB::table('bookings as b')
                        ->leftJoin('vehicle_type as t', 't.id', '=', 'b.vechicle_id')
                        ->where('b.booking_status', '5')
                        ->where('t.type', 'car')->count();
                        
        $minibus_rides  = DB::table('bookings')
                        ->where('booking_status', '5')
                        ->where('vechicle_id', 'minibus')->count();
 
        // ── Recent rides ──
        $recent_rides = DB::table('bookings')
                          ->where('booking_status', '5')
                          ->orderBy('created_at', 'desc')
                          ->take(6)
                          ->get();
                          
        // dd($recent_rides);                  
 
        // ── Live driver locations ──
        $driver_locations = DB::table('users')
                              ->where('type', '2')
                              ->where('is_live', 1)
                              ->whereNotNull('latitude')
                              ->whereNotNull('longtitude')
                              ->select('name', 'latitude as lat', 'longtitude as lng')
                              ->get();
 
        // ── Chart data (default: this year) ──
        $chart_data = $this->getChartData('year');
 
        return view('admin.index', compact(
            'all_users', 'all_drivers', 'all_earnings', 'complete_bookings',
            'today_rides', 'today_earnings',
            'drivers_online', 'pending_rides', 'active_sos', 'scheduled_today',
            'bike_rides', 'auto_rides', 'car_rides', 'minibus_rides',
            'recent_rides', 'driver_locations', 'chart_data'
        ));
    }
 
    public function chartData(Request $request)
    {
        $data = $this->getChartData($request->get('filter', 'year'));
        return response()->json($data);
    }
 
    private function getChartData(string $filter): array
    {
        $now = Carbon::now();
 
        switch ($filter) {
            case 'today':
                $periods = collect(range(0, 23))->map(fn($h) => $now->copy()->startOfDay()->addHours($h));
                $format  = 'Y-m-d H:00:00';
                $label   = fn($p) => $p->format('Y-m-d\TH:i:s\Z');
                $group   = "DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00')";
                $start   = $now->copy()->startOfDay();
                $end     = $now->copy()->endOfDay();
                break;
 
            case 'month':
                $days    = $now->daysInMonth;
                $periods = collect(range(1, $days))->map(fn($d) => $now->copy()->startOfMonth()->addDays($d - 1));
                $format  = 'Y-m-d';
                $label   = fn($p) => $p->format('Y-m-d\T00:00:00\Z');
                $group   = "DATE(created_at)";
                $start   = $now->copy()->startOfMonth();
                $end     = $now->copy()->endOfMonth();
                break;
 
            default: // year
                $periods = collect(range(1, 12))->map(fn($m) => Carbon::create($now->year, $m, 1));
                $format  = 'Y-m';
                $label   = fn($p) => $p->format('Y-m-01\T00:00:00\Z');
                $group   = "DATE_FORMAT(created_at, '%Y-%m')";
                $start   = $now->copy()->startOfYear();
                $end     = $now->copy()->endOfYear();
                break;
        }
 
        $ridesRaw = DB::table('bookings')
            ->selectRaw("$group as period, COUNT(*) as total")
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw($group)
            ->pluck('total', 'period');
 
        $revenueRaw = DB::table('bookings')
            ->selectRaw("$group as period, SUM(fare) as total")
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw($group)
            ->pluck('total', 'period');
 
        $bookingsRaw = DB::table('bookings')
            ->selectRaw("$group as period, COUNT(*) as total")
            ->where('booking_status', '5')
            ->whereBetween('created_at', [$start, $end])
            ->groupByRaw($group)
            ->pluck('total', 'period');
 
        $rides      = [];
        $revenue    = [];
        $bookings   = [];
        $categories = [];
 
        foreach ($periods as $period) {
            $key          = $period->format($format);
            $categories[] = $label($period);
            $rides[]      = (int)   ($ridesRaw[$key]    ?? 0);
            $revenue[]    = (float) ($revenueRaw[$key]  ?? 0);
            $bookings[]   = (int)   ($bookingsRaw[$key] ?? 0);
        }
 
        return compact('rides', 'revenue', 'bookings', 'categories');
    }
    
    public function normal_users(Request $request)
    {
        $users = DB::table('users')->where('type', '1')->orderBy('id', 'DESC')->get();
        return view('admin.users', compact('users'));
    }
    
    public function awative_drivers(Request $request)
    {
        $doc_status1 =0;
        
        $data = DB::table('users')
            ->leftJoin('driver_details_basic_details as b', 'users.id', '=', 'b.driver_id')
            ->leftJoin('vehicle_type as vt', 'vt.id', '=', 'b.vehicle_type')
                ->select('users.*','b.driver_id','b.vehicle_number','b.vehicle_type', 'vt.name as vehicle_type_name', 'b.id as detail_id') 
            ->where(function($query) use ($doc_status1) {
                $query->where('doc_status1',$doc_status1)
                      ->orWhere('doc_status2', $doc_status1)
                      ->orWhere('doc_status3', $doc_status1)
                      ->orWhere('doc_status4', $doc_status1)
                      ->orWhere('doc_status5', $doc_status1);
            })
            ->where('users.type',2);
            
            if((isset($request->fromdate) && $request->fromdate != "") && (isset($request->enddate) && $request->enddate != "")){
                $data->whereBetween('users.created_at', [$request->fromdate, $request->enddate]);
            }

        $data = $data->orderBy('users.id','DESC')->get();

        return view('admin.awative_drivers', compact('data'));
    }
    
    public function user_change_status(Request $request){
        
        $users = DB::table('users')->where('id', $request->id)->first();
        if($users){
            $status = ($users->status == "1")?"0":"1";
            DB::table('users')->where('id', $users->id)->update(['status' => $status]);
        }
        return back()->with('success', 'Status change successfully!');
        
    }
    
    public function cancel_reason_change_status(Request $request){
        
        $users = DB::table('reason_cancel')->where('id', $request->id)->first();
        if($users){
            $status = ($users->status == "1")?"0":"1";
            DB::table('reason_cancel')->where('id', $users->id)->update(['status' => $status]);
        }
        return back()->with('success', 'Status change successfully!');
        
    }
    
    public function faqs_change_status(Request $request){
        
        $users = DB::table('faqs')->where('id', $request->id)->first();
        if($users){
            $status = ($users->status == "1")?"0":"1";
            DB::table('faqs')->where('id', $users->id)->update(['status' => $status]);
        }
        return back()->with('success', 'Status change successfully!');
        
    }

    public function getUserDetails(Request $request)
    {
        $id = $request->id;
        $user = DB::table('users')->select('name', 'email', 'mobile')->where('id', $id)->first();
    
        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['error' => 'User not found'], 404);
        }
    }
    
    public function edit_user(Request $request){
        
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'name' => 'required',
                'email' => 'required',
                'mobile' => 'required',
                'dob' => 'required',
                'status' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }
            
            if($request->password != $request->confirmPassword){
                return response()->json([
                    "status" => "false",
                    "msg" => ['confirmPassword'=>['Confirm Passsword not matched !']],
                ]);
            }
            
            $data = [
                'name' => $request->name,
                'email' => $request->email,  
                'mobile_no' => $request->mobile,
                'status' => $request->status, 
                'dob' => $request->dob,
            ];
            
            if($request->password != ""){
                $data['password'] = Hash::make($request->password);
                $data['confirm_password'] = $request->confirmPassword;
            }
            
            if ($request->hasFile('profile')) {
                $image = $request->file('profile');
                $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('assets/profile/'), $filename);
                $data['profile'] = $filename;
            }
            
            // dd($data);
            
            $id = DB::table('users')->where('id', $request->id)->update($data);

            return response()->json([
                "status" => "true",
                "msg" => 'Update Successfully !',
                "return_url" => "",
            ]);

        }
        
        $user = DB::table('users')->where('id', $request->id)->first();
        return view('admin.edit_user', compact('user'));
        
    }
    
    public function edit_driver_details(Request $request, $id)
    {
        $driver = DB::table('users')->where('id', $id)->where('type', 2)->first();
        if (!$driver) abort(404);
    
        $states = DB::table('states')->get();
        $cities = DB::table('cities')->get();
    
        return view('admin.edit_driver_details', compact('driver', 'states', 'cities'));
    }
    
    public function update_driver_details(Request $request, $id)
    {
        $request->validate([
            'name'                     => 'required|string|max:255',
            'email'                    => 'nullable|email|unique:users,email,' . $id,
            'mobile_no'                => 'required|string|max:30|unique:users,mobile_no,' . $id,
            'state'                    => 'nullable|integer',
            'city'                     => 'nullable|integer',
            'password'                 => 'nullable|min:6',
            'govt_id_type'             => 'required|in:passport,national_id,driving_license',
            'govt_id_number'           => 'required|string|max:100',
            'driving_licence_number'   => 'required|string|max:100',
            'driving_licence_expiry'   => 'required|date',
            'licence_document'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bank_account_number'      => 'required|string|max:50',
            'bank_name'                => 'required|string|max:255',
            'account_holder_name'      => 'required|string|max:255',
            'status'                   => 'required|in:0,1',
        ]);
    
        $updateData = [
            'name'                    => $request->name,
            'email'                   => $request->email,
            'mobile_no'               => $request->mobile_no,
            'gender'                  => $request->gender ?? 0,
            'dob'                     => $request->dob,
            'address'                 => $request->address,
            'state'                   => $request->state,
            'city'                    => $request->city,
            'pincode'                 => $request->pincode,
            'about_us'                => $request->about_us,
            'govt_id_type'            => $request->govt_id_type,
            'govt_id_number'          => $request->govt_id_number,
            'driving_licence_number'  => $request->driving_licence_number,
            'driving_licence_expiry'  => $request->driving_licence_expiry,
            'bank_account'     => $request->bank_account_number,
            'bank_name'               => $request->bank_name,
            'bank_ac_holder_name'     => $request->account_holder_name,
            'status'                  => $request->status,
            'updated_at'              => now(),
        ];
    
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }
    
        $oldDriver = DB::table('users')->where('id', $id)->first();
    
        if ($request->hasFile('profile')) {
            $file     = $request->file('profile');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('profile_image'), $filename);
    
            if (!empty($oldDriver->profile)) {
                $oldPath = public_path('profile_image/' . $oldDriver->profile);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
    
            $updateData['profile'] = $filename;
        }
    
        if ($request->hasFile('licence_document')) {
            $licFile     = $request->file('licence_document');
            $licFilename = time() . '_licence_' . $licFile->getClientOriginalName();
            $licFile->move(public_path('licence_documents'), $licFilename);
    
            if (!empty($oldDriver->licence_document)) {
                $oldLicPath = public_path('licence_documents/' . $oldDriver->licence_document);
                if (file_exists($oldLicPath)) {
                    @unlink($oldLicPath);
                }
            }
    
            $updateData['licence_document'] = $licFilename;
        }
    
        DB::table('users')->where('id', $id)->update($updateData);
    
        return redirect('admin/approved_drivers_list')
            ->with('success', 'Driver details updated successfully!');
    }
    
    public function suspend_driver(Request $request, $id)
    {
        $request->validate([
            'suspension_reason' => 'required|string|max:1000',
        ]);
    
        DB::table('users')->where('id', $id)->update([
            'is_suspended'      => 1,
            'status'            => 0,
            'is_live'           => 0,
            'suspension_reason' => $request->suspension_reason,
            'suspended_at'      => now(),
            'updated_at'        => now(),
        ]);
    
        return redirect()->back()->with('success', 'Driver suspended successfully.');
    }
    
    public function reactivate_driver($id)
    {
        DB::table('users')->where('id', $id)->update([
            'is_suspended'      => 0,
            'status'            => 1,
            'suspension_reason' => null,
            'suspended_at'      => null,
            'updated_at'        => now(),
        ]);
    
        return redirect()->back()->with('success', 'Driver reactivated successfully.');
    }
    
    public function delete_user(Request $request){
        
        try {
            $userId = $request->id;
            
            DB::table('users')->where('id', $userId)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
        
    }
    
    public function delete_faqs(Request $request){
        
        try {
            $userId = $request->id;
            
            DB::table('faqs')->where('id', $userId)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
        
    }
    
    
    /////////////////////// About Us ////////////////////////////////////
    
    public function about_us(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'key' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            set_option_data($request->key, $request->title, $request->description);
            
            return response()->json([
                "status" => "true",
                "msg" => "Updated successfully !",
                "return_url" => "",
            ]);
    
        }

        $data = get_option_all_data('about_us');
        return view('admin.about_us', compact('data'));
    }
    
    
    public function contact_us(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'email' => 'required',
                'phone' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            set_option_data("contact_email", "Contact Email", $request->email);
            set_option_data("contact_phone", "Contact Phone", $request->phone);
            
            return response()->json([
                "status" => "true",
                "msg" => "Updated successfully !",
                "return_url" => "",
            ]);
    
        }

        $email = get_option_data('contact_email');
        $phone = get_option_data('contact_phone');
        return view('admin.contact_us', compact('email', 'phone'));
    }
    
    public function terms_conditions(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'key' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            set_option_data($request->key, $request->title, $request->description);
            
            return response()->json([
                "status" => "true",
                "msg" => "Updated successfully !",
                "return_url" => "",
            ]);
    
        }

        $data = get_option_all_data('terms_condition');
        return view('admin.terms_condition', compact('data'));
    }
    
    public function terms_conditions_driver(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'key' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            set_option_data($request->key, $request->title, $request->description);
            
            return response()->json([
                "status" => "true",
                "msg" => "Updated successfully !",
                "return_url" => "",
            ]);
    
        }

        $data = get_option_all_data('terms_conditions_driver');
        return view('admin.terms_conditions_driver', compact('data'));
    }
    
    public function privacy_policy(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'key' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            set_option_data($request->key, $request->title, $request->description);
            
            return response()->json([
                "status" => "true",
                "msg" => "Updated successfully !",
                "return_url" => "",
            ]);
    
        }

        $data = get_option_all_data('privacy_policy');
        return view('admin.privacy_policy', compact('data'));
    }
    
    
    public function privacy_policy_driver(Request $request)
    {
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'key' => 'required',
                'title' => 'required',
                'description' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            set_option_data($request->key, $request->title, $request->description);
            
            return response()->json([
                "status" => "true",
                "msg" => "Updated successfully !",
                "return_url" => "",
            ]);
    
        }

        $data = get_option_all_data('privacy_policy_driver');
        return view('admin.privacy_policy_driver', compact('data'));
    }
    
    public function ride_cancel_reasons(Request $request){
        
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'name' => 'required',
                'status' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            $data = [
                'reason' => $request->name,
                'status' => $request->status,
            ];
            
            if($request->update_id != ""){
                DB::table('reason_cancel')->where('id', $request->update_id)->update($data);
                $msg = "Updated Successfully !";
            }else{
                DB::table('reason_cancel')->insert($data);
                $msg = "Added Successfully !";
            }
            
            return response()->json([
                "status" => "true",
                "msg" => $msg,
                "return_url" => "",
            ]);
    
        }

        $data = DB::table('reason_cancel')->orderBy('id', 'DESC')->get();
        return view('admin.ride_cancel_reasons', compact('data'));
        
    }
    
    public function delete_reason(Request $request){
        
        try {
            $userId = $request->id;
            
            DB::table('reason_cancel')->where('id', $userId)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'reason deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting : ' . $e->getMessage()
            ], 500);
        }
        
    }
    
    public function faqs(Request $request){
        
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'type' => 'required',
                'questions' => 'required',
                'answer' => 'required',
                'status' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            $data = [
                'type' => $request->type,
                'questions' => $request->questions,
                'answers' => $request->answer,
                'status' => $request->status,
            ];
            
            if($request->update_id != ""){
                DB::table('faqs')->where('id', $request->update_id)->update($data);
                $msg = "Updated Successfully !";
            }else{
                DB::table('faqs')->insert($data);
                $msg = "Added Successfully !";
            }
            
            return response()->json([
                "status" => "true",
                "msg" => $msg,
                "return_url" => "",
            ]);
    
        }

        $data = DB::table('faqs')->orderBy('id', 'DESC')->get();
        return view('admin.faqs', compact('data'));
        
    }
    
    public function show_drivers_map(Request $request){
        
        $allUsers = DB::table('users')->select('id', 'name', 'latitude', 'longtitude as longitude')->where('type', '2')->where('is_live', '1')->get();
        
        return view('admin.drivers_map', compact('allUsers'));
    }
    
    public function driverLocations(Request $request)
    {
        $allUsers = DB::table('users')->select('name', 'latitude', 'longtitude as longitude', 'location_change_time')
                        ->whereNotNull('latitude')
                        ->whereNotNull('longtitude')
                        ->where('type', '2')
                        ->where('is_live', '1')
                        ->get();
        
        return response()->json(["data" => $allUsers]);
    }
    
    public function approved_drivers_list(Request $request)
    {

        $doc_status =1;
        $data = DB::table('users')
            ->leftJoin('driver_details_basic_details', 'users.id', '=', 'driver_details_basic_details.driver_id')
            ->leftJoin('cities', 'users.city', '=', 'cities.id')
            ->leftJoin('states', 'users.state', '=', 'states.id')
            ->leftJoin('vehicle_type as v', 'v.id', '=', 'driver_details_basic_details.vehicle_type')
            ->select('users.*','driver_details_basic_details.driver_id','driver_details_basic_details.vehicle_number',
            'driver_details_basic_details.vehicle_type','cities.name as city_name','states.state_name' , 'v.name as vehicle_name');
            $data = $data->where('users.type',2)
            ->orderBy('users.id','DESC')
            ->get();   
            

        foreach($data as $res){
            $res->wallet_amount = get_wallet_balance($res->id);
        }
        
        return view('admin.approved_drivers_list', compact('data'));
     }
     
    public function active_drivers_list(Request $request)
    {

        $doc_status =1;
        $data = DB::table('users')
            ->leftJoin('driver_details_basic_details', 'users.id', '=', 'driver_details_basic_details.driver_id')
            ->leftJoin('cities', 'users.city', '=', 'cities.id')
            ->leftJoin('states', 'users.state', '=', 'states.id')
            ->leftJoin('vehicle_type as v', 'v.id', '=', 'driver_details_basic_details.vehicle_type')
            ->select('users.*','driver_details_basic_details.driver_id','driver_details_basic_details.vehicle_number',
            'driver_details_basic_details.vehicle_type','cities.name as city_name','states.state_name' , 'v.name as vehicle_name');
            $data = $data->where('users.type', 2)->where('users.is_live', 1)->where('users.is_suspended', 0)
            ->orderBy('users.id','DESC')
            ->get();   
            

        foreach($data as $res){
            $res->wallet_amount = get_wallet_balance($res->id);
        }
        
        return view('admin.active_drivers_list', compact('data'));
     }
     
    public function sespended_drivers_list(Request $request)
    {

        $doc_status =1;
        $data = DB::table('users')
            ->leftJoin('driver_details_basic_details', 'users.id', '=', 'driver_details_basic_details.driver_id')
            ->leftJoin('cities', 'users.city', '=', 'cities.id')
            ->leftJoin('states', 'users.state', '=', 'states.id')
            ->leftJoin('vehicle_type as v', 'v.id', '=', 'driver_details_basic_details.vehicle_type')
            ->select('users.*','driver_details_basic_details.driver_id','driver_details_basic_details.vehicle_number',
            'driver_details_basic_details.vehicle_type','cities.name as city_name','states.state_name' , 'v.name as vehicle_name');
            $data = $data->where('users.type', 2)->where('users.is_suspended', 1)
            ->orderBy('users.id','DESC')
            ->get();   
            

        foreach($data as $res){
            $res->wallet_amount = get_wallet_balance($res->id);
        }
        
        return view('admin.sespended_drivers_list', compact('data'));
     }
     
    public function add_new_drivers(Request $request)
    {
        $states  = DB::table('states')->get();
        $cities  = DB::table('cities')->get();
    
        $unassigned_vehicles = DB::table('driver_details_basic_details as d')
            ->leftJoin('vehicle_type as vt', 'vt.id', '=', 'd.vehicle_type')
            ->select('d.*', 'vt.name as vehicle_type_name')
            ->where(function ($q) {
                $q->whereNull('d.driver_id')
                  ->orWhere('d.driver_id', '')
                  ->orWhere('d.driver_id', 0);
            })
            ->get();
    
        return view('admin.add_new_drivers', compact('states', 'cities', 'unassigned_vehicles'));
    }
    
    public function store_new_driver(Request $request)
    {
        $request->validate([
            'name'                    => 'required|string|max:255',
            'email'                   => 'nullable|email|unique:users,email',
            'mobile_no'               => 'required|string|max:30|unique:users,mobile_no',
            'password'                => 'required|min:6',
            'state'                   => 'nullable|integer',
            'city'                    => 'nullable|integer',
            'vehicle_id'              => 'nullable|integer|exists:driver_details_basic_details,id',
            'govt_id_type'            => 'required|in:passport,national_id,driving_license',
            'govt_id_number'          => 'required|string|max:100',
            'driving_licence_number'  => 'required|string|max:100',
            'driving_licence_expiry'  => 'required|date|after:today',
            'licence_document'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bank_account_number'     => 'required|string|max:50',
            'bank_name'               => 'required|string|max:255',
            'account_holder_name'     => 'required|string|max:255',
            'status'                  => 'required|in:0,1',
        ]);
    
        $profile_path = '';
        if ($request->hasFile('profile')) {
            $file     = $request->file('profile');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('profile_image'), $filename);
            $profile_path = $filename;
        }
    
        $licence_doc_path = '';
        if ($request->hasFile('licence_document')) {
            $licFile     = $request->file('licence_document');
            $licFilename = time() . '_licence_' . $licFile->getClientOriginalName();
            $licFile->move(public_path('vehicle_image/driving_licence'), $licFilename);
            $licence_doc_path = $licFilename;
        }
    
        $sendCredentials = $request->has('send_credentials') ? 1 : 0;
    
        $userId = DB::table('users')->insertGetId([
            'type'                    => 2,
            'name'                    => $request->name,
            'email'                   => $request->email,
            'mobile_no'               => $request->mobile_no,
            'password'                => bcrypt($request->password),
            'gender'                  => $request->gender ?? 0,
            'dob'                     => $request->dob ? date('d-m-Y', strtotime($request->dob)) : null,
            'address'                 => $request->address,
            'state'                   => $request->state,
            'city'                    => $request->city,
            'pincode'                 => $request->pincode,
            'profile'                 => $profile_path,
            'govt_id_type'            => $request->govt_id_type,
            'govt_id_number'          => $request->govt_id_number,
            'driving_licence_number'  => $request->driving_licence_number,
            'driving_licence_expiry'  => $request->driving_licence_expiry,
            'licence_document'        => $licence_doc_path,
            'bank_account'            => $request->bank_account_number,
            'bank_name'               => $request->bank_name,
            'bank_ac_holder_name'     => $request->account_holder_name,
            'status'                  => $request->status,
            'send_credentials'        => $sendCredentials,
            'is_suspended'            => 0,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);
    
        if ($request->filled('vehicle_id')) {
            DB::table('driver_details_basic_details')
                ->where('id', $request->vehicle_id)
                ->update(['driver_id' => $userId]);
        }
    
        if ($sendCredentials) {
            $this->sendDriverCredentials($request->name, $request->email, $request->mobile_no, $request->password);
        }
    
        return redirect('admin/approved_drivers_list')
            ->with('success', 'Driver added successfully!');
    }
    
    private function sendDriverCredentials($name, $email, $mobile, $password)
    {
        if (!empty($email)) {
            try {
                \Mail::send('emails.driver_credentials', [
                    'name'     => $name,
                    'mobile'   => $mobile,
                    'password' => $password,
                ], function ($message) use ($email, $name) {
                    $message->to($email, $name)
                            ->subject('Your Driver Account Credentials');
                });
            } catch (\Exception $e) {
                \Log::error('Failed to send credential email: ' . $e->getMessage());
            }
        }
    
        try {
            $smsText = "Welcome {$name}! Your login mobile: {$mobile}, password: {$password}";
            SmsService::send($mobile, $smsText);
        } catch (\Exception $e) {
            \Log::error('Failed to send credential SMS: ' . $e->getMessage());
        }
    }
     
     
    public function driver_face_verifications($id)
    {
        $driver = DB::table('users')->where('id', $id)->where('type', 2)->first();
        if (!$driver) abort(404);
    
        $verifications = DB::table('face_verification_logs')
            ->where('driver_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
    
        return view('admin.driver_face_verifications', compact('driver', 'verifications'));
    } 

    public function assign_vehicle(Request $request, $driver_id)
    {
        $driver = DB::table('users')->where('id', $driver_id)->where('type', 2)->first();
        if (!$driver) abort(404);
     
        $vehicle_detail = DB::table('driver_details_basic_details as d')
            ->leftJoin('vehicle_type as vt', 'vt.id', '=', 'd.vehicle_type')
            ->select('d.*', 'vt.name as vehicle_type_name')
            ->where('d.driver_id', $driver_id)
            ->first();
     
        $unassigned_vehicles = DB::table('driver_details_basic_details as d')
            ->leftJoin('vehicle_type as vt', 'vt.id', '=', 'd.vehicle_type')
            ->select('d.*', 'vt.name as vehicle_type_name')
            ->where(function ($q) use ($driver_id) {
                $q->whereNull('d.driver_id')
                  ->orWhere('d.driver_id', '')
                  ->orWhere('d.driver_id', 0)
                  ->orWhere('d.driver_id', $driver_id); // include currently assigned
            })
            ->get();
     
        return view('admin.assign_vehicle',
            compact('driver', 'vehicle_detail', 'unassigned_vehicles'));
    }
     

    public function save_assign_vehicle(Request $request, $driver_id)
    {
        $request->validate([
            'vehicle_id' => 'nullable|integer|exists:driver_details_basic_details,id',
        ]);
     
        DB::table('driver_details_basic_details')
            ->where('driver_id', $driver_id)
            ->update(['driver_id' => null]);
     
        if ($request->filled('vehicle_id')) {
            DB::table('driver_details_basic_details')
                ->where('id', $request->vehicle_id)
                ->update(['driver_id' => $driver_id]);
     
            $msg = 'Vehicle assigned successfully!';
        } else {
            $msg = 'Vehicle unassigned successfully!';
        }
     
        return redirect('admin/approved_drivers_list')
            ->with('success', $msg);
    }
     
     public function driver_details(Request $req,$id){

           $data['driver_data'] = DB::table('users')->select('users.*','cities.name as city_name','states.state_name')
                               ->leftJoin('states', 'users.state', '=', 'states.id')
                               ->leftJoin('cities', 'users.city', '=', 'cities.id')->where('users.id', $id)->first();
           
           $d_id =$data['driver_data']->id??'';
           
           $data['vehicle_details'] = DB::table('driver_details_basic_details as d')
                                    ->select('d.*', 'vt.name as vehicle_type_name', 'vv.varient_name', 'vm.name as vehicle_model_name')
                                    ->leftJoin('vehicle_type as vt', 'vt.id', '=', 'd.vehicle_type')
                                    ->leftJoin('vehicle_varients as vv', 'vv.id', '=', 'd.vehicle_fuel_type')
                                    ->leftJoin('vehicle_models_name as vm', 'vm.id', '=', 'd.vehicle_model')
                                    ->where('driver_id',$d_id)->first();
           
           $data['wallet_amount'] = get_wallet_balance($id);                                 
           $data['driver_id'] = $id;

           return view('admin.driver_details',$data);
        
    }
    
    
    public function driver_licence_status(Request $request)
    {  
            $id     =  $request->id;
            $status_data =  $request->status;
            $status = $status_data == 0 ? 1 : 0;
          
           $data = DB::table('driver_details_basic_details')->where('id', $id)->update(array('doc_status1'=>$status));
            
    return response()->json(["data" => $data]);
        
        
    }
    
    public function driver_idproof_status(Request $request)
    {  
            $id     =  $request->id;
            $status_data =  $request->status;
            $status = $status_data == 0 ? 1 : 0;
          
           $data = DB::table('driver_details_basic_details')->where('id', $id)->update(array('doc_status2'=>$status));
            
    return response()->json(["data" => $data]);
        
        
    }
    public function driver_rc_status(Request $request)
    {  
            $id     =  $request->id;
            $status_data =  $request->status;
            $status = $status_data == 0 ? 1 : 0;
          
           $data = DB::table('driver_details_basic_details')->where('id', $id)->update(array('doc_status3'=>$status));
            
    return response()->json(["data" => $data]);
        
        
    }
    public function driver_pollution_status(Request $request)
    {  
            $id     =  $request->id;
            $status_data =  $request->status;
            $status = $status_data == 0 ? 1 : 0;
          
           $data = DB::table('driver_details_basic_details')->where('id', $id)->update(array('doc_status4'=>$status));
            
    return response()->json(["data" => $data]);
        
        
    }
    public function driver_audit_status(Request $request)
    {  
            $id     =  $request->id;
            $status_data =  $request->status;
            $status = $status_data == 0 ? 1 : 0;
          
           $data = DB::table('driver_details_basic_details')->where('id', $id)->update(array('doc_status5'=>$status));
            
    return response()->json(["data" => $data]);
        
        
    }
    
    public function change_reupload_status(Request $request)
    {  
            $id     =  $request->id;
            $status_data =  $request->status;
            $status = $status_data == 0 ? 1 : 0;
          
            $data = DB::table('driver_details_basic_details')->where('id', $id)->update(array($request->column_name=>$status));
            
            if($status == "0"){
                
                $reg_data = DB::table('driver_details_basic_details')->where('id', $id)->first();
                
                if($request->column_name == "doc1_reupload"){
                    $document_name = "Driving Licence";
                }
                else if($request->column_name == "doc2_reupload"){
                    $document_name = "Driving Licence Back";
                }
                else if($request->column_name == "doc3_reupload"){
                    $document_name = "Driver Id Proof";
                }
                else if($request->column_name == "doc4_reupload"){
                    $document_name = "Driver Id Proof Back";
                }
                else if($request->column_name == "doc5_reupload"){
                    $document_name = "RC Front Image";
                }
                else if($request->column_name == "doc6_reupload"){
                    $document_name = "RC Back Image";
                }
                else if($request->column_name == "doc7_reupload"){
                    $document_name = "Pollution certificate Front";
                }
                else if($request->column_name == "doc8_reupload"){
                    $document_name = "Pollution certificate Back";
                }
                else if($request->column_name == "doc9_reupload"){
                    $document_name = "Vehicle Audit Front";
                }
                else{
                    $document_name = "Vehicle Audit Back";
                }
                
                $title = "Re Upload Document";
                $message = "Please Re Upload ".$document_name." Document ";
                
                // send_firebase_notification($title, $message, $reg_data->driver_id);
            }
            
            return response()->json(["data" => $data]);

    }
    
     public function vehicle_all_types(Request $request){

        $vehicle = DB::table('vehicle_type')->orderBy('id','DESC')->get();
        return view('admin.vehicle_all_types', compact('vehicle')); 
    }
    
    ////////////////////////////////////////////////////////////////////////////
    
    public function all_vehicles(Request $request)
    {
        $query = DB::table('driver_details_basic_details as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.driver_id')
            ->leftJoin('vehicle_type as vt', 'vt.id', '=', 'd.vehicle_type')
            ->leftJoin('vehicle_models_name as vm', 'vm.id', '=', 'd.vehicle_model')
            ->select(
                'd.*',
                'u.name as driver_name',
                'u.mobile_no as driver_mobile',
                'u.email as driver_email',
                'vm.name as vehicle_model_name',
                'vt.name as vehicle_type_name'
            );
 
        if ($request->filled('assigned')) {
            if ($request->assigned == '1') {
                $query->where(function ($q) {
                    $q->whereNotNull('d.driver_id')->where('d.driver_id', '!=', '');
                });
            } else {
                $query->where(function ($q) {
                    $q->whereNull('d.driver_id')->orWhere('d.driver_id', '');
                });
            }
        }
 
        if ($request->filled('vehicle_type')) {
            $query->where('d.vehicle_type', $request->vehicle_type);
        }
 
        if ($request->filled('status')) {
            $query->where('d.doc_status1', $request->status);
        }
 
        if ($request->filled('fuel_type')) {
            $query->where('d.vehicle_fuel_type', $request->fuel_type);
        }
 
        $vehicles      = $query->orderBy('d.id', 'DESC')->get();
        $vehicle_types = DB::table('vehicle_type')->where('status', 1)->get();
 
        return view('admin.all_vehicles', compact('vehicles', 'vehicle_types'));
    }
 
    public function add_vehicle_page()
    {
        $vehicle_types  = DB::table('vehicle_type')->where('status', 1)->get();
        $vehicle_models = DB::table('vehicle_models_name')->where('status', 1)->get();
 
        return view('admin.add_vehicle', compact('vehicle_types', 'vehicle_models'));
    }
 
    public function add_vehicle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_number'      => 'required',
            'vehicle_name'        => 'required',
            'vehicle_manufacture' => 'required',
            'vehicle_model'       => 'required',
            'vehicle_type'        => 'required',
            'vehicle_reg_year'    => 'required',
            'vehicle_fuel_type'   => 'required',
            'seating_capicity'    => 'required',
            'vehicle_color'       => 'required',
        ]);
 
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'msg'    => $validator->errors(),
            ]);
        }
 
        $data = [
            'driver_id'           => '',
            'vehicle_number'      => $request->vehicle_number,
            'vehicle_name'        => $request->vehicle_name,
            'vehicle_manufacture' => $request->vehicle_manufacture,
            'vehicle_model'       => $request->vehicle_model,
            'vehicle_type'        => $request->vehicle_type,
            'vehicle_reg_year'    => $request->vehicle_reg_year,
            'vehicle_fuel_type'   => $request->vehicle_fuel_type,
            'vehicle_owner_name'  => $request->vehicle_owner_name ?? '',
            'country_operating'   => $request->country_operating ?? '',
            'doc_status1'         => $request->doc_status1 ?? 1,
            'doc_status2'         => 1,
            'doc_status3'         => 1,
            'seating_capicity'    => $request->seating_capicity,
            'vehicle_color'       => $request->vehicle_color,
        ];
 

        if ($request->hasFile('photo1')) {
            $image = $request->file('photo1');
            $img_name1 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name1);
            $data['certificate1'] = $img_name1;
        }
      
        if ($request->hasFile('photo2')) {
            $image = $request->file('photo2');
            $img_name2 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name2);
            $data['certificate2'] = $img_name2;
        }
       
        if ($request->hasFile('insurance')) {
            $image = $request->file('insurance');
            $img_name = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name);
            $data['photo1'] = $img_name2;
        }
     
       if ($request->hasFile('inspection')) {
            $image = $request->file('inspection');
            $img_name3 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name3);
            $data['photo2'] = $img_name3;
        }
      
      
        if ($request->hasFile('reg_front')) {
            $image = $request->file('reg_front');
            $img_name4 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name4);
            $data['id_proof'] = $img_name4;
        }
      
       if ($request->hasFile('reg_back')) {
            $image = $request->file('reg_back');
            $img_name5 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name5);
            $data['id_proof2'] = $img_name5;
        }
        
 
        DB::table('driver_details_basic_details')->insert($data);
 
        return response()->json([
            'status'     => 'true',
            'msg'        => 'Vehicle added successfully..!',
            'return_url' => url('admin/all_vehicles'),
        ]);
    }
 
    public function view_vehicle($id)
    {
        $vehicle = DB::table('driver_details_basic_details as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.driver_id')
            ->leftJoin('vehicle_type as vt', 'vt.id', '=', 'd.vehicle_type')
            ->where('d.id', $id)
            ->select('d.*', 'u.name as driver_name', 'u.mobile_no as driver_mobile', 'u.email as driver_email', 'vt.name as vehicle_type_name')
            ->first();
 
        if (!$vehicle) {
            return redirect(url('admin/all_vehicles'))->with('error', 'Vehicle not found.');
        }
 
        return view('admin.view_vehicle', compact('vehicle'));
    }
 
    public function edit_vehicle_page($id)
    {
        $vehicle = DB::table('driver_details_basic_details as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.driver_id')
            ->leftJoin('vehicle_type as vt', 'vt.id', '=', 'd.vehicle_type')
            ->where('d.id', $id)
            ->select(
                'd.*',
                'u.name as driver_name',
                'u.mobile_no as driver_mobile',
                'u.email as driver_email',
                'vt.name as vehicle_type_name'
            )
            ->first();
     
        if (!$vehicle) {
            return redirect(url('admin/all_vehicles'))->with('error', 'Vehicle not found.');
        }
     
        $vehicle_types = DB::table('vehicle_type')->where('status', 1)->get();
     
        return view('admin.edit_vehicle', compact('vehicle', 'vehicle_types'));
    }
     
    public function edit_vehicle(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'id'                  => 'required',
            'vehicle_number'      => 'required',
            'vehicle_name'        => 'required',
            'vehicle_manufacture' => 'required',
            'vehicle_model'       => 'required',
            'vehicle_type'        => 'required',
            'vehicle_reg_year'    => 'required',
            'vehicle_fuel_type'   => 'required',
            'seating_capicity'    => 'required',
            'vehicle_color'       => 'required',
        ]);
     
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'msg'    => $validator->errors(),
            ]);
        }
     
        $data = [
            'vehicle_number'      => $request->vehicle_number,
            'vehicle_name'        => $request->vehicle_name,
            'vehicle_manufacture' => $request->vehicle_manufacture,
            'vehicle_model'       => $request->vehicle_model,
            'vehicle_type'        => $request->vehicle_type,
            'vehicle_reg_year'    => $request->vehicle_reg_year,
            'vehicle_fuel_type'   => $request->vehicle_fuel_type,
            'vehicle_owner_name'  => $request->vehicle_owner_name ?? '',
            'country_operating'   => $request->country_operating ?? '',
            'doc_status1'         => $request->doc_status1 ?? 1,
            'seating_capicity'    => $request->seating_capicity,
            'vehicle_color'       => $request->vehicle_color,
        ];
     
        if ($request->hasFile('photo1')) {
            $image = $request->file('photo1');
            $img_name1 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name1);
            $data['certificate1'] = $img_name1;
        }
      
        if ($request->hasFile('photo2')) {
            $image = $request->file('photo2');
            $img_name2 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name2);
            $data['certificate2'] = $img_name2;
        }
       
        if ($request->hasFile('insurance')) {
            $image = $request->file('insurance');
            $img_name = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name);
            $data['photo1'] = $img_name2;
        }
     
       if ($request->hasFile('inspection')) {
            $image = $request->file('inspection');
            $img_name3 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name3);
            $data['photo2'] = $img_name3;
        }
      
      
        if ($request->hasFile('reg_front')) {
            $image = $request->file('reg_front');
            $img_name4 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name4);
            $data['id_proof'] = $img_name4;
        }
      
       if ($request->hasFile('reg_back')) {
            $image = $request->file('reg_back');
            $img_name5 = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_photo/');
            $image->move($destinationPath, $img_name5);
            $data['id_proof2'] = $img_name5;
        }
     
        DB::table('driver_details_basic_details')->where('id', $request->id)->update($data);
     
        return response()->json([
            'status' => 'true',
            'msg'    => 'Vehicle updated successfully..!',
        ]);
    }
 
 
    public function change_vehicle_status(Request $request)
    {
        $vehicle = DB::table('driver_details_basic_details')->where('id', $request->id)->first();
 
        if (!$vehicle) {
            return response()->json(['status' => 'false', 'msg' => 'Not found.']);
        }
 
        $newStatus = ($vehicle->doc_status1 == 1) ? 2 : 1;
        DB::table('driver_details_basic_details')->where('id', $request->id)->update(['doc_status1' => $newStatus]);
 
        return response()->json(['status' => 'true', 'new_status' => $newStatus]);
    }
 
    public function delete_vehicle(Request $request)
    {
        DB::table('driver_details_basic_details')->where('id', $request->id)->delete();
 
        return response()->json(['status' => 'true', 'msg' => 'Vehicle deleted successfully.']);
    }
 
    public function get_models_by_type(Request $request)
    {
        $models = DB::table('vehicle_models_name')
            ->where('type_id', $request->type_id)
            ->where('status', 1)
            ->get(['id', 'name']);
 
        return response()->json($models);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    
    public function delete_type_vehicle(Request $request){
        
        try {
            $userId = $request->id;
            
            DB::table('vehicle_type')->where('id', $userId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vehicle deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
        
    }
    
    public function change_vehicle_type_status(Request $request){
        
        $users = DB::table('vehicle_type')->where('id', $request->id)->first();
        if($users){
            $status = ($users->status == "1")?"0":"1";
            DB::table('vehicle_type')->where('id', $users->id)->update(['status' => $status]);
        }
        return back()->with('success', 'Status change successfully!');
        
    }
    
    public function add_type_vehicle(Request $request){
         $validator=Validator::make($request->all(),[
              'vehicle_category_type'=>'required',
              'vehicle_type'=>'required',
              'price'=>'required',
              'status'=>'required',
              'base_fare'=>'required',
        ]);

        if($validator->fails())
        {
            return response()->json([
                "status" => "false",
                "msg" => $validator->errors(),
            ]);
        }
        
        $img_name = "";                

        if ($request->hasFile('vehicle_image')) {
            $image = $request->file('vehicle_image');
            $img_name = time().rand(1,4). '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/vehicle_image/vehicle_type_image/');
            $image->move($destinationPath, $img_name);
        }
        
        $data=[
              'type'=>$request->vehicle_category_type,
              'name'=>$request->vehicle_type,
              'price'=>$request->price,
              'status'=>$request->status,
              'base_fare'=>$request->base_fare,
        ];
        
        if(isset($request->vid) && $request->vid != ""){
            if($img_name != ""){
                $data['image'] = $img_name;
            }
            DB::table('vehicle_type')->where('id', $request->vid)->update($data);
            $message = "Vehicle Type Update Successfully !";
        }else{
            $data['image'] = $img_name;
            DB::table('vehicle_type')->insert($data);
            $message = "Vehicle Type Added Successfully !";
        }
        
        return response()->json([
            "status" => "true",
            "msg" => $message,
            "return_url" => url('admin/vehicle_all_types'),
        ]);
    }
    
    public function pending_booking(Request $request){
       
        $filter = $request->input('filter', null);
        $complete_booking= DB::table('bookings')
            ->select('bookings.*','users.name','users.email','users.mobile_no',
            'u.name as driver_name','u.email as driver_email','u.mobile_no as driver_mobile',
            'vehicle_type.name as vehicle_name', 'l.admin_amount', 'l.driver_amount',
            'l.admin_commision_percentage', 'bs.name as booking_status_name', 'bs.id as booking_status_id')
            ->leftJoin('users','users.id','=','bookings.user_id')
            ->leftJoin('users as u','u.id','=','bookings.driver_id')
            ->leftJoin('vehicle_type','vehicle_type.id','=','bookings.vechicle_id')
            ->leftJoin('list_subscription as l','l.booking_id','=','bookings.booking_id')
            ->leftJoin('booking_status as bs','bs.id','=','bookings.booking_status')
            ->whereIN('bookings.booking_status', [0,1,2,4])
            ->where('bookings.booking_type',1);
    
        if($filter == "today"){
            $complete_booking->whereDate('bookings.created_at', Carbon::today());
        }else if($filter == "weekly"){
            $newDate = Carbon::now()->subDays(7)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }else if($filter == "monthly"){
            $newDate = Carbon::now()->subMonth(1)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }else if($filter == "yearly"){
            $newDate = Carbon::now()->subMonth(12)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }
        if($request->fromdate != '' && $request->enddate != ''){
            $complete_booking->whereBetween('bookings.created_at', [$request->fromdate, $request->enddate]);
        }
    
        $complete_booking = $complete_booking->orderBy('bookings.id',"DESC")->get()->toArray();
    
        return view('admin.pending_booking',compact('filter','complete_booking'));
    }

    /**
     * AJAX: return the booking_activity_log timeline for a single booking,
     * resolving driver/user names from the users table.
     */
    public function getBookingActivityLog(Request $request){
    
        $bookingId = $request->input('booking_id');
    
        $log = DB::table('booking_activity_log')->where('booking_id', $bookingId)->first();
    
        if(!$log){
            return response()->json(['status' => false, 'message' => 'No activity log found']);
        }
    
        // helper to fetch a user's name by id (works for both users and drivers,
        // since drivers are stored in the same `users` table)
        $getName = function($id) {
            if(empty($id)) return null;
            return DB::table('users')->where('id', $id)->value('name');
        };
    
        $timeline = [];
    
        // 1. Booking created
        $timeline[] = [
            'title'    => 'BOOKING CREATED',
            'by'       => $getName($log->user_id) ?? 'N/A',
            'datetime' => $log->booked_datetime,
            'status'   => 'CREATED',
            'color'    => 'secondary',
        ];
    
        // 2. Driver accepted
        if(!empty($log->accept_driver_id)){
            $timeline[] = [
                'title'    => 'DRIVER ACCEPTED',
                'by'       => $getName($log->accept_driver_id) ?? 'N/A',
                'datetime' => $log->accept_datetime,
                'status'   => 'UPCOMING',
                'color'    => 'info',
            ];
        }
    
        // 3. Passenger picked up
        if(!empty($log->pickup_driver_id)){
            $timeline[] = [
                'title'    => 'PASSENGER PICKED UP',
                'by'       => $getName($log->pickup_driver_id) ?? 'N/A',
                'datetime' => $log->pickup_datetime,
                'status'   => 'ONGOING',
                'color'    => 'primary',
            ];
        }
    
        // 4. Driver arrived
        if(!empty($log->arrived_driver_id)){
            $timeline[] = [
                'title'    => 'DRIVER ARRIVED',
                'by'       => $getName($log->arrived_driver_id) ?? 'N/A',
                'datetime' => $log->arrived_datetime,
                'status'   => 'ARRIVED',
                'color'    => 'warning',
            ];
        }
    
        // 5. OTP verified
        if(!empty($log->otp_driver_id)){
            $timeline[] = [
                'title'    => 'OTP VERIFIED',
                'by'       => $getName($log->otp_driver_id) ?? 'N/A',
                'datetime' => $log->otp_datetime,
                'status'   => 'VERIFIED',
                'color'    => 'info',
            ];
        }
    
        // 6. Ride completed
        if(!empty($log->complete_driver_id)){
            $timeline[] = [
                'title'    => 'RIDE COMPLETED',
                'by'       => $getName($log->complete_driver_id) ?? 'N/A',
                'datetime' => $log->complete_datetime,
                'status'   => 'COMPLETED',
                'color'    => 'success',
            ];
        }
    
        // 7. Driver payment
        if(!empty($log->driver_amount)){
            $timeline[] = [
                'title'    => 'DRIVER PAYMENT',
                'by'       => $getName($log->amount_driver_id) ?? 'N/A',
                'datetime' => null,
                'amount'   => $log->driver_amount,
                'status'   => null,
                'color'    => 'success',
            ];
        }
    
        // 8. Cancelled (by driver or user)
        if(!empty($log->cancel_type)){
            $cancelledBy = !empty($log->cancel_driver_id)
                ? $getName($log->cancel_driver_id)
                : $getName($log->cancel_user_id);
    
            $timeline[] = [
                'title'    => strtoupper($log->cancel_type) == 'DRIVER' ? 'DRIVER CANCEL' : 'USER CANCEL',
                'by'       => $cancelledBy ?? 'N/A',
                'datetime' => $log->cancel_datetime,
                'status'   => 'CANCEL',
                'color'    => 'danger',
            ];
        }
    
        return response()->json([
            'status'   => true,
            'booking_id' => $log->booking_id,
            'timeline' => $timeline,
        ]);
    }
    
    public function complete_booking(Request $request){
           
        $filter = $request->input('filter', null);
        $complete_booking= DB::table('bookings')
            ->select('bookings.*','users.name','users.email','users.mobile_no',
            'u.name as driver_name','u.email as driver_email','u.mobile_no as driver_mobile',
            'vehicle_type.name as vehicle_name', 'l.admin_amount', 'l.driver_amount',
            'l.admin_commision_percentage', 'bs.name as booking_status_name', 'bs.id as booking_status_id')
            ->leftJoin('users','users.id','=','bookings.user_id')
            ->leftJoin('users as u','u.id','=','bookings.driver_id')
            ->leftJoin('vehicle_type','vehicle_type.id','=','bookings.vechicle_id')
            ->leftJoin('list_subscription as l','l.booking_id','=','bookings.booking_id')
            ->leftJoin('booking_status as bs','bs.id','=','bookings.booking_status')
            ->whereIN('bookings.booking_status', [5])
            ->where('bookings.booking_type',1);
    
        if($filter == "today"){
            $complete_booking->whereDate('bookings.created_at', Carbon::today());
        }else if($filter == "weekly"){
            $newDate = Carbon::now()->subDays(7)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }else if($filter == "monthly"){
            $newDate = Carbon::now()->subMonth(1)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }else if($filter == "yearly"){
            $newDate = Carbon::now()->subMonth(12)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }
        if($request->fromdate != '' && $request->enddate != ''){
            $complete_booking->whereBetween('bookings.created_at', [$request->fromdate, $request->enddate]);
        }
    
        $complete_booking = $complete_booking->orderBy('bookings.id',"DESC")->get()->toArray();
    
        return view('admin.complete_booking',compact('filter','complete_booking'));
    }
    
    public function cancelled_booking(Request $request){
       
        $filter = $request->input('filter', null);
        $complete_booking= DB::table('bookings')
            ->select('bookings.*','users.name','users.email','users.mobile_no',
            'u.name as driver_name','u.email as driver_email','u.mobile_no as driver_mobile',
            'vehicle_type.name as vehicle_name', 'l.admin_amount', 'l.driver_amount',
            'l.admin_commision_percentage', 'bs.name as booking_status_name', 'bs.id as booking_status_id')
            ->leftJoin('users','users.id','=','bookings.user_id')
            ->leftJoin('users as u','u.id','=','bookings.driver_id')
            ->leftJoin('vehicle_type','vehicle_type.id','=','bookings.vechicle_id')
            ->leftJoin('list_subscription as l','l.booking_id','=','bookings.booking_id')
            ->leftJoin('booking_status as bs','bs.id','=','bookings.booking_status')
            ->whereIN('bookings.booking_status', [8, 6])
            ->where('bookings.booking_type',1);
    
        if($filter == "today"){
            $complete_booking->whereDate('bookings.created_at', Carbon::today());
        }else if($filter == "weekly"){
            $newDate = Carbon::now()->subDays(7)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }else if($filter == "monthly"){
            $newDate = Carbon::now()->subMonth(1)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }else if($filter == "yearly"){
            $newDate = Carbon::now()->subMonth(12)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }
        if($request->fromdate != '' && $request->enddate != ''){
            $complete_booking->whereBetween('bookings.created_at', [$request->fromdate, $request->enddate]);
        }
    
        $complete_booking = $complete_booking->orderBy('bookings.id',"DESC")->get()->toArray();
    
        return view('admin.cancelled_booking',compact('filter','complete_booking'));
    }
    
    public function schedule_booking(Request $request){
       
        $filter = $request->input('filter', null);
        $complete_booking= DB::table('bookings')
            ->select('bookings.*','users.name','users.email','users.mobile_no',
            'u.name as driver_name','u.email as driver_email','u.mobile_no as driver_mobile',
            'vehicle_type.name as vehicle_name', 'l.admin_amount', 'l.driver_amount',
            'l.admin_commision_percentage', 'bs.name as booking_status_name', 'bs.id as booking_status_id')
            ->leftJoin('users','users.id','=','bookings.user_id')
            ->leftJoin('users as u','u.id','=','bookings.driver_id')
            ->leftJoin('vehicle_type','vehicle_type.id','=','bookings.vechicle_id')
            ->leftJoin('list_subscription as l','l.booking_id','=','bookings.booking_id')
            ->leftJoin('booking_status as bs','bs.id','=','bookings.booking_status')
            // ->whereIN('bookings.booking_status', [8, 6])
            ->where('bookings.booking_type', 2);
    
        if($filter == "today"){
            $complete_booking->whereDate('bookings.created_at', Carbon::today());
        }else if($filter == "weekly"){
            $newDate = Carbon::now()->subDays(7)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }else if($filter == "monthly"){
            $newDate = Carbon::now()->subMonth(1)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }else if($filter == "yearly"){
            $newDate = Carbon::now()->subMonth(12)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
            $complete_booking->whereDate('bookings.created_at','>=',$newDate)->whereDate('bookings.created_at','<=',$toDate);
        }
        if($request->fromdate != '' && $request->enddate != ''){
            $complete_booking->whereBetween('bookings.created_at', [$request->fromdate, $request->enddate]);
        }
    
        $complete_booking = $complete_booking->orderBy('bookings.id',"DESC")->get()->toArray();
    
        return view('admin.schedule_booking',compact('filter','complete_booking'));
    }
    
    public function booking_payments(Request $request){

        $query = DB::table('list_subscription as l');
        $query->select('b.*','vt.name as vehicle_type_name','u.name','u.email','u.mobile_no','d.name as driver_name','d.email as driver_email',
          'd.mobile_no as driver_mobile', 'l.admin_commision_percentage', 'l.admin_amount', 'l.driver_amount', 'l.amount as fare_amount', 'l.payment_type', 'l.gst_percentage',
          'l.gst_amount', 'l.created_at as transaction_date', 'pm.name as payment_mode')
          ->leftJoin('bookings as b','b.booking_id','=','l.booking_id')
          ->leftJoin('vehicle_type as vt','vt.id','=','b.vechicle_id')
          ->leftJoin('payment_modes as pm','pm.id','=','b.payment_mode')
          ->leftJoin('users as u','u.id','=','b.user_id')
          ->leftJoin('users as d','d.id','=','b.driver_id');
          
          if(isset($request->payment_type) && $request->payment_type != ""){
              $query->where('l.payment_type', $request->payment_type);
          }
          
          if(isset($request->vehicle_type) && $request->vehicle_type != ""){
              $query->where('b.vechicle_id', $request->vehicle_type);
          }
          
          if(isset($request->from_date) && $request->from_date != ""){
              $query->whereDate('l.created_at', '>=', $request->from_date);
          }
          
          if(isset($request->to_date) && $request->to_date != ""){
              $query->whereDate('l.created_at', '<=', $request->to_date);
          }
          
            $Booking= $query->orderBy('l.id',"DESC")->get();
            
            // dd($Booking);
            
            $income_data = DB::table('list_subscription as l')
                ->leftJoin('bookings as b','l.booking_id','=','b.booking_id')
                ->select(DB::raw('sum(l.amount) total_amount'), DB::raw('sum(l.admin_amount) admin_amount'), DB::raw('sum(l.driver_amount) driver_amount'), DB::raw('sum(l.gst_amount) total_gst_amount'));
                
              if(isset($request->payment_type) && $request->payment_type != ""){
                  $income_data->where('l.payment_type', $request->payment_type);
              }
              
              if(isset($request->vehicle_type) && $request->vehicle_type != ""){
                  $income_data->where('b.vechicle_id', $request->vehicle_type);
              }
              
              if(isset($request->from_date) && $request->from_date != ""){
                  $income_data->whereDate('l.created_at', '>=', $request->from_date);
              }
              
              if(isset($request->to_date) && $request->to_date != ""){
                  $income_data->whereDate('l.created_at', '<=', $request->to_date);
              }
                
            $income_data = $income_data->first(); 
        
        return view('admin.booking_payments', ['data'=> $Booking, 'income_data'=>$income_data]);
        
    }
    
    public function manage_comission(Request $request){
        
        if ($request->isMethod('post')) {
            
            $validator=Validator::make($request->all(),[
                'update_id' => 'required',
                'commission' => 'required',
            ]);
    
            if($validator->fails())
            {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }

            $data = [
                'percent' => $request->commission,
            ];

            $id = DB::table('mlm_percentage')->where('id', $request->update_id)->update($data);

            return response()->json([
                "status" => "true",
                "msg" => 'Update Successfully !',
                "return_url" => "",
            ]);

        }
        
        $data['data'] = DB::table('mlm_percentage')->get();   
        return view('admin.manage_comission',$data);
        
    }
    
    public function all_notifications(Request $request){
 
        if ($request->isMethod('post')) {
     
            $rules = [
                'title' => 'required',
                'message' => 'required',
                'send_to' => 'required|in:all_users,all_drivers,specific_user,specific_driver',
            ];
     
            if ($request->send_to == 'specific_user') {
                $rules['target_id'] = 'required|integer|exists:users,id';
            } elseif ($request->send_to == 'specific_driver') {
                $rules['target_id'] = 'required|integer|exists:drivers,id';
            }
     
            $validator = Validator::make($request->all(), $rules);
     
            if ($validator->fails()) {
                return response()->json([
                    "status" => "false",
                    "msg" => $validator->errors(),
                ]);
            }
     
            $target_id = null;
            if (in_array($request->send_to, ['specific_user', 'specific_driver'])) {
                $target_id = $request->target_id;
            }
     
            $data = [
                "title" => $request->title,
                "message" => $request->message,
                "send_to" => $request->send_to,
                "target_id" => $target_id,
                "created_at" => now(),
                "updated_at" => now(),
            ];
     
            DB::table('notifications')->insert($data);
     
            return response()->json([
                "status" => "true",
                "msg" => "Added successfully !",
                "return_url" => "",
            ]);
     
        }
     
        $data = DB::table('notifications')->orderBy('id', 'DESC')->get();
        return view('admin.notifications', compact('data'));
    }
     
    public function delete_notifications(Request $request){
        $id = $request->id;
        DB::table('notifications')->where('id', $id)->delete();
     
        return response()->json([
            "status" => "true",
            "msg" => "Deleted successfully !",
        ]);
    }
    
    public function withdrawal_request(Request $request)
    {
       $data= DB::table('withdrawal_request')
               ->select('withdrawal_request.*','users.type','users.id as user_id',
               'users.name', 'users.type as user_type', 'users.bank_ac_holder_name as user_ac_holder',
               'users.bank_name as user_bank_name', 'users.bank_account as user_bank_accaount',
               'users.branch as user_branch_name', 'users.code as user_code', 
               'users.bank_status as user_bank_status')
               ->leftjoin('users','users.id','=','withdrawal_request.driver_id');
               
            //   $data->where('withdrawal_request.payment_status', 1);
      
        $data = $data->orderBy('withdrawal_request.id',"DESC")->get()->toArray();

       return view('admin.withdraw_request',compact('data'));
    }
    
    public function third_party(Request $request){
        if($request->method()=="POST")
        {
            set_option_all_data('merchant_id', $request->merchant_id);
            set_option_all_data('access_code', $request->access_code);
            set_option_all_data('google_map_api_key', $request->google_map_api_key);
            
            set_option_all_data('user_cancel_time_duration', $request->user_cancel_time_duration);
            set_option_all_data('user_cancel_charge', $request->user_cancel_charge);
            set_option_all_data('driver_cancel_time_duration', $request->driver_cancel_time_duration);
            set_option_all_data('driver_cancel_charge', $request->driver_cancel_charge);
            
            if($request->radius_range){
                DB::table('radius')->where('id', '1')->update(['radius_range' => $request->radius_range]);
            }
            
            $radius = DB::table('radius')->first();
            return view('admin.third_party_setting', compact('radius'));
        }
        else
        {
            $radius = DB::table('radius')->first();
            return view('admin.third_party_setting', compact('radius'));
        }
    }
    
    
    public function withdrawal_status(Request $request){
        
        try {
            
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'status' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => $validator->errors()->first()
                ], 400);
            }

            $id = $request->input('id');
            $status = $request->input('status');

            $withdrawal = DB::table('withdrawal_request')->where('id', $id)->first();

            if (!$withdrawal) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Withdrawal request not found'
                ], 404);
            }


            DB::table('withdrawal_request')
                ->where('id', $id)
                ->update([
                    'payment_status' => $status,
                    'updated_at' => now()
                ]);
                
            // $data = DB::table('withdrawal_request')
            //     ->where('id', $id)->first();
                
            // dd($data);    


            switch ($status) {
                case '2': 

                    $this->processAcceptedWithdrawal($withdrawal);
                    $message = 'Withdrawal request accepted successfully';
                    break;

                case '3': 

                    $this->processDeniedWithdrawal($withdrawal);
                    $message = 'Withdrawal request denied';
                    break;

                default:
                    $message = 'Withdrawal status updated to pending';
                    break;
            }

            // $this->sendNotificationToUser($withdrawal->user_id, $status);

            return response()->json([
                'status' => true,
                'msg' => $message,
                'return_url' => ""
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => false,
                'msg' => 'Failed to update status: ' . $e->getMessage()
            ], 500);
        }
        
    }
    

    private function processAcceptedWithdrawal($withdrawal)
    {

        // DB::table('withdrawal_requests')
        //     ->where('id', $withdrawal->id)
        //     ->update([
        //         'processed_at' => now(),
        //         'processed_by' => auth()->id()
        //     ]);

    }


    private function processDeniedWithdrawal($withdrawal)
    {

        $data = [
            'user_id' => $withdrawal->driver_id,
            'amount' => $withdrawal->amount,
            'transaction_type' => '1',
            'transaction_date' => date('d-m-Y'),
            'withdraw_token' => $withdrawal->withdraw_token,
            'payment_type' => 'wallet',
            'status' => '1',
        ];

        DB::table('my_wallet')->insert($data);

    }


    private function sendNotificationToUser($userId, $status)
    {

        $statusText = [
            '1' => 'pending',
            '2' => 'accepted',
            '3' => 'denied'
        ];

        // Example notification
        // Mail::to($user->email)->send(new WithdrawalStatusUpdated($statusText[$status]));
    }
    
    public function coupon_all_codes(Request $request)
    {
        $coupons = DB::table('coupon_codes')->orderBy('id', 'DESC')->get();
        return view('admin.coupon_all_codes', compact('coupons'));
    }
 
    public function add_coupon_code(Request $request)
    {
        $rules = [
            'code' => 'required|max:50',
            'discount_type' => 'required',
            'discount_value' => 'required|numeric',
            'expiry_date' => 'required|date',
            'status' => 'required',
        ];
 
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            return response()->json([
                "status" => "false",
                "msg" => $validator->errors(),
            ]);
        }
        
        $cid = $request->cid;
 
        if ($cid == '' || $cid == null) {
            $exists = DB::table('coupon_codes')->where('code', $request->code)->first();
 
            if ($exists) {
                return response()->json([
                    'status' => 0,
                    'errors' => ['code' => ['Coupon code already exists']]
                ]);
            }
 
            DB::table('coupon_codes')->insert([
                'code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'expiry_date' => $request->expiry_date,
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return response()->json([
                "status" => "true",
                "msg" => 'Coupon code added successfully...!',
                "return_url" => "",
            ]);
            
        } else {
            $exists = DB::table('coupon_codes')
                ->where('code', $request->code)
                ->where('id', '!=', $cid)
                ->first();
 
            if ($exists) {
                return response()->json([
                    'status' => 0,
                    'errors' => ['code' => ['Coupon code already exists']]
                ]);
            }
 
            DB::table('coupon_codes')->where('id', $cid)->update([
                'code' => $request->code,
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'expiry_date' => $request->expiry_date,
                'status' => $request->status,
                'updated_at' => now(),
            ]);
 
            return response()->json([
                "status" => "true",
                "msg" => 'Coupon code updated successfully...!',
                "return_url" => "",
            ]);
 
        }
    }
 
    public function edit_coupon_code($id)
    {
        $coupon = DB::table('coupon_codes')->where('id', $id)->first();
 
        if (!$coupon) {
            return response()->json(['status' => 0, 'message' => 'Coupon not found']);
        }
 
        return response()->json(['status' => 1, 'data' => $coupon]);
    }
 
    public function delete_coupon_code(Request $request)
    {
        $id = $request->id;
        $coupon = DB::table('coupon_codes')->where('id', $id)->first();
 
        if (!$coupon) {
            return response()->json(['status' => 0, 'message' => 'Coupon not found']);
        }
 
        DB::table('coupon_codes')->where('id', $id)->delete();
 
        return response()->json([
            'status' => 1,
            'message' => 'Coupon code deleted successfully'
        ]);
    }
 
    public function change_coupon_status(Request $request)
    {
        $id = $request->id;
        $coupon = DB::table('coupon_codes')->where('id', $id)->first();
 
        if (!$coupon) {
            return response()->json(['status' => 0, 'message' => 'Coupon not found']);
        }
 
        $new_status = $coupon->status == 1 ? 2 : 1;
 
        DB::table('coupon_codes')->where('id', $id)->update(['status' => $new_status]);
 
        return response()->json([
            'status' => 1,
            'new_status' => $new_status
        ]);
    }
    
    public function weekly_payouts(Request $request)
    {
        $query = DB::table('weekly_payouts')
            ->leftJoin('users', 'weekly_payouts.user_id', '=', 'users.id')
            ->select(
                'weekly_payouts.id',
                'weekly_payouts.payout_token',
                'weekly_payouts.user_id',
                'weekly_payouts.amount',
                'weekly_payouts.payment_status',
                'weekly_payouts.created_at',
                'weekly_payouts.updated_at',
                'users.name as user_name',
                'users.email as user_email'
            );
     
        // Custom date range filter (filters on created_at)
        if ($request->filled('from_date')) {
            $query->whereDate('weekly_payouts.created_at', '>=', $request->from_date);
        }
     
        if ($request->filled('to_date')) {
            $query->whereDate('weekly_payouts.created_at', '<=', $request->to_date);
        }
     
        $data = $query->orderBy('weekly_payouts.id', 'DESC')->get();
     
        return view('admin.weekly_payouts', compact('data'))->with([
            'from_date' => $request->from_date,
            'to_date'   => $request->to_date,
        ]);
    }
    
    
}    