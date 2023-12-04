<?php

namespace App\Core;

use App\Domain\ServiceOrder;
use App\Domain\ServiceOrder\ServiceOrderRepository;

class ListByIdServiceOrderUseCase
{
    private ServiceOrderRepository $repository;

    public function __construct(ServiceOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $serviceOrderid)
    {
        return $this->repository->listById($serviceOrderid);
    }
}
