<?php

namespace App\Http\Controllers\Admin;

use App\Filters\TeamFilter;
use App\Http\Controllers\Controller;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TeamFilter $filter)
    {
        return $this->success(
            TeamService::instance()->getTeamList($filter)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:teams',
            'department_id' => 'required|integer',
            'remark'        => 'string|max:255'
        ]);

        return $this->success(
            TeamService::instance()->createTeam($data)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', Rule::unique('teams')->ignore($id)],
            'department_id' => 'required|integer',
            'remark'        => 'string|max:255'
        ]);
        TeamService::instance()->updateTeam($id, $data);
        return $this->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        TeamService::instance()->deleteTeam($id);
        return $this->success();
    }
}
