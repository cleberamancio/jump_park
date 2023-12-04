<?php

namespace App\Core;

use App\Domain\ServiceOrder;
use App\Domain\ServiceOrder\ServiceOrderRepository;

class ListPaginationOrderUseCase
{
    private ServiceOrderRepository $repository;

    public function __construct(ServiceOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $search = "", int $start = 0, int $limit = 5)
    {
        return $this->repository->list($search, $start, $limit);
    }
}
