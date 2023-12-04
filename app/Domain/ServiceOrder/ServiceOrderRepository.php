<?php

namespace App\Domain\ServiceOrder;

use App\Core\ServiceOrderRepositoryInterface;
use App\Domain\ServiceOrder;
use App\Utils\Messages;
use InvalidArgumentException;

class ServiceOrderRepository implements ServiceOrderRepositoryInterface
{
    private ServiceOrderRepositoryInterface $repository;
    
    public function __construct(ServiceOrderRepositoryInterface $repository )
    {
        $this->repository = $repository;
    }

    public function save(ServiceOrder $serviceOrder): ServiceOrder{
        return $this->repository->save($serviceOrder);
    }

    public function update(array $serviceOrderArray, ServiceOrder $serviceOrder): int{
        return $this->repository->update($serviceOrderArray, $serviceOrder);
    }

    public function remove(int $serviceOrderId): bool
    {
        return $this->repository->remove($serviceOrderId);
    }
    
    public function listByPlate(string $plate, float $price) : array
    {
        $return = $this->repository->listByPlate($plate, $price);
        if (empty($return) or $return==null) 
        {
            throw new InvalidArgumentException(Messages::$VEHICLE_NOT_FOUND);
        }
        return $return;
    }

    public function listById(int $serviceOrderId): ServiceOrder | bool{
        $return = $this->repository->listById($serviceOrderId);
        return $return;
    }

    public function list(string $search, int $start, int $limit) : array{
        return $this->repository->list($search, $start, $limit);
    }
    
}