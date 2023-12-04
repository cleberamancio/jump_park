<?php

namespace App\Core;

use App\Domain\ServiceOrder;
use App\Domain\ServiceOrder\ServiceOrderRepository;

class CreateServiceOrderUseCase
{

    private ServiceOrderRepository $repository;

    public function __construct(ServiceOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ServiceOrder $serviceOrder): ServiceOrder
    {
        return $this->repository->save($serviceOrder);
    }
}
