<?php

namespace Tests\Unit;

use App\Core\CreateServiceOrderUseCase;
use App\Core\EditServiceOrderUseCase;
use App\Core\ListByIdServiceOrderUseCase;
use App\Core\ListByPlateServiceOrderUseCase;
use App\Core\ListPaginationOrderUseCase;
use App\Core\RemoveServiceOrderUseCase;
use App\Domain\ServiceOrder;
use App\Domain\ServiceOrder\ServiceOrderRepository;
use App\Utils\Messages;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

use Mockery;

class ServiceOrderTest extends TestCase
{
   private string $vehiclePlate = "GSA2934";
   private string $entryDateTime = "2022-11-30 16:00:00";
   private string $exitDateTime = "2022-11-31 16:00:00";
   private string $priceType = "";
   private float $price = 30.00;
   private int $userId = 1;
    
   public function tearDown(): void {
      Mockery::close();
   }

   #Criação
   //Deve receber como parâmetro todas as colunas da tabela 'service_orders'.
   public function test_if_receive_all_columns_of_service_orders_when_create(): void
   {
      $service_order_return = new ServiceOrder();
      $service_order_return->setId(1);
      $service_order_return->setVehiclePlate($this->vehiclePlate);
      $service_order_return->setEntryDateTime($this->entryDateTime);
      $service_order_return->setExitDateTime($this->exitDateTime);
      $service_order_return->setPriceType($this->priceType);
      $service_order_return->setPrice($this->price);
      $service_order_return->setUserId($this->userId);

      $service_order = new ServiceOrder();
      $service_order->setVehiclePlate($this->vehiclePlate);
      $service_order->setEntryDateTime($this->entryDateTime);
      $service_order->setExitDateTime($this->exitDateTime);
      $service_order->setPriceType($this->priceType);
      $service_order->setPrice($this->price);
      $service_order->setUserId($this->userId);

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $repository->shouldReceive('save')->andReturn($service_order_return);

      $createServiceOrderUseCase = new CreateServiceOrderUseCase($repository);
      $service_order_created = $createServiceOrderUseCase->execute($service_order);

      $this->assertEquals($service_order_created->getVehiclePlate(), $this->vehiclePlate);
      $this->assertEquals($service_order_created->getEntryDateTime(), $this->entryDateTime);
      $this->assertEquals($service_order_created->getExitDateTime(), $this->exitDateTime);
      $this->assertEquals($service_order_created->getPriceType(), $this->priceType);
      $this->assertEquals($service_order_created->getPrice(), $this->price);
      $this->assertEquals($service_order_created->getUserId(), $this->userId);
      $this->assertIsInt($service_order_created->getId());
      $this->assertNotEquals($service_order_created->getId(), 0);
   }

   /**
    * É importante fazer validações nos parâmetros de entrada a fim de evitar erros 
    * de inserção no banco de dados e inconsistência de dados.
    **/
   public function test_if_setVehiclePlate_is_invalid(): void
   {
      $this->expectExceptionMessage(Messages::$INVALID_VEHICLE_PLATE);

      $wrongVehiclePlate = "GSA2934c";
      $service_order = new ServiceOrder();
      $service_order->setVehiclePlate($wrongVehiclePlate);

      $wrongVehiclePlate = "GSA293J";
      $service_order = new ServiceOrder();
      $service_order->setVehiclePlate($wrongVehiclePlate);

      $wrongVehiclePlate = "2SA293J";
      $service_order = new ServiceOrder();
      $service_order->setVehiclePlate($wrongVehiclePlate);
   }

   public function test_if_setEntryDateTime_is_invalid(): void
   {
      $this->expectExceptionMessage(Messages::$DATE_OR_TIME_INVALID);

      $entryDateTime = "2023-300-10";
      $service_order = new ServiceOrder();
      $service_order->setEntryDateTime($entryDateTime);
   }

   public function test_if_setEntryDateTime_was_setted_before_setExitDateTime(): void
   {
      $this->expectExceptionMessage(Messages::$INSERT_ENTRY_DATETIME_BEFORE_EXIT_DATETIME);

      $exitDateTime = "2023-11-10 16:00:00";
      $service_order = new ServiceOrder();
      $service_order->setExitDateTime($exitDateTime);
   }

   public function test_if_setEntryDateTime_is_less_than_setExitDateTime(): void
   {
      $this->expectExceptionMessage(Messages::$DATETIME_ENTRY_NOT_LESS_THAN_DATETIME_EXIT);
      
      $entryDateTime = "2023-11-11 16:00:00";
      $exitDateTime = "2023-11-10 16:00:00";
      $service_order = new ServiceOrder();
      $service_order->setEntryDateTime($entryDateTime);
      $service_order->setExitDateTime($exitDateTime);
   }


   #Edição
   /**
    * Deve receber como parâmetro apenas as colunas da placa do veículo e valor de estadia 
    * para edição do registro na tabela 'service_orders'.
    *
    * É importante fazer validações nos parâmetros de entrada a fim de evitar erros de inserção no banco de dados e inconsistência de dados
    **/

    public function test_if_service_order_is_updated(): void
    {
      $service_order_return = new ServiceOrder();
      $service_order_return->setId(1);
      $service_order_return->setVehiclePlate($this->vehiclePlate);
      $service_order_return->setEntryDateTime($this->entryDateTime);
      $service_order_return->setExitDateTime($this->exitDateTime);
      $service_order_return->setPriceType($this->priceType);
      $service_order_return->setPrice($this->price);
      $service_order_return->setUserId($this->userId);

      $list[] = $service_order_return;

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $repository->shouldReceive('listByPlate')->andReturn($list);
      $listByPlateServiceOrderUseCase = new ListByPlateServiceOrderUseCase($repository);

      $repository->shouldReceive('update')->andReturn(1);
      $editServiceOrderUseCase = new EditServiceOrderUseCase($repository, $listByPlateServiceOrderUseCase);
      
      $vehiclePlate = "GSA2934";
      $newPrice = 60.0;

      $result = $editServiceOrderUseCase->execute($vehiclePlate, $newPrice, $service_order_return);

      $this->assertEquals($result, 1);
    }

    public function test_if_service_order_is_updated_if_plate_not_set(): void
    {
      $this->expectExceptionMessage(Messages::$INVALID_VEHICLE_PLATE);
      
      $service_order_return = new ServiceOrder();
      $service_order_return->setId(1);
      $service_order_return->setVehiclePlate($this->vehiclePlate);
      $service_order_return->setEntryDateTime($this->entryDateTime);
      $service_order_return->setExitDateTime($this->exitDateTime);
      $service_order_return->setPriceType($this->priceType);
      $service_order_return->setPrice($this->price);
      $service_order_return->setUserId($this->userId);

      $list[] = $service_order_return;
      
      $repository = Mockery::mock(ServiceOrderRepository::class);
      $repository->shouldReceive('listByPlate')->andReturn($list);
      $listByPlateServiceOrderUseCase = new ListByPlateServiceOrderUseCase($repository);
      $editServiceOrderUseCase = new EditServiceOrderUseCase($repository, $listByPlateServiceOrderUseCase);

      $service_order = new ServiceOrder();
      $service_order->setId(1);
      $service_order->setVehiclePlate($this->vehiclePlate);
      $service_order->setEntryDateTime($this->entryDateTime);
      $service_order->setExitDateTime($this->exitDateTime);
      $service_order->setPriceType($this->priceType);
      $service_order->setPrice($this->price);
      $service_order->setUserId($this->userId);

      $editServiceOrderUseCase->execute("", 60.0, $service_order);
    }

   public function test_if_service_order_is_updated_if_value_not_set(): void
   {
      $this->expectExceptionMessage(Messages::$INVALID_PRICE);
      
      $service_order_return = new ServiceOrder();
      $service_order_return->setId(1);
      $service_order_return->setVehiclePlate($this->vehiclePlate);
      $service_order_return->setEntryDateTime($this->entryDateTime);
      $service_order_return->setExitDateTime($this->exitDateTime);
      $service_order_return->setPriceType($this->priceType);
      $service_order_return->setPrice($this->price);
      $service_order_return->setUserId($this->userId);
      
      $list[] = $service_order_return;

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $repository->shouldReceive('listByPlate')->andReturn($list);
      $listByPlateServiceOrderUseCase = new ListByPlateServiceOrderUseCase($repository);
      $editServiceOrderUseCase = new EditServiceOrderUseCase($repository, $listByPlateServiceOrderUseCase);

      $service_order = new ServiceOrder();
      $service_order->setId(1);
      $service_order->setVehiclePlate($this->vehiclePlate);
      $service_order->setEntryDateTime($this->entryDateTime);
      $service_order->setExitDateTime($this->exitDateTime);
      $service_order->setPriceType($this->priceType);
      $service_order->setPrice($this->price);
      $service_order->setUserId($this->userId);

      $serviceOrder = $editServiceOrderUseCase->execute("GSA3044", -20.0, $service_order);
   }

    public function test_if_remove_service_order_by_order_service_id(): void
    {
      $service_order_return = new ServiceOrder();
      $service_order_return->setId(1);
      $service_order_return->setVehiclePlate($this->vehiclePlate);
      $service_order_return->setEntryDateTime($this->entryDateTime);
      $service_order_return->setExitDateTime($this->exitDateTime);
      $service_order_return->setPriceType($this->priceType);
      $service_order_return->setPrice($this->price);
      $service_order_return->setUserId($this->userId);

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $repository->shouldReceive('listById')->andReturn($service_order_return);
      $listByIdServiceOrderUseCase = new ListByIdServiceOrderUseCase($repository);

      $repository->shouldReceive('remove')->andReturn(true);
      $removeServiceOrderUseCase = new RemoveServiceOrderUseCase($repository, $listByIdServiceOrderUseCase);
      $result = $removeServiceOrderUseCase->execute(1);
      $this->assertTrue($result);
    }

    public function test_if_display_exception_if_service_order_not_exist(): void
    {
      $this->expectExceptionMessage(Messages::$VEHICLE_NOT_FOUND);

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $listByIdServiceOrderUseCase = Mockery::mock(ListByIdServiceOrderUseCase::class);
      $listByIdServiceOrderUseCase->shouldReceive('execute')->with(2)->andReturn([]);

      $removeServiceOrderUseCase = new RemoveServiceOrderUseCase($repository, $listByIdServiceOrderUseCase);
      $removeServiceOrderUseCase->execute(2);
    }

   #Lista
   //Deve ter um filtro para buscar por placa de veículo.
   public function test_if_service_order_is_listed_by_plate(): void
   {
      $vehiclePlateSearched = "GSA2934";

      $service_order_return = new ServiceOrder();
      $service_order_return->setId(1);
      $service_order_return->setVehiclePlate($this->vehiclePlate);
      $service_order_return->setEntryDateTime($this->entryDateTime);
      $service_order_return->setExitDateTime($this->exitDateTime);
      $service_order_return->setPriceType($this->priceType);
      $service_order_return->setPrice($this->price);
      $service_order_return->setUserId($this->userId);

      $list[] = $service_order_return;

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $repository->shouldReceive('listByPlate')->andReturn($list);
      $listByPlateServiceOrderUseCase = new ListByPlateServiceOrderUseCase($repository);
      $result = $listByPlateServiceOrderUseCase->execute($this->vehiclePlate, $this->price);
      $this->assertIsArray($result);
      $this->assertEquals($result[0]->getVehiclePlate(), $vehiclePlateSearched);
   }

   #Deve listar todas as colunas presentes na tabela 'service_orders'
   #Deve conter um mecanismo de paginação.
   #Cada página deve ter apenas 5 itens.
   #Deve ter um filtro para buscar por placa de veículo.
   public function test_if_list_by_page_and_filter(): void
   {
      $service_order = new ServiceOrder();
      $service_order->setId(1);
      $service_order->setVehiclePlate("GSA2934");
      $service_order->setEntryDateTime("2022-11-30 16:00:00");
      $service_order->setExitDateTime("2022-11-31 16:00:00");
      $service_order->setPriceType("");
      $service_order->setPrice(30.00);
      $service_order->setUserId(1);

      $list[] = $service_order;

      $service_order = new ServiceOrder();
      $service_order->setId(1);
      $service_order->setVehiclePlate("YKV0384");
      $service_order->setEntryDateTime("2022-11-27 14:00:00");
      $service_order->setExitDateTime("2022-11-39 14:00:00");
      $service_order->setPriceType("");
      $service_order->setPrice(70.00);
      $service_order->setUserId(2);

      $list[] = $service_order;

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $listPaginationOrderUseCase = new ListPaginationOrderUseCase($repository);
      $repository->shouldReceive('list')->andReturn($list);
      $return = $listPaginationOrderUseCase->execute("");
      $this->assertEquals(count($return), 2);
   }

   public function test_if_list_by_page_and_filter_paginate_1_item(): void
   {
      $serviceOrder1 = new ServiceOrder();
      $serviceOrder1->setId(1);
      $serviceOrder1->setVehiclePlate("GSA2934");
      $serviceOrder1->setEntryDateTime("2022-11-30 16:00:00");
      $serviceOrder1->setExitDateTime("2022-11-31 16:00:00");
      $serviceOrder1->setPriceType("");
      $serviceOrder1->setPrice(30.00);
      $serviceOrder1->setUserId(1);
  
      $serviceOrder2 = new ServiceOrder();
      $serviceOrder2->setId(2);
      $serviceOrder2->setVehiclePlate("YKV0384");
      $serviceOrder2->setEntryDateTime("2022-11-27 13:00:00");
      $serviceOrder2->setExitDateTime("2022-11-29 14:00:00"); // Corrigindo a data de saída aqui
      $serviceOrder2->setPriceType("");
      $serviceOrder2->setPrice(80.00);
      $serviceOrder2->setUserId(2);
  
      $serviceOrder3 = new ServiceOrder();
      $serviceOrder3->setId(3);
      $serviceOrder3->setVehiclePlate("HGS9384");
      $serviceOrder3->setEntryDateTime("2022-10-01 13:00:00");
      $serviceOrder3->setExitDateTime("2022-10-02 15:00:00");
      $serviceOrder3->setPriceType("");
      $serviceOrder3->setPrice(40.00);
      $serviceOrder3->setUserId(2);
  
      $list = [$serviceOrder1, $serviceOrder2, $serviceOrder3];

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $listPaginationOrderUseCase = new ListPaginationOrderUseCase($repository);
      $repository->shouldReceive('list')->andReturn($list);
  
      $return = $listPaginationOrderUseCase->execute("", 0, 1);
  
      $this->assertIsArray($return);
   }


   public function test_if_list_return_a_list_plate_orders_service(): void
   {

      $serviceOrder = new ServiceOrder();
      $serviceOrder->setId(2);
      $serviceOrder->setVehiclePlate("YKV0384");
      $serviceOrder->setEntryDateTime("2022-11-27 13:00:00");
      $serviceOrder->setExitDateTime("2022-11-29 14:00:00"); // Corrigindo a data de saída aqui
      $serviceOrder->setPriceType("");
      $serviceOrder->setPrice(80.00);
      $serviceOrder->setUserId(2);

      $list[] = $serviceOrder;

      $repository = Mockery::mock(ServiceOrderRepository::class);
      $listPaginationOrderUseCase = new ListPaginationOrderUseCase($repository);
      $repository->shouldReceive('list')->andReturn($list);
  
      $serviceOrder = $listPaginationOrderUseCase->execute("YKV0384");
  
      $this->assertEquals($serviceOrder[0]->getVehiclePlate(), "YKV0384");
   }
}