<?php

namespace App\Domain\Strategy;

class BrazilPriceValidationStrategy implements PriceValidationStrategyInterface {
    public function validate(float $price): bool
    {
        $result = false;
        if( is_float($price) && $price >= 0){
            $result = true;
        } 
        return $result;
    }
}