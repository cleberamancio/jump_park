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

class EditServiceOrderUseCase
{
    private ServiceOrderRepository $repository;
    private ListByPlateServiceOrderUseCase $listByPlateServiceOrderUseCase;

    public function __construct(ServiceOrderRepository $repository, ListByPlateServiceOrderUseCase $listByPlateServiceOrderUseCase)
    {
        $this->repository = $repository;
        $this->listByPlateServiceOrderUseCase = $listByPlateServiceOrderUseCase;
    }

    public function execute(string $vehiclePlate, float $price, ServiceOrder $serviceOrder): int
    {
        $plateValidation = new PlateValidation(new BrazilPlateValidationStrategy());
        if (!$plateValidation->validate($vehiclePlate)) {
            throw new InvalidArgumentException(Messages::$INVALID_VEHICLE_PLATE);
        }
        $priceValidation = new PriceValidation(new BrazilPriceValidationStrategy());
        if (!$priceValidation->validate($price)) {
            throw new InvalidArgumentException(Messages::$INVALID_PRICE);
        }
        $serviceOrderArray = $this->listByPlateServiceOrderUseCase->execute($vehiclePlate, $price);
        if (empty($serviceOrderArray) or $serviceOrderArray == null) {
            throw new InvalidArgumentException(Messages::$VEHICLE_NOT_FOUND);
        }
        return $this->repository->update($serviceOrderArray, $serviceOrder);
    }
}
