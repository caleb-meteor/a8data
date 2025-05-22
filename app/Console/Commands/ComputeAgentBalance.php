<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Services\AgentService;
use Illuminate\Console\Command;

class ComputeAgentBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:compute-agent-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算代理余额';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Agent::query()->chunk(100, function ($agents){
            $agents->each(function (Agent $agent) {
                $agent->balance = AgentService::instance()->getBalance($agent->id);
                $agent->balance_updated_at = now();
                // $this->info("计算代理余额: {$agent->name}, $agent->balance");
                $agent->save();
            });
        });
    }
}
