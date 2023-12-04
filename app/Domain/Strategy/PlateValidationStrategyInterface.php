<?php

namespace App\Domain\Strategy;

interface PlateValidationStrategyInterface {
    public function validate(string $vehiclePlate): bool;
}