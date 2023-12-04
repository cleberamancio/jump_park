<?php

namespace App\Adapters;

use App\Core\ServiceOrderRepositoryInterface;
use App\Domain\ServiceOrder;
use App\Models\ServiceOrder as ServiceOrderEloquentModel;

class EloquentServiceOrderRepository implements ServiceOrderRepositoryInterface
{
    public function save(ServiceOrder $serviceOrder): ServiceOrder{

        $data = [];
        $data['exitDateTime'] = null;
        if( !empty($serviceOrder->getVehiclePlate()) ){
            $data['vehiclePlate'] = $serviceOrder->getVehiclePlate();
        }
        if( !empty($serviceOrder->getEntryDateTime()) ){
            $data['entryDateTime'] = $serviceOrder->getEntryDateTime();
        }
        if( !empty($serviceOrder->getExitDateTime()) ){
            $data['exitDateTime'] = $serviceOrder->getExitDateTime();
        }
        if( !empty($serviceOrder->getPriceType()) ){
            $data['priceType'] = $serviceOrder->getPriceType();
        }
        if( !empty($serviceOrder->getPrice()) ){
            $data['price'] = $serviceOrder->getPrice();
        }
        if( !empty($serviceOrder->getUserId()) ){
            $data['user_id'] = $serviceOrder->getUserId();
        }
        
        return $this->convertDomainObjet(ServiceOrderEloquentModel::create($data));
    }

    private function convertDomainObjet(ServiceOrderEloquentModel $serviceOrderEloquentModel): ServiceOrder
    {
        $serviceOrder = new ServiceOrder();
        $serviceOrder->setId($serviceOrderEloquentModel->id);
        $serviceOrder->setVehiclePlate($serviceOrderEloquentModel->vehiclePlate);
        $serviceOrder->setEntryDateTime($serviceOrderEloquentModel->entryDateTime);
        if( !empty($serviceOrderEloquentModel->exitDateTime) )
        {
            $serviceOrder->setExitDateTime($serviceOrderEloquentModel->exitDateTime);
        }
        $serviceOrder->setPriceType($serviceOrderEloquentModel->priceType);
        $serviceOrder->setPrice($serviceOrderEloquentModel->price);
        $serviceOrder->setUserId((int) $serviceOrderEloquentModel->user_id);
        return $serviceOrder;
    }

    private function getAllIdServiceOrder(array $serviceOrders){
        $arrayId = array();
        foreach($serviceOrders as $serviceOrder){
            $arrayId[] = $serviceOrder['id'];
        }
        return $arrayId;
    }
    public function update(array $serviceOrdersArray, ServiceOrder $serviceOrder): int{
        $arrayId = $this->getAllIdServiceOrder( $serviceOrdersArray );
        $data = [];
        if( !empty($serviceOrder->getVehiclePlate()) ){
            $data['vehiclePlate'] = $serviceOrder->getVehiclePlate();
        }
        if( !empty($serviceOrder->getEntryDateTime()) ){
            $data['entryDateTime'] = $serviceOrder->getEntryDateTime();
        }
        if( !empty($serviceOrder->getExitDateTime()) ){
            $data['exitDateTime'] = $serviceOrder->getExitDateTime();
        }
        if( !empty($serviceOrder->getPriceType()) ){
            $data['priceType'] = $serviceOrder->getPriceType();
        }
        if( !empty($serviceOrder->getPrice()) ){
            $data['price'] = $serviceOrder->getPrice();
        }
        return ServiceOrderEloquentModel::whereIn('id', $arrayId)->update($data);
    }

    public function remove(int $serviceOrderId): bool
    {
        $serviceOrderEloquentModel = ServiceOrderEloquentModel::findOrFail($serviceOrderId);
        return $serviceOrderEloquentModel->delete();
    }
    
    public function listByPlate(string $plate, float $price): array{
        return ServiceOrderEloquentModel::where('vehiclePlate', $plate)
        ->where('price', $price)->get()->toArray();
    }

    public function listById(int $serviceOrderId): ServiceOrder | bool
    {
        $serviceOrder = ServiceOrderEloquentModel::find($serviceOrderId);
        if( empty($serviceOrder) || $serviceOrder == null)
        {
            return false;
        }
        return $this->convertDomainObjet($serviceOrder);
    }

    public function list(string $search, int $start=0, int $limit=5) : array{
        return ServiceOrderEloquentModel::with(['user' => function ($query) {
            $query->select('id', 'name', 'email');
        }])
        ->select('service_orders.*')
        ->where('vehiclePlate', 'like', "%$search%")
        ->orWhereNull('vehiclePlate')
        ->skip($start)
        ->take($limit)
        ->get()
        ->toArray();
    }
}