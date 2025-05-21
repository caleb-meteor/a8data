<?php

namespace App\Http\Controllers\Admin;

use App\Filters\FinanceFilter;
use App\Http\Controllers\Controller;
use App\Services\FinanceService;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, FinanceFilter $filter)
    {
        $request->validate([
            'date' => 'required|array',
            'date.*' => 'date_format:Y-m-d',
            'date.0' => 'required',
            'date.1' => 'required|after_or_equal:date.0',
        ]);

        $statistic = FinanceService::instance()->statistic($filter);
        $list = FinanceService::instance()->getFinanceList($filter)->toArray();
        $list['statistic'] = $statistic;
        return $this->success($list);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'month'            => 'required|integer|min:1|max:12',
            'date'             => 'required|date_format:Y-m-d',
            'department_id'    => 'required|integer',
            'team_id'          => [$request->team_id ? 'exists:teams,id' : 'integer'],
            'product_id'       => [$request->product_id ? 'exists:products,id' : 'integer'],
            'agent_id'         => [$request->agent_id ? 'exists:agents,id' : 'integer'],
            'counterparty_fee' => 'numeric|decimal:0,6',
            'media_fee'        => 'numeric|decimal:0,6',
            'transaction_fee'  => 'numeric|decimal:0,6',
            'service_fee'      => 'numeric|decimal:0,6',
            'usd_loss_percent' => 'numeric|decimal:0,6|min:0|max:100',
            'usd'              => 'numeric|decimal:0,6',
            'ustd'             => 'numeric|decimal:0,6',
            'commission'       => 'numeric|decimal:0,6|min:0|max:100',
            'purpose'          => 'string',
            'description'      => 'string',
            'account'          => 'required|string',
            'handler'          => 'required|string',
            'remark'           => 'string',
            'balance'          => 'numeric|decimal:0,6',
        ]);

        return $this->success(
            FinanceService::instance()->createFinance($data)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'month'            => 'required|integer|min:1|max:12',
            'date'             => 'required|date_format:Y-m-d',
            'department_id'    => 'required|integer',
            'team_id'          => [$request->team_id ? 'exists:teams,id' : 'integer'],
            'product_id'       => [$request->product_id ? 'exists:products,id' : 'integer'],
            'agent_id'         => [$request->agent_id ? 'exists:agents,id' : 'integer'],
            'counterparty_fee' => 'numeric|decimal:0,6',
            'media_fee'        => 'numeric|decimal:0,6',
            'transaction_fee'  => 'numeric|decimal:0,6',
            'service_fee'      => 'numeric|decimal:0,6',
            'usd_loss_percent' => 'numeric|decimal:0,6|min:0|max:100',
            'usd'              => 'numeric|decimal:0,6',
            'ustd'             => 'numeric|decimal:0,6',
            'commission'       => 'numeric|decimal:0,6|min:0|max:100',
            'purpose'          => 'string',
            'description'      => 'string',
            'account'          => 'required|string',
            'handler'          => 'required|string',
            'remark'           => 'string',
            'balance'          => 'numeric|decimal:0,6',
        ]);

        FinanceService::instance()->updateFinance($id, $data);

        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        FinanceService::instance()->deleteFinance($id);
        return $this->success();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Caleb\Practice\Exceptions\PracticeAppException
     * @author Caleb 2025/5/13
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        // $filePath = $file->getRealPath();
        return $this->success([
            'count' => FinanceService::instance()->import($file)
        ]);
    }
}
