<?php

namespace App\Services;

use App\Models\Team;
use Caleb\Practice\QueryFilter;
use Caleb\Practice\Service;

class TeamService extends Service
{
    public function getTeamList(QueryFilter $filter)
    {
        return Team::filter($filter)->with('creator')->orderByDesc('id')->paginate();
    }

    public function createTeam(array $data)
    {
        return Team::query()->create($data);
    }

    /**
     * @param int|Team $team
     * @return Team|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     * @author Caleb 2025/5/8
     */
    public function getTeam(int|Team $team)
    {
        return $team instanceof Team ? $team : Team::query()->find($team);
    }

    public function updateTeam(int $team, array $data)
    {
        $team = $this->getTeam($team);
        return $team->update($data);
    }

    public function deleteTeam(int $team)
    {
        $team = $this->getTeam($team);
        return $team->delete();
    }
}
