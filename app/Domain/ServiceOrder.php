<?php

namespace App\Domain;

use App\Application\PlateValidation;
use App\Domain\Strategy\BrazilPlateValidationStrategy;
use App\Utils\Messages;
use DateTime;
use InvalidArgumentException;

class ServiceOrder {
    private int $id = 0;
    private string $vehiclePlate = "";
    private string $entryDateTime = "";
    private string $exitDateTime = "";
    private string $priceType = "";
    private float $price = 0.00;
    private int $userId = 0;

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getVehiclePlate(): string {
        return $this->vehiclePlate;
    }

    public function setVehiclePlate(string $vehiclePlate): void {
        $plateValidation = new PlateValidation(new BrazilPlateValidationStrategy());
        if (!$plateValidation->validate($vehiclePlate)) {
            throw new InvalidArgumentException(Messages::$INVALID_VEHICLE_PLATE);
        }
        $this->vehiclePlate = $vehiclePlate;
    }

    public function getEntryDateTime(): string {
        return $this->entryDateTime;
    }

    public function setEntryDateTime(string $entryDateTime): void {
        if ($this->isValidDateTime($entryDateTime) ) {
            $this->entryDateTime = $entryDateTime;
        }
    }

    public function getExitDateTime(): string {
        return $this->exitDateTime;
    }

    public function setExitDateTime(string $exitDateTime): void {

        if( !empty($this->getEntryDateTime()) )
        {
            if( $this->checkIfEntryDateTimeIsLessThanExitDateTime($this->getEntryDateTime(), $exitDateTime) == false ){
                throw new InvalidArgumentException(Messages::$DATETIME_ENTRY_NOT_LESS_THAN_DATETIME_EXIT);
            }
            $this->exitDateTime = $exitDateTime;
        }
        else
        {
            throw new InvalidArgumentException(Messages::$INSERT_ENTRY_DATETIME_BEFORE_EXIT_DATETIME);
        }
    }

    public function getPriceType(): string {
        return $this->priceType;
    }

    public function setPriceType(string $priceType): void {
        $this->priceType = $priceType;
    }

    public function getPrice(): float {
        return $this->price;
    }

    public function setPrice(float $price): void {
        $this->price = $price;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }

    private function isValidDateTime(string $dateTime){
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        if ($dateTime === false) {
            throw new InvalidArgumentException(Messages::$DATE_OR_TIME_INVALID);
            return false;
        }
        return true;
    }

    private function checkIfEntryDateTimeIsLessThanExitDateTime(string $entryDateTime, string $exitDateTime){
        $startDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $entryDateTime);
        $endDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $exitDateTime);
        return match (true) {
            $startDateTime < $endDateTime => true,
            $startDateTime > $endDateTime => false,
            default => false,
        };
    }

    public function toArray()
    {
        return array(
            "id"=>$this->id, 
            "vehiclePlate"=>$this->vehiclePlate, 
            "entryDateTime"=>$this->entryDateTime, 
            "exitDateTime"=>$this->exitDateTime,
            "priceType"=>$this->priceType,
            "price"=>$this->price, 
            "userId"=>$this->userId);
    }
}
