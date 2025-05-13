<?php

namespace App\Filters;

use Caleb\Practice\QueryFilter;

class UserFilter extends QueryFilter
{
    public function id($id)
    {
        $this->query->where('id', $id);
    }

    public function name($name)
    {
        $this->query->whereLike('name', '%'.$name.'%');
    }

    public function username($username)
    {
        $this->query->whereLike('username', '%'.$username.'%');
    }
}
