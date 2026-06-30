<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScheduleOnDemandService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-on-demand-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes the schedule_ondemand_service.js file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $node_path = env('NODE_PATH','');

        if(!empty($node_path)){

            // Run the JS file using Node.js
            $command = $node_path.' '.storage_path('app/firebase/schedule_ondemand_service.js');
        
            $output = shell_exec($command." /dev/null 2>&1");

            // Log the output
            \Log::info('ScheduleOnDemandService Output: ' . $output);

            $this->info('Schedule OnDemand Service  process executed.');

        }else{

            // Log the output
            \Log::info('ScheduleOnDemandService Output: Node path is not defined');
        }
    }
}
