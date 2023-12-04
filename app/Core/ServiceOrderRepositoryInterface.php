<?php

namespace App\Core;

use App\Domain\ServiceOrder;

interface ServiceOrderRepositoryInterface
{
    public function save(ServiceOrder $serviceOrder): ServiceOrder;
    public function update(array $serviceOrderArray, ServiceOrder $serviceOrder): int;
    public function remove(int $serviceOrderId): bool;
    public function list(string $search, int $start, int $limit): array;
    public function listByPlate(string $plate, float $price): array;
    public function listById(int $serviceOrder): ServiceOrder | bool;

    
}