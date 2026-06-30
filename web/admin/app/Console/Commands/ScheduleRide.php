<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScheduleRide extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-ride';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes the scheduleRide.js file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $node_path = env('NODE_PATH','');

        if(!empty($node_path)){

            // Run the JS file using Node.js
            $command = $node_path.' '.storage_path('app/firebase/scheduleRide.js');
        
            $output = shell_exec($command." /dev/null 2>&1");

            // Log the output
            \Log::info('ScheduleRide Output: ' . $output);

            $this->info('Schedule ride process executed.');

        }else{

            // Log the output
            \Log::info('ScheduleRide Output: Node path is not defined');
        }
    }
}
