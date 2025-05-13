<?php

namespace App\Filters;

use Caleb\Practice\QueryFilter;

class FinanceFilter extends QueryFilter
{
    public function teamId($teamId)
    {
        return $this->query->where('team_id', $teamId);
    }

    public function productId($productId)
    {
        return $this->query->where('product_id', $productId);
    }

    public function agentId($agentId)
    {
        return $this->query->where('agent_id', $agentId);
    }

    public function date($date)
    {
        return $this->query->whereBetween('date', $date);
    }
}
