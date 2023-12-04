<?php

namespace App\Domain\Strategy;

interface PriceValidationStrategyInterface {
    public function validate(float $price): bool;
}