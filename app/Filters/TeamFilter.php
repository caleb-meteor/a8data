<?php

namespace App\Filters;

use Caleb\Practice\QueryFilter;

class TeamFilter extends QueryFilter
{
    public function id($id)
    {
        $this->query->where('id', $id);
    }

    public function name($name)
    {
        return $this->query->where('name', 'like', "%$name%");
    }

    public function departmentId($departmentId)
    {
        return $this->query->where('department_id', $departmentId);
    }
}
