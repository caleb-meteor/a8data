<?php

namespace App\Filters;

use Caleb\Practice\QueryFilter;

class AgentFilter extends QueryFilter
{
    public function name($name)
    {
        return $this->query->where('name', 'like', "%$name%");
    }
}
