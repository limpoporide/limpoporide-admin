<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('✅ Cron Started: send:notification at ' . now());
        send_today_schedule_ride_15min_before_notifications();
        send_today_schedule_ride_notifications();
        
        date_default_timezone_set('Africa/Johannesburg');
        $current_date =  date('Y-m-d');
        $current_time =  date('H:i');
        $current_day = date('l');
        
        if($current_time == "23:59"){
            
            if($current_day == "Tuesday"){
                generate_weekly_payout();
            }
            
        }
        
    }
}
