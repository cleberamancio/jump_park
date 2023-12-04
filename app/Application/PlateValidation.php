<?php

namespace App\Application;

use App\Domain\Strategy\PlateValidationStrategyInterface;

class PlateValidation {
    private PlateValidationStrategyInterface $strategyValidation;
    public function __construct(PlateValidationStrategyInterface $strategyValidation) 
    {
        $this->strategyValidation = $strategyValidation;
    } 

    public function validate(string $vehiclePlate): bool 
    {
        return $this->strategyValidation->validate($vehiclePlate);
    }
}