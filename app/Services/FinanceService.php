<?php

namespace App\Services;

use App\Models\Finance;
use Caleb\Practice\QueryFilter;
use Caleb\Practice\Service;

class FinanceService extends Service
{
    public function getFinanceList(QueryFilter $filter)
    {
        return Finance::filter($filter)
            ->with('creator', 'team', 'product', 'agent')
            ->orderByDesc('id')->paginate();
    }

    public function createFinance(array $data)
    {
        return Finance::query()->create($data);
    }

    /**
     * @param int|Finance $finance
     * @return Finance
     * @author Caleb 2025/5/8
     */
    public function getFinance(int|Finance $finance)
    {
        return $finance instanceof Finance ? $finance : Finance::query()->find($finance);
    }

    public function updateFinance(int $finance, array $data)
    {
        $finance = $this->getFinance($finance);
        if (!$finance) {
            $this->throwAppException('记录不存在');
        }
        return $finance->update($data);
    }

    public function deleteFinance(int $finance)
    {
        $finance = $this->getFinance($finance);
        if (!$finance) {
            return true;
        }
        return $finance->delete();
    }
}
