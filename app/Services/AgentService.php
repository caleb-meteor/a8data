<?php

namespace App\Services;

use App\Models\Agent;
use Caleb\Practice\QueryFilter;
use Caleb\Practice\Service;

class AgentService extends Service
{
    public function getAgentList(QueryFilter $filter)
    {
        return Agent::filter($filter)
            ->with('creator')
            ->orderByDesc('id')->paginate();
    }

    public function createAgent(array $data)
    {
        return Agent::query()->create($data);
    }

    /**
     * @param int|Agent $agent
     * @return Agent
     * @author Caleb 2025/5/8
     */
    public function getAgent(int|Agent $agent)
    {
        return $agent instanceof Agent ? $agent : Agent::query()->find($agent);
    }

    public function updateAgent(int $agent, array $data)
    {
        $agent = $this->getAgent($agent);
        return $agent->update($data);
    }

    public function deleteAgent(int $agent)
    {
        $agent = $this->getAgent($agent);
        return $agent->delete();
    }

    public function getAgentByNames(array $names)
    {
        return Agent::query()->whereIn('name', $names)->get();
    }

    public function getBalance(int $agent)
    {
        $agent = $this->getAgent($agent);
        $actualUsage = $agent->usages()->sum('actual_usage');
        $amount = $agent->finances()->selectRaw('sum(counterparty_fee + media_fee + usd + ustd - transaction_fee - service_fee - usd_loss_percent) as amount')->first()?->amount ?: 0;
        return round($amount - $actualUsage, 6);
    }
}
