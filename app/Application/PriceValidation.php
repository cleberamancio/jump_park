<?php

namespace App\Application;

use App\Domain\Strategy\PriceValidationStrategyInterface;

class PriceValidation {
    private PriceValidationStrategyInterface $strategy;
    public function __construct(PriceValidationStrategyInterface $strategy) 
    {
        $this->strategy = $strategy;
    } 

    public function validate(float $price): bool 
    {
        return $this->strategy->validate($price);
    }
}