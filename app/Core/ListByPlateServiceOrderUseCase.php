<?php

namespace App\Core;

use App\Domain\ServiceOrder;
use App\Domain\ServiceOrder\ServiceOrderRepository;

class ListByPlateServiceOrderUseCase
{

    private ServiceOrderRepository $repository;

    public function __construct(ServiceOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $vehiclePlate, float $price): array
    {
        return $this->repository->listByPlate($vehiclePlate, $price);
    }
}
