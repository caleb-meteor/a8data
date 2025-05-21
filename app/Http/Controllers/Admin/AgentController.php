<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AgentExport;
use App\Filters\AgentFilter;
use App\Http\Controllers\Controller;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(AgentFilter $filter)
    {
        return $this->success(
            AgentService::instance()->getAgentList($filter)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|unique:agents',
            'media'      => 'integer',
            'remark'     => 'string',
            'commission' => 'numeric|min:0|max:100'
        ]);

        return $this->success(
            AgentService::instance()->createAgent($data)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', Rule::unique('agents')->ignore($id)],
            'media'      => 'integer',
            'remark'     => 'string',
            'commission' => 'numeric|min:0|max:100'
        ]);
        AgentService::instance()->updateAgent($id, $data);
        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        AgentService::instance()->deleteAgent($id);
        return $this->success();
    }

    public function export(AgentFilter $filter)
    {
        return Excel::download(new AgentExport($filter), 'agents_' . date('Y_m_d_H_i') . '.xlsx');
    }

    public function balance(int $id)
    {
        return $this->success(
            ['balance' => AgentService::instance()->getBalance($id)]
        );
    }
}
