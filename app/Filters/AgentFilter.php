<?php

namespace App\Filters;

use Caleb\Practice\QueryFilter;

class AgentFilter extends QueryFilter
{
    public function id($id)
    {
        $this->query->where('id', $id);
    }

    public function name($name)
    {
        return $this->query->where('name', 'like', "%$name%");
    }

    public function media($media)
    {
        return $this->query->where('media', $media);
    }
}
