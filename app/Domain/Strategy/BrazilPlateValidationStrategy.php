<?php

namespace App\Domain\Strategy;

#Rules of plate vahicle in Brasil.
#https://www.gov.br/transportes/pt-br/assuntos/transito/conteudo-contran/resolucoes/resolucao9692022anexos.pdf
    
class BrazilPlateValidationStrategy implements PlateValidationStrategyInterface {
    public function validate(string $vehiclePlate): bool
    {
        $vehiclePlate = trim($vehiclePlate);
        $placa = preg_replace('/\s+/', '', $vehiclePlate);
        $regex = '/^[A-Za-z]{3}\d{4}$|^\d{4}[A-Za-z]{3}$/';
        $response = preg_match($regex, $placa) === 1;
        return $response;
    }
}