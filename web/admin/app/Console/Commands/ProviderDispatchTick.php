<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProviderDispatchTick extends Command
{
    protected $signature = 'app:provider-dispatch-tick';

    protected $description = 'Avança pings sequenciais de prestador próximo e fecha pedidos sem aceite em 10 minutos';

    public function handle()
    {
        $node_path = env('NODE_PATH', '');

        if (!empty($node_path)) {
            $command = $node_path.' '.storage_path('app/firebase/providerDispatchTick.js');
            $output = shell_exec($command.' /dev/null 2>&1');
            \Log::info('Provider dispatch tick: '.$output);
            $this->info('Provider dispatch tick executed.');
        } else {
            \Log::info('Provider dispatch tick: Node path is not defined');
        }
    }
}
