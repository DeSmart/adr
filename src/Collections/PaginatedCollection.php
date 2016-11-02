<?php

namespace DeSmart\ADR\Collections;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginatedCollection extends Collection
{

    /**
     * @var LengthAwarePaginator
     */
    protected $paginator;

    public function setPaginator(LengthAwarePaginator $paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
}