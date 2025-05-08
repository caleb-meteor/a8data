<?php

namespace App\Http\Controllers\Admin;

use App\Filters\AgentFilter;
use App\Http\Controllers\Controller;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'name'       => 'required|string|max:255|unique:agents',
            'media'      => 'string|max:255',
            'remark'     => 'string|max:255',
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
            'name'       => ['required', 'string', 'max:255', Rule::unique('agents')->ignore($id)],
            'media'      => 'string|max:255',
            'remark'     => 'string|max:255',
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
}
