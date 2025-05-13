<?php

namespace App\Http\Controllers\Admin;

use App\Filters\UsageFilter;
use App\Http\Controllers\Controller;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UsageFilter $filter)
    {
        $request->validate([
            'date' => 'required|array',
            'date.*' => 'date_format:Y-m-d',
            'date.0' => 'required',
            'date.1' => 'required|after_or_equal:date.0',
        ]);
        
        $statistic = UsageService::instance()->statistic($filter);
        $list = UsageService::instance()->getUsageList($filter)->toArray();
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
            'actual_usage'     => 'integer',
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
            'actual_usage'     => 'integer',
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
        $file = $request->file('file');
        $filePath = $file->getRealPath();
        return $this->success([
            'count' => UsageService::instance()->import($filePath)
        ]);
    }
}
