<?php

namespace App\Filters;

use Caleb\Practice\QueryFilter;

class ProductFilter extends QueryFilter
{
    public function id($id)
    {
        $this->query->where('id', $id);
    }

    public function name($name)
    {
        return $this->query->where('name', 'like', "%$name%");
    }

    public function teamId($teamId)
    {
        return $this->query->where('team_id', $teamId);
    }
}
