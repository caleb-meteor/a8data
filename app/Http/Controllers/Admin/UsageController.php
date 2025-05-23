<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsageExport;
use App\Filters\UsageFilter;
use App\Http\Controllers\Controller;
use App\Services\UsageService;
use Caleb\Practice\QueryFilter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class UsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UsageFilter $filter)
    {
        $request->validate([
            'date'   => 'required|array',
            'date.*' => 'date_format:Y-m-d',
            'date.0' => 'required',
            'date.1' => 'required|after_or_equal:date.0',
        ]);

        $statistic         = UsageService::instance()->statistic($filter);
        $list              = UsageService::instance()->getUsageList($filter)->toArray();
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
            'exclusive_agent'  => 'string',
            'channel'          => 'string',
            'media'            => 'integer',
            'agent_id'         => [$request->agent_id ? 'exists:agents,id' : 'integer'],
            'placement_method' => 'string',
            'actual_usage'     => 'numeric',
            'view'             => 'integer',
            'click'            => 'integer',
            'install'          => 'integer',
            'send_num'         => 'integer',
            'price'            => 'numeric|min:0|decimal:0,6',
            'unique_id'        => 'string',
        ]);

        return $this->success(
            UsageService::instance()->createUsage($data)
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
            'exclusive_agent'  => 'string',
            'channel'          => 'string',
            'media'            => 'integer',
            'agent_id'         => [$request->agent_id ? 'exists:agents,id' : 'integer'],
            'placement_method' => 'string',
            'actual_usage'     => 'numeric',
            'view'             => 'integer',
            'click'            => 'integer',
            'install'          => 'integer',
            'send_num'         => 'integer',
            'price'            => 'numeric|min:0|decimal:0,6',
            'unique_id'        => 'string',
        ]);

        UsageService::instance()->updateUsage($id, $data);

        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        UsageService::instance()->deleteUsage($id);
        return $this->success();
    }

    public function import(Request $request)
    {
        $file     = $request->file('file');
        // $filePath = $file->getRealPath();
        return $this->success([
            'count' => UsageService::instance()->import($file)
        ]);
    }

    public function getDailyUsage(Request $request)
    {
        $request->validate([
            'date'     => 'required|array',
            'date.*'   => 'date_format:Y-m-d',
            'date.0'   => 'required',
            'date.1'   => 'required|after_or_equal:date.0',
            'group_by' => 'string|in:department,team'
        ]);

        $groupBy = [
            'department' => 'department_id',
            'team'       => 'team_id'
        ][$request->input('group_by', 'department')];

        return $this->success(UsageService::instance()->getDailyUsage($request->date, $groupBy));
    }

    public function export(UsageFilter $filter)
    {
        return Excel::download(new UsageExport($filter), 'usages_' . date('Y_m_d_H_i') . '.xlsx');
    }
}
