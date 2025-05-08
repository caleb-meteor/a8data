<?php

namespace App\Services;

use App\Models\Usage;
use Caleb\Practice\QueryFilter;
use Caleb\Practice\Service;

class UsageService extends Service
{
    public function getUsageList(QueryFilter $filter)
    {
        return Usage::filter($filter)
            ->with('creator', 'team', 'product', 'agent')
            ->orderByDesc('id')->paginate();
    }

    public function createUsage(array $data)
    {
        return Usage::query()->create($data);
    }

    /**
     * @param int|Usage $usage
     * @return Usage
     * @author Caleb 2025/5/8
     */
    public function getUsage(int|Usage $usage)
    {
        return $usage instanceof Usage ? $usage : Usage::query()->find($usage);
    }

    public function updateUsage(int $usage, array $data)
    {
        $usage = $this->getUsage($usage);
        return $usage->update($data);
    }

    public function deleteUsage(int $usage)
    {
        $usage = $this->getUsage($usage);
        return $usage->delete();
    }
}
