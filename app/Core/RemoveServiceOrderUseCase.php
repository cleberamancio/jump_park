<?php

namespace App\Core;

use App\Application\PlateValidation;
use App\Application\PriceValidation;
use App\Domain\ServiceOrder;
use App\Domain\ServiceOrder\ServiceOrderRepository;
use App\Domain\Strategy\BrazilPlateValidationStrategy;
use App\Domain\Strategy\BrazilPriceValidationStrategy;
use App\Utils\Messages;
use InvalidArgumentException;

class RemoveServiceOrderUseCase
{
    private ServiceOrderRepository $repository;
    private ListByIdServiceOrderUseCase $listByIdServiceOrderUseCase;

    public function __construct(ServiceOrderRepository $repository, ListByIdServiceOrderUseCase $listByIdServiceOrderUseCase)
    {
        $this->repository = $repository;
        $this->listByIdServiceOrderUseCase = $listByIdServiceOrderUseCase;
    }

    public function execute(int $serviceOrderId ): bool
    {
        $serviceOrder = $this->listByIdServiceOrderUseCase->execute($serviceOrderId);
        if (empty($serviceOrder) or $serviceOrder==null) 
        {
            throw new InvalidArgumentException(Messages::$VEHICLE_NOT_FOUND);
        }
        return $this->repository->remove($serviceOrderId);
    }
}
